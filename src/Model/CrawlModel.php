<?php

namespace App\Model;

use DateTime;

class CrawlModel
{
    public function __construct(
        public ?string $url = null,
        public ?string $folderName = null,
        public ?DateTime $date = null,
        public ?SummaryModel $summary = null,
        /** @var TableModel[] $tables */
        public array $tables = [],
    )
    {
    }
}
