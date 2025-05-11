<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Lead;
use Illuminate\Http\Response;

class DailyReport
{
    protected $id, $campaign_id, $date_from, $date_to, $filter_by, $reminder_for_conversation;

    public function __construct($id, $campaign_id, $date_from, $date_to, $filter_by, $reminder_for_conversation)
    {
        $this->id = $id;
        $this->campaign_id = $campaign_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->filter_by = $filter_by;
        $this->reminder_for_conversation = $reminder_for_conversation;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Campaign Name')
            ->setCellValue('B1', 'Sub-Campaign Name')
            ->setCellValue('C1', 'Organization')
            ->setCellValue('D1', 'Prospect Name')
            ->setCellValue('E1', 'Designation')
            ->setCellValue('F1', 'LinkedIn')
            ->setCellValue('G1', 'Notes')
            ->setCellValue('H1', 'Conversation Type')
            ->setCellValue('I1', 'Comment Date')
            ->setCellValue('J1', 'Comment Time');

        // Apply header styles
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF00'],
            ],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Fetch data
        $leads = $this->getLeadsData();

        $rowNumber = 2;
        foreach ($leads as $lead) {
            $sheet->setCellValue('A' . $rowNumber, $lead->source->source_name)
                ->setCellValue('B' . $rowNumber, $lead->source->description)
                ->setCellValue('C' . $rowNumber, $lead->company_name)
                ->setCellValue('D' . $rowNumber, $lead->prospect_first_name . ' ' . $lead->prospect_last_name)
                ->setCellValue('E' . $rowNumber, $lead->designation)
                ->setCellValue('F' . $rowNumber, $lead->linkedin_address)
                ->setCellValue('G' . $rowNumber, $lead->feedback)
                ->setCellValue('H' . $rowNumber, $lead->reminder_for)
                ->setCellValue('I' . $rowNumber, date('d/m/Y', strtotime($lead->updated_at)))
                ->setCellValue('J' . $rowNumber, date('h:i a', strtotime($lead->updated_at)));

            $rowNumber++;
        }

        // Set download headers
        $fileName = 'daily_report_' . date('Y_m_d_H_i_s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        // Output file to browser
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit();
    }

    private function getLeadsData()
    {
        $query = Lead::where('asign_to', $this->id)
            ->join('notes', 'notes.lead_id', '=', 'leads.id')
            ->select('leads.id', 'leads.prospect_first_name', 'leads.prospect_last_name',
                'notes.reminder_for', 'notes.feedback', 'notes.updated_at', 'leads.status', 'leads.linkedin_address',
                'leads.company_name', 'leads.designation', 'leads.source_id')
            ->with('source');

        // Apply filters (campaign, date range, etc.)
        if ($this->campaign_id) {
            $query->where('notes.source_id', $this->campaign_id);
        }
        if ($this->date_from && $this->date_to) {
            $date_from_new = date('Y-m-d H:i:s', strtotime($this->date_from));
            $date_to_new = date('Y-m-d H:i:s', strtotime($this->date_to));
            $query->whereBetween('notes.updated_at', [$date_from_new, $date_to_new]);
        }

        if ($this->filter_by) {
            if ($this->filter_by == 1) {
                $query->whereNull('notes.reminder_for');
            } elseif ($this->filter_by == 2 && $this->reminder_for_conversation) {
                $query->where('notes.reminder_for', $this->reminder_for_conversation);
            }
        }

        return $query->get();
    }
}
