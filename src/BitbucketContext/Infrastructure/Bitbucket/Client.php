<?php

declare(strict_types=1);

namespace Regis\BitbucketContext\Infrastructure\Bitbucket;

use Bitbucket\API\Http\Response\Pager;
use Bitbucket\API\Repositories;
use Psr\Log\LoggerInterface as Logger;

use Bitbucket\API\Api as VendorClient;
use Regis\BitbucketContext\Application\Bitbucket\Client as BitbucketClient;
use Regis\BitbucketContext\Domain\Entity\BitbucketDetails;
use Regis\BitbucketContext\Domain\Model;

class Client implements BitbucketClient
{
    private $client;
    private $user;
    private $logger;
    private $authenticated = false;

    public function __construct(VendorClient $client, BitbucketDetails $user, Logger $logger)
    {
        $this->client = $client;
        $this->user = $user;
        $this->logger = $logger;
    }

    public function listRepositories(): \Traversable
    {
        $this->logger->info('Fetching repositories list for user {owner_id}', [
            'owner_id' => $this->user->accountId(),
        ]);

        /** @var Repositories $repositories */
        $repositories = $this->client->api('Repositories');

        $page = new Pager($repositories->getClient(), $repositories->all($this->user->getUsername()));
        while ($response = $page->fetchNext()) {
            $content = json_decode($response->getContent(), true);

            yield from $this->parseRepositories($content);
        }
    }

    public function getCloneUrl(Model\RepositoryIdentifier $repository): string
    {
        $this->logger->info('Finding clone URL for repository {repository_id}', [
            'repository_id' => $repository->value(),
            'owner_id' => $this->user->accountId(),
        ]);

        /** @var Repositories $repositories */
        $repositories = $this->client->api('Repositories');
        $response = $repositories->all($this->user->getUsername(), [
            'q' => sprintf('uuid="%s"', $repository->value()),
        ]);

        $decodedResponse = json_decode($response->getContent(), true);

        if (count($decodedResponse['values']) !== 1) {
            throw new \RuntimeException('Expected a single result.');
        }

        $repositoryModel = $this->hydrateRepository($decodedResponse['values'][0]);

        return $repositoryModel->getCloneUrl();
    }

    private function parseRepositories(array $response)
    {
        foreach ($response['values'] as $item) {
            if ($item['scm'] !== 'git') {
                continue;
            }

            yield $this->hydrateRepository($item);
        }
    }

    private function hydrateRepository(array $data)
    {
        $cloneEndpoint = array_filter($data['links']['clone'], function (array $endpoint) {
            return $endpoint['name'] === 'https';
        });

        return new Model\Repository(
            new Model\RepositoryIdentifier($data['uuid']),
            $data['name'],
            $cloneEndpoint[0]['href'],
            $data['links']['html']['href']
        );
    }
}
