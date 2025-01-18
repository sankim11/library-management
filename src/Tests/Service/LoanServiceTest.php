<?php

namespace App\Tests\Service;

use App\Entity\Loan;
use App\Service\LoanService;
use PHPUnit\Framework\TestCase;

class LoanServiceTest extends TestCase
{
    public function testLoanBook(): void
    {
        $loanService = $this->createMock(LoanService::class);

        $loan = new Loan();
        $loanService->expects($this->once())
            ->method('loanBook')
            ->willReturn($loan);

        $this->assertInstanceOf(Loan::class, $loan);
    }
}
