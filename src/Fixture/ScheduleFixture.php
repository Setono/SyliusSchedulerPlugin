<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Fixture;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class ScheduleFixture extends AbstractResourceFixture
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'setono_scheduler_schedule';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('name')->end()
                ->scalarNode('command')->cannotBeEmpty()->end()
                ->arrayNode('args')
                    ->requiresAtLeastOneElement()
                    ->treatNullLike([])
                    ->beforeNormalization()->castToArray()->end()
                    ->scalarPrototype()->cannotBeEmpty()->end()
                ->end()
                ->scalarNode('queue')->cannotBeEmpty()->end()
                ->scalarNode('priority')->cannotBeEmpty()->end()
                ->scalarNode('created_at')->cannotBeEmpty()->end()
                ->scalarNode('cron_expression')->cannotBeEmpty()->end()
            ->end()
        ;
    }
}
