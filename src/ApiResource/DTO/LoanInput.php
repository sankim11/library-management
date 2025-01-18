<?php

namespace App\ApiResource\DTO;

use App\Entity\Book;
use App\Entity\Member;
use Symfony\Component\Validator\Constraints as Assert;

class LoanInput
{
    #[Assert\NotBlank(message: "Member is required.")]
    public ?Member $member = null;

    #[Assert\NotBlank(message: "Book is required.")]
    public ?Book $book = null;

    #[Assert\NotBlank(message: "Loan date is required.")]
    #[Assert\Type("\DateTimeInterface")]
    public ?\DateTimeInterface $loanDate = null;

    #[Assert\Type("\DateTimeInterface")]
    public ?\DateTimeInterface $returnDate = null;

    #[Assert\NotBlank(message: "Loan status is required.")]
    public string $status;
}
