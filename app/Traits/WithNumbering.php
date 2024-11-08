<?php

namespace App\Traits;

trait WithNumbering
{
    protected function generateDigits($numbers)
    {
        if (strlen($numbers) == 1) {
            return '0000';
        } else if (strlen($numbers) == 2) {
            return '000';
        } else if (strlen($numbers) == 3) {
            return '00';
        } else if (strlen($numbers) == 4) {
            return '0';
        }
    }
}