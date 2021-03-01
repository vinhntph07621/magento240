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

use Magento\Framework\App\State;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCommand extends Command
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * InfoCommand constructor.
     * @param SegmentRepositoryInterface $segmentRepository
     * @param State $state
     */
    public function __construct(
        SegmentRepositoryInterface $segmentRepository,
        State $state
    ) {
        parent::__construct();

        $this->state             = $state;
        $this->segmentRepository = $segmentRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:customer-segment:info')
            ->setDescription('List available customer segments')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);
        } catch (\Exception $e) {
        }

        foreach ($this->segmentRepository->getCollection() as $segment) {
            $output->writeln(sprintf('%d: %s', $segment->getId(), $segment->getTitle()));
        }
    }
}
