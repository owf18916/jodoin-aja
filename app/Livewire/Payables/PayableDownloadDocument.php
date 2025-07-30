<?php

namespace App\Livewire\Payables;

use App\Models\Payable;
use App\Traits\Swalable;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class PayableDownloadDocument extends Component
{
    public $id;

    use Swalable;

    public function downloadBulk()
    {
        
    }

    #[On('download-payable-pdf-clicked')]
    public function download(Payable $payable)
    {
        // $payable = Payable::find($this->id);
        $year = Carbon::parse($payable->accounted_date)->format('Y');
        $month = Carbon::parse($payable->accounted_date)->format('m');
        $cleanedInvoiceNumber = preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $payable->invoice_number);
        $pdfDirectory = storage_path('app/public/documents/payables/');
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

    public function render()
    {
        return view('livewire.payables.payable-download-document');
    }
}
