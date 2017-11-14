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
        $this->logger->info('Fetching repositories list for user {id}', [
            'id' => $this->user->accountId(),
        ]);

        /** @var Repositories $repositories */
        $repositories = $this->client->api('Repositories');

        $page = new Pager($repositories->getClient(), $repositories->all($this->user->getUsername()));
        while ($response = $page->fetchNext()) {
            $content = json_decode($response->getContent(), true);

            yield from $this->parseRepositories($content);
        }
    }

    private function parseRepositories(array $response)
    {
        foreach ($response['values'] as $item) {
            if ($item['scm'] !== 'git') {
                continue;
            }

            $cloneEndpoint = array_filter($item['links']['clone'], function (array $endpoint) {
                return $endpoint['name'] === 'https';
            });

            yield new Model\Repository(
                new Model\RepositoryIdentifier($item['uuid']),
                $item['name'],
                $cloneEndpoint[0]['href'],
                $item['links']['html']['href']
            );
        }
    }
}
