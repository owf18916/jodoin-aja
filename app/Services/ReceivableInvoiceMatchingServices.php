<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Receivable;
use Illuminate\Support\Facades\File;

class ReceivableInvoiceMatchingServices {
    protected $matchedReceivables = [];
    protected $unmatchedReceivables = [];

    public function matchReceivables()
    {
        $openReceivables = Receivable::select('id','invoice_number','accounted_date')
            ->where('status_invoice',1)
            ->get();

        $pdfDirectory = storage_path('app/public/documents/receivables/');

        foreach ($openReceivables as $receivable) {
            $cleanedInvoiceNumber = preg_replace('/[^A-Za-z0-9. ]/', '-', $receivable->invoice_number);
            $originalFile = $pdfDirectory.'copy-receivables-here/'.$cleanedInvoiceNumber.'.pdf';
            
            if (File::exists($originalFile)) {
                $year = Carbon::parse($receivable->accounted_date)->format('Y');
                $month = Carbon::parse($receivable->accounted_date)->format('m');
                
                $destinationFile = $pdfDirectory . $year .'/'. $month .'/'. $cleanedInvoiceNumber . '.pdf';

                File::move($originalFile, $destinationFile);
                
                $receivable->status_invoice = 2;

                $this->matchedReceivables[] = $receivable->toArray();
            } else {
                $this->unmatchedReceivables[] =  [
                    "row" => $receivable->id,
                    "errors" => [
                      0 => [
                        0 => 'Dokumen untuk invoice : '. $receivable->invoice_number .' tidak ditemukan'
                      ]
                    ]
                ];
            }
        }
    }

    public function getMatchedReceivables()
    {
        return $this->matchedReceivables;
    }

    public function getUnmatchedReceivables()
    {
        return $this->unmatchedReceivables;
    }
}