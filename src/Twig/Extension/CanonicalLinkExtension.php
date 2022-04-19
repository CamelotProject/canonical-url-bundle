<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CanonicalLinkExtension extends AbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;
    private RequestStack $requestStack;

    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
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

    public function generateUrl(string $route = null, array $parameters = null): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $route ?? $request->attributes->get('_route');
        $parameters = $parameters ?? $request->attributes->get('_route_params', []);

        if (!$route) {
            return '';
        }

        return $this->urlGenerator->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
