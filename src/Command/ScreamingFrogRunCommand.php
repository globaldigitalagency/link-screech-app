<?php

namespace App\Command;

use App\Helper\CommandHelper;
use App\Helper\CsvHelper;
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
        private readonly CommandHelper $commandHelper,
        private readonly CsvHelper $csvHelper,
    ) {
        parent::__construct($this->getName());
    }

    protected function configure()
    {
        $this
            ->addArgument('filePath', InputArgument::REQUIRED, 'File path with URLs to crawl')
            ->addArgument('crawlName', InputArgument::REQUIRED, 'Name of the crawl group');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('filePath');
        $crawlName = $input->getArgument('crawlName');

        $urls = $this->csvHelper->getAllRows($filePath);

        // if there are headers, remove them to get only the urls
        $headers = array_shift($urls);
        if (isset($headers[0]) && preg_match('/^(?:\xEF\xBB\xBF)?(?:http|https):\/\/.*$/i', $headers[0])) {
            $headers[0] = str_replace('\u{FEFF}', '', $headers[0]);
            $urls[] = $headers;
        }
        // checks empty after headers presence check
        if (empty($urls)) {
            throw new \Exception('No URLs found in the file.');
        }

        $urls = array_map(fn($url) => $url[0], $urls);
        $this->commandHelper->runCrawls($urls, $crawlName);

        $fs = new Filesystem();
        $fs->remove($filePath);

        return Command::SUCCESS;
    }
}
