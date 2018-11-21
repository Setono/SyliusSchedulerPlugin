<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class JobType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('command', TextType::class, [
                'label' => 'setono_sylius_scheduler.form.job.command',
            ])
            ->add('args', CollectionType::class, [
                'label' => 'setono_sylius_scheduler.form.job.args.label',
                'entry_type' => TextType::class,
                'allow_add' => true,
                'by_reference' => false,
                'button_add_label' => 'setono_sylius_scheduler.form.job.args.add_arg',
                'required' => false,
            ])
            ->add('queue', TextType::class, [
                'label' => 'setono_sylius_scheduler.form.job.queue',
                'required' => false,
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'setono_sylius_scheduler.form.job.priority',
                'required' => false,
            ])
            ->add('executeAfter', DateTimeType::class, [
                'label' => 'setono_sylius_scheduler.form.job.execute_after',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'setono_sylius_scheduler_job';
    }
}
