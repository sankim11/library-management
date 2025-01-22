<?php

namespace App\ApiResource\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoanExtensionInput
{
    #[Assert\NotBlank(message: "Loan ID is required.")]
    public ?int $loanId = null;

    #[Assert\NotBlank(message: "New return date is required.")]
    #[Assert\Date(message: "Return date must be a valid date.")]
    public ?string $newReturnDate = null;
}
