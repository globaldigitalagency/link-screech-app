<?php

namespace App\ScreamingFrog\Messenger\Object;

class ScreamingFrogCmd
{
    public function __construct(private string $filePath, private string $crawlName)
    {
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getCrawlName(): string
    {
        return $this->crawlName;
    }
}
