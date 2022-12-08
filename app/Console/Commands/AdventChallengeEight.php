<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as CommandAlias;

#[AsCommand('advent:challenge:eight')]
final class AdventChallengeEight extends Command
{
    protected $signature = 'advent:challenge:eight {--sample}';

    public function handle(): int
    {
        if ($this->option('sample')) {
            $input = file_get_contents(storage_path('advent/advent_input_8-sample.txt'));
        } else {
            $input = file_get_contents(storage_path('advent/advent_input_8.txt'));
        }

        $start = \microtime(true);

        $forestRows = \explode(PHP_EOL, $input);
        $forestRows = \array_filter($forestRows);
        $forest = \array_map(
            function (string $rowData) {
                return \str_split($rowData);
            },
            $forestRows,
        );

        $forestHeight = \count($forest);
        $forestWidth = \count($forest[0]);
        $visibleTrees = 0;

        for ($y = 0; $y < $forestHeight; $y++) {
            for ($x = 0; $x < $forestWidth; $x++) {
                $this->markTreeVisible($x, $y, $forest, $visibleTrees);
            }
        }

        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('Total visible trees: ' . $visibleTrees);
        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }

    private function markTreeVisible(int $x, int $y, array $forest, int &$visibleTrees): void
    {
        if ($this->isEdge($x, $y, $forest)) {
            $visibleTrees++;
            return;
        }

        if ($this->treeVisibleLeft($x, $y, $forest)) {
            $visibleTrees++;
            return;
        }

        if ($this->treeVisibleRight($x, $y, $forest)) {
            $visibleTrees++;
            return;
        }

        if ($this->treeVisibleTop($x, $y, $forest)) {
            $visibleTrees++;
            return;
        }

        if ($this->treeVisibleBottom($x, $y, $forest)) {
            $visibleTrees++;
        }
    }

    private function isEdge(int $x, int $y, array $forest): bool
    {
        if ($x === 0 || $y === 0 || $x === \count($forest[0]) || $y === \count($forest)) {
            return true;
        }

        return false;
    }

    private function treeVisibleLeft(int $x, int $y, array $forest): bool
    {
        for ($i = $x - 1; $i >= 0; $i--) {
            if ($forest[$y][$i] >= $forest[$y][$x]) {
                return false;
            }
        }

        return true;
    }

    private function treeVisibleRight(int $x, int $y, array $forest): bool
    {
        for ($i = $x + 1; $i < \count($forest[0]); $i++) {
            if ($forest[$y][$i] >= $forest[$y][$x]) {
                return false;
            }
        }

        return true;
    }

    private function treeVisibleTop(int $x, int $y, array $forest): bool
    {
        for ($i = $y - 1; $i >= 0; $i--) {
            if ($forest[$i][$x] >= $forest[$y][$x]) {
                return false;
            }
        }

        return true;
    }

    private function treeVisibleBottom(int $x, int $y, array $forest): bool
    {
        for ($i = $y + 1; $i < \count($forest); $i++) {
            if ($forest[$i][$x] >= $forest[$y][$x]) {
                return false;
            }
        }

        return true;
    }
}
