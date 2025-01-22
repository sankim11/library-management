<?php

namespace App\ApiResource\DTO;

class SignUpOutput
{
    public int $member_id;
    public string $name;
    public string $email;
    public string $role;

    public function __construct(int $member_id, string $name, string $email, string $role)
    {
        $this->member_id = $member_id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
    }
}
