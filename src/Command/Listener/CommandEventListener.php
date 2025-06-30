<?php

namespace App\Command\Listener;

use App\Helper\LogHelper;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommandEventListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly LogHelper $logHelper,
    ) {
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

        switch (get_class($event)) {
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
        $this->logHelper->log(ConsoleEvents::COMMAND, 'Starting command '.$event->getCommand()->getName());
    }

    private function onConsoleError(ConsoleErrorEvent $event)
    {
        $this->logHelper->log(ConsoleEvents::ERROR, $event->getError()->getMessage());
    }

    private function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        $this->logHelper->log(ConsoleEvents::TERMINATE, $event->getCommand()->getName().' finished');
    }
}
