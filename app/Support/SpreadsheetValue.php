<?php

declare(strict_types=1);

namespace App\Support;

final class SpreadsheetValue
{
    /**
     * Prevent user-controlled text from being interpreted as a spreadsheet
     * formula when a CSV or XLSX file is opened.
     */
    public static function escape(mixed $state): mixed
    {
        if (! is_string($state) || ! preg_match('/^[=+\-@]/', ltrim($state))) {
            return $state;
        }

        return "'{$state}";
    }
}
