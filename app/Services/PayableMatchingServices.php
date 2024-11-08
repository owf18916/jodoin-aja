<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Payable;
use Illuminate\Support\Facades\File;

class PayableMatchingServices {
    protected $matchedPayables = [];
    protected $unmatchedPayables = [];

    public function matchPayables()
    {
        $openPayables = Payable::select('id','invoice_number','payment_date')
            ->where('status',1)
            ->get();

        $pdfDirectory = storage_path('app/public/documents/payables/');

        foreach ($openPayables as $payable) {
            $cleanedInvoiceNumber = preg_replace('/[^A-Za-z0-9]/', '-', $payable->invoice_number);
            $year = Carbon::parse($payable->payment_date)->format('Y');
            $month = Carbon::parse($payable->payment_date)->format('m');
            $pdfFilePath = $pdfDirectory . $year .'/'. $month .'/'. $cleanedInvoiceNumber . '.pdf';

            if (File::exists($pdfFilePath)) {
                $this->matchedPayables[] = [
                    'id' => $payable->id,
                    'status' => File::exists($pdfFilePath) ? 2 : 1
                ];
            } else {
                $this->unmatchedPayables[] =  [
                    "row" => $payable->id,
                    "errors" => [
                      0 => [
                        0 => "Dokumen tidak ditemukan"
                      ]
                    ]
                ];
            }
        }
    }

    public function getMatchedPayables()
    {
        return $this->matchedPayables;
    }

    public function getUnmatchedPayables()
    {
        return $this->unmatchedPayables;
    }
}