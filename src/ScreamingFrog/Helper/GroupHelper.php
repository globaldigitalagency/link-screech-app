<?php

namespace App\ScreamingFrog\Helper;

use App\ScreamingFrog\Model\GroupModel;
use DateTime;
use Symfony\Component\Finder\Finder;

class GroupHelper
{
    public function getMappedGroups(Finder $groupsDir, int $page, int $numberByPage): \Generator
    {
        $start = ($page - 1) * $numberByPage;

        $groups = iterator_to_array($groupsDir);
        usort($groups, function ($a, $b) {
            return $b->getCTime() <=> $a->getCTime();
        });
        $wantedGroups = array_slice($groups, $start, $numberByPage);

        foreach ($wantedGroups as $group) {
            if (!$group->isDir()) {
                continue;
            }

            $fd = new Finder();
            $fd->directories()->in($group->getPathname())->depth(0);

            $groupModel = new GroupModel(
                crawlsNumber: $fd->count(),
                name: $group->getBasename(),
                date: new DateTime('@' . $group->getCTime()),
            );

            yield $groupModel;
        }
    }
}
