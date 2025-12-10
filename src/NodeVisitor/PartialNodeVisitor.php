<?php

declare(strict_types=1);

namespace TwigPartials\NodeVisitor;

use Twig\Environment;
use Twig\Node\EmptyNode;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\Node\Nodes;
use Twig\NodeVisitor\NodeVisitorInterface;
use TwigPartials\Node\PartialDefNode;

class PartialNodeVisitor implements NodeVisitorInterface
{
    private array $partials = [];

    public function enterNode(Node $node, Environment $env): Node
    {
        if ($node instanceof ModuleNode) {
            $this->partials = [];
        }

        return $node;
    }

    public function leaveNode(Node $node, Environment $env): ?Node
    {
        if ($node instanceof PartialDefNode) {
            $this->partials[] = $node;
            return new EmptyNode();
        }

        if ($node instanceof ModuleNode) {
            if (!empty($this->partials)) {
                $partialsNode = new Nodes($this->partials);
                $node->setNode('class_end', new Nodes(array_merge(
                    iterator_to_array($node->getNode('class_end')),
                    [$partialsNode]
                )));
            }
        }

        return $node;
    }

    public function getPriority(): int
    {
        return 0;
    }
}
