<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

final class AdventChallengeTwo extends Command
{
    protected $signature = 'advent:challenge:two';

    protected $description = 'Command description';

    public function handle(): int
    {
        $input = file_get_contents(storage_path('advent/advent_input_2.txt'));

        $start = \microtime(true);
        $array = \explode(PHP_EOL, $input);

        // Rules:
        // Enemy
        // A Rock
        // B Paper
        // C Scissors

        // Part One
        // Self
        // X Rock       (1)
        // Y Paper      (2)
        // Z Scissors   (3)
        // Score: 0 lost, 3 draw, 6 win

        // Part Two
        // Self
        // X means lose
        // Y means draw
        // Z means win

        $partOneScoreArray = [];
        $partTwoScoreArray = [];
        foreach ($array as $rockPaperScissorsGame) {
            if ($rockPaperScissorsGame === '') {
                continue;
            }

            $rockPaperScissorsGameParts = \explode(' ', $rockPaperScissorsGame);

            $opponent = $rockPaperScissorsGameParts[0];
            $myself = $rockPaperScissorsGameParts[1];

            $bonusScore = $this->determineBonusScorePartOne($myself);
            $gameOutcomeScore = $this->determineGameOutcomePartOne($opponent, $myself);
            $partOneScoreArray[] = ($bonusScore + $gameOutcomeScore);

            $bonusScore = $this->determineBonusScorePartTwo($opponent, $myself);
            $gameOutcomeScore = $this->determineGameOutcomePartTwo($myself);
            $partTwoScoreArray[] = ($bonusScore + $gameOutcomeScore);
        }

        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('GameScore Part One: ' . \array_sum($partOneScoreArray));
        $this->getOutput()->writeln('GameScore Part Two: ' . \array_sum($partTwoScoreArray));
        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }

    private function determineBonusScorePartOne(string $myself): int
    {
        return match($myself) {
            'X' => 1,
            'Y' => 2,
            'Z' => 3,
        };
    }

    private function determineGameOutcomePartOne(string $opponent, string $myself): int
    {
        return match ($opponent) {
            'A' => match ($myself) {
                'X' => 3,
                'Y' => 6,
                'Z' => 0,
            },
            'B' => match ($myself) {
                'X' => 0,
                'Y' => 3,
                'Z' => 6,
            },
            'C' => match ($myself) {
                'X' => 6,
                'Y' => 0,
                'Z' => 3,
            },
            default => throw new \LogicException('This should not be possible'),
        };
    }

    private function determineBonusScorePartTwo(string $opponent, string $myself): int
    {
        return match ($opponent) {
            'A' => match ($myself) {
                'X' => 3,
                'Y' => 1,
                'Z' => 2,
            },
            'B' => match ($myself) {
                'X' => 1,
                'Y' => 2,
                'Z' => 3,
            },
            'C' => match ($myself) {
                'X' => 2,
                'Y' => 3,
                'Z' => 1,
            },
            default => throw new \LogicException('This should not be possible'),
        };
    }

    private function determineGameOutcomePartTwo(string $myself): int
    {
        return match ($myself) {
            'X' => 0,
            'Y' => 3,
            'Z' => 6,
        };
    }
}
