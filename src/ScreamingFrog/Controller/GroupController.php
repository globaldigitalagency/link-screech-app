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
use Symfony\Component\Routing\Attribute\Route;

class GroupController extends AbstractController
{
    const NUMBER_BY_PAGE = 10;

    public function __construct(
        private readonly string $projectDir,
        private readonly string $reportsPath,
        private readonly string $csvUploadPath,
        private readonly GroupHelper $groupHelper,
        private readonly MessageBusInterface $bus,
    )
    {
    }

    public function list(int $page): Response
    {
        $reportsPath = sprintf('%s/%s', $this->projectDir, $this->reportsPath);

        if (!is_dir($reportsPath) ) {
            throw new \Exception('Reports directory does not exist: ' . $reportsPath);
        }

        $fd = new Finder();
        $groupsDir = $fd->directories()->in($reportsPath)->depth(0);

        $maxPage = ceil($fd->count() / self::NUMBER_BY_PAGE);
        if ($maxPage <= 0) {
            return $this->redirectToRoute('screaming_frog_group_new');
        } else if ($page < 1 || $page > $maxPage) {
            return $this->redirectToRoute('screaming_frog_group_list', [
                'page' => 1,
            ]);
        }

        $groups = $this->groupHelper->getMappedGroups($groupsDir, $page, self::NUMBER_BY_PAGE);

        return $this->render('screaming_frog/group/list.html.twig', [
            'groups' => $groups,
            'maxPage' => $maxPage,
            'page' => 1,
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

            return $this->redirectToRoute('screaming_frog_group_list', [
                'page' => 1
            ]);
        }

        return $this->render('screaming_frog/group/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
