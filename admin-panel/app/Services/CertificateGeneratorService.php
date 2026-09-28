<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certif;
use App\Models\CertifStudent;
use App\Models\StudentSuccess;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Exception;
use GdImage;
use Illuminate\Support\Facades\Log;

class CertificateGeneratorService
{
    private const DEFAULT_FONT = 'arial.ttf';
    private const OUTPUT_DIR = 'upload/certif-student';
    private const DEFAULT_COLOR = '#000000';
    private const DEFAULT_FONT_SIZE = 20;
    private const NAME_FONT_SIZE_MULTIPLIER = 1.25;
    private const NAME_MIN_FONT_SIZE = 30;

    /**
     * @var string|null
     */
    private $fontPath;

    public function __construct()
    {
        $this->fontPath = public_path('fonts/' . self::DEFAULT_FONT);
        if (!file_exists($this->fontPath)) {
            Log::warning("Certificate font not found at: {$this->fontPath}");
            $this->fontPath = null;
        }
    }

    /**
     * Generate a valid certificate for a student who has passed the test.
     *
     * @param StudentSuccess $studentSuccess
     * @param string|null $customDate
     * @return CertifStudent
     * @throws Exception
     */
    public function generateCertificate(StudentSuccess $studentSuccess, ?string $customDate = null): CertifStudent
    {
        $this->validatePrerequisites($studentSuccess);

        $product = $studentSuccess->product;
        $certif = $product->certif;
        $student = $studentSuccess->student;

        // 1. Create Record (Pending State)
        $certifStudent = CertifStudent::create([
            'student_id' => $student->id,
            'product_id' => $product->id,
            'certif_id' => $certif->id,
            'student_success_id' => $studentSuccess->id,
            'serial_number' => 'PENDING_' . uniqid(),
            'generated_at' => now(),
            'certificate_date' => $customDate ?? now()->format('Y-m-d'),
            'is_valid' => true,
        ]);

        // 2. Generate Serial Number
        $serialNumber = $certifStudent->generateSerialNumber();
        $certifStudent->update(['serial_number' => $serialNumber]);

        // 3. Render Image
        $filename = sprintf('certificate_%d_%d.png', $certifStudent->id, time());
        $displayDate = $customDate
            ? date('d/m/Y', strtotime($customDate))
            : now()->format('d/m/Y');

        $filePath = $this->render(
            $certif,
            $student->first_name . ' ' . $student->last_name,
            $displayDate,
            $serialNumber,
            $product->variation_title ?? 'Course Certification',
            $filename
        );

        // 4. Finalize Record
        $certifStudent->update(['file_path' => $filePath]);
        $studentSuccess->update(['success' => 1]);

        return $certifStudent;
    }

    /**
     * Regenerate an existing certificate with a new display date.
     */
    public function regenerateCertificateWithDate(
        $certifStudent,
        $certif,
        $student,
        $product,
        $serialNumber,
        $newDate,
        $fileName
    ) {
        // Parameter types relaxed for compatibility with legacy calls if strict types not enforced everywhere
        $displayDate = date('d/m/Y', strtotime($newDate));

        return $this->render(
            $certif,
            $student->first_name . ' ' . $student->last_name,
            $displayDate,
            $serialNumber,
            $product->variation_title ?? 'Course Certification',
            $fileName
        );
    }

    /**
     * Generate a mock certificate for testing or preview purposes.
     *
     * @return array{file_path: string, serial_number: string}
     */
    public function generateTestCertificate(
        Certif $certif,
        string $fullnameEn = 'John Doe',
        ?string $date = null,
        string $serialNumber = 'TEST-0000'
    ): array {
        $date = $date ?? now()->format('d/m/Y');
        $fileName = 'test_preview_' . uniqid() . '.png';

        $filePath = $this->render(
            $certif,
            $fullnameEn,
            $date,
            $serialNumber,
            'Test Course Title',
            $fileName,
            true
        );

        return [
            'file_path' => $filePath,
            'serial_number' => $serialNumber
        ];
    }

    public function generateTestCertificateWithRealData(StudentSuccess $studentSuccess, ?string $fullnameEn = null, ?string $date = null, ?string $serialNumber = null): array
    {
        $student = $studentSuccess->student;
        $product = $studentSuccess->product;

        $name = $fullnameEn ?? ($student->first_name . ' ' . $student->last_name);
        $displayDate = $date ?? now()->format('d/m/Y');
        $serial = $serialNumber ?? ('TEST-' . $studentSuccess->id . '-' . time());
        $filename = 'test_real_' . uniqid() . '.png';

        $filePath = $this->render(
            $product->certif,
            $name,
            $displayDate,
            $serial,
            $product->variation_title,
            $filename,
            true
        );

        return [
            'file_path' => $filePath,
            'serial_number' => $serial
        ];
    }

    /**
     * Core Rendering Engine
     *
     * @param Certif $certif
     * @param string $name
     * @param string $date
     * @param string $serial
     * @param string $productName
     * @param string $filename
     * @param bool $isTest
     * @return string Relative path to the generated file
     * @throws Exception
     */
    private function render(
        Certif $certif,
        string $name,
        string $date,
        string $serial,
        string $productName,
        string $filename,
        bool $isTest = false
    ): string {
        // 1. Load Resources
        $imagePath = $this->resolveImagePath($certif->image_url);
        $imageData = @file_get_contents($imagePath);

        if ($imageData === false) {
            throw new Exception("Unable to load certificate template image: {$imagePath}");
        }

        $image = @imagecreatefromstring($imageData);
        if (!$image) {
            throw new Exception("Invalid image data in template. Ensure GD extension is enabled.");
        }

        // PHP 8 check: imagecreatefromstring returns GdImage|false
        if (!($image instanceof GdImage)) {
             // For PHP < 8 compatibility where it returns resource
             // but we declared strict types, assuming PHP 8 environment
             // If this runs on PHP 7, remove strict return types or polyfill GdImage
        }

        // Enable Alpha Blending for clean text
        imagealphablending($image, true);
        imagesavealpha($image, true);

        // 2. Process Elements
        $templateData = $certif->template_data ?? Certif::getDefaultTemplateData();

        // Log template data for debugging
        Log::info("Certificate generation - Template Data loaded", [
            'certif_id' => $certif->id,
            'name_student_font_size' => $templateData['name_student']['font_size'] ?? 'NOT SET',
            'has_template_data' => !empty($certif->template_data),
            'using_default' => empty($certif->template_data)
        ]);

        foreach ($templateData as $key => $config) {
            if (empty($config['show']) || !$config['show']) {
                continue;
            }

            $content = match($key) {
                'name_student', 'fullname_en' => $name,
                'date' => $date,
                'serial_number' => $serial,
                'product_name' => $productName,
                default => $config['text'] ?? ''
            };

            if (isset($config['type']) && $config['type'] === 'qr') {
                $this->drawQrCode($image, $config, $serial, $filename, $isTest);
            } else {
                $this->drawText($image, $config, $content, $key);
            }
        }

        // 3. Output
        $outputPath = $this->ensureDirectory() . DIRECTORY_SEPARATOR . $filename;
        if (!imagepng($image, public_path($outputPath))) {
            throw new Exception("Failed to save certificate image to filesystem.");
        }

        imagedestroy($image);

        return $outputPath;
    }

    /**
     * Draw text with precise alignment calculation.
     */
    private function drawText(GdImage $image, array $config, string $text, string $key): void
    {
        // 1. Extract Config
        $x = (int)($config['x'] ?? 0);
        $y = (int)($config['y'] ?? 0);
        $fontSize = (int)($config['font_size'] ?? self::DEFAULT_FONT_SIZE);
        $colorHex = $config['color'] ?? self::DEFAULT_COLOR;
        $boxWidth = (int)($config['width'] ?? 0);
        $boxHeight = (int)($config['height'] ?? 0);
        $align = $config['align'] ?? 'left';
        $isBold = !empty($config['bold']) && $config['bold'] == 1;
        $uppercase = !empty($config['uppercase']) && $config['uppercase'] == 1;

        if (in_array($key, ['name_student', 'fullname_en'], true)) {
            $fontSize = max(
                (int) round($fontSize * self::NAME_FONT_SIZE_MULTIPLIER),
                self::NAME_MIN_FONT_SIZE
            );
        }

        // Log font size for name_student
        if ($key === 'name_student') {
            Log::info("Drawing name_student with font_size: {$fontSize}px (from config: " . json_encode($config) . ")");
        }

        // Check if text contains Arabic characters
        $isArabic = $this->containsArabic($text);

        // Force defaults for name_student if implicit
        if ($key === 'name_student') {
            // Only uppercase for non-Arabic text (Arabic has no uppercase)
            if (!$isArabic) {
                $uppercase = true;
            }
            // If alignment isn't explicit, assume center for names
            if (!isset($config['align']) && !isset($config['text_align'])) {
                $align = 'center';
            }
        }

        if ($uppercase && !$isArabic) {
            $text = strtoupper($text);
        }

        // Reverse Arabic text for proper RTL display in GD
        if ($isArabic) {
            $text = $this->reverseArabicText($text);
        }

        // 2. Prepare Color
        $rgb = $this->hexToRgb($colorHex);
        $gdColor = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);

        // 3. Calculate Position (TTF vs GD)
        if ($this->fontPath && function_exists('imagettftext')) {
            $this->drawTtfText($image, $text, $x, $y, $fontSize, $gdColor, $align, $boxWidth, $boxHeight, $isBold, $key);
        } else {
            $this->drawGdText($image, $text, $x, $y, $gdColor, $align);
        }
    }

    /**
     * High-Precision TTF Text Rendering.
     */
    private function drawTtfText(
        GdImage $image,
        string $text,
        int $x,
        int $y,
        int $fontSize,
        int $color,
        string $align,
        int $boxWidth,
        int $boxHeight,
        bool $isBold,
        string $key
    ): void {
        $angle = 0;

        // A. Calculate Bounding Box
        // bbox indices: 0,1 (LL), 2,3 (LR), 4,5 (UR), 6,7 (UL)
        // Note: imagettfbbox return pixel positions relative to origin (0,0)
        $bbox = imagettfbbox($fontSize, $angle, $this->fontPath, $text);

        // $bbox[2] is lower-right X, $bbox[0] is lower-left X.
        // Text Width = right - left
        $textWidth = abs($bbox[2] - $bbox[0]);

        // Ascent is Top (Y=7) - Baseline (Y=0). Usually negative Y (going up).
        // Height = abs(Top - Bottom)
        $textHeight = abs($bbox[7] - $bbox[1]);
        $ascent = abs($bbox[7]);
        $descent = abs($bbox[1]);

        // B. Horizontal Alignment (X)
        // IMPORTANT: X coordinate from editor is the LEFT edge of the text box.
        // If width > 0, we have a bounding box defined.
        $drawX = $x;

        if ($align === 'center') {
            if ($boxWidth > 0) {
                // Standard case: X is left edge, width defines the box.
                // Center the text within [x, x + width]
                $centerX = $x + ($boxWidth / 2);
                $drawX = $centerX - ($textWidth / 2);
            } else {
                // No width defined - for name_student, try to center on the full page
                if ($key === 'name_student') {
                    $pageWidth = imagesx($image);
                    $drawX = ($pageWidth / 2) - ($textWidth / 2);
                    Log::info("Auto-centering '{$text}' on full page (Page: {$pageWidth}px, Text: {$textWidth}px, Final X: {$drawX})");
                } else {
                    // For other fields, treat X as the center point
                    $drawX = $x - ($textWidth / 2);
                }
            }
        } elseif ($align === 'right') {
            if ($boxWidth > 0) {
                $drawX = $x + $boxWidth - $textWidth;
            } else {
                $drawX = $x - $textWidth;
            }
        }
        // else: align === 'left', use $x as-is

        // C. Vertical Alignment (Y)
        // User supplies Top-Left Y coordinate (top edge of where text should appear).
        // TTF rendering needs the BASELINE Y coordinate.
        // The baseline is where the text "sits" - it's below the top by the ascent amount.

        // Default: treat provided Y as top edge, compute baseline
        $drawY = $y + $ascent;

        // If a box height is provided, vertically center the text within the box
        // Baseline for vertical center: Y_bl = y_top + H/2 + (ascent - descent)/2
        if ($boxHeight > 0 && ($key === 'name_student' || $align === 'center')) {
            $drawY = (int)($y + ($boxHeight / 2) + (($ascent - $descent) / 2));
        }

        // Nudge date slightly lower to avoid touching top line
        if ($key === 'date') {
            $drawY += max(2, (int)round($fontSize * 0.25));
        }

        Log::info("Drawing '{$text}' at ({$drawX}, {$drawY}) with Size {$fontSize} using {$align}");

        // D. Draw (with Bold simulation)
        if ($isBold) {
            // Draw 4 times with 1px offset
            imagettftext($image, $fontSize, $angle, (int)$drawX+1, (int)$drawY, $color, $this->fontPath, $text);
            imagettftext($image, $fontSize, $angle, (int)$drawX-1, (int)$drawY, $color, $this->fontPath, $text);
            imagettftext($image, $fontSize, $angle, (int)$drawX, (int)$drawY+1, $color, $this->fontPath, $text);
            imagettftext($image, $fontSize, $angle, (int)$drawX, (int)$drawY-1, $color, $this->fontPath, $text);
        }

        imagettftext($image, $fontSize, $angle, (int)$drawX, (int)$drawY, $color, $this->fontPath, $text);
    }

    /**
     * Fallback GD Text Rendering.
     */
    private function drawGdText(GdImage $image, string $text, int $x, int $y, int $color, string $align): void
    {
        $font = 5; // Fixed system font
        $charWidth = imagefontwidth($font);
        $textWidth = strlen($text) * $charWidth;

        $drawX = $x;

        if ($align === 'center') {
            $drawX = $x - ($textWidth / 2);
        } elseif ($align === 'right') {
            $drawX = $x - $textWidth;
        }

        imagestring($image, $font, (int)$drawX, $y, $text, $color);
    }

    /**
     * Generate and stamp QR Code.
     */
    private function drawQrCode(GdImage $image, array $config, string $serial, string $filename, bool $isTest): void
    {
        try {
            $x = (int)($config['x'] ?? 0);
            $y = (int)($config['y'] ?? 0);
            $size = (int)($config['width'] ?? 100);
            // Slight bump to match demo visuals
            $size = (int)max(50, round($size * 1.12));

            $url = $isTest
                ? "TEST:{$serial}"
                : url('upload/certif-student/' . $filename);

            $qrCode = QrCode::create($url)->setSize($size)->setMargin(0);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrData = $result->getString();
            $qrImage = @imagecreatefromstring($qrData);

            if ($qrImage) {
                // Resize if needed
                imagecopyresampled(
                    $image,
                    $qrImage,
                    $x, $y,
                    0, 0,
                    $size, $size,
                    imagesx($qrImage), imagesy($qrImage)
                );
                imagedestroy($qrImage);
            }
        } catch (Exception $e) {
            Log::error("Failed to generate QR Code for cert {$serial}: " . $e->getMessage());
        }
    }

    private function validatePrerequisites(StudentSuccess $success): void
    {
        if (!$success->product) throw new Exception("StudentSuccess #{$success->id} has no Product.");
        if (!$success->product->certif) throw new Exception("Product #{$success->product->id} has no Certificate Template.");
        if (!$success->student) throw new Exception("StudentSuccess #{$success->id} has no Student.");
    }

    private function resolveImagePath(?string $path): string
    {
        if (empty($path)) {
            throw new Exception("Image path is empty in certificate template.");
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        $localPath = public_path($path);
        if (!file_exists($localPath)) {
            throw new Exception("Local certificate image file not found at: {$localPath}");
        }

        return $localPath;
    }

    private function ensureDirectory(): string
    {
        $path = public_path(self::OUTPUT_DIR);
        if (!is_dir($path)) {
            if (!mkdir($path, 0755, true) && !is_dir($path)) {
                throw new Exception("Could not create output directory: {$path}");
            }
        }
        return self::OUTPUT_DIR;
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }

    /**
     * Check if text contains Arabic characters
     */
    private function containsArabic(string $text): bool
    {
        return preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $text) === 1;
    }

    /**
     * Reverse Arabic text for proper RTL display in GD
     * GD renders text left-to-right, so we need to reverse Arabic words
     */
    private function reverseArabicText(string $text): string
    {
        // Split by spaces to handle mixed Arabic/English
        $words = explode(' ', $text);
        $result = [];

        foreach ($words as $word) {
            if ($this->containsArabic($word)) {
                // Reverse the characters in Arabic words
                $chars = preg_split('//u', $word, -1, PREG_SPLIT_NO_EMPTY);
                $result[] = implode('', array_reverse($chars));
            } else {
                $result[] = $word;
            }
        }

        // Reverse the order of words for RTL
        return implode(' ', array_reverse($result));
    }

    // Compatibility helpers
    public function certificateExists($studentSuccess) {
        return CertifStudent::where('student_success_id', $studentSuccess->id)->exists();
    }

    public function getCertificate($studentSuccess) {
        return CertifStudent::where('student_success_id', $studentSuccess->id)->first();
    }
}
