<?php

declare(strict_types=1);

namespace TwigPartials\Tests;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Source;
use TwigPartials\Extension\PartialExtension;
use TwigPartials\Loader\PartialLoader;

class PartialIsolationTest extends TestCase
{
    private string $templateDir;

    protected function setUp(): void
    {
        $this->templateDir = sys_get_temp_dir() . '/twig_partials_test_' . uniqid();
        mkdir($this->templateDir, 0777, true);

        file_put_contents(
            $this->templateDir . '/video.twig',
            '{% partialdef view_count %}<span>{{ views }} views</span>{% endpartialdef %}' .
            '<h1>{{ title }}</h1>{% partial view_count %}'
        );
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->templateDir . '/*'));
        rmdir($this->templateDir);
    }

    public function testPartialLoaderRecognizesFragmentSyntax(): void
    {
        $loader = new PartialLoader([$this->templateDir]);

        $source = $loader->getSourceContext('video.twig#view_count');
        $fragment = $loader->getFragment('video.twig#view_count');

        $this->assertInstanceOf(Source::class, $source);
        $this->assertEquals('view_count', $fragment);
        $this->assertTrue($loader->hasFragment('video.twig#view_count'));
    }

    public function testPartialLoaderReturnsRegularSourceWithoutFragment(): void
    {
        $loader = new PartialLoader([$this->templateDir]);

        $source = $loader->getSourceContext('video.twig');

        $this->assertFalse($loader->hasFragment('video.twig'));
        $this->assertNull($loader->getFragment('video.twig'));
    }

    public function testParseTemplateName(): void
    {
        $loader = new PartialLoader([$this->templateDir]);

        [$file, $fragment] = $loader->parseTemplateName('video.twig#view_count');
        $this->assertEquals('video.twig', $file);
        $this->assertEquals('view_count', $fragment);

        [$file, $fragment] = $loader->parseTemplateName('video.twig');
        $this->assertEquals('video.twig', $file);
        $this->assertNull($fragment);
    }

    public function testFullTemplateRendering(): void
    {
        $loader = new PartialLoader([$this->templateDir]);
        $twig = new Environment($loader);
        $twig->addExtension(new PartialExtension());

        $output = $twig->render('video.twig', ['title' => 'My Video', 'views' => 1000]);

        $this->assertStringContainsString('<h1>My Video</h1>', $output);
        $this->assertStringContainsString('<span>1000 views</span>', $output);
    }
}
