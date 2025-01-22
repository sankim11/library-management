<?php

namespace App\Service;

use App\Entity\Member;
use App\Enum\Role;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MemberService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    public function createMember(string $name, string $email, string $password, Role $role): Member
    {
        $member = new Member();
        $member->setName($name);
        $member->setEmail($email);
        $member->setRole($role);
        $member->setPassword(
            $this->passwordHasher->hashPassword($member, $password)
        );

        $errors = $this->validator->validate($member);
        if (count($errors) > 0) {
            throw new ValidationException((string) $errors);
        }

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    public function updateMember(int $id, array $data): void
    {
        $member = $this->entityManager->getRepository(Member::class)->find($id);
        $member->setName($data['name']);
        
        if (isset($data['role'])) {
            try {
                $role = Role::from($data['role']);
                $member->setRole($role);
            } catch (\ValueError $e) {
                throw new \InvalidArgumentException("Invalid role provided: {$data['role']}");
            }
        }

        $this->entityManager->flush();
    }

    public function deleteMember(int $id): void
    {
        $member = $this->entityManager->getRepository(Member::class)->find($id);

        if (!$member) {
            throw new ValidationException('Member not found');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();
    }
}
