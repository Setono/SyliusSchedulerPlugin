<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractResourceFixture implements FixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ExampleFactoryInterface
     */
    private $exampleFactory;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param ObjectManager $objectManager
     * @param ExampleFactoryInterface $exampleFactory
     */
    public function __construct(ObjectManager $objectManager, ExampleFactoryInterface $exampleFactory)
    {
        $this->objectManager = $objectManager;
        $this->exampleFactory = $exampleFactory;

        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('random', 0)
                ->setAllowedTypes('random', 'int')
                ->setDefault('prototype', [])
                ->setAllowedTypes('prototype', 'array')
                ->setDefault('custom', [])
                ->setAllowedTypes('custom', 'array')
                ->setNormalizer('custom', function (Options $options, array $custom) {
                    if ($options['random'] <= 0) {
                        return $custom;
                    }

                    return array_merge($custom, array_fill(0, $options['random'], $options['prototype']));
                })
        ;
    }

    /**
     * @param array $options
     */
    final public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        $i = 0;
        foreach ($options['custom'] as $resourceOptions) {
            $resource = $this->exampleFactory->create($resourceOptions);

            $this->objectManager->persist($resource);

            ++$i;

            if (0 === ($i % 10)) {
                $this->objectManager->flush();
                $this->objectManager->clear();
            }
        }

        $this->objectManager->flush();
        $this->objectManager->clear();
    }

    /**
     * {@inheritdoc}
     */
    final public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        /** @var ArrayNodeDefinition $optionsNode */
        $optionsNode = $treeBuilder->root($this->getName());
        $optionsNode->children()->integerNode('random')->min(0)->defaultValue(0);
        $optionsNode->children()->variableNode('prototype');

        /** @var ArrayNodeDefinition $resourcesNode */
        $resourcesNode = $optionsNode->children()->arrayNode('custom');

        /** @var ArrayNodeDefinition $resourceNode */
        $resourceNode = $resourcesNode->requiresAtLeastOneElement()->arrayPrototype();
        $this->configureResourceNode($resourceNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $resourceNode
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        // empty
    }
}
