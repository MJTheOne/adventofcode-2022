<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

final class AdventChallengeSeven extends Command
{
    protected $signature = 'advent:challenge:seven {--sample}';

    protected $description = 'Command description';

    public function handle(): int
    {
        if ($this->option('sample')) {
            $input = file_get_contents(storage_path('advent/advent_input_7-sample.txt'));
        } else {
            $input = file_get_contents(storage_path('advent/advent_input_7.txt'));
        }

        $start = \microtime(true);

        $directoryStructureData = \explode(PHP_EOL, $input);
        \array_filter($directoryStructureData);

        $directoryListing = [];
        $curDir = '/';
        foreach ($directoryStructureData as $row) {
            if ($row === '$ cd /') {
                $curDir = '/';
            } elseif ($row === '$ cd ..') {
                $curDir = \str_replace('//', '/', dirname($curDir) . '/');
            } elseif (\str_starts_with($row, '$ cd ')) {
                $curDir .= \str_replace('$ cd ', '', $row) . '/';
            }

            if (!isset($directoryListing[$curDir])) {
                $directoryListing[$curDir] = [];
            }

            if (\preg_match('/^\d/', $row)) {
                $fileData = \explode(' ', $row);
                $directoryListing[$curDir][] = [
                    'filename' => $fileData[1],
                    'filesize' => (int) $fileData[0],
                ];
            }
        }

        foreach ($directoryListing as $dirPath => $directoryData) {
            $totalCurDirSize = 0;
            foreach ($directoryData as $file) {
                $totalCurDirSize += (int) $file['filesize'];
            }

            $directoryListing[$dirPath]['total_dir_size'] = $totalCurDirSize;

            if ($dirPath === '/') {
                continue;
            }

            $parents = \count(\explode('/', \rtrim($dirPath))) - 1;
            for ($i = 1; $i < $parents; $i++) {
                $parentPath = \str_replace('//', '/', dirname($dirPath, $i) . '/');

                $directoryListing[$parentPath]['total_dir_size'] += $totalCurDirSize;
            }
        }

        $totalSizeSmallerThanHundredK = 0;
        foreach ($directoryListing as $directoryData) {
            if ($directoryData['total_dir_size'] <= 100_000) {
                $totalSizeSmallerThanHundredK += (int) $directoryData['total_dir_size'];
            }
        }

        $totalDiskSize = 70_000_000;
        $availableDiskSize = ($totalDiskSize - $directoryListing['/']['total_dir_size']);
        $updateNeeds = 30_000_000;
        $spaceStillNeeded = $updateNeeds - $availableDiskSize;

        \usort($directoryListing, function ($a, $b) {
            return $b['total_dir_size'] <=> $a['total_dir_size'];
        });

        $dirSizeWeAreSearchingFor = '';
        foreach ($directoryListing as $key => $directoryData) {
            if ($directoryData['total_dir_size'] <= $spaceStillNeeded) {
                $dirSizeWeAreSearchingFor = $directoryListing[$key - 1]['total_dir_size'];
                break;
            }
        }

        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('Total directory size: ' . $totalSizeSmallerThanHundredK);
        $this->getOutput()->writeln('Directory with enough space (size): ' . $dirSizeWeAreSearchingFor);
        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }
}
