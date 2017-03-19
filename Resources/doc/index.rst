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
