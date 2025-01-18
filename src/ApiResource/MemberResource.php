<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\DTO\MemberInput;
use App\ApiResource\DTO\MemberOutput;

#[ApiResource(
    operations: [
        new Get(output: MemberOutput::class),
        new Post(input: MemberInput::class, output: MemberOutput::class),
        new Put(input: MemberInput::class, output: MemberOutput::class),
        new Delete()
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
class MemberResource {}
