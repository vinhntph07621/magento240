<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 5/12/2019
 * Time: 4:03 PM
 */

namespace Omnyfy\VendorFeatured\Controller\Adminhtml\Locations;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Omnyfy\Vendor\Model\Location\CollectionFactory
     */
    protected $_locationCollectionFactory;

    /**
     * @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory
     */
    protected $_tagCollectionFactory;

    /**
     * @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\CollectionFactory
     */
    protected $_featuredCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    public function __construct(
        Action\Context $context,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory,
        \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\CollectionFactory $featuredCollectionFactory,
        \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory $tagCollectionFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_locationCollectionFactory = $locationCollectionFactory;
        $this->_featuredCollectionFactory = $featuredCollectionFactory;
        $this->_tagCollectionFactory = $tagCollectionFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $responseData = [
            'error' => true
        ];

        try {
            $vendorId = $this->_request->getParam('vendorId', null);
            if ($vendorId) {
                /** @var \Omnyfy\Vendor\Model\Resource\Location\Collection $locationCollection */
                $locationCollection = $this->_locationCollectionFactory->create();
                $locationCollection->addFieldToFilter('vendor_id',['eq' => $vendorId]);

                $options = [];

                foreach($locationCollection as $location){
                    $options[] = [
                        'label' => $location->getData('location_name'),
                        'value' => $location->getData('entity_id'),
                    ];
                }


                $vendorFeaturedId = $this->_request->getParam('vendorFeaturedId', null);

                if (!empty($vendorFeaturedId)) {
                    try {
                        /** @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\Collection $featuredCollection */
                        $featuredCollection = $this->_featuredCollectionFactory->create();
                        $featuredCollection->addFieldToSelect('vendor_featured_id', ['eq' => $vendorFeaturedId]);

                        if ($featuredCollection->count() == 0) {
                            $responseData['location'] = $featuredCollection->getFirstItem()->getData('location_id');
                        } else {
                            $responseData['location'] = 0;
                        }
                    } catch (\Exception $exception){
                        $responseData['location'] = 0;
                    }

                    /** @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\Collection $collection */
                    $collection = $this->_tagCollectionFactory->create();
                    $collection->addFieldToSelect('vendor_tag_id');
                    $collection->addFieldToFilter('vendor_featured_id', ['eq' => $vendorFeaturedId]);

                    foreach ($collection as $tag) {
                        $tagsIds[] = $tag->getData('vendor_tag_id');
                    }

                    $responseData['tags'] = $tagsIds;
                }

                $responseData['error'] = false;
                $responseData['options'] = $options;

            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
            $this->_logger->debug($e->getTraceAsString());
        }

        return $result->setData($responseData);
    }
}