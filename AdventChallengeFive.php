<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Data\Move;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

final class AdventChallengeFive extends Command
{
    protected $signature = 'advent:challenge:five {--sample}';

    protected $description = 'Command description';

    private int $maxCrates;

    public function handle(): int
    {
        if ($this->option('sample')) {
            $input = Storage::get('advent_input_5-sample.txt');
            $this->maxCrates = 3;
        } else {
            $input = Storage::get('advent_input_5.txt');
            $this->maxCrates = 9;
        }

        $start = \microtime(true);

        $inputParts = \explode(PHP_EOL . PHP_EOL, $input);

        $cratePositions = $this->figureOutCratePositions($inputParts[0]);
        $movesList = $this->figureOutMovesList($inputParts[1]);

        $cratePositionsCrateMover9000 = $cratePositions;
        $cratePositionsCrateMover9001 = $cratePositions;

        $this->processMovesForCratesForCrateMover9000($cratePositionsCrateMover9000, $movesList);
        $cratesOnTop9000 = $this->getTopCrates($cratePositionsCrateMover9000);

        $this->processMovesForCratesForCrateMover9001($cratePositionsCrateMover9001, $movesList);
        $cratesOnTop9001 = $this->getTopCrates($cratePositionsCrateMover9001);

        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('Crates on top [CrateMover 9000]: ' . $cratesOnTop9000);
        $this->getOutput()->writeln('Crates on top [CrateMover 9001]: ' . $cratesOnTop9001);
        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }

    /**
     * @return array<int, array<int, string>>
     */
    private function figureOutCratePositions(string $input): array
    {
        $data = \explode(PHP_EOL, $input);

        $cratePositions = [];
        $maximumCrateRows = $this->maxCrates;
        foreach ($data as $value) {
            $crates = \str_split($value, 4);
            for ($i = 0; $i < $maximumCrateRows; $i++) {
                if (isset($crates[$i]) && \str_starts_with($crates[$i], '[')) {
                    $cratePositions[$i][] = \trim($crates[$i], '[] ');
                }
            }
        }

        \ksort($cratePositions);

        return \array_combine(\range(1, \count($cratePositions)), \array_values($cratePositions));
    }

    /**
     * @return array<int, Move>
     */
    private function figureOutMovesList(string $input): array
    {
        $data = \explode(PHP_EOL, $input);
        \array_pop($data);

        $movesList = [];
        foreach ($data as $move) {
            $moveData = \explode(' ', $move);
            $movesList[] = Move::create(
                nrOfCratesToMove: (int) $moveData[1],
                from: (int) $moveData[3],
                to: (int) $moveData[5],
            );
        }

        return $movesList;
    }

    /**
     * @param array<int, array<int, string>> $cratePositions
     * @param array<int, Move> $movesList
     */
    private function processMovesForCratesForCrateMover9000(array &$cratePositions, array $movesList): void
    {
        foreach ($movesList as $move) {
            for ($i = 0; $i < $move->nrOfCratesToMove; $i++) {
                $crate = \array_shift($cratePositions[$move->from]);
                \array_unshift($cratePositions[$move->to], $crate);
            }
        }
    }

    /**
     * @param array<int, array<int, string>> $cratePositions
     * @param array<int, Move> $movesList
     */
    private function processMovesForCratesForCrateMover9001(array &$cratePositions, array $movesList): void
    {
        foreach ($movesList as $move) {
            $crates = [];
            for ($i = 0; $i < $move->nrOfCratesToMove; $i++) {
                $crates[] = \array_shift($cratePositions[$move->from]);
            }

            $crates = \array_reverse($crates);
            foreach ($crates as $crate) {
                \array_unshift($cratePositions[$move->to], $crate);
            }

        }
    }

    private function getTopCrates(array $cratePositions): string
    {
        $output = '';
        foreach ($cratePositions as $crate) {
            $output .= $crate[0];
        }

        return $output;
    }
}
