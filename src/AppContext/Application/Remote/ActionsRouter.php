<?php

declare(strict_types=1);

namespace Regis\AppContext\Application\Remote;

use Regis\AppContext\Domain\Entity;

class ActionsRouter implements Actions
{
    private $implementations;

    /**
     * @param Actions[] $implementations
     */
    public function __construct(array $implementations)
    {
        $this->implementations = $implementations;
    }

    public function createWebhook(Entity\Repository $repository, string $hookUrl)
    {
        $this->implementation($repository->getType())->createWebhook($repository, $hookUrl);
    }

    private function implementation(string $type): Actions
    {
        if (!isset($this->implementations[$type])) {
            throw new \LogicException(sprintf('No implementation found for type "%s"', $type));
        }

        return $this->implementations[$type];
    }
}
