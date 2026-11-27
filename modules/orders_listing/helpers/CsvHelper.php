<?php

namespace app\modules\orders_listing\helpers;

use RuntimeException;

final class CsvHelper
{
    public static function encodeRow(array $row, string $delimiter = ';'): string
    {
        $handle = fopen('php://memory', 'w+');
        if ($handle === false) {
            throw new RuntimeException('Cannot open memory stream');
        }
        fputcsv($handle, $row, $delimiter, '"', '\\');
        rewind($handle);
        $line = (string) stream_get_contents($handle);
        fclose($handle);

        return $line;
    }
}
