<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CreateExcelTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Excel template for student import';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['first_name', 'last_name', 'email', 'phone'];
        
        // Set header row
        $sheet->setCellValue('A1', 'first_name');
        $sheet->setCellValue('B1', 'last_name');
        $sheet->setCellValue('C1', 'email');
        $sheet->setCellValue('D1', 'phone');

        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Sample data
        $sampleData = [
            ['Ahmed', 'Ben Ali', 'ahmed.benali@example.com', '+216 12 345 678'],
            ['Fatma', 'Khelil', 'fatma.khelil@example.com', '+216 98 765 432'],
            ['Mohamed', 'Trabelsi', 'mohamed.trabelsi@example.com', '+216 55 123 456'],
            ['Aicha', 'Mansouri', 'aicha.mansouri@example.com', '+216 77 987 654'],
            ['Karim', 'Bouaziz', 'karim.bouaziz@example.com', '+216 22 456 789'],
            ['Salma', 'Hamdi', 'salma.hamdi@example.com', '+216 33 789 123'],
            ['Youssef', 'Ben Salem', 'youssef.bensalem@example.com', '+216 44 321 987'],
            ['Nour', 'Chaabane', 'nour.chaabane@example.com', '+216 66 654 321'],
            ['Omar', 'Mejri', 'omar.mejri@example.com', '+216 88 147 258'],
            ['Lina', 'Ben Youssef', 'lina.benyoussef@example.com', '+216 99 369 147']
        ];

        // Add sample data
        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $sheet->setCellValue('C' . $row, $data[2]);
            $sheet->setCellValue('D' . $row, $data[3]);
            $row++;
        }

        // Style data rows
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];
        
        $sheet->getStyle('A2:D' . ($row - 1))->applyFromArray($dataStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);

        // Add instructions sheet
        $instructionsSheet = $spreadsheet->createSheet();
        $instructionsSheet->setTitle('Instructions');
        
        $instructions = [
            ['INSTRUCTIONS POUR L\'IMPORT DES ÉTUDIANTS'],
            [''],
            ['1. FORMAT REQUIS:'],
            ['   - Colonnes obligatoires: first_name, last_name, email, phone'],
            ['   - L\'email doit être unique pour chaque étudiant'],
            ['   - Tous les champs sont obligatoires'],
            [''],
            ['2. RÈGLES:'],
            ['   - Pas de lignes vides'],
            ['   - Format email valide'],
            ['   - Numéro de téléphone au format international'],
            [''],
            ['3. EXEMPLE:'],
            ['   first_name: Ahmed'],
            ['   last_name: Ben Ali'],
            ['   email: ahmed.benali@example.com'],
            ['   phone: +216 12 345 678'],
            [''],
            ['4. FORMATS ACCEPTÉS:'],
            ['   - Excel (.xlsx, .xls)'],
            ['   - CSV (.csv)'],
            [''],
            ['5. CONSEILS:'],
            ['   - Supprimez les lignes d\'exemple avant l\'import'],
            ['   - Vérifiez que tous les emails sont uniques'],
            ['   - Sauvegardez le fichier avant l\'upload']
        ];

        $instructionRow = 1;
        foreach ($instructions as $instruction) {
            $instructionsSheet->setCellValue('A' . $instructionRow, $instruction[0]);
            $instructionRow++;
        }

        // Style instructions
        $instructionsSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instructionsSheet->getColumnDimension('A')->setWidth(60);

        // Set active sheet back to data sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Save the file
        $filename = 'modele_import_etudiants.xlsx';
        $filepath = storage_path('app/public/' . $filename);
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        $this->info("Template Excel créé avec succès: {$filepath}");
        $this->info("Fichier: {$filename}");
        
        return 0;
    }
}