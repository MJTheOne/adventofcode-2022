<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

final class Move
{
    private function __construct(
        public readonly int $nrOfCratesToMove,
        public readonly int $from,
        public readonly int $to,
    ) {
    }

    public static function create(
        int $nrOfCratesToMove,
        int $from,
        int $to,
    ): self {
        return new self($nrOfCratesToMove, $from, $to);
    }
}
