# Twig Partials

A Twig extension that adds Django-style partial template rendering. Define reusable template fragments and render them inline or via AJAX requests.

## Requirements

- PHP 8.3 or higher
- Twig 3.15 or higher

## Installation

```bash
composer require kevinquillen/twig-partials
```

## Usage

### Defining Partials

Use the `partialdef` tag to define a reusable fragment within a template:

```twig
{% partialdef view_count %}
  <span class="views">{{ video.views }} views</span>
{% endpartialdef %}
```

### Rendering Partials Inline

Use the `partial` tag to render a defined partial within the same template:

```twig
{% partialdef info %}
  <div class="video-info">
    <h2>{{ video.title }}</h2>
    <span>{{ video.views }} views</span>
  </div>
{% endpartialdef %}

<main>
  {% partial info %}
</main>
```

### Full Example

```twig
{# video.twig #}

{% partialdef view_count %}
  <span id="view-count">{{ video.views }} views</span>
{% endpartialdef %}

{% partialdef comments_section %}
  <div id="comments">
    {% for comment in video.comments %}
      <p>{{ comment.text }}</p>
    {% endfor %}
  </div>
{% endpartialdef %}

<article>
  <h1>{{ video.title }}</h1>
  {% partial view_count %}
  <video src="{{ video.url }}"></video>
  {% partial comments_section %}
</article>
```

### Rendering the Full Template

```php
$twig->render('video.twig', ['video' => $video]);
```

This renders the entire template including all partials.

### Rendering Only a Fragment (AJAX)

The extension supports rendering only a specific partial using fragment syntax:

```php
$twig->render('video.twig#view_count', ['video' => $video]);
```

This renders only the `view_count` partial, which is useful for AJAX updates.

## Setup

### Standard Twig

```php
use Twig\Environment;
use TwigPartials\Extension\PartialExtension;
use TwigPartials\Loader\PartialLoader;

$loader = new PartialLoader(['/path/to/templates']);
$twig = new Environment($loader);
$twig->addExtension(new PartialExtension());
```

### Symfony

Register the extension as a service:

```yaml
services:
  TwigPartials\Extension\PartialExtension:
    tags: ['twig.extension']
```

To enable fragment rendering, replace the default loader:

```yaml
services:
  TwigPartials\Loader\PartialLoader:
    decorates: twig.loader.native_filesystem
    arguments: ['@TwigPartials\Loader\PartialLoader.inner']
```

## How It Works

The `partialdef` tag compiles into a separate method on the template class. When you use `partial`, it calls that method and outputs the result. The content has access to the same context variables as the rest of the template.

When using the fragment syntax (`template.twig#fragment_name`), the loader recognizes the fragment identifier and the runtime can render only that specific partial instead of the full template.

## Running Tests

```bash
composer install
./vendor/bin/phpunit
```

## License

MIT
