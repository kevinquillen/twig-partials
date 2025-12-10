<?php

declare(strict_types=1);

namespace TwigPartials\Loader;

use Twig\Loader\FilesystemLoader;
use Twig\Source;
use TwigPartials\PartialSource;

class PartialLoader extends FilesystemLoader
{
    private array $fragmentRegistry = [];

    public function getSourceContext(string $name): Source
    {
        if (!str_contains($name, '#')) {
            return parent::getSourceContext($name);
        }

        [$file, $fragment] = explode('#', $name, 2);
        $source = parent::getSourceContext($file);

        $this->fragmentRegistry[$name] = $fragment;

        return new Source($source->getCode(), $name, $source->getPath());
    }

    public function getFragment(string $name): ?string
    {
        return $this->fragmentRegistry[$name] ?? null;
    }

    public function hasFragment(string $name): bool
    {
        return isset($this->fragmentRegistry[$name]);
    }

    public function parseTemplateName(string $name): array
    {
        if (!str_contains($name, '#')) {
            return [$name, null];
        }

        return explode('#', $name, 2);
    }
}
