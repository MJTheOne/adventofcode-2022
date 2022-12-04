<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

final class AdventChallengeFour extends Command
{
    protected $signature = 'advent:challenge:four';

    protected $description = 'Command description';

    public function handle(): int
    {
        $input = Storage::get('advent_input_4.txt');

        $start = \microtime(true);
        $elfPairs = \explode(PHP_EOL, $input);

        $nrOfFullyContains = 0;
        $nrOfPartiallyContains = 0;
        foreach ($elfPairs as $elfPair) {
            if ($elfPair === '') {
                continue;
            }

            $assignmentGroups = \explode(',', $elfPair);
            if ($this->fullyContains($assignmentGroups)) {
                $nrOfFullyContains++;
                continue;
            }

            if ($this->partiallyContains($assignmentGroups)) {
                $nrOfPartiallyContains++;
            }
        }

        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('Nr of fully contains Part One: ' . $nrOfFullyContains);
        $this->getOutput()->writeln('Nr of partially contains Part Two: ' . ($nrOfPartiallyContains + $nrOfFullyContains));
        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }

    private function fullyContains(array $assignmentGroups): bool
    {
        $assignmentGroupPartsOne = \explode('-', $assignmentGroups[0]);
        $assignmentGroupPartsTwo = \explode('-', $assignmentGroups[1]);

        $startHigher = $assignmentGroupPartsOne[0] >= $assignmentGroupPartsTwo[0];
        $endLower = $assignmentGroupPartsOne[1] <= $assignmentGroupPartsTwo[1];
        if ($startHigher && $endLower) {
            return true;
        }

        $startHigher = $assignmentGroupPartsTwo[0] >= $assignmentGroupPartsOne[0];
        $endLower = $assignmentGroupPartsTwo[1] <= $assignmentGroupPartsOne[1];
        if ($startHigher && $endLower) {
            return true;
        }

        return false;
    }

    private function partiallyContains(array $assignmentGroups): bool
    {
        $assignmentGroupPartsOne = \explode('-', $assignmentGroups[0]);
        $assignmentGroupPartsTwo = \explode('-', $assignmentGroups[1]);

        $startLower = $assignmentGroupPartsOne[0] < $assignmentGroupPartsTwo[0];
        $endHigher = $assignmentGroupPartsOne[1] >= $assignmentGroupPartsTwo[0];
        if ($startLower && $endHigher) {
            return true;
        }

        $startLower = $assignmentGroupPartsTwo[0] < $assignmentGroupPartsOne[0];
        $endHigher = $assignmentGroupPartsTwo[1] >= $assignmentGroupPartsOne[0];
        if ($startLower && $endHigher) {
            return true;
        }

        return false;
    }
}
