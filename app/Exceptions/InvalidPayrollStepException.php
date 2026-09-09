<?php

namespace App\Exceptions;

use App\Enums\PayrollStep;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a run is asked to move to a step it cannot legally reach from
 * where it is.
 */
class InvalidPayrollStepException extends HttpException
{
    public static function unknown(int $step): self
    {
        return new self(sprintf(
            'Step %d is not a payroll step. Valid steps are %s.',
            $step,
            implode(', ', PayrollStep::all())
        ));
    }

    public static function skipped(int $from, int $to): self
    {
        return new self(sprintf(
            'A payroll run cannot jump from step %d (%s) to step %d (%s). '
            . 'Complete step %d (%s) first.',
            $from,
            PayrollStep::label($from),
            $to,
            PayrollStep::label($to),
            $from + 1,
            PayrollStep::label($from + 1)
        ));
    }

    public function __construct(string $message)
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, $message);
    }
}
