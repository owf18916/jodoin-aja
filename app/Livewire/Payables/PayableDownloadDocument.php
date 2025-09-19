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

    #[On('download-payable-pdf-clicked')]
    public function download(Payable $payable)
    {
        // $payable = Payable::find($this->id);
        $year = Carbon::parse($payable->accounted_date)->format('Y');
        $month = Carbon::parse($payable->accounted_date)->format('m');
        $cleanedInvoiceNumber = preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $payable->invoice_number);
        // $pdfDirectory = storage_path('app/public/documents/payables/');
        $pdfDirectory = Storage::disk('nas')->path('documents/payables/');
        
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
