<?php

declare(strict_types=1);

namespace TwigPartials\Tests;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use TwigPartials\Extension\PartialExtension;

class PartialRenderTest extends TestCase
{
    private Environment $twig;

    protected function setUp(): void
    {
        $loader = new ArrayLoader([
            'inline.twig' => '{% partialdef info %}<div>Info content</div>{% endpartialdef %}<main>{% partial info %}</main>',
            'with_context.twig' => '{% partialdef greeting %}<span>Hello, {{ name }}!</span>{% endpartialdef %}Page: {% partial greeting %}',
            'multiple_calls.twig' => '{% partialdef item %}<li>{{ value }}</li>{% endpartialdef %}<ul>{% partial item %}{% partial item %}</ul>',
        ]);
        $this->twig = new Environment($loader);
        $this->twig->addExtension(new PartialExtension());
    }

    public function testInlinePartialRendering(): void
    {
        $output = $this->twig->render('inline.twig');

        $this->assertStringContainsString('<main><div>Info content</div></main>', $output);
    }

    public function testPartialWithContextRendering(): void
    {
        $output = $this->twig->render('with_context.twig', ['name' => 'World']);

        $this->assertStringContainsString('<span>Hello, World!</span>', $output);
    }

    public function testMultiplePartialCalls(): void
    {
        $output = $this->twig->render('multiple_calls.twig', ['value' => 'Test']);

        $this->assertStringContainsString('<ul><li>Test</li><li>Test</li></ul>', $output);
    }
}
