<?php

declare(strict_types=1);

namespace Regis\Application;

use Regis\Application\Model\Violation;

class ReportSummary
{
    private $warnings = 0;
    private $errors = 0;

    public function newViolation(Violation $violation)
    {
        if ($violation->isError()) {
            $this->errors += 1;
        } else {
            $this->warnings += 1;
        }
    }

    public function warningsCount(): int
    {
        return $this->warnings;
    }

    public function errorsCount(): int
    {
        return $this->errors;
    }
}
