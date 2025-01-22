<?php

namespace App\ApiResource\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SignUpInput
{
    #[Assert\NotBlank(message: "Name cannot be blank.")]
    public string $name;

    #[Assert\NotBlank(message: "Email cannot be blank.")]
    #[Assert\Email(message: "Invalid email address.")]
    public string $email;

    #[Assert\NotBlank(message: "Password cannot be blank.")]
    public string $password;
}
