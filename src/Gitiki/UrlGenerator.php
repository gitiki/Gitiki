<?php

namespace Gitiki;

use Symfony\Component\Routing\Generator\ConfigurableRequirementsInterface,
    Symfony\Component\Routing\Generator\UrlGenerator as RealUrlGenerator,
    Symfony\Component\Routing\Generator\UrlGeneratorInterface,
    Symfony\Component\Routing\RequestContext;

class UrlGenerator implements UrlGeneratorInterface, ConfigurableRequirementsInterface
{
    private $specialPages = [
        '_index' => 'homepage',
    ];

    private $urlGenerator;

    /**
     * Constructor.
     *
     * @param RealUrlGenerator $urlGenerator The real UrlGenerator
     */
    public function __construct(RealUrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->urlGenerator->setContext($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $urlGenerator->getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function setStrictRequirements($enabled)
    {
        $this->urlGenerator->setStrictRequirements($enabled);
    }

    /**
     * {@inheritdoc}
     */
    public function isStrictRequirements()
    {
        return $this->urlGenerator->isStrictRequirements();
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        if ('page' === $name && '_' === $parameters['page']{0}) {
            $name = $this->specialPages[$parameters['page']];
            unset($parameters['page']);
        }

        return $this->urlGenerator->generate($name, $parameters, $referenceType);
    }
}
