<?php

namespace App\ScreamingFrog\Helper;

class CsvHelper
{
    public function getCsvData(string $fileName, bool $countOnly = false): array|int
    {
        ini_set('memory_limit', '2G');

        if ($countOnly) {
            $rowCount = 0;
            foreach ($this->getCsvDataGenerator($fileName) as $row) {
                $rowCount++;
            }
            return $rowCount;
        }

        $rows = [];
        foreach ($this->getCsvDataGenerator($fileName) as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function getCsvDataGenerator(string $fileName): \Generator
    {
        if (!file_exists($fileName)) {
            throw new \RuntimeException(sprintf('File "%s" does not exist.', $fileName));
        }

        $file = fopen($fileName, 'r');
        if ($file === false) {
            throw new \RuntimeException(sprintf('Could not open file "%s".', $fileName));
        }

        while (($row = fgetcsv($file, 0, ',')) !== false) {
            yield $row;
        }

        fclose($file);
    }
}
