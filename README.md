# CanonicalUrlBundle

[![License](http://img.shields.io/packagist/l/palmtree/canonical-url-bundle.svg)](LICENSE)
[![Travis](https://img.shields.io/travis/palmtreephp/canonical-url-bundle.svg)]()
[![Scrutinizer](https://img.shields.io/scrutinizer/g/palmtreephp/canonical-url-bundle.svg)]()
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/palmtreephp/canonical-url-bundle.svg)]()
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
perform an HTTP redirect to it if the requested URL does not match.

The bundle can also add a `<link rel="canonical">` tag to your twig templates, see the [Usage](#usage) section for how.

## Installation

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require palmtree/canonical-url-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Palmtree\CanonicalUrlBundle\PalmtreeCanonicalUrlBundle(),
        );

        // ...
    }

    // ...
}
```

### Step 3: Configure the Bundle

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
{{ palmtree_canonical_link_tag() }}
```

The href attribute will default to the canonical URL for the current request, but this can be overidden:

```twig
{{ palmtree_canonical_link_tag('http://example.org/my-custom-link') }}
```

## License

This bundle is released under the [MIT license](LICENSE)
