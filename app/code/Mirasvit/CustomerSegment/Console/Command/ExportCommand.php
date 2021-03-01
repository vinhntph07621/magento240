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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Filesystem;
use Magento\Ui\Model\Export\ConvertToCsvFactory;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    const INPUT_SEGMENT = 'segment';
    const INPUT_FILE    = 'file';
    const IS_GUEST      = 'guest';

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var ConvertToCsvFactory
     */
    private $converterFactory;

    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * ExportCommand constructor.
     * @param ConvertToCsvFactory $converterFactory
     * @param SegmentRepositoryInterface $segmentRepository
     * @param RequestInterface $request
     * @param Filesystem $fs
     * @param AppState $appState
     */
    public function __construct(
        ConvertToCsvFactory $converterFactory,
        SegmentRepositoryInterface $segmentRepository,
        RequestInterface $request,
        Filesystem $fs,
        AppState $appState
    ) {
        parent::__construct();

        $this->converterFactory  = $converterFactory;
        $this->segmentRepository = $segmentRepository;
        $this->request           = $request;
        $this->fs                = $fs;
        $this->appState          = $appState;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:customer-segment:export')
            ->setDescription('Export customers')
            ->addArgument(
                self::INPUT_SEGMENT,
                InputArgument::REQUIRED,
                'Segment ID'
            )->addArgument(
                self::INPUT_FILE,
                InputArgument::REQUIRED,
                'File path'
            )->addOption(
                self::IS_GUEST
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

        $segmentId = $input->getArgument(self::INPUT_SEGMENT);
        $segment   = $this->segmentRepository->get($segmentId);

        $file = $input->getArgument(self::INPUT_FILE);

        if (!$segment) {
            $output->writeln("Wrong segment ID");

            return;
        }

        $ns = 'customersegment_customer_listing';

        if ($input->getOption(self::IS_GUEST)) {
            $ns = 'customersegment_guest_listing';
        }

        $this->request->setParams([
            'namespace' => $ns,
            'search'    => '',
            'selected'  => false,
        ]);

        $this->request->setQueryValue(SegmentInterface::ID, $segmentId);

        $data = $this->converterFactory->create()->getCsvFile();

        $dir      = $this->fs->getDirectoryWrite(DirectoryList::VAR_DIR);
        $filePath = $dir->getAbsolutePath($data['value']);

        copy($filePath, $file);

        $output->writeln("Customers were exported to file '$file'");
    }
}
