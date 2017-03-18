# CanonicalUrlBundle


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

The bundle can also add a `<link rel="canonical">` tag to your twig templates, see the [Usage](#Usage) section for how.

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
    site_url:       'https://example.org' # replace with your site URL
    redirect:       true
    redirect_code:  301
    trailing_slash: false
```

## Usage

To add a `<link rel="canonical">` tag to your pages include the following code in the `<head>` of a twig tempalte:

```twig
{% include '@PalmtreeCanonicalUrl/canonical_link_tag.html.twig' %}
```

## License

This bundle is released under the [MIT license](LICENSE)
