<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 25/08/2020
 * Time: 11:47 AM
 */

namespace Omnyfy\Vendor\Helper;


class Info  extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_locationRepository;

    /**
     *
     * @var \Omnyfy\Vendor\Api\VendorRepositoryInterface
     */
    protected $vendorRepository;

    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Omnyfy\Vendor\Api\LocationRepositoryInterface $locationRepository,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_locationRepository = $locationRepository;
        $this->vendorRepository = $vendorRepository;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }
    
    public function getLocationById($id){
        try{
            return $this->_locationRepository->getById($id);
        }catch (\Exception $exception){
            return null;
        }
    }

    public function getLocationInfo($id, $addBreak = false){
        if (empty($id))
            return '';

        $location = $this->getLocationById($id);

        if ($location) {
            $info = $location->getName();
            return $info;
        }

        return '';
    }

    public function getVendorInfo($vendorId)
    {
        $vendorData = $this->vendorRepository->getById(
            $vendorId,
            false,
            $this->_storeManager->getStore()->getId()
        );

        if (!empty($vendorData)) {
            return $vendorData;
        }
        return '';
    }
}