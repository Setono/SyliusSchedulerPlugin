<?php

/** @noinspection NullPointerExceptionInspection */

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\DependencyInjection;

use Setono\SyliusSchedulerPlugin\Doctrine\ORM\JobRepository;
use Setono\SyliusSchedulerPlugin\Doctrine\ORM\ScheduleRepository;
use Setono\SyliusSchedulerPlugin\Form\Type\ScheduleType;
use Setono\SyliusSchedulerPlugin\Model\Job;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Setono\SyliusSchedulerPlugin\Model\Schedule;
use Setono\SyliusSchedulerPlugin\Model\ScheduleInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Webmozart\Assert\Assert;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->root('setono_sylius_scheduler');

        $rootNodeChildren = $rootNode->children();
        $rootNodeChildren->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM);

        // Wipe logs in X days after command execution
        // Specify 0 to never wipe logs
        $rootNodeChildren->scalarNode('wipe_logs_in')->defaultValue(0);

        // We can specify emails to receive error reports on every Job
        // But here we can specify emails which receive error reports for all Jobs
        $rootNodeChildren->arrayNode('error_report_emails')
            ->treatNullLike([])
            ->beforeNormalization()->castToArray()->end()
            ->beforeNormalization()
                ->always(function (array $emails) {
                    foreach ($emails as $email) {
                        Assert::true((bool) filter_var($email, FILTER_VALIDATE_EMAIL), sprintf(
                            "You should provide valid email. '%s' not looks like valid email.",
                            $email
                        ));
                    }

                    return $emails;
                })
            ->end()
            ->scalarPrototype()
            ->cannotBeEmpty()
        ;

        $defaultOptionsNode = $rootNode
            ->children()
            ->arrayNode('queue_options_defaults')
                ->addDefaultsIfNotSet()
            ;
        $this->addQueueOptions($defaultOptionsNode);

        $queueOptionsNode = $rootNode
            ->children()
            ->arrayNode('queue_options')
                ->useAttributeAsKey('queue')
                ->prototype('array')
            ->end()
            ;
        $this->addQueueOptions($queueOptionsNode);

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()

                        ->arrayNode('schedule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Schedule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ScheduleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ScheduleRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(ScheduleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->arrayNode('job')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Job::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(JobInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(JobRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $nodeDefinition
     */
    private function addQueueOptions(ArrayNodeDefinition $nodeDefinition): void
    {
        $nodeDefinition
            ->children()
            ->scalarNode('max_concurrent_jobs')->end()
        ;
    }
}
