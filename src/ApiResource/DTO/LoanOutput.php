<?php

namespace App\ApiResource\DTO;

class LoanOutput
{
    public int $id;
    public string $memberName;
    public string $bookTitle;
    public string $loanDate;
    public ?string $returnDate = null;
    public string $status;
}
