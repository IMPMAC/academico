<?php

namespace App\Services;

use App\Interfaces\EnrollmentSheetInterface;
use App\Models\Enrollment;

class GenericEnrollmentSheetService implements EnrollmentSheetInterface
{
    public function exportToWord(Enrollment $enrollment): never
    {
        abort(403, 'This method is not implemented yet');
    }
}
