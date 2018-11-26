<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Fixture;

use Setono\SyliusSchedulerPlugin\Model\Job;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Webmozart\Assert\Assert;

final class JobFixture extends AbstractResourceFixture
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'setono_scheduler_job';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('schedule')->cannotBeEmpty()->end()
                ->scalarNode('command')->cannotBeEmpty()->end()
                ->arrayNode('args')
                    ->requiresAtLeastOneElement()
                    ->treatNullLike([])
                    ->beforeNormalization()->castToArray()->end()
                    ->scalarPrototype()->cannotBeEmpty()->end()
                ->end()
                ->scalarNode('state')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(function (string $value) {
                            Assert::oneOf($value, Job::getStates(), sprintf(
                                'Invalid state provided: %s. Expected one of: %s',
                                $value,
                                implode(', ', Job::getStates())
                            ));

                            return $value;
                        })
                    ->end()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('queue')->cannotBeEmpty()->end()
                ->scalarNode('priority')->cannotBeEmpty()->end()
//                ->scalarNode('created_at')->cannotBeEmpty()->end()
//                ->scalarNode('started_at')->cannotBeEmpty()->end()
//                ->scalarNode('checked_at')->cannotBeEmpty()->end()
//                ->scalarNode('closed_at')->cannotBeEmpty()->end()
                ->scalarNode('execute_after')->cannotBeEmpty()->end()
//                ->arrayNode('dependencies')
//                    ->defaultValue([])
//                    ->requiresAtLeastOneElement()
//                    ->treatNullLike([])
//                    ->beforeNormalization()
//                        ->ifTrue(function ($values){
//                            return !empty($values);
//                        })
//                        ->then(function(){
//                            throw new NotImplementedException(sprintf(
//                                'Providing "dependencies" at %s fixture not yet implemented',
//                                $this->getName()
//                            ));
//                        })
//                    ->end()
//                    ->beforeNormalization()->castToArray()->end()
//                    ->scalarPrototype()->cannotBeEmpty()->end()
//                ->end()
                ->scalarNode('worker_name')->cannotBeEmpty()->end()
                ->scalarNode('output')->cannotBeEmpty()->end()
                ->scalarNode('error_output')->cannotBeEmpty()->end()
                ->scalarNode('exit_code')->cannotBeEmpty()->end()
                ->scalarNode('max_runtime')->cannotBeEmpty()->end()
                ->scalarNode('max_retries')->cannotBeEmpty()->end()
//                ->scalarNode('original_job')
//                    ->cannotBeEmpty()
//                    ->beforeNormalization()
//                        ->ifString()
//                        ->then(function(){
//                            throw new NotImplementedException(sprintf(
//                                'Providing "original_job" at %s fixture not yet implemented',
//                                $this->getName()
//                            ));
//                        })
//                    ->end()
//                ->end()
                ->scalarNode('retry_jobs')->cannotBeEmpty()->end()
            ->end()
        ;
    }
}
