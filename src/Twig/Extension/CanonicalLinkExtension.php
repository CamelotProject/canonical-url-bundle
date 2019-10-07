<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Twig\Extension;

use Camelot\CanonicalUrlBundle\Routing\Generator\CanonicalUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function func_num_args;

final class CanonicalLinkExtension extends AbstractExtension
{
    /** @var CanonicalUrlGenerator */
    private $canonicalUrlGenerator;
    /** @var RequestStack */
    private $requestStack;

    public function __construct(CanonicalUrlGenerator $canonicalUrlGenerator, RequestStack $requestStack)
    {
        $this->canonicalUrlGenerator = $canonicalUrlGenerator;
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('camelot_canonical_url', [$this, 'generateUrl']),
            new TwigFunction('camelot_canonical_link_tag', [$this, 'renderLinkTag'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function renderLinkTag(Environment $environment, string $href = null): string
    {
        return $environment->render('@CamelotCanonicalUrl/canonical_link_tag.html.twig', ['href' => $href]);
    }

    public function generateUrl(string $route = null, $parameters = []): string
    {
        if (func_num_args() === 0) {
            $request = $this->requestStack->getCurrentRequest();

            $route = $request->attributes->get('_route');
            $parameters = $request->attributes->get('_route_params');
        }

        return $this->canonicalUrlGenerator->generate($route, $parameters);
    }
}
