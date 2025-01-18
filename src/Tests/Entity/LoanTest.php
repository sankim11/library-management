<?php

namespace App\Tests\Entity;

use App\Entity\Loan;
use App\Entity\Book;
use App\Entity\Member;
use App\Enum\LoanStatus;
use PHPUnit\Framework\TestCase;

class LoanTest extends TestCase
{
    public function testLoanEntity(): void
    {
        $loan = new Loan();
        $member = new Member();
        $book = new Book();
        $loanDate = new \DateTime();
        $returnDate = new \DateTime();

        $loan->setMember($member)
            ->setBook($book)
            ->setLoanDate($loanDate)
            ->setReturnDate($returnDate)
            ->setStatus(LoanStatus::ACTIVE);

        $this->assertSame($member, $loan->getMember());
        $this->assertSame($book, $loan->getBook());
        $this->assertSame($loanDate, $loan->getLoanDate());
        $this->assertSame($returnDate, actual: $loan->getReturnDate());
        $this->assertSame(LoanStatus::ACTIVE, $loan->getStatus());
    }
}
