<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MultiRegressionExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    private $regressionResults;
    private $scenarios;
    private $suits;

    public function __construct($regressionResults, $scenarios, $suits)
    {
        $this->regressionResults = $regressionResults;
        $this->scenarios = $scenarios;
        $this->suits = $suits;
    }

    public function array(): array
    {
        $data = [];
        $currentRow = 1;

        foreach ($this->suits as $suit) {
            // Suit başlığı ekle
            $data[] = [
                '',
                "TEST SUIT: {$suit->name}",
                '',
                '',
                '',
                '',
                '',
                ''
            ];
            $currentRow++;

            // Bu suit'e ait regresyon sonucunu bul
            $suitRegressionResult = $this->regressionResults->where('suit_id', $suit->id)->first();
            
            if ($suitRegressionResult) {
                // Bu suit'e ait senaryoları filtrele
                $suitScenarios = $this->scenarios->whereIn('id', $suit->scenario_ids);
                
                foreach ($suitScenarios as $index => $scenario) {
                    $scenarioId = $scenario->id;
                    $result = $suitRegressionResult->results[$scenarioId] ?? 'not_tested';
                    
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
                        $suitRegressionResult->run_date->format('d.m.Y H:i'),
                        $result === 'fail' ? 'Hata detayları eklenebilir' : '',
                        $result // Stil için kullanılacak
                    ];
                    $currentRow++;
                }
            } else {
                // Regresyon sonucu bulunamazsa
                $data[] = [
                    '',
                    'Bu suit için regresyon sonucu bulunamadı',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                ];
                $currentRow++;
            }

            // Suit'ler arasında boş satır ekle
            $data[] = ['', '', '', '', '', '', '', ''];
            $currentRow++;
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
            'Notlar',
            '' // Gizli kolon (stil için)
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

        // H sütununu gizle (stil verisi için kullanıldı)
        $sheet->getColumnDimension('H')->setVisible(false);

        $rowCount = count($this->array()) + 1;
        
        for ($row = 2; $row <= $rowCount; $row++) {
            $dataIndex = $row - 2;
            $rowData = $this->array()[$dataIndex] ?? null;
            
            if ($rowData) {
                // Suit başlığı satırları için stil
                if (strpos($rowData[1], 'TEST SUIT:') === 0) {
                    $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '2F5597']
                        ]
                    ]);
                    
                    // Suit başlığını merge et
                    $sheet->mergeCells("A{$row}:G{$row}");
                }
                
                // Test sonucu için renkli hücreler
                if (isset($rowData[7])) {
                    $result = $rowData[7];
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
        }

        // Tüm hücrelere border ekle
        $sheet->getStyle("A1:G{$rowCount}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

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
            'H' => 1,   // Gizli kolon
        ];
    }

    public function title(): string
    {
        return 'Çoklu Regresyon Testi';
    }
}
