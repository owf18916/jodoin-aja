<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Payable;
use Illuminate\Support\Facades\Storage;

class PayableMatchingServices
{
    protected array $matchedPayables = [];
    protected array $unmatchedPayables = [];

    public function matchPayables(): void
    {
        $openPayables = Payable::select('id','invoice_number','accounted_date')
            ->where('status', 1)
            ->get();

        // Struktur di NAS:
        //   \\10.62.230.21\data fatp\jodoin\copy-payables-here\
        //   \\10.62.230.21\data fatp\jodoin\payables\YYYY\MM\
        $inboxBase = 'copy-payables-here/';
        $targetRoot = 'payables/';

        foreach ($openPayables as $payable) {
            $clean = preg_replace('/[\\\\\/:\*\?"<>|]/', '-', $payable->invoice_number);
            $srcRel = $inboxBase . $clean . '.pdf';

            if (Storage::disk('nas_fatp')->exists($srcRel)) {
                $year  = Carbon::parse($payable->accounted_date)->format('Y');
                $month = Carbon::parse($payable->accounted_date)->format('m');

                $destDir = $targetRoot . $year . '/' . $month . '/';
                $destRel = $destDir . $clean . '.pdf';

                Storage::disk('nas_fatp')->makeDirectory($destDir);
                Storage::disk('nas_fatp')->move($srcRel, $destRel);

                $payable->status = 2;
                $payable->document_type = 'pdf';
                $payable->save();

                $this->matchedPayables[] = $payable->toArray();
            } else {
                $this->unmatchedPayables[] = [
                    'row' => $payable->id,
                    'errors' => [[ 'Dokumen untuk invoice : ' . $payable->invoice_number . ' tidak ditemukan' ]],
                ];
            }
        }
    }

    public function getMatchedPayables(): array { return $this->matchedPayables; }
    public function getUnmatchedPayables(): array { return $this->unmatchedPayables; }
}