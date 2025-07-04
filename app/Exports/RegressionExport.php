<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RegressionExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    private $regressionResult;
    private $scenarios;
    private $suit;

    public function __construct($regressionResult, $scenarios, $suit)
    {
        $this->regressionResult = $regressionResult;
        $this->scenarios = $scenarios;
        $this->suit = $suit;
    }

    public function array(): array
    {
        $data = [];
        $rowIndex = 2; // Starting from row 2 (after headers)

        foreach ($this->scenarios as $index => $scenario) {
            $scenarioId = $scenario->id;
            $result = $this->regressionResult->results[$scenarioId] ?? 'not_tested';
            
            $statusText = '';
            switch($result) {
                case 'pass':
                    $statusText = 'BAŞARILI';
                    break;
                case 'fail':
                    $statusText = 'BAŞARISIZ';
                    break;
                case 'skip':
                    $statusText = 'ATLANDI';
                    break;
                default:
                    $statusText = 'TEST EDİLMEDİ';
            }

            $data[] = [
                $index + 1,
                $scenario->title,
                $scenario->description,
                implode('; ', $scenario->steps ?? []),
                $statusText,
                $this->regressionResult->run_date->format('d.m.Y H:i'),
                $result === 'fail' ? 'Hata detayları eklenebilir' : ''
            ];
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Sıra No',
            'Senaryo Adı',
            'Açıklama',
            'Test Adımları',
            'Sonuç',
            'Test Tarihi',
            'Notlar'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header stilini ayarla
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ]
        ]);

        // Sonuç sütunu için renkli hücreler
        $rowCount = count($this->scenarios) + 1;
        for ($row = 2; $row <= $rowCount; $row++) {
            $scenarioIndex = $row - 2;
            $scenario = $this->scenarios->values()[$scenarioIndex] ?? null;
            
            if ($scenario) {
                $result = $this->regressionResult->results[$scenario->id] ?? 'not_tested';
                
                $color = '';
                switch($result) {
                    case 'pass':
                        $color = 'C6EFCE'; // Açık yeşil
                        break;
                    case 'fail':
                        $color = 'FFC7CE'; // Açık kırmızı
                        break;
                    case 'skip':
                        $color = 'FFEB9C'; // Açık sarı
                        break;
                }

                if ($color) {
                    $sheet->getStyle("E{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $color]
                        ]
                    ]);
                }
            }
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // Sıra No
            'B' => 25,  // Senaryo Adı
            'C' => 40,  // Açıklama
            'D' => 50,  // Test Adımları
            'E' => 15,  // Sonuç
            'F' => 18,  // Test Tarihi
            'G' => 30,  // Notlar
        ];
    }
}
