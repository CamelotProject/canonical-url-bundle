# CanonicalUrlBundle

[![License](http://img.shields.io/packagist/l/palmtree/canonical-url-bundle.svg)](LICENSE)
[![Latest Stable Version](https://img.shields.io/badge/release-beta-yellow.svg)](https://packagist.org/packages/palmtree/canonical-url-bundle)

The `CanonicalUrlBundle` is a Symfony bundle to redirect requests from multiple URLs for the same resource to a single canonical URL.

For example, if you had a resource named /about-us for your site example.org it could potentially be accessed with:

```
http://example.org/about-us
http://example.org/about-us/
http://www.example.org/about-us
http://www.example.org/about-us/
https://example.org/about-us
https://example.org/about-us/
https://www.example.org/about-us
https://www.example.org/about-us/
```

When a user requests the resource with any of the above URLs, `CanonicalUrlBundle` will build a canonical URL based on a predefined site URL and will
perform an HTTP redirect to it.

The bundle can also add a `<link rel="canonical">` tag to your twig templates, see the [Usage](#usage) section for how.

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
    site_url:       'https://example.org' # replace with your site URL (without trailing slash)
    redirect:       true # Set to false disable redirects if you just want to use the canonical link tag
    redirect_code:  301 # Leave this at 301 for SEO
    trailing_slash: false # Whether canonical URLs should contain a trailing slash
```

## Usage

To add a `<link rel="canonical">` tag to your pages include the following code in the `<head>` of a twig tempalte:

```twig
{% include '@PalmtreeCanonicalUrl/canonical_link_tag.html.twig' %}
```

The href attribute will default to the canonical URL for the current request, but this can be overidden:

```twig
{% include '@PalmtreeCanonicalUrl/canonical_link_tag.html.twig' with { href: 'http://example.org' } %}
```

## License

This bundle is released under the [MIT license](LICENSE)
