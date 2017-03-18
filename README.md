# CanonicalUrlBundle


The `CanonicalUrlBundle` is a Symfony bundle 

## Installation

Add the bundle to your project via composer:

```bash
composer require palmtree/canonical-url-bundle
```

Add the bundle to `app/AppKernel.php`:

```php
public function registerBundles()
{
    return array(
        new Palmtree\CanonicalUrlBundle\PalmtreeCanonicalUrlBundle(),
    );
}
```

Add your configuration for the bundle to `app/config/config.yml`:

```yaml
palmtree_canonical_url:
    site_url:      'https://example.org' # replace with your site URL
    redirect:      true
    redirect_code: 301
```

## Usage

To add a `<link rel="canonical">` tag to your pages include the following code in the `<head>` of a twig tempalte:

```twig
{% include '@PalmtreeCanonicalUrl/canonical_link_tag.html.twig' %}
```

## License

This bundle is released under the [MIT license](LICENSE)
