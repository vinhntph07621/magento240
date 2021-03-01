<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 5/12/2019
 * Time: 5:59 PM
 */

namespace Omnyfy\VendorFeatured\Controller\Adminhtml\Vendortag;


use Magento\Backend\App\Action;

class Get extends \Magento\Backend\App\Action
{
    /**
     * @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\CollectionFactory
     */
    protected $_featuredCollectionFactory;
    /**
     * @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory
     */
    protected $_tagCollectionFactory;

    public function __construct(
        Action\Context $context,
        \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory $tagCollectionFactory
    )
    {
        $this->_featuredCollectionFactory = $featuredCollectionFactory;
        $this->_tagCollectionFactory = $tagCollectionFactory;
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

        $vendorFeaturedId = $this->_request->getParam('vendorFeaturedId', null);


        try{
            if (!isEmpty($vendorFeaturedId)) {
                /** @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\Collection $collection */
                $collection = $this->_tagCollectionFactory->create();
                $collection->addFieldToFilter('vendor_featured_id',['eq' => $vendorFeaturedId]);

                $responseData['error'] = false;
                $responseData['options'] = $collection->toArray('vendor_tag_id');
            }
        } catch(\Exception $exception){
        }

        return $result->setData($responseData);
    }
}