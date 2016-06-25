<?php

declare(strict_types=1);

namespace Regis\Application\Entity;

class Analysis
{
    private $id;
    private $inspection;
    private $result;

    public function getId(): string
    {
        return $this->id;
    }

    public function getInspection(): Inspection
    {
        return $this->inspection;
    }

    public function setInspection(Inspection $inspection)
    {
        $this->inspection = $inspection;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result)
    {
        $this->result = $result;
    }
}
