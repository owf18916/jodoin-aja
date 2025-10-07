<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Receivable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ReceivableInvoiceMatchingServices {
    protected $matchedReceivables = [];
    protected $unmatchedReceivables = [];

    public function matchReceivables()
    {
        $openReceivables = Receivable::select('id','invoice_number','accounted_date')
            ->where('status_invoice',1)
            ->get();

        // Struktur di NAS:
        //   \\10.62.230.21\data fatp\jodoin\copy-receivables-here\
        //   \\10.62.230.21\data fatp\jodoin\receivables\YYYY\MM\
        $inboxBase = 'copy-receivables-here/';
        $targetRoot = 'receivables/';

        foreach ($openReceivables as $receivable) {
            $clean = preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $receivable->invoice_number);
            $srcRel = $inboxBase . $clean . '.pdf';
            
            if (Storage::disk('nas_fatp')->exists($srcRel)) {
                $year = Carbon::parse($receivable->accounted_date)->format('Y');
                $month = Carbon::parse($receivable->accounted_date)->format('m');
                
                $destDir = $targetRoot . $year . '/' . $month . '/';
                $destRel = $destDir . $clean . '.pdf';

                Storage::disk('nas_fatp')->makeDirectory($destDir);
                Storage::disk('nas_fatp')->move($srcRel, $destRel);
                
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