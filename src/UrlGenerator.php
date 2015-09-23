<?php

namespace Gitiki;

use Symfony\Component\Routing\Generator\ConfigurableRequirementsInterface,
    Symfony\Component\Routing\Generator\UrlGeneratorInterface,
    Symfony\Component\Routing\RequestContext;

class UrlGenerator implements UrlGeneratorInterface, ConfigurableRequirementsInterface
{
    private $urlGenerator;

    /**
     * Constructor.
     *
     * @param PathResolver          $pathResolver The path resolver
     * @param UrlGeneratorInterface $urlGenerator The real UrlGenerator
     */
    public function __construct(PathResolver $pathResolver, UrlGeneratorInterface $urlGenerator)
    {
        $this->pathResolver = $pathResolver;
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
        return $this->urlGenerator->getContext();
    }

    /**
     * {@inheritdoc}
     */
    public function setStrictRequirements($enabled)
    {
        if ($this->urlGenerator instanceof ConfigurableRequirementsInterface) {
            $this->urlGenerator->setStrictRequirements($enabled);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isStrictRequirements()
    {
        return $this->urlGenerator instanceof ConfigurableRequirementsInterface ? $this->urlGenerator->isStrictRequirements() : true;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        if ('page' === $name && isset($parameters['path'])) {
            $parameters['path'] = $this->pathResolver->resolve($parameters['path']);

            if ('' === $parameters['path'] || '/' === substr($parameters['path'], -1)) {
                $name = 'page_dir';
            } elseif (!isset($parameters['_format'])) {
                $parameters['path'] = preg_replace('#\.md$#', '', $parameters['path'], 1);
                $parameters['_format'] = 'html';
            }
        } elseif ('image' === $name && isset($parameters['path'])) {
            $parameters['path'] = $this->pathResolver->resolve($parameters['path']);

            if (!isset($parameters['_format']) && preg_match('#(.*)\.(jpe?g|png|gif)$#', $parameters['path'], $match)) {
                $parameters['path'] = $match[1];
                $parameters['_format'] = $match[2];
            }
        }

        return $this->urlGenerator->generate($name, $parameters, $referenceType);
    }
}
