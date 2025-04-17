<?php

namespace App\ScreamingFrog\Helper;

use App\ScreamingFrog\Enum\CrawlFileEnum;
use App\ScreamingFrog\Model\CrawlModel;
use App\ScreamingFrog\Model\SummaryModel;
use App\ScreamingFrog\Model\TableModel;
use DateTime;
use Symfony\Component\Finder\Finder;

class CrawlHelper
{
    public function __construct(private readonly CsvHelper $csvHelper)
    {
    }

    public function getGroupedCrawls(Finder $crawls, int $page, int $number): array
    {
        $start = ($page - 1) * $number;

        $crawls = iterator_to_array($crawls);
        usort($crawls, fn ($a, $b) => $a->getBasename() <=> $b->getBasename());
        $wantedCrawls = array_slice($crawls, $start, $number);

        $mappedCrawls = [];
        foreach ($wantedCrawls as $crawl) {
            if (!$crawl->isDir()) {
                continue;
            }

            $crawlModel = $this->getCurrentCrawl($crawl->getPathname(), $crawl->getBasename(), true);
            if ($crawlModel === null) {
                continue;
            }

            $mappedCrawls[] = $crawlModel;
        }

        return $mappedCrawls;
    }

    public function getCurrentCrawl(string $crawlPath, string $crawlDir, bool $groupDisplay = false): ?CrawlModel
    {
        if(!is_dir($crawlPath)) {
            return null;
        }

        $crawlModel = new CrawlModel();
        $url = $this->getUrlFromCrawl(sprintf('%s/%s', $crawlPath, CrawlFileEnum::MAIN_FILE_NAME->value));

        if (empty($url)) {
            return null;
        }

        $crawlModel->setUrl($url);
        $crawlModel->setFolderName($crawlDir);
        $crawlModel->setDate(DateTime::createFromFormat('Y.m.d.H.i.s', $crawlDir));

        $crawlModel->setSummary($this->buildSummary($crawlPath));
        if(!$groupDisplay) {
            $crawlModel->addTable(
                $this->buildTable(sprintf('%s/%s', $crawlPath, CrawlFileEnum::EXTERNAL_NO_RESPONSE_FILE_NAME->value), 'Domaines inactifs')
            );
        }

        return $crawlModel;
    }

    private function buildSummary(string $crawlDir): SummaryModel
    {
        $summary = new SummaryModel();

        $urlsEncountered = $this->csvHelper->countRows($crawlDir . '/' . CrawlFileEnum::ALL_URL_FILE_NAME->value);
        $outlinks = $this->csvHelper->countRows($crawlDir . '/' . CrawlFileEnum::OUTLINKS_FILE_NAME->value);
        $urlsDomainExpired = $this->csvHelper->countRows($crawlDir . '/' . CrawlFileEnum::EXTERNAL_NO_RESPONSE_FILE_NAME->value);

        $summary->setUrlsEncounteredNumber($urlsEncountered - 1); // Exclude header row
        $summary->setOutlinksNumber($outlinks - 1); // Exclude header row
        $summary->setUrlsDomainExpiredNumber($urlsDomainExpired - 1); // Exclude header row

        return $summary;
    }

    private function buildTable(string $filePath, string $name): TableModel
    {
        $table = new TableModel();

        $data = $this->csvHelper->getAllRows($filePath);

        $header = array_shift($data); // Get the header row

        $table->setName($name);
        $table->setFields($header);
        $data = array_map(fn($row) => array_combine($header, $row), $data); // Combine header with each row
        $table->setData($data);

        return $table;
    }

    private function getUrlFromCrawl(string $filePath): bool|string
    {
        $urlRow = $this->csvHelper->getRow($filePath, line: 0);

        if (empty($urlRow) || !isset($urlRow[1]) || !str_starts_with($urlRow[1], 'http')) {
            return false;
        }

        return $urlRow[1];
    }
}
