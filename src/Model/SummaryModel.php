<?php

namespace App\Model;

class SummaryModel
{
    public function __construct(
        public int $urlsEncounteredNumber = 0,
        public int $outlinksNumber = 0,
        public int $urlsDomainExpiredNumber = 0,
    ) {
    }
}
