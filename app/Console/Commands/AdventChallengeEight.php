<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use JetBrains\PhpStorm\ArrayShape;
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
        $scenicScore = 0;

        for ($y = 0; $y < $forestHeight; $y++) {
            for ($x = 0; $x < $forestWidth; $x++) {
                $this->markTreeVisible(
                    x: $x,
                    y: $y,
                    forest: $forest,
                    visibleTrees: $visibleTrees,
                    scenicScore: $scenicScore,
                );
            }
        }

        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('Total visible trees: ' . $visibleTrees);
        $this->getOutput()->writeln('Highest scenic score: ' . $scenicScore);
        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }

    private function markTreeVisible(
        int $x,
        int $y,
        array $forest,
        int &$visibleTrees,
        int &$scenicScore,
    ): void {
        if ($this->isEdge($x, $y, $forest)) {
            $visibleTrees++;
            return;
        }

        $left = $this->treeVisibleLeft($x, $y, $forest);
        $right = $this->treeVisibleRight($x, $y, $forest);
        $top = $this->treeVisibleTop($x, $y, $forest);
        $bottom = $this->treeVisibleBottom($x, $y, $forest);

        if ($left['bool'] || $right['bool'] || $top['bool'] || $bottom['bool']) {
            $visibleTrees++;
        }

        $score = $left['score'] * $right['score'] * $top['score'] * $bottom['score'];

        $scenicScore = \max($scenicScore, $score);
    }

    private function isEdge(int $x, int $y, array $forest): bool
    {
        if ($x === 0 || $y === 0 || $x === \count($forest[0]) || $y === \count($forest)) {
            return true;
        }

        return false;
    }

    #[ArrayShape(['bool' => 'bool', 'score' => 'int'])]
    private function treeVisibleLeft(int $x, int $y, array $forest): array
    {
        $bool = true;
        $score = 0;

        for ($i = $x - 1; $i >= 0; $i--) {
            $score++;
            if ($forest[$y][$i] >= $forest[$y][$x]) {
                $bool = false;
                break;
            }
        }

        return ['bool' => $bool, 'score' => $score];
    }

    #[ArrayShape(['bool' => 'bool', 'score' => 'int'])]
    private function treeVisibleRight(int $x, int $y, array $forest): array
    {
        $bool = true;
        $score = 0;

        for ($i = $x + 1; $i < \count($forest[0]); $i++) {
            $score++;
            if ($forest[$y][$i] >= $forest[$y][$x]) {
                $bool = false;
                break;
            }
        }

        return ['bool' => $bool, 'score' => $score];
    }

    #[ArrayShape(['bool' => 'bool', 'score' => 'int'])]
    private function treeVisibleTop(int $x, int $y, array $forest): array
    {
        $bool = true;
        $score = 0;

        for ($i = $y - 1; $i >= 0; $i--) {
            $score++;
            if ($forest[$i][$x] >= $forest[$y][$x]) {
                $bool = false;
                break;
            }
        }

        return ['bool' => $bool, 'score' => $score];
    }

    #[ArrayShape(['bool' => 'bool', 'score' => 'int'])]
    private function treeVisibleBottom(int $x, int $y, array $forest): array
    {
        $bool = true;
        $score = 0;

        for ($i = $y + 1; $i < \count($forest); $i++) {
            $score++;
            if ($forest[$i][$x] >= $forest[$y][$x]) {
                $bool = false;
                break;
            }
        }

        return ['bool' => $bool, 'score' => $score];
    }
}
