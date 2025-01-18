<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\ApiResource\DTO\ReservationInput;
use App\ApiResource\DTO\ReservationOutput;

#[ApiResource(
    operations: [
        new Get(output: ReservationOutput::class),
        new Post(input: ReservationInput::class, output: ReservationOutput::class),
        new Put(input: ReservationInput::class, output: ReservationOutput::class),
        new Delete()
    ],
    routePrefix: '/api',
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
class ReservationResource {}
