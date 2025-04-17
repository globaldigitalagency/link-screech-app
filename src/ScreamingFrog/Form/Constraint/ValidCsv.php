<?php

namespace App\ScreamingFrog\Form\Constraint;

use App\ScreamingFrog\Form\Constraint\Validator\ValidCsvValidator;
use Symfony\Component\Validator\Constraint;

class ValidCsv extends Constraint
{
    public $message = 'The file is not a valid CSV.';

    public function validatedBy()
    {
        return ValidCsvValidator::class;
    }
}
