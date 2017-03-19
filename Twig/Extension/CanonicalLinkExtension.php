<?php

namespace Palmtree\CanonicalUrlBundle\Twig\Extension;

use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlService;
use Symfony\Component\HttpFoundation\RequestStack;

class CanonicalLinkExtension extends \Twig_Extension
{
    protected $canonicalUrlService;
    protected $requestStack;

    public function __construct(CanonicalUrlService $canonicalUrlService, RequestStack $requestStack)
    {
        $this->canonicalUrlService = $canonicalUrlService;
        $this->requestStack        = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('palmtree_canonical_url', [$this, 'generateUrl']),
            new \Twig_Function('palmtree_canonical_link_tag', [$this, 'renderLinkTag'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
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
        if (! $parameters || ! $route) {
            $request = $this->requestStack->getCurrentRequest();

            if (! $route) {
                $route = $request->attributes->get('_route');
            }

            if (! $parameters) {
                $parameters = $request->getQueryString();
            }
        }

        return $this->canonicalUrlService->generateUrl($route, $parameters);
    }
}
