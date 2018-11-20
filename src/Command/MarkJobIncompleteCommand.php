<?php

/** @noinspection ReturnTypeCanBeDeclaredInspection */

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Command;

use Setono\SyliusSchedulerPlugin\Doctrine\ORM\JobRepository;
use Setono\SyliusSchedulerPlugin\JobManager\JobManager;
use Setono\SyliusSchedulerPlugin\Model\JobInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MarkJobIncompleteCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'setono:scheduler:mark-incomplete';

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var JobRepository
     */
    private $jobRepository;

    /**
     * @param JobManager $jobManager
     * @param JobRepository $jobRepository
     */
    public function __construct(
        JobManager $jobManager,
        JobRepository $jobRepository
    ) {
        parent::__construct();

        $this->jobManager = $jobManager;
        $this->jobRepository = $jobRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Internal command (do not use). It marks jobs as incomplete.')
            ->addArgument('job-id', InputArgument::REQUIRED, 'The ID of the Job.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var JobInterface $job */
        $job = $this->jobRepository->find(
            $input->getArgument('job-id')
        );

        if (!$job instanceof JobInterface) {
            $output->writeln('<error>Job was not found.</error>');

            return 1;
        }

        try {
            $this->jobManager->closeJob($job, JobInterface::STATE_INCOMPLETE);
        } catch (\Exception $e) {
            $output->writeln(sprintf(
                '<error>Failed to close job: %s</error>',
                $e->getMessage()
            ));

            return 1;
        }

        return 0;
    }
}
