<?php

namespace App\Services;

class ValidationErrorService
{
    public $totalRecords, $chunkSize, $totalChunks, $filePath, $fileName;

    private $fields;

    public function __construct(public $errors)
    {
        $this->setData();
    }

    public function setData()
    {
        $this->totalRecords = count($this->errors);
        $this->chunkSize = 1000;
        ceil($this->totalRecords / $this->chunkSize) == 0 ? $this->totalChunks = 1 : $this->totalChunks = $this->totalRecords / $this->chunkSize;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function setCsvHeader($header = null): array
    {
        if (empty($header)) {
            return [
                'No',
                'Row',
                'Field',
                'Error'
            ];
        } else {
            return $header;
        }
    }

    public function processInChunks()
    {
        $chunk = [];
        $count = 0;

        foreach ($this->errors as $error) {
            $row = $error['row'];
            $errors = $error['errors'];

            foreach ($errors as $index => $error) {
                // Menambahkan hasil ke chunk
                $chunk[] = [
                    "row" => $row,
                    "field" => $this->fields[$index],
                    "error" => $error[0]
                ];

                $count++;

                // Jika jumlah elemen dalam chunk sudah mencapai chunkSize
                if ($count % $this->chunkSize === 0) {
                    // Menghasilkan chunk
                    yield $chunk;

                    // Reset chunk setelah dikirim
                    $chunk = [];
                }
            }
        }

        // Jika ada sisa data yang belum dihasilkan
        if (!empty($chunk)) {
            yield $chunk;
        }
    }

    public function writeToCsv($activityId)
    {
        $this->fileName = 'error-log-'.$activityId.'.csv';
        $this->filePath = storage_path('app/public/exports/'.$this->fileName);
        // Membuka file CSV untuk ditulis
        $file = fopen($this->filePath, 'w');

        // Menulis header CSV
        fputcsv($file, $this->setCsvHeader());

        $nomor = 1;  // Untuk kolom Nomor
        // Iterasi hasil dari processInChunks
        foreach ($this->processInChunks($this->chunkSize) as $chunk) {
            foreach ($chunk as $item) {
                // Menulis setiap item ke file CSV
                fputcsv($file, [
                    $nomor,
                    $item['row'],
                    $item['field'],
                    $item['error']
                ]);
                $nomor++;
            }
        }

        // Menutup file CSV setelah selesai
        fclose($file);
    }
}