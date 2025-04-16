<?php

namespace App\ScreamingFrog\Model;

use DateTime;

class CrawlModel
{
    private string $url = '';
    private string $folderName = '';
    private ?DateTime $date = null;
    private ?SummaryModel $summary = null;
    /** @var TableModel[] $tables */
    private array $tables = [];

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getFolderName(): string
    {
        return $this->folderName;
    }

    public function setFolderName(string $folderName): void
    {
        $this->folderName = $folderName;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }

    public function getSummary(): ?SummaryModel
    {
        return $this->summary;
    }

    public function setSummary(?SummaryModel $summary): void
    {
        $this->summary = $summary;
    }

    public function getTables(): array
    {
        return $this->tables;
    }

    public function setTables(array $tables): void
    {
        $this->tables = $tables;
    }

    public function addTable(TableModel $table): void
    {
        $this->tables[] = $table;
    }
}
