<?php

namespace App\Livewire\Receivables;

use App\Models\Receivable;
use App\Traits\Swalable;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class ReceivableDownloadDocument extends Component
{
    public $id;

    use Swalable;

    #[On('download-receivable-pdf-clicked')]
    public function downloadInvoice(Receivable $receivable)
    {
        $year = Carbon::parse($receivable->accounted_date)->format('Y');
        $month = Carbon::parse($receivable->accounted_date)->format('m');
        $cleanedInvoiceNumber = preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $receivable->invoice_number);
        $pdfDirectory = storage_path('app/public/documents/receivables/');
        $pdfFilePath = $pdfDirectory . $year .'/'. $month .'/'. $cleanedInvoiceNumber . '.pdf';

        if (!file_exists($pdfFilePath)) {
            $this->flashError('Dokumen tidak ditemukan.');
        }

        return response()->streamDownload(function () use ($pdfFilePath) {
            readfile($pdfFilePath);
        }, $cleanedInvoiceNumber . '.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $cleanedInvoiceNumber . '.pdf"',
        ]);
    }

    #[On('download-receivable-bl-pdf-clicked')]
    public function downloadBl(Receivable $receivable)
    {
        $year = Carbon::parse($receivable->accounted_date)->format('Y');
        $month = Carbon::parse($receivable->accounted_date)->format('m');
        $cleanedBlNumber = preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $receivable->bl_number);
        $pdfDirectory = storage_path('app/public/documents/bl/');
        $pdfFilePath = $pdfDirectory . $year .'/'. $month .'/'. $cleanedBlNumber . '.pdf';

        if (!file_exists($pdfFilePath)) {
            $this->flashError('Dokumen tidak ditemukan.');
        }

        return response()->streamDownload(function () use ($pdfFilePath) {
            readfile($pdfFilePath);
        }, $cleanedBlNumber . '.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $cleanedBlNumber . '.pdf"',
        ]);
    }

    public function render()
    {
        return view('livewire.receivables.receivable-download-document');
    }
}
