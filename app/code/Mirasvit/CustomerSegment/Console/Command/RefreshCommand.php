<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Shell;
use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Service\Segment\AjaxRuleServiceFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommand extends Command
{
    const INPUT_SEGMENT = 'segment';
    const INPUT_STATE   = 'state';

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @var AjaxRuleServiceFactory
     */
    private $ruleServiceFactory;

    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var string
     */
    private $binPath;

    /**
     * RefreshCommand constructor.
     * @param Shell $shell
     * @param AjaxRuleServiceFactory $ruleServiceFactory
     * @param SegmentRepositoryInterface $segmentRepository
     * @param AppState $appState
     */
    public function __construct(
        Shell $shell,
        AjaxRuleServiceFactory $ruleServiceFactory,
        SegmentRepositoryInterface $segmentRepository,
        AppState $appState
    ) {
        parent::__construct();

        $this->appState           = $appState;
        $this->segmentRepository  = $segmentRepository;
        $this->ruleServiceFactory = $ruleServiceFactory;
        $this->shell              = $shell;

        $this->binPath = PHP_BINARY . ' ' . BP . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'magento ';
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:customer-segment:refresh')
            ->setDescription('Refresh customer segment')
            ->addArgument(
                self::INPUT_SEGMENT,
                InputArgument::OPTIONAL,
                'Segment ID'
            )->addArgument(
                self::INPUT_STATE,
                InputArgument::OPTIONAL
            );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode(Area::AREA_CRONTAB);
        } catch (\Exception $e) {
        }

        if ($input->getArgument(self::INPUT_SEGMENT)) {
            $segmentId = $input->getArgument(self::INPUT_SEGMENT);
            $segment   = $this->segmentRepository->get($segmentId);

            if (!$segment) {
                $output->writeln("Wrong segment ID");

                return;
            }

            $state = $this->segmentRepository->getState($segment);

            // new process or sub-run
            if (!$input->getArgument(self::INPUT_STATE)) {
                $output->writeln("Refreshing segment {$segment->getTitle()}...");

                $state->setStatus(StateInterface::STATUS_NEW)
                    ->setSize(0)
                    ->setLimit(1000);

                $ruleService = $this->ruleServiceFactory->create();
                $ruleService->apply($segment);

                $progressBar = $this->createProgressBar($output, $state->getStepTotalSize());

                while (!$state->isFinished()) {
                    $stateJson = \Zend_Json::encode($state->getData());

                    if (1) {
                        $cmd    = "{$this->binPath} mirasvit:customer-segment:refresh {$segmentId} '{$stateJson}'";
                        $result = $this->shell->execute($cmd);
                        $state->setData(\Zend_Json::decode($result));
                    } else {
                        $ruleService = $this->ruleServiceFactory->create();
                        $ruleService->apply($segment);
                    }

                    $progressBar->advance($state->getIndex());
                }

                $progressBar->finish();

                $total = $state->getTotalSize() ? : 0;
                $output->writeln(PHP_EOL . "<info>Done, {$total} customers match this segment</info>");
            } else {
                $state->setData(
                    \Zend_Json::decode($input->getArgument(self::INPUT_STATE))
                );

                $start = microtime(true);

                $ruleService = $this->ruleServiceFactory->create();
                $ruleService->apply($segment);

                $state->setData('time', round(microtime(true) - $start, 4));
                $state->setData('success', true);
                $state->setData(StateInterface::PROGRESS, $state->getProgress());

                $output->write(\Zend_Json::encode($state->getData()));
            }
        } else {
            foreach ($this->segmentRepository->getCollection() as $segment) {
                $cmd    = "{$this->binPath} mirasvit:customer-segment:refresh {$segment->getId()}";
                $result = $this->shell->execute($cmd);
                $output->writeln($result);
            }
        }
    }

    /**
     * @param OutputInterface $output
     * @param int             $max
     *
     * @return ProgressBar
     */
    private function createProgressBar(OutputInterface $output, $max = 100)
    {
        if ($max === null) {
            $max = 100;
        }

        ProgressBar::setFormatDefinition(
            'standard',
            ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s% %memory:6s%'
        );

        $progressBar = new ProgressBar($output, $max);

        $progressBar->setFormat('standard');

        return $progressBar;
    }
}
