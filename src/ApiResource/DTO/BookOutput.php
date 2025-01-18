<?php

namespace App\ApiResource\DTO;

class BookOutput
{
    public ?int $id = null;
    public ?string $title = null;
    public ?string $author = null;
    public ?string $isbn = null;
    public ?string $publishedDate = null;
    public ?int $quantity = null;

    public function __construct(
        int $id,
        string $title,
        string $author,
        string $isbn,
        \DateTimeInterface $publishedDate,
        int $quantity
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->isbn = $isbn;
        $this->publishedDate = $publishedDate->format('Y-m-d');
        $this->quantity = $quantity;
    }
}
