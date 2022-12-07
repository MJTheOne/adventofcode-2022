<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

final class AdventChallengeOne extends Command
{
    protected $signature = 'advent:challenge:one';

    protected $description = 'Command description';

    public function handle(): int
    {
        $input = file_get_contents(storage_path('advent/advent_input_1.txt'));

        $start = \microtime(true);
        $array = \explode(PHP_EOL, $input);

        $newArray = [];
        $index = 0;
        \array_walk($array, function ($value) use (&$index, &$newArray) {
            $newArray[$index][] = $value;

            if (!\is_numeric($value)) {
                $index++;
            }
        });

        $totalArray = [];
        \array_walk($newArray, function ($value) use (&$totalArray) {
            $totalArray[] = \array_sum($value);
        });

        \arsort($totalArray);
        $values = \array_values($totalArray);

        $totalTopThree = 0;
        for ($i = 0; $i < 3; $i++) {
            $totalTopThree += $values[$i];
        }

        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('Challenge A: ' . $values[0]);
        $this->getOutput()->writeln('Challenge B: ' . $totalTopThree);
        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }
}
