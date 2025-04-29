<?php

namespace App\Controller;

use App\Helper\CrawlHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;

class CrawlController extends AbstractController
{
    const NUMBER_BY_PAGE = 10;

    public function __construct(
        private readonly string $projectDir,
        private readonly string $reportsPath,
        private readonly CrawlHelper $crawlHelper,
    )
    {
    }

    public function list(string $group, int $page = 1): Response
    {
        $groupDir = sprintf('%s/%s/%s', $this->projectDir, $this->reportsPath, $group);
        if (!is_dir($groupDir) ) {
            throw new \Exception('Crawl directory does not exist: ' . $groupDir);
        }

        $fd = new Finder();
        $crawls = $fd->directories()->in($groupDir)->depth(0);

        $maxPage = ceil($fd->count() / self::NUMBER_BY_PAGE);
        if ($maxPage <= 0) {
            return $this->redirectToRoute('screaming_frog_group_list', [
                'page' => 1
            ]);
        } elseif ($page < 1 || $page > $maxPage) {
            return $this->redirectToRoute('screaming_frog_crawl_list', [
                'group' => $group,
                'page' => 1,
            ]);
        }

        $crawls = $this->crawlHelper->getMappedCrawls($crawls, $page, self::NUMBER_BY_PAGE);

        return $this->render('screaming_frog/crawl/list.html.twig', [
            'crawls' => $crawls,
            'maxPage' => $maxPage,
            'page' => $page,
            'group' => $group,
        ]);
    }

    public function show(string $group, string $crawl): Response
    {
        $crawlDir = sprintf('%s/%s/%s/%s', $this->projectDir, $this->reportsPath, $group, $crawl);
        if (!is_dir($crawlDir) ) {
            throw new \Exception('Crawl directory does not exist: ' . $crawlDir);
        }

        $crawlModel = $this->crawlHelper->getCurrentCrawl($crawlDir, $crawl);

        return $this->render('screaming_frog/crawl/show.html.twig', [
            'crawl' => $crawlModel,
        ]);
    }
}
