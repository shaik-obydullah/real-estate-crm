<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Opportunity;
use App\Models\User;
use App\Models\Ticket;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.app', ['title' => 'Reports'])]
class Index extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.reports.index', ['data' => $this->reportData()]);
    }

    private function reportData(): array
    {
        $from = $this->dateFrom ? Carbon::parse($this->dateFrom) : Carbon::now()->subMonth();
        $to = $this->dateTo ? Carbon::parse($this->dateTo) : Carbon::now();

        $data = [
            'total_customers' => Customer::count(),
            'new_customers_period' => Customer::whereBetween('created_at', [$from, $to])->count(),
            'total_leads' => Lead::count(),
            'leads_in_period' => Lead::whereBetween('created_at', [$from, $to])->count(),
            'converted_leads' => Lead::where('status', 'converted')->whereBetween('created_at', [$from, $to])->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'revenue_in_period' => Payment::where('status', 'completed')->whereBetween('payment_date', [$from, $to])->sum('amount'),
            'total_invoices' => Invoice::count(),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'total_opportunities' => Opportunity::count(),
            'won_opportunities' => Opportunity::where('stage', 'won')->count(),
            'open_opportunities' => Opportunity::whereNotIn('stage', ['won', 'lost'])->count(),
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::where('status', 'open')->count(),
            'resolved_tickets' => Ticket::where('status', 'resolved')->count(),
            'total_users' => User::count(),
            'conversion_rate' => 0,
            'pipeline_value' => Opportunity::whereNotIn('stage', ['won', 'lost'])->sum('value'),
            'revenue_chart_labels' => [],
            'revenue_chart_values' => [],
            'lead_sources_chart_labels' => [],
            'lead_sources_chart_values' => [],
        ];

        $monthly = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$from, $to])
            ->get(['payment_date', 'amount'])
            ->groupBy(fn ($payment) => $payment->payment_date->format('Y-m'))
            ->map(fn ($group) => $group->sum(fn ($payment) => (float) $payment->amount))
            ->sortKeys();

        $data['revenue_chart_labels'] = $monthly->keys()
            ->map(fn ($month) => Carbon::parse($month . '-01')->format('M Y'))
            ->values()
            ->toArray();
        $data['revenue_chart_values'] = $monthly->values()->toArray();

        $leadSources = Lead::query()
            ->get(['source'])
            ->groupBy(fn ($lead) => $lead->source ?: 'Unknown');

        $data['lead_sources_chart_labels'] = $leadSources->keys()->toArray();
        $data['lead_sources_chart_values'] = $leadSources->map->count()->values()->toArray();

        if ($data['total_leads'] > 0) {
            $data['conversion_rate'] = round(($data['converted_leads'] / max($data['leads_in_period'], 1)) * 100, 1);
        }

        return $data;
    }

    private function reportRows(array $data): array
    {
        return [
            ['label' => 'Report Period', 'value' => $this->dateFrom . ' to ' . $this->dateTo],
            ['label' => 'Total Revenue', 'value' => '$' . number_format($data['total_revenue'], 2)],
            ['label' => 'Revenue in Period', 'value' => '$' . number_format($data['revenue_in_period'], 2)],
            ['label' => 'Total Customers', 'value' => number_format($data['total_customers'])],
            ['label' => 'New Customers in Period', 'value' => number_format($data['new_customers_period'])],
            ['label' => 'Total Leads', 'value' => number_format($data['total_leads'])],
            ['label' => 'Leads in Period', 'value' => number_format($data['leads_in_period'])],
            ['label' => 'Converted Leads', 'value' => number_format($data['converted_leads'])],
            ['label' => 'Conversion Rate', 'value' => $data['conversion_rate'] . '%'],
            ['label' => 'Total Invoices', 'value' => number_format($data['total_invoices'])],
            ['label' => 'Paid Invoices', 'value' => number_format($data['paid_invoices'])],
            ['label' => 'Pending Invoices', 'value' => number_format($data['pending_invoices'])],
            ['label' => 'Pipeline Value', 'value' => '$' . number_format($data['pipeline_value'], 2)],
            ['label' => 'Total Opportunities', 'value' => number_format($data['total_opportunities'])],
            ['label' => 'Won Opportunities', 'value' => number_format($data['won_opportunities'])],
            ['label' => 'Open Opportunities', 'value' => number_format($data['open_opportunities'])],
            ['label' => 'Total Tickets', 'value' => number_format($data['total_tickets'])],
            ['label' => 'Open Tickets', 'value' => number_format($data['open_tickets'])],
            ['label' => 'Resolved Tickets', 'value' => number_format($data['resolved_tickets'])],
            ['label' => 'Total Users', 'value' => number_format($data['total_users'])],
        ];
    }

    public function exportData(string $format): ?StreamedResponse
    {
        $rows = $this->reportRows($this->reportData());
        $filename = 'crm-report-' . Carbon::now()->format('Y-m-d');

        return match ($format) {
            'csv' => response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fwrite($out, "\xEF\xBB\xBF");
                fputcsv($out, ['Metric', 'Value']);
                foreach ($rows as $row) {
                    fputcsv($out, [$row['label'], $row['value']]);
                }
                fclose($out);
            }, $filename . '.csv', ['Content-Type' => 'text/csv']),

            'excel' => response()->streamDownload(function () use ($rows) {
                $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
                $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
                $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
                $xml .= '<Worksheet ss:Name="CRM Report"><Table>' . "\n";
                $xml .= '<Row><Cell><Data ss:Type="String"><Bold><Text>Metric</Text></Bold></Data></Cell><Cell><Data ss:Type="String"><Bold><Text>Value</Text></Bold></Data></Cell></Row>' . "\n";
                foreach ($rows as $row) {
                    $xml .= '<Row><Cell><Data ss:Type="String">' . htmlspecialchars($row['label'], ENT_XML1) . '</Data></Cell>'
                        . '<Cell><Data ss:Type="String">' . htmlspecialchars($row['value'], ENT_XML1) . '</Data></Cell></Row>' . "\n";
                }
                $xml .= '</Table></Worksheet></Workbook>';
                echo $xml;
            }, $filename . '.xls', ['Content-Type' => 'application/vnd.ms-excel']),

            'pdf' => response()->streamDownload(function () use ($rows) {
                echo $this->buildPdf($rows);
            }, $filename . '.pdf', ['Content-Type' => 'application/pdf']),

            default => abort(404),
        };
    }

    private function pdfEscape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function buildPdf(array $rows): string
    {
        $margin = 50;
        $pageWidth = 595.28;
        $pageHeight = 841.89;
        $lineHeight = 18;
        $y = $pageHeight - 60;

        $stream = "BT\n";
        $stream .= "/F2 16 Tf\n";
        $stream .= sprintf("1 0 0 1 %.2f %.2f Tm\n", $margin, $y);
        $stream .= '(' . $this->pdfEscape('CRM Business Report') . ") Tj\n";
        $y -= 26;

        $stream .= "/F1 9 Tf\n";
        $stream .= sprintf("1 0 0 1 %.2f %.2f Tm\n", $margin, $y);
        $stream .= '(' . $this->pdfEscape('Period: ' . $this->dateFrom . ' to ' . $this->dateTo) . ") Tj\n";
        $y -= 12;

        $stream .= sprintf("%.2f %.2f %.2f %.2f re\nf\n", $margin, $y - 2, $pageWidth - (2 * $margin), 0.5);
        $y -= 22;

        $stream .= "/F1 10.5 Tf\n";
        foreach ($rows as $row) {
            if ($y < $margin) {
                break;
            }
            $stream .= sprintf("1 0 0 1 %.2f %.2f Tm\n", $margin, $y);
            $stream .= '(' . $this->pdfEscape($row['label']) . ") Tj\n";
            $stream .= sprintf("1 0 0 1 %.2f %.2f Tm\n", $pageWidth - $margin - 160, $y);
            $stream .= '(' . $this->pdfEscape($row['value']) . ") Tj\n";
            $y -= $lineHeight;
        }
        $stream .= "ET\n";

        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 {$pageWidth} {$pageHeight}] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>",
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
            6 => '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . 'endstream',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            $pdf .= $num . " 0 obj\n" . $body . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF\n";

        return $pdf;
    }
}
