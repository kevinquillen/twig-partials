<?php

declare(strict_types=1);

namespace TwigPartials;

use Twig\Source;

class PartialSource
{
    private Source $source;
    private string $fragment;

    public function __construct(Source $source, string $fragment)
    {
        $this->source = $source;
        $this->fragment = $fragment;
    }

    public function getSource(): Source
    {
        return $this->source;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function getCode(): string
    {
        return $this->source->getCode();
    }

    public function getName(): string
    {
        return $this->source->getName();
    }

    public function getPath(): string
    {
        return $this->source->getPath();
    }
}
