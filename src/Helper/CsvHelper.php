<?php

namespace App\Helper;

use RuntimeException;
use SplFileObject;

class CsvHelper
{
    public function __construct(
        private readonly string $encoding = 'UTF-8',
        private readonly string $delimiter = ',',
        private readonly string $enclosure = '"',
        private readonly string $escape = '\\'
    ) {
    }

    public function getAllRows(string $fileName): array
    {
        $rows = [];
        foreach ($this->getRowGenerator($fileName) as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function getRow(string $fileName, int $line): ?array
    {
        if ($line < 0) {
            throw new \InvalidArgumentException('Line number must be non-negative.');
        }

        $file = $this->openFile($fileName);
        $file->seek($line);

        $row = $file->fgetcsv($this->delimiter, $this->enclosure, $this->escape);

        return $row !== false ? $row : null;
    }

    public function countRows(string $fileName): int
    {
        $file = fopen($fileName, 'r');
        if ($file === false) {
            throw new \RuntimeException(sprintf('Could not open file "%s" for counting.', $fileName));
        }

        if ($this->encoding === 'UTF-8' && fread($file, 3) !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        $count = 0;
        while (!feof($file)) {
            $line = fgets($file);
            if ($line !== false && trim($line) !== '') {
                $count++;
            }
        }

        fclose($file);

        return $count;
    }

    public function getRowGenerator(string $fileName): \Generator
    {
        $file = $this->openFile($fileName);

        while (!$file->eof() && ($row = $file->fgetcsv($this->delimiter, $this->enclosure, $this->escape)) !== false) {
            yield $row;
        }
    }

    private function openFile(string $fileName): SplFileObject
    {
        if (!file_exists($fileName)) {
            throw new RuntimeException(sprintf('File "%s" does not exist.', $fileName));
        }

        if (!is_readable($fileName)) {
            throw new RuntimeException(sprintf('File "%s" is not readable.', $fileName));
        }

        try {
            $file = new SplFileObject($fileName, 'r');
            $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);
            $file->setCsvControl($this->delimiter, $this->enclosure, $this->escape);

            return $file;
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf('Could not open file "%s": %s', $fileName, $e->getMessage()));
        }
    }
}
