<?php

namespace App\Helper;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class CommandHelper
{
    public function __construct(
        private readonly string $projectDir,
        private readonly string $screamingFrogConfigPath,
        private readonly string $reportsPath,
    ) {
    }

    public function runCrawls(array $urls, string $crawlName): string
    {
        $crawlPath = sprintf('%s/%s/%s', $this->projectDir, $this->reportsPath, $crawlName);
        if (is_dir($crawlPath)) {
            throw new \RuntimeException(sprintf('Crawl directory "%s" already exists.', $crawlPath));
        }

        $filesystem = new Filesystem();
        $filesystem->mkdir($crawlPath);

        $result = true;
        foreach ($urls as $url) {
            $commandResult = $this->runCrawlCommand($crawlPath, $url);
            $result = $result && $commandResult;

            usleep(100000);
        }

        return $result;
    }

    private function runCrawlCommand(string $crawlPath, string $crawl): string
    {
        $exportFormat = 'csv';
        $bulkExport = ['All Outlinks'];
        $exportTabs = ['Response Codes:External No Response', 'URL:All'];
        $saveReport = ['Crawl Overview'];
        $config = sprintf('%s/%s/config.seospiderconfig', $this->projectDir, $this->screamingFrogConfigPath);

        $command = [
            'screamingfrogseospider',
            '--crawl',
            $crawl,
            '--headless',
            '--output-folder',
            $crawlPath,
            '--config',
            $config,
            '--export-format',
            $exportFormat,
            '--bulk-export',
            implode(',', $bulkExport),
            '--export-tabs',
            implode(',', $exportTabs),
            '--timestamped-output',
            '--save-report',
            implode(',', $saveReport),
        ];

        return $this->runCommand($command);
    }

    public function runCommand(array $command): int
    {
        $process = new Process($command, $this->projectDir);
        $process->setTimeout(null);
        $process->mustRun(function ($type, $buffer) {
            echo $buffer;
        });

        return $process->getExitCode();
    }
}
