<?php

namespace App\ScreamingFrog\Model;

use DateTime;

class GroupModel
{
    private int $crawlsNumber = 0;
    private string $name = '';
    private ?DateTime $date = null;

    public function getCrawlsNumber(): int
    {
        return $this->crawlsNumber;
    }

    public function setCrawlsNumber(int $crawlsNumber): void
    {
        $this->crawlsNumber = $crawlsNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }
}
