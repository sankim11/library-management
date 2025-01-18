<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\DTO\BookInput;
use App\ApiResource\DTO\BookOutput;

#[ApiResource(
    operations: [
        new Get(output: BookOutput::class),
        new Post(input: BookInput::class, output: BookOutput::class),
        new Put(input: BookInput::class, output: BookOutput::class),
        new Delete()
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
class BookResource {}
