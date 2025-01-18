<?php

namespace App\ApiResource\DTO;

use App\Enum\Role;
use Symfony\Component\Validator\Constraints as Assert;

class MemberInput
{
    #[Assert\NotBlank(message: "Name cannot be blank.")]
    public string $name;

    #[Assert\NotBlank(message: "Email cannot be blank.")]
    #[Assert\Email(message: "Invalid email address.")]
    public string $email;

    #[Assert\NotBlank(message: "Password cannot be blank.")]
    public string $password;

    #[Assert\NotBlank(message: "Role cannot be blank.")]
    public Role $role;
}
