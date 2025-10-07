<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReceivableDocumentController extends Controller
{
    // ==== PDF: view inline di tab baru ====
    public function showInvoicePdf(Receivable $receivable)
    {
        [$year, $month] = $this->ym($receivable);
        $name = $this->clean($receivable->invoice_number).'.pdf';
        $rel  = "receivables/{$year}/{$month}/{$name}";

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

    public function showBlPdf(Receivable $receivable)
    {
        [$year, $month] = $this->ym($receivable);
        $name = $this->clean($receivable->bl_number).'.pdf';
        $rel  = "bl/{$year}/{$month}/{$name}";

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

    private function ym(Receivable $p): array
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
