<?php

declare(strict_types=1);

namespace TwigPartials\Node;

use Twig\Attribute\YieldReady;
use Twig\Compiler;
use Twig\Node\CaptureNode;
use Twig\Node\Node;

#[YieldReady]
class PartialDefNode extends Node
{
    public function __construct(string $name, Node $body, int $line)
    {
        parent::__construct(['body' => $body], ['name' => $name], $line);
    }

    public function compile(Compiler $compiler): void
    {
        $name = $this->getAttribute('name');
        $node = new CaptureNode($this->getNode('body'), $this->getNode('body')->getTemplateLine());

        $compiler
            ->addDebugInfo($this)
            ->write("public function renderPartial_{$name}(\$context, \$blocks = []): string|\\Twig\\Markup\n")
            ->write("{\n")
            ->indent()
            ->write("\$macros = \$this->macros;\n")
            ->write("return ")
            ->subcompile($node)
            ->raw("\n")
            ->outdent()
            ->write("}\n\n");
    }
}
