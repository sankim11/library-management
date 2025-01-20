<?php

namespace App\ApiResource\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoanInput
{
    #[Assert\NotBlank(message: "Member ID is required.")]
    public int $member_id;

    #[Assert\NotBlank(message: "Book ID is required.")]
    public int $book_id;

    #[Assert\NotBlank(message: "Loan date is required.")]
    #[Assert\DateTime(message: "Loan date must be a valid date in YYYY-MM-DD format.")]
    public string $loan_date;

    #[Assert\DateTime(message: "Return date must be a valid date in YYYY-MM-DD format.")]
    public ?string $return_date = null;

    #[Assert\NotBlank(message: "Loan status is required.")]
    #[Assert\Choice(choices: ["ACTIVE", "OVERDUE", "COMPLETED"], message: "Invalid status value. Allowed values are: ACTIVE, OVERDUE, COMPLETED.")]
    public string $status;
}
