<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Data\Move;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as CommandAlias;

final class AdventChallengeSix extends Command
{
    protected $signature = 'advent:challenge:six {--sample}';

    protected $description = 'Command description';

    public function handle(): int
    {
        if ($this->option('sample')) {
            $input = Storage::get('advent_input_6-sample.txt');
        } else {
            $input = Storage::get('advent_input_6.txt');
        }

        $start = \microtime(true);

        $startOfPacketPosition = 0;
        foreach (\str_split($input) as $key => $letter) {
            $currentPacket = \substr($input, $key, 4);
            $currentFourSplit = \str_split($currentPacket);
            $maybeFourUnique = \array_unique(\str_split($currentPacket));

            if (\count($currentFourSplit) === \count($maybeFourUnique)) {
                $startOfPacketPosition = ($key + 4);
                break;
            }
        }

        $startOfMessagePosition = 0;
        foreach (\str_split($input) as $key => $letter) {
            $currentMessage = \substr($input, $key, 14);
            $currentFourSplit = \str_split($currentMessage);
            $maybeFourUnique = \array_unique(\str_split($currentMessage));

            if (\count($currentFourSplit) === \count($maybeFourUnique)) {
                $startOfMessagePosition = ($key + 14);
                break;
            }
        }

        $totalTime = (\microtime(true) - $start);

        $this->getOutput()->writeln('Position for packet start: ' . $startOfPacketPosition);
        $this->getOutput()->writeln('Position for message start: ' . $startOfMessagePosition);
        $this->getOutput()->writeln('Total execution time: ' . $totalTime . 's');

        return CommandAlias::SUCCESS;
    }
}
