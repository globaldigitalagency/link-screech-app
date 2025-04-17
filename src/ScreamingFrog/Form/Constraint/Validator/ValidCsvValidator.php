<?php

namespace App\ScreamingFrog\Form\Constraint\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidCsvValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $allowaedMimeTypes = [
            'text/csv',
            'application/csv',
            'text/plain',
        ];
        if ($value instanceof UploadedFile) {
            $handle = fopen($value->getPathname(), 'r');
            if (
                $handle === false ||
                fgetcsv($handle) === false ||
                $value->getClientOriginalExtension() !== 'csv' ||
                !in_array($value->getMimeType(), $allowaedMimeTypes)
            ) {
                $this->context->buildViolation('The file is not a valid CSV.')
                    ->addViolation();
            }
            fclose($handle);
        }
    }
}
