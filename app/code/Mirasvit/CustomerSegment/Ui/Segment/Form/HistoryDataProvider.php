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



namespace Mirasvit\CustomerSegment\Ui\Segment\Form;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\Segment\HistoryRepositoryInterface;

class HistoryDataProvider extends AbstractDataProvider
{
    /**
     * @var HistoryRepositoryInterface
     */
    private $repository;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * HistoryDataProvider constructor.
     * @param HistoryRepositoryInterface $historyRepository
     * @param ContextInterface $context
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        HistoryRepositoryInterface $historyRepository,
        ContextInterface $context,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->repository = $historyRepository;
        $this->collection = $historyRepository->getCollection();
        $this->context    = $context;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $arrItems = [];

        $segmentId = $this->context->getRequestParam(SegmentInterface::ID);

        $this->collection->addFieldToFilter(HistoryInterface::SEGMENT_ID, $segmentId);

        $arrItems['items'] = [];
        foreach ($this->collection as $item) {
            $itemData            = $item->getData();
            $arrItems['items'][] = $itemData;
        }

        $arrItems['totalRecords'] = $this->collection->getSize();

        return $arrItems;
    }
}
