<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

final class AdventChallengeSkeleton extends Command
{
    protected $signature = 'advent:challenge:skeleton {--sample}';

    protected $description = 'Command description';

    public function handle(): int
    {
        if ($this->option('sample')) {
            $input = file_get_contents(storage_path('advent/advent_input_X-sample.txt'));
        } else {
            $input = file_get_contents(storage_path('advent/advent_input_X.txt'));
        }

        $start = \microtime(true);



        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }
}
