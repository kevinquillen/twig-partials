<?php

declare(strict_types=1);

namespace TwigPartials\Extension;

use Twig\Extension\AbstractExtension;
use TwigPartials\NodeVisitor\PartialNodeVisitor;
use TwigPartials\TokenParser\PartialDefTokenParser;
use TwigPartials\TokenParser\PartialTokenParser;

class PartialExtension extends AbstractExtension
{
    public function getTokenParsers(): array
    {
        return [
            new PartialDefTokenParser(),
            new PartialTokenParser(),
        ];
    }

    public function getNodeVisitors(): array
    {
        return [
            new PartialNodeVisitor(),
        ];
    }
}
