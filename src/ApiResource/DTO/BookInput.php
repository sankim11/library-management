<?php

namespace App\ApiResource\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BookInput
{
    #[Assert\NotBlank(message: "Title is required.")]
    public ?string $title = null;

    #[Assert\NotBlank(message: "Author is required.")]
    public ?string $author = null;

    #[Assert\NotBlank(message: "ISBN is required.")]
    #[Assert\Length(min: 10, max: 13, exactMessage: "ISBN must be 10 or 13 characters.")]
    public ?string $isbn = null;

    #[Assert\NotBlank(message: "Published date is required.")]
    #[Assert\Date(message: "Published date must be a valid date.")]
    public ?string $publishedDate = null;

    #[Assert\NotBlank(message: "Quantity is required.")]
    #[Assert\Positive(message: "Quantity must be greater than zero.")]
    public ?int $quantity = null;
}
