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



namespace Mirasvit\CustomerSegment\Service\Segment;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout as ResultLayout;
use Magento\Ui\Model\Export\ConvertToCsvFactory;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;

class Exporter
{
    /**
     * @var ResultLayout
     */
    private $resultLayout;

    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var Filesystem
     */
    private $fs;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var ConvertToCsvFactory
     */
    private $converterFactory;

    /**
     * Exporter constructor.
     * @param ConvertToCsvFactory $converterFactory
     * @param RequestInterface $request
     * @param Filesystem $fs
     */
    public function __construct(
        ConvertToCsvFactory $converterFactory,
        RequestInterface $request,
        Filesystem $fs
    ) {
        $this->converterFactory = $converterFactory;
        $this->request          = $request;
        $this->fs               = $fs;
    }

    /**
     * @param int $segmentId
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function export($segmentId)
    {
        $nsList = [
            'customersegment_customer_listing' => 'customer',
            'customersegment_guest_listing'    => 'guest',
        ];

        foreach ($nsList as $ns => $prefix) {
            $this->request->setParams([
                'namespace' => $ns,
                'search'    => '',
                'selected'  => false,
            ]);

            $this->request->setQueryValue(SegmentInterface::ID, $segmentId);

            $data = $this->converterFactory->create()->getCsvFile();

            $dir      = $this->fs->getDirectoryWrite(DirectoryList::VAR_DIR);
            $filePath = $dir->getAbsolutePath($data['value']);

            $newPath = $this->fs->getDirectoryWrite(DirectoryList::VAR_DIR)
                ->getAbsolutePath('export');

            set_error_handler(function () {
            }, E_WARNING);
            copy($filePath, $newPath . '/segment_' . $segmentId . '_' . $prefix . '.csv');
            unlink($filePath);
            restore_error_handler();
        }
    }
}
