<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Receivable;
use Illuminate\Support\Facades\File;

class ReceivableBlMatchingServices {
    protected $matchedBl = [];
    protected $unmatchedBl = [];

    public function matchBl()
    {
        $openBl = Receivable::select('id','bl_number','accounted_date')
            ->where('category', 1)
            ->where('status_bl', 1)
            ->get();

        $pdfDirectory = storage_path('app/public/documents/bl/');

        foreach ($openBl as $bl) {
            $cleanedBlNumber = preg_replace('/[^A-Za-z0-9]/', '-', $bl->bl_number);
            $originalFile = $pdfDirectory.'copy-bl-here/'.$cleanedBlNumber.'.pdf';
            
            if (File::exists($originalFile)) {
                $year = Carbon::parse($bl->accounted_date)->format('Y');
                $month = Carbon::parse($bl->accounted_date)->format('m');
                
                $destinationFile = $pdfDirectory . $year .'/'. $month .'/'. $cleanedBlNumber . '.pdf';

                File::move($originalFile, $destinationFile);
                
                $bl->status_bl = 2;

                $this->matchedBl[] = $bl->toArray();
            } else {
                $this->unmatchedBl[] =  [
                    "row" => $bl->id,
                    "errors" => [
                      0 => [
                        0 => 'Dokumen untuk invoice : '. $bl->invoice_number .' dengan identifikasi nomor BL '.$bl->bl_number.' tidak ditemukan'
                      ]
                    ]
                ];
            }
        }
    }

    public function getMatchedBl()
    {
        return $this->matchedBl;
    }

    public function getUnmatchedBl()
    {
        return $this->unmatchedBl;
    }
}