<?php

declare(strict_types=1);

namespace App\ScreamingFrog\Command;

use App\ScreamingFrog\Helper\CommandHelper;
use App\ScreamingFrog\Helper\CsvHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'screaming-frog:run', description: 'Hello PhpStorm')]
class ScreamingFrogRunCommand extends Command
{
    public function __construct(
        private readonly string $projectDir,
        private readonly CommandHelper $commandHelper,
        private readonly CsvHelper $csvHelper,
    )
    {
        parent::__construct($this->getName());
    }

    protected function configure()
    {
        $this
            ->addArgument('filePath', InputArgument::REQUIRED, 'File path with URLs to crawl')
            ->addArgument('crawlName', InputArgument::REQUIRED, 'Name of the crawl group')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        dump(time());
        $filePath = $input->getArgument('filePath');
        $crawlName = $input->getArgument('crawlName');

        $urls = $this->csvHelper->getCsvData($filePath);

        $fs = new Filesystem();
        $fs->remove($filePath);

        // if there are headers, remove them to get only the urls
        $headers = array_shift($urls);
        if ($headers !== null && preg_match('/(?:http|https):\/\/.*/i', $headers[0])) {
            $urls[] = $headers;
        }
        // checks empty after headers presence check
        if (empty($urls)) {
            throw new \Exception('No URLs found in the file.');
        }

        $urls = array_map(fn($url) => $url[0], $urls);
        $this->commandHelper->runCrawls($urls, $crawlName);

        dump(time());
        return Command::SUCCESS;
    }
}
