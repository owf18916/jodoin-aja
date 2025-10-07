<?php

namespace App\Livewire\Payables;

use Carbon\Carbon;
use App\Models\Payable;
use Livewire\Component;
use App\Traits\Swalable;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;

class PayableDownloadDocument extends Component
{
    public $id;

    use Swalable;

    public function downloadBulk()
    {
        
    }

    private function clean(string $invoice): string
    {
        return preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $invoice);
    }

    private function pdfRelPath(\App\Models\Payable $payable): string
    {
        $raw = $payable->accounted_date;
        $dt  = $raw instanceof \Carbon\Carbon
            ? $raw
            : \Carbon\Carbon::parse($raw ?? now());

        $year  = $dt->format('Y');
        $month = $dt->format('m');

        return "payables/{$year}/{$month}/" . $this->clean($payable->invoice_number) . ".pdf";
    }

    private function zipRelPath(\App\Models\Payable $payable): string
    {
        $dt = optional($payable->accounted_date) ?: now();
        $year = \Carbon\Carbon::parse($dt)->format('Y');
        $month = \Carbon\Carbon::parse($dt)->format('m');

        return "payables/{$year}/{$month}/_BUNDLE_" . $this->clean($payable->invoice_number) . ".zip";
    }

    #[On('view-payable-pdf-clicked')]
    public function viewPdf(\App\Models\Payable $payable)
    {
        $disk = Storage::disk('nas_fatp');
        $rel  = $this->pdfRelPath($payable);
        $name = $this->clean($payable->invoice_number) . '.pdf';

        if (!$disk->exists($rel)) {
            $this->flashError('Dokumen tidak ditemukan.');
            return;
        }

        $stream = $disk->readStream($rel);
        if ($stream === false) {
            $this->flashError('Gagal membaca dokumen.');
            return;
        }

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, $name, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$name.'"',
        ]);
    }

    public function render()
    {
        return view('livewire.payables.payable-download-document');
    }
}
