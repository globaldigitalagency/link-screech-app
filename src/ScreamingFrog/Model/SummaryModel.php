<?php

namespace App\ScreamingFrog\Model;

class SummaryModel
{
    private int $urlsEncounteredNumber = 0;
    private int $outlinksNumber = 0;
    private int $urlsDomainExpiredNumber = 0;

    public function getUrlsEncounteredNumber(): int
    {
        return $this->urlsEncounteredNumber;
    }

    public function setUrlsEncounteredNumber(int $urlsEncounteredNumber): void
    {
        $this->urlsEncounteredNumber = $urlsEncounteredNumber;
    }

    public function getOutlinksNumber(): int
    {
        return $this->outlinksNumber;
    }

    public function setOutlinksNumber(int $outlinksNumber): void
    {
        $this->outlinksNumber = $outlinksNumber;
    }

    public function getUrlsDomainExpiredNumber(): int
    {
        return $this->urlsDomainExpiredNumber;
    }

    public function setUrlsDomainExpiredNumber(int $urlsDomainExpiredNumber): void
    {
        $this->urlsDomainExpiredNumber = $urlsDomainExpiredNumber;
    }
}
