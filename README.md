# Twig Partials

A Twig extension that adds Django-style partial template rendering. Define reusable template fragments and render them inline or via AJAX requests.

## Requirements

- PHP 8.3 or higher
- Twig 3.8 or higher

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

### Drupal 11

Drupal 11 uses Twig 3.x, which is fully compatible with this extension.

#### Step 1: Install the Package

Add the package to your Drupal project:

```bash
composer require kevinquillen/twig-partials
```

#### Step 2: Create a Custom Module

Create a custom module to register the extension. For example, create `modules/custom/twig_partials_integration`.

File: `twig_partials_integration.info.yml`

```yaml
name: Twig Partials Integration
type: module
description: Integrates the Twig Partials extension with Drupal.
core_version_requirement: ^11
package: Custom
```

File: `twig_partials_integration.services.yml`

```yaml
services:
  twig_partials_integration.extension:
    class: TwigPartials\Extension\PartialExtension
    tags:
      - { name: twig.extension }
```

#### Step 3: Enable the Module

```bash
drush en twig_partials_integration
```

#### Step 4: Use in Drupal Templates

You can now use partials in any Drupal Twig template:

```twig
{# node--article.html.twig #}

{% partialdef article_meta %}
  <div class="article-meta">
    <span class="author">{{ node.getOwner.getDisplayName }}</span>
    <span class="date">{{ node.getCreatedTime|date('F j, Y') }}</span>
  </div>
{% endpartialdef %}

{% partialdef share_buttons %}
  <div class="share-buttons">
    <a href="https://twitter.com/share?url={{ url }}">Twitter</a>
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url }}">Facebook</a>
  </div>
{% endpartialdef %}

<article{{ attributes }}>
  {{ title_prefix }}
  <h2{{ title_attributes }}>{{ label }}</h2>
  {{ title_suffix }}

  {% partial article_meta %}

  <div{{ content_attributes }}>
    {{ content }}
  </div>

  {% partial share_buttons %}
</article>
```

#### Fragment Rendering in Controllers

To render only a specific partial from a controller (useful for AJAX endpoints), you need to enable the custom loader.

Add to `twig_partials_integration.services.yml`:

```yaml
services:
  twig_partials_integration.extension:
    class: TwigPartials\Extension\PartialExtension
    tags:
      - { name: twig.extension }

  twig_partials_integration.loader:
    class: TwigPartials\Loader\PartialLoader
    decorates: twig.loader.filesystem
    arguments: ['@twig_partials_integration.loader.inner']
```

Then in a controller:

```php
namespace Drupal\my_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

class PartialController extends ControllerBase {

  public function renderPartial($node_id, $partial_name) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($node_id);

    $template = 'node--article.html.twig#' . $partial_name;
    $renderer = \Drupal::service('twig');

    $content = $renderer->render($template, [
      'node' => $node,
      'url' => $node->toUrl()->setAbsolute()->toString(),
    ]);

    return new Response($content);
  }

}
```

#### Use Case: Live Updates with AJAX

Partials are useful for updating parts of a page without a full reload:

```javascript
// Refresh only the article metadata
fetch('/partial/article/123/article_meta')
  .then(response => response.text())
  .then(html => {
    document.querySelector('.article-meta').outerHTML = html;
  });
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
