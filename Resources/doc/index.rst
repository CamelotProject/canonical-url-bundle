## Installation

Add the bundle to your project via composer:

```bash
composer require palmtree/canonical-url-bundle
```

Add the bundle to `app/AppKernel.php`:

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
