<?php


namespace Palmtree\CanonicalUrlBundle\Twig\Extension;


use Palmtree\CanonicalUrlBundle\Service\CanonicalUrlService;

class CanonicalLinkExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    protected $canonicalUrlService;

    public function __construct(CanonicalUrlService $canonicalUrlService)
    {
        $this->canonicalUrlService = $canonicalUrlService;
    }

    public function getGlobals()
    {
        return [
            'palmtree_canonical_url' => $this->canonicalUrlService,
        ];
    }
}
