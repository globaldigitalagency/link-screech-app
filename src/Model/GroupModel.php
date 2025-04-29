<?php

namespace App\Model;

use DateTime;

class GroupModel
{
    public function __construct(
        public int $crawlsNumber = 0,
        public ?string $name = null,
        public ?DateTime $date = null,
    )
    {
    }
}
