<?php

namespace App\Tests\Entity;

use App\Entity\Member;
use App\Entity\Loan;
use App\Enum\Role;
use PHPUnit\Framework\TestCase;

class MemberTest extends TestCase
{
    public function testMemberEntity(): void
    {
        $member = new Member();
        $loan = $this->createMock(Loan::class);

        $member->setName('John Doe')
            ->setEmail('john.doe@example.com')
            ->setPassword('securepassword123')
            ->setRole(Role::ADMIN)
            ->addLoan($loans = new Loan());

        $this->assertEquals('John Doe', $member->getName());
        $this->assertEquals('john.doe@example.com', $member->getEmail());
        $this->assertEquals('securepassword123', $member->getPassword());
        $this->assertEquals(Role::ADMIN, $member->getRole());
        $this->assertCount(1, $member->getLoans());
        $this->assertSame($loan, $member->getLoans()->first());
    }
}
