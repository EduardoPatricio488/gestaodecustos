<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorePdfService
{
    public function generateGuidePdf(string $title, array $sections, string $relativePath): string
    {
        $pdf = Pdf::loadView('store.pdf.guide', [
            'title' => $title,
            'sections' => $sections,
        ]);

        Storage::disk('local')->put($relativePath, $pdf->output());

        return $relativePath;
    }

    public function generateCourseModulePdf(string $courseTitle, array $module, string $relativePath): string
    {
        $pdf = Pdf::loadView('store.pdf.course-module', [
            'courseTitle' => $courseTitle,
            'module' => $module,
        ]);

        Storage::disk('local')->put($relativePath, $pdf->output());

        return $relativePath;
    }

    public function generateCourseWorkbookPdf(string $title, array $modules, string $relativePath): string
    {
        $pdf = Pdf::loadView('store.pdf.course-workbook', [
            'title' => $title,
            'modules' => $modules,
        ]);

        Storage::disk('local')->put($relativePath, $pdf->output());

        return $relativePath;
    }

    public function guidePath(string $slug): string
    {
        return 'store/guides/'.Str::slug($slug).'.pdf';
    }

    public function courseModulePath(string $courseSlug, int $moduleNumber): string
    {
        return 'store/courses/'.Str::slug($courseSlug).'/modulo-'.$moduleNumber.'.pdf';
    }

    public function courseWorkbookPath(string $courseSlug): string
    {
        return 'store/courses/'.Str::slug($courseSlug).'/workbook-completo.pdf';
    }
}
