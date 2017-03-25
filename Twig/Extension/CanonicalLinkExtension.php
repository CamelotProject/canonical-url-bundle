<?php

namespace Palmtree\CanonicalUrlBundle\Twig\Extension;

use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;

class CanonicalLinkExtension extends \Twig_Extension
{
    /** @var CanonicalUrlGenerator */
    protected $canonicalUrlGenerator;
    /** @var RequestStack */
    protected $requestStack;

    public function __construct(CanonicalUrlGenerator $canonicalUrlGenerator, RequestStack $requestStack)
    {
        $this->canonicalUrlGenerator = $canonicalUrlGenerator;
        $this->requestStack          = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('palmtree_canonical_url', [$this, 'generateUrl']),
            new \Twig_Function('palmtree_canonical_link_tag', [$this, 'renderLinkTag'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
        ];
    }

    public function renderLinkTag(\Twig_Environment $environment, $href = null)
    {
        $output = $environment->render('@PalmtreeCanonicalUrl/canonical_link_tag.html.twig', [
            'href' => $href,
        ]);

        return $output;
    }

    public function generateUrl($route = null, $parameters = null)
    {
        if (!$parameters || !$route) {
            $request = $this->requestStack->getCurrentRequest();

            if (!$route) {
                $route = $request->attributes->get('_route');
            }

            if (!$parameters) {
                $parameters = $request->getQueryString();
            }
        }

        return $this->canonicalUrlGenerator->generateUrl($route, $parameters);
    }
}
