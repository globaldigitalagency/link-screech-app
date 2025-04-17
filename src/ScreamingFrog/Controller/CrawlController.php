<?php

declare(strict_types=1);

namespace App\ScreamingFrog\Controller;

use App\ScreamingFrog\Form\GroupFormType;
use App\ScreamingFrog\Helper\CrawlHelper;
use App\ScreamingFrog\Helper\GroupHelper;
use App\ScreamingFrog\Messenger\Object\ScreamingFrogCmd;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

class CrawlController extends AbstractController
{
    const NUMBER_BY_PAGE = 10;

    public function __construct(
        private readonly string $projectDir,
        private readonly string $reportsPath,
        private readonly string $csvUploadPath,
        private readonly CrawlHelper $crawlHelper,
        private readonly GroupHelper $groupHelper,
        private readonly MessageBusInterface $bus,
    )
    {
    }

    public function list(): Response
    {
        $reportsPath = sprintf('%s/%s', $this->projectDir, $this->reportsPath);

        if (!is_dir($reportsPath) ) {
            throw new \Exception('Reports directory does not exist: ' . $reportsPath);
        }

        $fd = new Finder();
        $groupsDir = $fd->directories()->in($reportsPath)->depth(0);

        $groups = $this->groupHelper->getMappedGroups($groupsDir);

        return $this->render('screaming_frog/crawl/list.html.twig', [
            'groups' => $groups,
        ]);
    }

    public function group(string $group, int $page = 1): Response
    {
        $groupDir = sprintf('%s/%s/%s', $this->projectDir, $this->reportsPath, $group);
        if (!is_dir($groupDir) ) {
            throw new \Exception('Crawl directory does not exist: ' . $groupDir);
        }

        $fd = new Finder();
        $fd->directories()->in($groupDir)->depth(0);

        $maxPage = ceil($fd->count() / self::NUMBER_BY_PAGE);
        if ($maxPage <= 0) {
            return $this->redirectToRoute('screaming_frog_list');
        } else if ($page < 1 || $page > $maxPage) {
            return $this->redirectToRoute('screaming_frog_group', [
                'group' => $group,
                'page' => 1,
            ]);
        }

        $crawls = $fd->directories()->in($groupDir)->depth(0);
        $crawls = $this->crawlHelper->getGroupedCrawls($crawls, $page, self::NUMBER_BY_PAGE);

        return $this->render('screaming_frog/crawl/group.html.twig', [
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

    public function new(Request $request): Response
    {
        $reportsPath = sprintf('%s/%s', $this->projectDir, $this->reportsPath);
        if (!is_dir($reportsPath) ) {
            throw new \Exception('Reports directory does not exist: ' . $reportsPath);
        }

        $form = $this->createForm(GroupFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $crawlName = $form->get('groupName')->getData();

            $file->move(sprintf('%s/%s', $this->projectDir, $this->csvUploadPath), $file->getClientOriginalName());

            $this->bus->dispatch(new ScreamingFrogCmd(
                sprintf('%s/%s/%s', $this->projectDir, $this->csvUploadPath, $file->getClientOriginalName()),
                $crawlName,
            ));

            return $this->redirectToRoute('screaming_frog_list');
        }

        return $this->render('screaming_frog/crawl/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
