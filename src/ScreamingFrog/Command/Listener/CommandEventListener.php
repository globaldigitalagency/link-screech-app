<?php

declare(strict_types=1);

namespace App\ScreamingFrog\Command\Listener;

use App\ScreamingFrog\Helper\LogHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommandEventListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly LogHelper $logHelper,
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND => 'manageEvent',
            ConsoleEvents::ERROR => 'manageEvent',
        ];
    }

    public function manageEvent(ConsoleEvent $event)
    {
        $this->logHelper->setFileName($event->getCommand()->getName());

        $instanceOf = get_class($event);
        switch ($instanceOf) {
            case ConsoleCommandEvent::class:
                $this->onConsoleCommand($event);
                break;
            case ConsoleErrorEvent::class:
                $this->onConsoleError($event);
                break;
            case ConsoleTerminateEvent::class:
                $this->onConsoleTerminate($event);
                break;
        }
    }

    private function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $this->logHelper->manageLogFiles();
        $this->logHelper->log(ConsoleEvents::COMMAND, 'Starting command ' . $event->getCommand()->getName());
    }

    private function onConsoleError(ConsoleErrorEvent $event)
    {
        $this->logHelper->log(ConsoleEvents::ERROR, $event->getError()->getMessage());
    }

    private function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        $this->logHelper->log(ConsoleEvents::TERMINATE, $event->getCommand()->getName() . ' finished');
    }
}
