<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

final class AdventChallengeThree extends Command
{
    protected $signature = 'advent:challenge:three';

    protected $description = 'Command description';

    public function handle(): int
    {
        $input = file_get_contents(storage_path('advent/advent_input_3.txt'));

        $start = \microtime(true);
        $data = \explode(PHP_EOL, $input);

        $totalScorePartOne = 0;
        foreach ($data as $ruckSack) {
            if ($ruckSack === '') {
                continue;
            }

            $ruckSackLength = \strlen($ruckSack);
            $oneCompartment = ($ruckSackLength / 2);
            $compartmentOne = \substr($ruckSack, 0, $oneCompartment);
            $compartmentTwo = \substr($ruckSack, $oneCompartment, $oneCompartment);

            foreach (\str_split($compartmentOne) as $itemType) {
                if (\str_contains($compartmentTwo, $itemType)) {
                    $totalScorePartOne += $this->determineAlphabetScore($itemType);
                    break;
                }
            }
        }

        $totalScorePartTwo = 0;
        $chunkedData = \array_chunk($data, 3);
        foreach ($chunkedData as $chunk) {
            if (\count($chunk) !== 3) {
                continue;
            }

            $splitOne = \str_split($chunk[0]);
            $splitTwo = \str_split($chunk[1]);
            $splitThree = \str_split($chunk[2]);

            $badge = \implode(\array_unique(\array_intersect($splitOne, $splitTwo, $splitThree)));
            $totalScorePartTwo += $this->determineAlphabetScore($badge);
        }

        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('TotalScore Part One: ' . $totalScorePartOne);
        $this->getOutput()->writeln('TotalScore Part Two: ' . $totalScorePartTwo);
        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }

    private function determineAlphabetScore(string $letter): int
    {
        if (\ctype_upper($letter)) {
            return (\ord($letter) - (\ord('A')) + 27);
        } else {
            return (\ord($letter) - (\ord('a')) + 1);
        }
    }
}
