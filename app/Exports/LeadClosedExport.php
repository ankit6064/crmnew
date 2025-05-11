<?php
namespace App\Exports;

use App\Models\Lead;
use App\Models\Note;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeadClosedExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function query()
    {
        return Lead::where('source_id', $this->id)
            ->select([
                'id', 'prospect_email', 'company_name', 'prospect_first_name',
                'prospect_last_name', 'linkedin_address', 'source_id',
                'asign_to', 'created_at', 'status'
            ])
            ->with(['source:id,source_name,description', 'user:id,name'])
            ->orderBy('status', 'desc');
    }

    public function map($lead): array
    {
        $user = $lead->user;
        $source = $lead->source;
        $notes = Note::where('lead_id', $lead->id)->pluck('feedback')->implode(",\n");

        return [
            $lead->id,
            $lead->prospect_email,
            $lead->company_name,
            "{$lead->prospect_first_name} {$lead->prospect_last_name}",
            $lead->linkedin_address,
            optional($source)->source_name ?? 'N/A',
            optional($source)->description ?? 'N/A',
            optional($user)->name ?? 'N/A',
            $notes,
            $lead->created_at->format('d/m/Y h:i a'),
        ];
    }

    public function headings(): array
    {
        return [
            'Lead ID', 'Email', 'Organization', 'Prospect Name',
            'LinkedIn', 'Campaign Name', 'Sub-Campaign Name',
            'Assigned To', 'Feedback', 'Created On'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4CAF50']],
        ]);
    }
}