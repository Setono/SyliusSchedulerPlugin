<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
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

        return $treeBuilder;
    }
}
