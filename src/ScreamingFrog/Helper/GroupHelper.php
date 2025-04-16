<?php

namespace App\ScreamingFrog\Helper;

use App\ScreamingFrog\Model\GroupModel;
use DateTime;
use Symfony\Component\Finder\Finder;

class GroupHelper
{
    public function getMappedGroups(Finder $groupsDir) {
        foreach ($groupsDir as $group) {
            if (!$group->isDir()) {
                continue;
            }

            $fd = new Finder();
            $fd->directories()->in($group->getPathname())->depth(0);

            $groupModel = new GroupModel();
            $groupModel->setCrawlsNumber($fd->count());
            $groupModel->setName($group->getBasename());
            $groupModel->setDate(new DateTime('@' . $group->getCTime()));

            yield $groupModel;
        }
    }
}
