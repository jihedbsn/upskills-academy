<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certif;

class FixCertificateReferenceDimensions extends Command
{
    protected $signature = 'certificates:fix-dimensions';
    protected $description = 'Add reference dimensions to all certificate templates for proper scaling';

    public function handle()
    {
        $this->info('Starting to fix certificate reference dimensions...');

        $certificates = Certif::all();
        $fixed = 0;
        $skipped = 0;

        foreach ($certificates as $certif) {
            $templateData = $certif->template_data ?? [];

            // Skip if already has reference dimensions
            if (isset($templateData['_reference_width']) && isset($templateData['_reference_height'])) {
                $skipped++;
                continue;
            }

            // Get image path
            $imagePath = $this->getImagePath($certif);

            if (!$imagePath || !file_exists($imagePath)) {
                $this->warn("Certificate {$certif->id}: Image not found at {$imagePath}");
                continue;
            }

            // Get image dimensions
            $imageSize = getimagesize($imagePath);

            if (!$imageSize) {
                $this->warn("Certificate {$certif->id}: Could not read image dimensions");
                continue;
            }

            // Add reference dimensions
            $templateData['_reference_width'] = $imageSize[0];
            $templateData['_reference_height'] = $imageSize[1];

            // Update certificate
            $certif->update(['template_data' => $templateData]);

            $this->info("Certificate {$certif->id}: Added dimensions {$imageSize[0]}x{$imageSize[1]}");
            $fixed++;
        }

        $this->info("Completed! Fixed: {$fixed}, Skipped: {$skipped}");

        return 0;
    }

    private function getImagePath($certif)
    {
        if (!$certif->image_url) {
            return null;
        }

        // If it's a full URL, return as is
        if (str_starts_with($certif->image_url, 'http')) {
            return $certif->image_url;
        }

        // Otherwise, look for local file
        $localPath = public_path($certif->image_url);
        if (file_exists($localPath)) {
            return $localPath;
        }

        return null;
    }
}
