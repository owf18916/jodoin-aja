<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Payable;
use Illuminate\Support\Facades\Storage;

class PayableDocumentController extends Controller
{
    // ==== PDF: view inline di tab baru ====
    public function showPdf(Payable $payable)
    {
        [$year, $month] = $this->ym($payable);
        $name = $this->clean($payable->invoice_number).'.pdf';
        $rel  = "payables/{$year}/{$month}/{$name}";

        abort_unless(Storage::disk('nas_fatp')->exists($rel), 404);

        // Inline stream (bisa juga Storage::response(...))
        $stream = Storage::disk('nas_fatp')->readStream($rel);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$name.'"',
            'X-Content-Type-Options' => 'nosniff',
            'Accept-Ranges'       => 'bytes',
            'Cache-Control'       => 'private, max-age=0, must-revalidate',
        ]);
    }

    // ==== ZIP: selalu download (attachment) ====
    public function downloadZip(Payable $payable)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('nas_fatp');
        [$year, $month] = $this->ym($payable);
        $name = '_BUNDLE_'.$this->clean($payable->invoice_number).'.zip';
        $rel  = "payables/{$year}/{$month}/{$name}";

        abort_unless(Storage::disk('nas_fatp')->exists($rel), 404);

        return $disk->download($rel, $name, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="'.$name.'"',
        ]);
    }

    private function ym(Payable $p): array
    {
        $raw = $p->accounted_date;
        $dt  = $raw instanceof Carbon ? $raw : Carbon::parse($raw ?? Carbon::now());

        return [$dt->format('Y'), $dt->format('m')];
    }
    private function clean(string $s): string
    {
        return preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $s);
    }
}
