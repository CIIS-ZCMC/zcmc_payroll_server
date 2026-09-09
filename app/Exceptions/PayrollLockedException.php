<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Thrown when a write is attempted against a payroll period that has been
 * locked.
 *
 * This used to be a bare `new \Exception("Payroll is already locked", 403)`.
 * The 403 there is an exception code, not an HTTP status — Laravel renders a
 * plain \Exception as a 500 and ignores the code entirely, so a locked-period
 * write reported itself to the client as a server error.
 */
class PayrollLockedException extends HttpException
{
    public function __construct(int $payrollPeriodId, ?string $detail = null)
    {
        parent::__construct(
            Response::HTTP_FORBIDDEN,
            $detail ?? "Payroll period {$payrollPeriodId} is locked and can no longer be modified."
        );
    }
}
