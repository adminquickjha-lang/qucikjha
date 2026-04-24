<?php

namespace App\Http\Controllers;

use App\Models\SafetyDocument;
use App\Models\Setting;
use App\Services\AdobePdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function pdf($id)
    {
        $document = SafetyDocument::findOrFail($id);

        if (! $document->is_paid && auth()->id() !== $document->user_id) {
            abort(403, 'Unauthorized access.');
        }

        $docType = strtolower(trim($document->document_type));
        $isAha = str_contains($docType, 'aha') || str_contains($docType, 'activity hazard');

        // Load admin-controlled template settings
        $settings = Setting::whereIn('key', [
            'header_color',
            'table_header_color',
            'aha_header_color',
            'aha_table_header_color',
            'jha_header_color',
            'jha_table_header_color',
            'jsa_header_color',
            'jsa_table_header_color',
            'rac_e_color',
            'rac_h_color',
            'rac_m_color',
            'rac_l_color',
            'required_ppe',
            'disclaimer_text',
        ])->pluck('value', 'key')->toArray();

        // Map specific colors based on document type
        if ($isAha) {
            $settings['header_color'] = $settings['aha_header_color'] ?? $settings['header_color'] ?? '#1a3a6b';
            $settings['table_header_color'] = $settings['aha_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e';
        } else {
            $settings['header_color'] = $settings['jha_header_color'] ?? $settings['jsa_header_color'] ?? $settings['header_color'] ?? '#1a3a6b';
            $settings['table_header_color'] = $settings['jha_table_header_color'] ?? $settings['jsa_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e';
        }

        $view = 'pdf.safety-document';
        $docType = strtolower(trim($document->document_type));
        $paperOrientation = 'landscape';

        if ($docType === 'aha' || str_contains($docType, 'aha') || str_contains($docType, 'activity hazard')) {
            $view = 'pdf.safety-document-aha';
        } elseif ($docType === 'jsa' || str_contains($docType, 'jsa') || str_contains($docType, 'job safety analysis')) {
            $view = 'pdf.safety-document-jsa';
            $paperOrientation = 'portrait';
        }

        $pdf = Pdf::loadView($view, compact('document', 'settings'))
            ->setPaper('a4', $paperOrientation)
            ->setOptions(['isPhpEnabled' => true]);

        return $pdf->download($document->document_type.'_'.$document->project_name.'_'.$document->id.'.pdf');
    }

    public function word($id, AdobePdfService $adobeService)
    {
        $document = SafetyDocument::findOrFail($id);

        if (! $document->is_paid && auth()->id() !== $document->user_id) {
            abort(403, 'Unauthorized access.');
        }

        $docType = strtolower(trim($document->document_type));
        $isAha = str_contains($docType, 'aha') || str_contains($docType, 'activity hazard');

        // 1. Load admin-controlled template settings
        $settings = Setting::whereIn('key', [
            'header_color',
            'table_header_color',
            'aha_header_color',
            'aha_table_header_color',
            'jsa_header_color',
            'jsa_table_header_color',
            'rac_e_color',
            'rac_h_color',
            'rac_m_color',
            'rac_l_color',
            'required_ppe',
            'disclaimer_text',
        ])->pluck('value', 'key')->toArray();

        // 1b. Map specific colors based on document type
        if ($isAha) {
            $settings['header_color'] = $settings['aha_header_color'] ?? $settings['header_color'] ?? '#1a3a6b';
            $settings['table_header_color'] = $settings['aha_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e';
        } else {
            $settings['header_color'] = $settings['jsa_header_color'] ?? $settings['header_color'] ?? '#1a3a6b';
            $settings['table_header_color'] = $settings['jsa_table_header_color'] ?? $settings['table_header_color'] ?? '#2c5f9e';
        }

        // 2. Generate PDF first (Temp file)
        $view = 'pdf.safety-document';
        $docType = strtolower(trim($document->document_type));
        $paperOrientation = 'landscape';

        if ($docType === 'aha' || str_contains($docType, 'aha') || str_contains($docType, 'activity hazard')) {
            $view = 'pdf.safety-document-aha';
        } elseif ($docType === 'jsa' || str_contains($docType, 'jsa') || str_contains($docType, 'job safety analysis')) {
            $view = 'pdf.safety-document-jsa';
            $paperOrientation = 'portrait';
        }

        $pdf = Pdf::loadView($view, compact('document', 'settings'))
            ->setPaper('a4', $paperOrientation)
            ->setOptions(['isPhpEnabled' => true]);

        $pdfFileName = 'temp_'.$document->id.'.pdf';
        $pdfPath = storage_path('app/public/'.$pdfFileName);
        $pdf->save($pdfPath);

        try {
            // 3. Convert to Word using Adobe
            $downloadUrl = $adobeService->convertPdfToDocx($pdfPath);

            // 4. Download file from Adobe and send to user
            $wordContent = Http::get($downloadUrl)->body();
            $cleanName = str_replace([' ', '/', '\\'], '_', $document->project_name);
            $wordFileName = "{$document->document_type}_{$cleanName}_{$document->id}.docx";

            // Cleanup temp PDF
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }

            return response()->streamDownload(function () use ($wordContent) {
                echo $wordContent;
            }, $wordFileName);

        } catch (\Exception $e) {
            // Cleanup on failure
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            Log::error('Adobe Conversion Failed (Route): '.$e->getMessage());
            abort(500, 'Word conversion failed.');
        }
    }
}
