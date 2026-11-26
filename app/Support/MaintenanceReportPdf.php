<?php

namespace App\Support;

use App\Models\MaintenanceReport;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use setasign\Fpdi\PdfParser\StreamReader;

class MaintenanceReportPdf
{
    public function technician(MaintenanceReport $report): string
    {
        $pdf = $this->document();
        $pdf->WriteHTML(view('maintenance.reports.pdf', [
            'report' => $report, 'snapshot' => $report->technician_snapshot, 'original' => true,
        ])->render());

        return $pdf->Output('', Destination::STRING_RETURN);
    }

    public function approved(MaintenanceReport $report): string
    {
        $report->load(['customer', 'equipmentUnit.model.manufacturer', 'fields', 'materials.material', 'approver', 'foremanReviewer']);
        $pdf = $this->document();
        $pdf->WriteHTML(view('maintenance.reports.pdf', [
            'report' => $report, 'snapshot' => $report->technician_snapshot, 'original' => false,
        ])->render());
        if ($report->technician_pdf_path) {
            $source = StreamReader::createByString(Storage::disk('maintenance_private')->get($report->technician_pdf_path));
            $pages = $pdf->setSourceFile($source);
            $pdf->SetHTMLHeader('');
            $pdf->SetHTMLFooter('');
            for ($page = 1; $page <= $pages; $page++) {
                $template = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation']);
                $pdf->useTemplate($template, 0, 0, $size['width'], $size['height'], true);
            }
        }

        return $pdf->Output('', Destination::STRING_RETURN);
    }

    public static function image(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        $contents = Storage::get($path);
        $info = getimagesizefromstring($contents);

        return $info ? 'data:'.$info['mime'].';base64,'.base64_encode($contents) : null;
    }

    private function document(): Mpdf
    {
        $temp = storage_path('app/maintenance-pdf-temp');
        if (! is_dir($temp)) {
            mkdir($temp, 0775, true);
        }
        $pdf = new Mpdf([
            'tempDir' => $temp, 'mode' => 'utf-8', 'format' => 'A4',
            'default_font' => 'dejavusans', 'margin_top' => 28, 'margin_bottom' => 22,
            'autoScriptToLang' => true, 'autoLangToFont' => true,
        ]);
        $pdf->SetTitle('Maintenance report / تقرير صيانة');
        $pdf->SetHTMLHeader('<div style="border-bottom:2px solid #0f766e;color:#0f766e;font-size:13pt;font-weight:bold">Aladdin Sweiti Engineering Est.<br><span dir="rtl">مؤسسة علاء الدين السويطي الهندسية</span></div>');
        $pdf->SetHTMLFooter('<div style="border-top:1px solid #ccc;text-align:center;font-size:8pt">info@asee-eng.com · www.asee-eng.com · +(962) (6) 585 5959<br>{PAGENO} / {nbpg}</div>');

        return $pdf;
    }
}
