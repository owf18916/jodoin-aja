<?php

namespace App\Traits;

use PhpOffice\PhpSpreadsheet\IOFactory;

trait ExcelHandleTrait {
    protected $spreadsheet;
    protected $sheet;

    /**
     * Load a spreadsheet from the provided file path.
     *
     * @param string $filePath
     */
    protected function loadSpreadsheet(string $filePath)
    {
        $reader = IOFactory::createReader(IOFactory::identify($filePath));
        $this->spreadsheet = $reader->load($filePath);
    }

    /**
     * Check if the specified sheet exists in the spreadsheet.
     *
     * @param string $sheetName
     * @return bool
     */
    protected function isSheetValid(string $sheetName): bool
    {
        $this->sheet = $this->spreadsheet->getSheetByName($sheetName);
        return $this->sheet !== null;
    }

    /**
     * Check if the sheet is blank.
     *
     * @return bool
     */
    protected function isSheetBlank(): bool
    {
        $headerRow = 1;

        for ($row = $headerRow + 1; $row <= $this->sheet->getHighestRow(); $row++) {
            for ($col = 'A'; $col <= $this->sheet->getHighestColumn(); $col++) {
                $cellValue = $this->sheet->getCell($col . $row)->getValue();
                // If any cell is not empty, return false
                if (!empty($cellValue)) {
                    return false;
                }
            }
        }

        return true;
    }
}