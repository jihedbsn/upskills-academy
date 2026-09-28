<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certif extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'file_url',
        'image_url',
        'template_data', // JSON pour stocker les positions des variables
        'orientation', // vertical ou horizontal
        'is_active',
    ];

    protected $casts = [
        'template_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the file URL with proper path.
     */
    public function getFileUrlAttribute($value)
    {
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        return asset($value);
    }

    /**
     * Get default template data structure.
     */
    public static function getDefaultTemplateData()
    {
        return [
            'name_student' => [
                'x' => 600,
                'y' => 100,
                'width' => 200,
                'height' => 30,
                'show' => true,
                'text' => 'Nom de l\'Étudiant',
                'font_size' => 20,
                'color' => '#000000',
                'font_family' => 'Arial',
                'type' => 'text',
                'is_dynamic' => false
            ],
            'date' => [
                'x' => 600,
                'y' => 300,
                'width' => 150,
                'height' => 30,
                'show' => true,
                'text' => 'Date',
                'font_size' => 14,
                'color' => '#000000',
                'font_family' => 'Arial',
                'type' => 'date',
                'is_dynamic' => false
            ],
            'qr_code' => [
                'x' => 600,
                'y' => 200,
                'width' => 100,
                'height' => 100,
                'show' => true,
                'text' => 'QR Code',
                'font_size' => 12,
                'color' => '#000000',
                'font_family' => 'Arial',
                'type' => 'qr',
                'is_dynamic' => false
            ],
            'serial_number' => [
                'x' => 600,
                'y' => 350,
                'width' => 150,
                'height' => 30,
                'show' => true,
                'text' => 'Serial Number',
                'font_size' => 14,
                'color' => '#000000',
                'font_family' => 'Arial',
                'type' => 'text',
                'is_dynamic' => false
            ]
        ];
    }

    /**
     * Get dynamic fields from template data
     */
    public function getDynamicFields()
    {
        $dynamicFields = [];
        if ($this->template_data) {
            foreach ($this->template_data as $key => $field) {
                if (isset($field['is_dynamic']) && $field['is_dynamic']) {
                    $dynamicFields[$key] = $field;
                }
            }
        }
        return $dynamicFields;
    }

    /**
     * Add a new dynamic field
     */
    public function addDynamicField($fieldKey, $fieldData)
    {
        $templateData = $this->template_data ?: [];
        $templateData[$fieldKey] = array_merge([
            'x' => 100,
            'y' => 100,
            'width' => 200,
            'height' => 30,
            'show' => true,
            'text' => 'Nouveau Champ',
            'font_size' => 16,
            'color' => '#000000',
            'font_family' => 'Arial',
            'type' => 'text',
            'is_dynamic' => true
        ], $fieldData);

        $this->template_data = $templateData;
        $this->save();
    }

    /**
     * Remove a dynamic field
     */
    public function removeDynamicField($fieldKey)
    {
        $templateData = $this->template_data ?: [];
        if (isset($templateData[$fieldKey]) && isset($templateData[$fieldKey]['is_dynamic']) && $templateData[$fieldKey]['is_dynamic']) {
            unset($templateData[$fieldKey]);
            $this->template_data = $templateData;
            $this->save();
            return true;
        }
        return false;
    }
}
