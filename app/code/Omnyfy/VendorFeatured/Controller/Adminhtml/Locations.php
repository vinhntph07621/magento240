<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 5/12/2019
 * Time: 4:03 PM
 */

namespace Omnyfy\VendorFeatured\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class Locations  extends \Magento\Backend\App\Action
{
    /**
     * @var \Omnyfy\Vendor\Model\Location\CollectionFactory
     */
    protected $_locationCollectionFactory;

    public function __construct(
        Action\Context $context,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory
    )
    {
        $this->_locationCollectionFactory = $locationCollectionFactory;
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

                $responseData['error'] = false;
                $responseData['options'] = $options;
            }
        } catch (\Exception $e) {
        }

        return $result->setData($responseData);
    }
}