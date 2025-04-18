<?php

namespace App\ScreamingFrog\Messenger\Handler;

use App\ScreamingFrog\Helper\CommandHelper;
use App\ScreamingFrog\Messenger\Object\ScreamingFrogCmd;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\PhpExecutableFinder;

#[AsMessageHandler]
class ScreamingFrogCmdHandler
{
    public function __construct(private readonly string $projectDir, private readonly CommandHelper $commandHelper)
    {
    }

    public function __invoke(ScreamingFrogCmd $screamingFrogCmd)
    {
        $phpBinaryFinder = new PhpExecutableFinder();
        $phpBinaryPath = $phpBinaryFinder->find();

        $command = [
            $phpBinaryPath,
            sprintf('%s/bin/console', $this->projectDir),
            'screaming-frog:run',
            $screamingFrogCmd->getFilePath(),
            $screamingFrogCmd->getCrawlName(),
        ];

        $this->commandHelper->runCommand($command);
    }
}
