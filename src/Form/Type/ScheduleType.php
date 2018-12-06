<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ScheduleType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'label' => 'setono_sylius_scheduler.form.schedule.name',
                'required' => false,
            ])
            ->add('command', TextType::class, [
                'label' => 'setono_sylius_scheduler.form.schedule.command',
            ])
            ->add('args', CollectionType::class, [
                'label' => 'setono_sylius_scheduler.form.schedule.args.label',
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_label' => 'setono_sylius_scheduler.form.schedule.args.add_arg',
                'button_delete_label' => 'setono_sylius_scheduler.form.schedule.args.delete_arg',
                'required' => false,
            ])
            ->add('queue', TextType::class, [
                'label' => 'setono_sylius_scheduler.form.schedule.queue',
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'setono_sylius_scheduler.form.schedule.priority',
            ])
            ->add('cronExpression', TextType::class, [
                'label' => 'setono_sylius_scheduler.form.schedule.cron_expression',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_scheduler_schedule';
    }
}
