<?php
namespace App\Exports;

use App\Models\Lead;
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
            ->with([
                'source:id,source_name,description', 
                'user:id,name',
                // This uses your existing notes() relationship from the model
                'notes' => function($query) {
                    $query->select('lead_id', 'feedback');
                }
            ])
            ->orderBy('status', 'desc');
    }

    public function map($lead): array
    {
        // 2. Access the pre-loaded notes collection instead of querying the DB
        $notes = $lead->notes->pluck('feedback')->implode(",\n");

        return [
            $lead->id,
            $lead->prospect_email,
            $lead->company_name,
            "{$lead->prospect_first_name} {$lead->prospect_last_name}",
            $lead->linkedin_address,
            $lead->source->source_name ?? 'N/A',
            $lead->source->description ?? 'N/A',
            $lead->user->name ?? 'N/A',
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
        // Added wrap text for feedback since it can have newlines
        $sheet->getStyle('I')->getAlignment()->setWrapText(true);

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4CAF50']],
            ],
        ];
    }
}