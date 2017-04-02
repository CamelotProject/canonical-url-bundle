<?php

namespace Palmtree\CanonicalUrlBundle\Twig\Extension;

use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;

class CanonicalLinkExtension extends \Twig_Extension
{
    /** @var CanonicalUrlGenerator */
    private $canonicalUrlGenerator;
    /** @var RequestStack */
    private $requestStack;

    /**
     * CanonicalLinkExtension constructor.
     * @param CanonicalUrlGenerator $canonicalUrlGenerator
     * @param RequestStack          $requestStack
     */
    public function __construct(CanonicalUrlGenerator $canonicalUrlGenerator, RequestStack $requestStack)
    {
        $this->canonicalUrlGenerator = $canonicalUrlGenerator;
        $this->requestStack          = $requestStack;
    }

    /**
     * @return \Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('palmtree_canonical_url', [$this, 'generateUrl']),
            new \Twig_SimpleFunction('palmtree_canonical_link_tag', [$this, 'renderLinkTag'], [
                'needs_environment' => true,
                'is_safe'           => ['html'],
            ]),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param string            $href
     *
     * @return string
     */
    public function renderLinkTag(\Twig_Environment $environment, $href = null)
    {
        $output = $environment->render('@PalmtreeCanonicalUrl/canonical_link_tag.html.twig', [
            'href' => $href,
        ]);

        return $output;
    }

    /**
     * @param string       $route
     * @param string|array $parameters
     *
     * @see CanonicalUrlGenerator::generate() For parameter descriptions.
     *
     * @return string
     */
    public function generateUrl($route = null, $parameters = [])
    {
        if (func_num_args() === 0) {
            $request = $this->requestStack->getCurrentRequest();

            $route      = $request->attributes->get('_route');
            $parameters = $request->attributes->get('_route_params');
        }

        return $this->canonicalUrlGenerator->generate($route, $parameters);
    }
}
