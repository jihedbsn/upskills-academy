<?php

namespace Tests\Unit;

use App\Services\InstallmentService;
use PHPUnit\Framework\TestCase;

/**
 * Locks on installment-backed course quizzes follow quiz_products.installment_month vs paid_installment count.
 */
class InstallmentQuizAccessTest extends TestCase
{
    private InstallmentService $installments;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installments = new InstallmentService();
    }

    public function test_zero_paid_installments_never_unlocks(): void
    {
        $this->assertFalse($this->installments->isQuizUnlockedForInstallmentProgress(0, null));
        $this->assertFalse($this->installments->isQuizUnlockedForInstallmentProgress(0, 1));
    }

    public function test_null_pivot_month_means_first_tranche(): void
    {
        $this->assertFalse($this->installments->isQuizUnlockedForInstallmentProgress(0, null));
        $this->assertTrue($this->installments->isQuizUnlockedForInstallmentProgress(1, null));
        $this->assertTrue($this->installments->isQuizUnlockedForInstallmentProgress(3, null));
    }

    public function test_explicit_month_requires_enough_payments(): void
    {
        $this->assertFalse($this->installments->isQuizUnlockedForInstallmentProgress(1, 2));
        $this->assertTrue($this->installments->isQuizUnlockedForInstallmentProgress(2, 2));
        $this->assertTrue($this->installments->isQuizUnlockedForInstallmentProgress(5, 2));
    }

    public function test_pivot_month_zero_clamps_to_one(): void
    {
        $this->assertTrue($this->installments->isQuizUnlockedForInstallmentProgress(1, 0));
    }

    public function test_string_pivot_values_coerce(): void
    {
        $this->assertFalse($this->installments->isQuizUnlockedForInstallmentProgress(1, '2'));
        $this->assertTrue($this->installments->isQuizUnlockedForInstallmentProgress(2, '2'));
    }

    public function test_empty_string_pivot_treated_as_first_tranche(): void
    {
        $this->assertTrue($this->installments->isQuizUnlockedForInstallmentProgress(1, ''));
    }
}
