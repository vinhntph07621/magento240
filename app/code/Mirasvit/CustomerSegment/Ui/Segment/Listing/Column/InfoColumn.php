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



namespace Mirasvit\CustomerSegment\Ui\Segment\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\CustomerDataProviderInterface;
use Mirasvit\CustomerSegment\Api\Service\SegmentServiceInterface;

class InfoColumn extends AbstractColumn
{
    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @var SegmentServiceInterface
     */
    private $segmentService;

    /**
     * @var CustomerDataProviderInterface
     */
    private $customerDataProvider;

    /**
     * InfoColumn constructor.
     * @param SegmentRepositoryInterface $segmentRepository
     * @param SegmentServiceInterface $segmentService
     * @param CustomerDataProviderInterface $customerDataProvider
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        SegmentRepositoryInterface $segmentRepository,
        SegmentServiceInterface $segmentService,
        CustomerDataProviderInterface $customerDataProvider,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        $this->segmentRepository    = $segmentRepository;
        $this->segmentService       = $segmentService;
        $this->customerDataProvider = $customerDataProvider;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritdoc
     */
    protected function prepareItem(array $item)
    {
        $segment = $this->segmentRepository->get($item[SegmentInterface::ID]);

        $conditions = nl2br(preg_replace(
            '/ /',
            '&nbsp;',
            $segment->getRule()->getConditions()->asStringRecursive()
        ));

        $size      = $this->segmentService->getCustomersCount($item['segment_id']);
        $totalSize = $this->customerDataProvider->countUniqueCustomers();

        return [
            'conditions'  => $conditions,
            'title'       => $item['title'],
            'size'        => $size,
            'totalSize'   => $totalSize,
            'percentSize' => $totalSize > 0 ? round($size / $totalSize * 100) : 0,
        ];
    }
}
