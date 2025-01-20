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

    public function __construct(
        int $id,
        string $memberName,
        string $bookTitle,
        string $loanDate,
        string $returnDate,
        string $status
    ) {
        $this->id = $id;
        $this->memberName = $memberName;
        $this->bookTitle = $bookTitle;
        $this->loanDate = $loanDate;
        $this->returnDate = $returnDate;
        $this->status = $status;
    }
}
