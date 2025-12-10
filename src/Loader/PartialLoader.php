<?php

declare(strict_types=1);

namespace TwigPartials\Loader;

use Twig\Loader\FilesystemLoader;
use Twig\Source;

class PartialLoader extends FilesystemLoader
{
    public function getSourceContext(string $name): Source
    {
        if (!str_contains($name, '#')) {
            return parent::getSourceContext($name);
        }

        [$file, $fragment] = explode('#', $name, 2);
        $source = parent::getSourceContext($file);

        return new \TwigPartials\PartialSource($source, $fragment);
    }
}
