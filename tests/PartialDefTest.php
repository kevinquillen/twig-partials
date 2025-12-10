<?php

declare(strict_types=1);

namespace TwigPartials\Tests;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use TwigPartials\Extension\PartialExtension;

class PartialDefTest extends TestCase
{
    private Environment $twig;

    protected function setUp(): void
    {
        $loader = new ArrayLoader([
            'simple.twig' => '{% partialdef info %}<div>Hello</div>{% endpartialdef %}',
            'with_vars.twig' => '{% partialdef greeting %}<span>{{ name }}</span>{% endpartialdef %}',
            'multiple.twig' => '{% partialdef header %}<h1>Header</h1>{% endpartialdef %}{% partialdef footer %}<footer>Footer</footer>{% endpartialdef %}',
        ]);
        $this->twig = new Environment($loader);
        $this->twig->addExtension(new PartialExtension());
    }

    public function testPartialDefCompilesToMethod(): void
    {
        $source = $this->twig->getLoader()->getSourceContext('simple.twig');
        $compiled = $this->twig->compileSource($source);

        $this->assertStringContainsString('renderPartial_info', $compiled);
    }

    public function testPartialDefWithVariablesCompiles(): void
    {
        $source = $this->twig->getLoader()->getSourceContext('with_vars.twig');
        $compiled = $this->twig->compileSource($source);

        $this->assertStringContainsString('renderPartial_greeting', $compiled);
    }

    public function testMultiplePartialDefsCompile(): void
    {
        $source = $this->twig->getLoader()->getSourceContext('multiple.twig');
        $compiled = $this->twig->compileSource($source);

        $this->assertStringContainsString('renderPartial_header', $compiled);
        $this->assertStringContainsString('renderPartial_footer', $compiled);
    }
}
