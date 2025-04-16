<?php

namespace App\ScreamingFrog\Helper;

use Symfony\Component\Filesystem\Filesystem;

class LogHelper
{
    const LOGS_DIR = 'var/log';

    private string $fileName = '';

    public function __construct(private readonly string $projectDir)
    {
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = str_replace(':', '_', $fileName);
    }

    private function getLogFilePath(?string $date = null): string
    {
        $filename = sprintf('%s%s.log', $this->fileName, empty($date) ? '' : '_'.$date );

        return sprintf('%s/%s/%s', $this->projectDir, self::LOGS_DIR, $filename);
    }

    public function manageLogFiles(): bool
    {
        $oldFilePath = $this->getLogFilePath();

        $date = new \DateTime('first day of last month');
        $newFilePath = $this->getLogFilePath($date->format('m-Y'));

        if (file_exists($oldFilePath) && !file_exists($newFilePath)) {
            $oldContent = file_get_contents($oldFilePath);

            return file_put_contents($newFilePath, $oldContent) && file_put_contents($oldFilePath, '');
        }

        return false;
    }

    public function log(string $type, string $message): void
    {
        $logFilePath = $this->getLogFilePath();
        $date = new \DateTime();
        $formattedMessage = sprintf("%s - [%s] %s\n", $date->format('Y-m-d H:i:s'), $type, $message);

        $fs = new Filesystem();
        if(!file_exists($logFilePath)) {
            $fs->touch($logFilePath);
        }

        $fs->appendToFile($logFilePath, $formattedMessage);
    }
}
