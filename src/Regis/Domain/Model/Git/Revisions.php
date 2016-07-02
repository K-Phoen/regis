<?php

declare(strict_types=1);

namespace Regis\Domain\Model\Git;

class Revisions
{
    private $base;
    private $head;

    public static function fromArray(array $data): Revisions
    {
        return new static($data['base'], $data['head']);
    }

    public function __construct(string $base, string $head)
    {
        $this->base = $base;
        $this->head  = $head;
    }

    public function toArray()
    {
        return [
            'base' => $this->base,
            'head' => $this->head,
        ];
    }

    public function getBase(): string
    {
        return $this->base;
    }

    public function getHead(): string
    {
        return $this->head;
    }
}