<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Receivable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ReceivableBlMatchingServices {
    protected $matchedBl = [];
    protected $unmatchedBl = [];

    public function matchBl()
    {
        $openBl = Receivable::select('id','bl_number','accounted_date')
            ->where('category', 1)
            ->where('status_bl', 1)
            ->get();

        // Struktur di NAS:
        //   \\10.62.230.21\data fatp\jodoin\copy-bl-here\
        //   \\10.62.230.21\data fatp\jodoin\bl\YYYY\MM\
        $inboxBase = 'copy-bl-here/';
        $targetRoot = 'bl/';

        foreach ($openBl as $bl) {
            $clean = preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $bl->bl_number);
            $srcRel = $inboxBase . $clean . '.pdf';
            
            if (Storage::disk('nas_fatp')->exists($srcRel)) {
                $year = Carbon::parse($bl->accounted_date)->format('Y');
                $month = Carbon::parse($bl->accounted_date)->format('m');
                
                $destDir = $targetRoot . $year . '/' . $month . '/';
                $destRel = $destDir . $clean . '.pdf';

                Storage::disk('nas_fatp')->makeDirectory($destDir);
                Storage::disk('nas_fatp')->move($srcRel, $destRel);
                
                $bl->status_bl = 2;

                $bl->save();

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