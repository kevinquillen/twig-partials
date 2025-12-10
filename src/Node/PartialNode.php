<?php

declare(strict_types=1);

namespace TwigPartials\Node;

use Twig\Compiler;
use Twig\Node\Node;

class PartialNode extends Node
{
    public function __construct(string $name, int $line)
    {
        parent::__construct([], ['name' => $name], $line);
    }

    public function compile(Compiler $compiler): void
    {
        $name = $this->getAttribute('name');

        $compiler
            ->addDebugInfo($this)
            ->write("echo \$this->renderPartial_{$name}(\$context);\n");
    }
}
