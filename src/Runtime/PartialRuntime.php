<?php

declare(strict_types=1);

namespace TwigPartials\Runtime;

use Twig\Error\RuntimeError;

trait PartialRuntime
{
    private ?string $fragment = null;

    public function setFragment(?string $fragment): void
    {
        $this->fragment = $fragment;
    }

    public function render(array $context = [], array $blocks = []): string
    {
        if ($this->fragment) {
            $method = "renderPartial_" . $this->fragment;

            if (!method_exists($this, $method)) {
                throw new RuntimeError("Partial '{$this->fragment}' not defined in template '{$this->getTemplateName()}'");
            }

            return $this->$method($context, $blocks);
        }

        return parent::render($context, $blocks);
    }
}
