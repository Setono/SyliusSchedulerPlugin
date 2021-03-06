<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Util\MenuManipulator;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    /**
     * @var MenuManipulator
     */
    private $menuManipulator;

    /**
     * @param MenuManipulator $menuManipulator
     */
    public function __construct(MenuManipulator $menuManipulator)
    {
        $this->menuManipulator = $menuManipulator;
    }

    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        /** @var ItemInterface $configurationSubmenu */
        $configurationSubmenu = $menu->getChild('configuration');

        $scheduleMenuItem = $configurationSubmenu
            ->addChild('setono_sylius_scheduler_schedules', [
                'route' => 'setono_sylius_scheduler_admin_schedule_index',
            ])
            ->setAttribute('type', 'link')
            ->setLabel('setono_sylius_scheduler.menu.admin.main.configuration.schedules')
            ->setLabelAttributes([
                'icon' => 'clone',
            ])
        ;

        $this->menuManipulator->moveToFirstPosition($scheduleMenuItem);
    }
}
