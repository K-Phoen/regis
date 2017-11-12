<?php

declare(strict_types=1);

namespace Regis\GithubContext\Application\Github\Exception;

class PayloadSignature extends \RuntimeException
{
    public static function missing(): self
    {
        return new static('Payload signature is missing.');
    }

    public static function invalid(): self
    {
        return new static('Payload signature is invalid.');
    }

    public static function couldNotDetermineRepository(): self
    {
        return new static('Could not determine the repository associated to the payload.');
    }

    public static function unknownRepository(string $repository, \Exception $previous = null): self
    {
        return new static(sprintf('Repository "%s" is not known.', $repository), 0, $previous);
    }

    public static function unknownAlgorithm(string $algorithm): self
    {
        return new static(sprintf('Algorithm "%s" is not known.', $algorithm));
    }
}
