<?php

namespace Tests\Unit;

use App\Enums\PayrollStep;
use PHPUnit\Framework\TestCase;

/**
 * The order of the seven steps, stated once.
 */
class PayrollStepTest extends TestCase
{
    public function test_the_ladder_has_seven_steps_in_order()
    {
        $this->assertSame([1, 2, 3, 4, 5, 6, 7], PayrollStep::all());
        $this->assertSame(PayrollStep::IMPORT, PayrollStep::FIRST);
        $this->assertSame(PayrollStep::PREVIEW, PayrollStep::LAST);
    }

    public function test_only_the_seven_steps_are_valid()
    {
        $this->assertTrue(PayrollStep::isValid(PayrollStep::IMPORT));
        $this->assertTrue(PayrollStep::isValid(PayrollStep::PREVIEW));

        $this->assertFalse(PayrollStep::isValid(0));
        $this->assertFalse(PayrollStep::isValid(8));
        $this->assertFalse(PayrollStep::isValid(-1));
    }

    public function test_a_run_moves_forward_one_step_at_a_time()
    {
        $this->assertTrue(PayrollStep::canAdvance(PayrollStep::IMPORT, PayrollStep::DEDUCTIONS));
        $this->assertTrue(PayrollStep::canAdvance(PayrollStep::RECOMPUTE, PayrollStep::PREVIEW));
    }

    /**
     * This is the transition the old code allowed: straight from the import
     * screen to preview, skipping adjustments, selection and the recompute.
     */
    public function test_a_run_cannot_skip_ahead()
    {
        $this->assertFalse(PayrollStep::canAdvance(PayrollStep::IMPORT, PayrollStep::PREVIEW));
        $this->assertFalse(PayrollStep::canAdvance(PayrollStep::IMPORT, PayrollStep::RECEIVABLES));
        $this->assertFalse(PayrollStep::canAdvance(PayrollStep::SELECTION, PayrollStep::PREVIEW));
    }

    /**
     * Going back to correct something is normal — the lock, not the step
     * counter, is what makes a run final.
     */
    public function test_a_run_may_go_back_to_any_earlier_step()
    {
        $this->assertTrue(PayrollStep::canAdvance(PayrollStep::PREVIEW, PayrollStep::IMPORT));
        $this->assertTrue(PayrollStep::canAdvance(PayrollStep::RECOMPUTE, PayrollStep::ADJUSTMENTS));
        $this->assertTrue(PayrollStep::canAdvance(PayrollStep::DEDUCTIONS, PayrollStep::DEDUCTIONS));
    }

    public function test_an_unknown_step_is_never_reachable()
    {
        $this->assertFalse(PayrollStep::canAdvance(PayrollStep::PREVIEW, 8));
        $this->assertFalse(PayrollStep::canAdvance(PayrollStep::IMPORT, 0));
    }

    public function test_every_step_is_labelled()
    {
        foreach (PayrollStep::all() as $step) {
            $this->assertNotSame('Unknown', PayrollStep::label($step));
        }

        $this->assertSame('Unknown', PayrollStep::label(99));
    }
}
