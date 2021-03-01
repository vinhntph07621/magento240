<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 5/09/2019
 * Time: 9:49 AM
 */

namespace Omnyfy\VendorDashBoard\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory
     */
    protected $_vendorCollectionFactory;

    /**
     * @var \Mirasvit\Dashboard\Model\ResourceModel\Board\CollectionFactory
     */
    protected $_dashboardCollectionFactory;

    /**
     * @var \Mirasvit\Dashboard\Model\Board
     */
    protected $_board;

    public function __construct(
        Context $context,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Mirasvit\Dashboard\Model\ResourceModel\Board\CollectionFactory $dashboardCollectionFactory,
        \Mirasvit\Dashboard\Model\Board $board
    )
    {
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_dashboardCollectionFactory = $dashboardCollectionFactory;
        $this->_board = $board;

        parent::__construct($context);
    }

    /**
     * Return all the vendors
     * @return \Omnyfy\Vendor\Model\Resource\Vendor\Collection
     */
    protected function getVendors(){
        /** @var \Omnyfy\Vendor\Model\Resource\Vendor\Collection $collection */
        $collection = $this->_vendorCollectionFactory->create();
        $collection->addFieldToFilter('status', ['eq' => 1]);
        $collection->getSelect()->join(
            ['au' => 'admin_user'],
            'au.email = e.email',
            ['user_id' => 'au.user_id']
        );
        return $collection;
    }

    /**
     * @return \Magento\Framework\DataObject
     * @throws \Exception
     */
    protected function getDefaultDashboard(){
        /** @var \Mirasvit\Dashboard\Model\ResourceModel\Board\Collection $collection */
        $collection = $this->_dashboardCollectionFactory->create();
        $collection->addFieldToFilter('title',['eq' => 'vendor_dashboard_default']);
        if ($collection->count() == 1)
            return $collection->getFirstItem();

        throw new \Exception('Default Vendor Dashboard is not created. Please create one before generate.');
    }

    /**
     * @param \Mirasvit\Dashboard\Model\Board $defaultBoard
     * @param $id
     * @param $name
     * @param $userId
     * @throws \Exception
     */
    protected function getVendorBoard($defaultBoard, $id, $name, $userId){
        try {
            $defaultBoard->setData('board_id', $this->isVendorBoard($userId));
            $defaultBoard->setData('title', 'Vendor Dashboard for ' . $name);
            $defaultBoard->setData('user_id', $userId);
            $defaultBoard->setData('is_default', 1);

            $json = $defaultBoard->getData('blocks_serialized');

            $defaultBoard->setData('blocks_serialized', $this->updateJson($json, $id));
            $defaultBoard->save();
        } catch (\Exception $exception){
            throw new \Exception('Error saving board for: '.$name);
        }
    }

    /**
     * @param $json
     * @param $userId
     */
    protected function updateJson($json, $userId){
        return str_replace("0000",$userId, $json);
    }

    /**
     * @param $userId
     * @return null
     */
    protected function isVendorBoard($userId){
        /** @var \Mirasvit\Dashboard\Model\ResourceModel\Board\Collection $collection */
        $collection = $this->_dashboardCollectionFactory->create();
        $collection->addFieldToFilter('user_id',['eq' => $userId]);
        if ($collection->count() > 0){
            return $collection->getFirstItem()->getId();
        }
        return null;
    }


    /**
     * @throws \Exception
     */
    public function generateDashBoards(){
        $this->_logger->debug(">> Start generating boards");
        $vendors = $this->getVendors();

        foreach ($vendors as $vendor){
            /** @var \Mirasvit\Dashboard\Model\Board $defaultBoard */
            $defaultBoard = $this->getDefaultDashboard();

            $name = $vendor->getData('name');
            $id = $vendor->getData('entity_id');
            $userId = $vendor->getData('user_id');
            $this->getVendorBoard($defaultBoard, $id, $name, $userId);
        }
    }
}