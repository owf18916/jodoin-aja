<?php

function fileExistsHelper($fileName) {
    $fullPath = 'C:\laragon\www\bulus\public\storage\\'.$fileName;

    return file_exists($fullPath) ? true : false;
}