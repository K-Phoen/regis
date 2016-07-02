<?php

namespace Regis\Application\Github\Exception;

class PayloadSignature extends \RuntimeException
{
    public static function missing(): PayloadSignature
    {
        return new static('Payload signature is missing.');
    }

    public static function invalid(): PayloadSignature
    {
        return new static('Payload signature is invalid.');
    }

    public static function couldNotDetermineRepository(): PayloadSignature
    {
        return new static('Could not determine the repository associated to the payload.');
    }

    public static function unknownRepository(string $repository, \Exception $previous = null): PayloadSignature
    {
        return new static(sprintf('Repository "%s" is not known.', $repository), 0, $previous);
    }

    public static function unknownAlgorithm(string $algorithm): PayloadSignature
    {
        return new static(sprintf('Algorithm "%s" is not known.', $algorithm));
    }
}
