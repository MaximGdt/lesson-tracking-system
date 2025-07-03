<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LessonsExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Return array of data for export.
     */
    public function array(): array
    {
        $rows = [];
        
        foreach ($this->data['lessons'] as $lesson) {
            $rows[] = [
                $lesson->schedule->date->format('d.m.Y'),
                $lesson->schedule->time_range,
                $lesson->schedule->group->code,
                $lesson->schedule->group->name,
                $lesson->schedule->teacher->full_name,
                $lesson->schedule->subject,
                $lesson->schedule->type_display,
                $lesson->schedule->room ?? '-',
                $lesson->students_present ?? '-',
                $lesson->marked_at->format('d.m.Y H:i'),
                $lesson->markedBy->full_name,
                $lesson->notes ?? '-',
            ];
        }

        // Add summary at the end
        $rows[] = [];
        $rows[] = ['ИТОГО:'];
        $rows[] = ['Всего занятий:', $this->data['summary']['total_lessons']];
        $rows[] = ['Всего часов:', $this->data['summary']['total_hours']];
        
        if (!empty($this->data['summary']['by_type'])) {
            $rows[] = [];
            $rows[] = ['По типам занятий:'];
            foreach ($this->data['summary']['by_type'] as $type => $count) {
                $rows[] = [$type . ':', $count];
            }
        }

        return $rows;
    }

    /**
     * Return headings for the export.
     */
    public function headings(): array
    {
        return [
            'Дата',
            'Время',
            'Код группы',
            'Название группы',
            'Преподаватель',
            'Предмет',
            'Тип занятия',
            'Аудитория',
            'Присутствовало',
            'Отмечено',
            'Отметил',
            'Примечания',
        ];
    }

    /**
     * Apply styles to the worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Style for header
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2c3e50'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Auto filter
        $sheet->setAutoFilter('A1:L1');

        // Freeze first row
        $sheet->freezePane('A2');

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Set column widths.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Дата
            'B' => 12,  // Время
            'C' => 12,  // Код группы
            'D' => 25,  // Название группы
            'E' => 25,  // Преподаватель
            'F' => 30,  // Предмет
            'G' => 15,  // Тип занятия
            'H' => 12,  // Аудитория
            'I' => 15,  // Присутствовало
            'J' => 18,  // Отмечено
            'K' => 25,  // Отметил
            'L' => 30,  // Примечания
        ];
    }

    /**
     * Set worksheet title.
     */
    public function title(): string
    {
        return 'Проведенные занятия';
    }
}