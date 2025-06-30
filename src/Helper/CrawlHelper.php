<?php

namespace App\Helper;

use App\Enum\CrawlFileEnum;
use App\Model\CrawlModel;
use App\Model\SummaryModel;
use App\Model\TableModel;
use DateTime;
use Symfony\Component\Finder\Finder;

class CrawlHelper
{
    public function __construct(private readonly CsvHelper $csvHelper)
    {
    }

    public function getMappedCrawls(Finder $crawls, int $page, int $numberByPage): array
    {
        $start = ($page - 1) * $numberByPage;

        $crawls = iterator_to_array($crawls);
        usort($crawls, fn($a, $b) => $a->getBasename() <=> $b->getBasename());
        $wantedCrawls = array_slice($crawls, $start, $numberByPage);

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
        if (!is_dir($crawlPath)) {
            return null;
        }

        $url = $this->getUrlFromCrawl(sprintf('%s/%s', $crawlPath, CrawlFileEnum::MAIN_FILE_NAME->value));

        if (empty($url)) {
            return null;
        }

        $crawlModel = new CrawlModel(
            $url,
            $crawlDir,
            DateTime::createFromFormat('Y.m.d.H.i.s', $crawlDir),
            $this->buildSummary($crawlPath)
        );
        if (!$groupDisplay) {
            $crawlModel->tables[] =
                $this->buildTable(
                    sprintf('%s/%s', $crawlPath, CrawlFileEnum::EXTERNAL_NO_RESPONSE_FILE_NAME->value),
                    'Domaines inactifs'
                );
        }

        return $crawlModel;
    }

    private function buildSummary(string $crawlDir): SummaryModel
    {
        return new SummaryModel(
            $this->csvHelper->countRows($crawlDir.'/'.CrawlFileEnum::ALL_URL_FILE_NAME->value) - 1,
            $this->csvHelper->countRows($crawlDir.'/'.CrawlFileEnum::OUTLINKS_FILE_NAME->value) - 1,
            $this->csvHelper->countRows($crawlDir.'/'.CrawlFileEnum::EXTERNAL_NO_RESPONSE_FILE_NAME->value) - 1,
        );
    }

    private function buildTable(string $filePath, string $name): TableModel
    {
        $data = $this->csvHelper->getAllRows($filePath);
        $header = array_shift($data); // Get the header row
        $data = array_map(fn($row) => array_combine($header, $row), $data); // Combine header with each row

        return new TableModel(
            $name,
            $header,
            $data,
        );
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
