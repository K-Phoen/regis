<?php

namespace Regis\Bundle\WebhooksBundle\Worker;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Regis\Domain\Event\PullRequestOpened;
use Regis\Domain\Inspector;

class WebhookEvent implements ConsumerInterface
{
    private $inspector;

    public function __construct(Inspector $inspector)
    {
        $this->inspector = $inspector;
    }

    public function execute(AMQPMessage $msg)
    {
        $event = unserialize($msg->body);

        if ($event instanceof PullRequestOpened) {
            $this->inspector->inspectPullRequest($event->getPullRequest());
        }
    }
}