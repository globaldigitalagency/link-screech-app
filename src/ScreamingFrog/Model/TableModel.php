<?php

namespace App\ScreamingFrog\Model;

class TableModel
{
    public function __construct(
        public ?string $name = null,
        public array $fields = [],
        public array $data = [],
    )
    {
    }
}
