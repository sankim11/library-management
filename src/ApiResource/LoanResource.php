<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\DTO\LoanInput;
use App\ApiResource\DTO\LoanOutput;

#[ApiResource(
    operations: [
        new Get(output: LoanOutput::class),
        new Post(input: LoanInput::class, output: LoanOutput::class),
        new Put(input: LoanInput::class, output: LoanOutput::class),
        new Delete()
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
class LoanResource {}
