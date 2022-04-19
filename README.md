# Canonical Url Bundle

The `CanonicalUrlBundle` is a Symfony bundle to redirect requests from multiple
URLs for the same resource to a single canonical URL.

For example, if you had a resource named `/about-us` for your site `example.org`
it could potentially be accessed with:

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

When a user requests the resource with any of the above URLs,
`CanonicalUrlBundle` will build a canonical URL based on a predefined site URL
and will perform an HTTP redirect to it if the request URL does not match.

The bundle can also add a `<link rel="canonical">` tag to your Twig templates,
see the [Usage](#usage) section for how.

## Installation

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require camelot/canonical-url-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require camelot/canonical-url-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter][composer] of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Camelot\CanonicalUrl\CamelotCanonicalUrl::class => ['all' => true],
];
```

### Step 3: Configure the Bundle

Add your configuration for the bundle to `config/packages/camelot_canonical_url.yml`:

```yaml
camelot_canonical_url:
    site_url:       'https://example.org' # Replace with your full site URL (without trailing slash)
    redirect:       true                  # Set to false disable redirects if you just want to use the canonical link tag
    redirect_code:  301                   # Leave this at 301 for SEO
    trailing_slash: false                 # Set to true if your routes and canonical URLs contain a trailing slash
```

Set the Symfony Router default URI in `config/packages/routing.yaml` 

```yaml
framework:
    router:
        # ...
        default_uri: 'https://example.org'
```

## Usage

To add a `<link rel="canonical">` tag to your pages include the following code in the `<head>` of a twig template:

```twig
{{ camelot_canonical_link_tag() }}
```

The href attribute will default to the canonical URL for the current request, but this can be overridden:

```twig
{{ camelot_canonical_link_tag('https://example.org/my-custom-link') }}
```

## License

This bundle is released under the [MIT license](LICENSE)
