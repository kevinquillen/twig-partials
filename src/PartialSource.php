<?php

declare(strict_types=1);

namespace TwigPartials;

use Twig\Source;

class PartialSource extends Source
{
    private string $fragment;

    public function __construct(Source $source, string $fragment)
    {
        parent::__construct($source->getCode(), $source->getName(), $source->getPath());
        $this->fragment = $fragment;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }
}
