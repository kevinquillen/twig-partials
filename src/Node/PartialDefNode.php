<?php

declare(strict_types=1);

namespace TwigPartials\Node;

use Twig\Compiler;
use Twig\Node\Node;

class PartialDefNode extends Node
{
    public function __construct(string $name, Node $body, int $line)
    {
        parent::__construct(['body' => $body], ['name' => $name], $line);
    }

    public function compile(Compiler $compiler): void
    {
        $name = $this->getAttribute('name');

        $compiler
            ->addDebugInfo($this)
            ->write("public function renderPartial_{$name}(\$context, \$blocks = [])\n")
            ->write("{\n")
            ->indent()
            ->write("ob_start();\n")
            ->subcompile($this->getNode('body'))
            ->write("return ob_get_clean();\n")
            ->outdent()
            ->write("}\n\n");
    }
}
