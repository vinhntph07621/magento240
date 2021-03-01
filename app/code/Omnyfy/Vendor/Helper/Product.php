<?php
namespace Omnyfy\Vendor\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Omnyfy\Vendor\Api\LocationRepositoryInterface;

class Product extends AbstractHelper
{
    /**
     * @var \Omnyfy\Vendor\Helper\Media
     */
    protected $_vendorMedia;
    /**
     * @var \Omnyfy\Vendor\Api\VendorRepositoryInterface
     */
    protected $_vendorRepository;
    /**
     * @var LocationRepositoryInterface
     */
    protected $_locationRepository;
    /**
     * @var \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface
     */
    protected $_typeRepository;
    /**
     * @var \Omnyfy\Vendor\Api\VendorProductRepositoryInterface
     */
    protected $_productRepository;
    public function __construct(
        Context $context,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\Vendor\Api\LocationRepositoryInterface $locationRepository,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $typeRepository,
        \Omnyfy\Vendor\Api\VendorProductRepositoryInterface $productRepository,
        \Omnyfy\Vendor\Helper\Media $media
    ){
        $this->_vendorRepository = $vendorRepository;
        $this->_locationRepository = $locationRepository;
        $this->_productRepository = $productRepository;
        $this->_typeRepository = $typeRepository;
        $this->_vendorMedia = $media;
        parent::__construct($context);
    }
    /**
     * Get the vendor Id
     *
     * @param \Omnyfy\Vendor\Api\VendorProductRepositoryInterface $productRepository
     * @return bool|string
     */
    public function getVendorId($productId){
        return $this->_productRepository->getByProduct($productId);
    }
    /**
     * Get the vendor logo
     *
     * @param \Omnyfy\Vendor\Model\Vendor $vendor
     * @return bool|string
     */
    public function getVendorLogo($vendor){
        return $this->_vendorMedia->getVendorLogoUrl($vendor);
    }
    /**
     * Get the vendor banner
     *
     * @param \Omnyfy\Vendor\Model\Vendor $vendor
     * @return bool|string
     */
    public function getVendorBanner($vendor){
        return $this->_vendorMedia->getVendorBannerUrl($vendor);
    }
    /**
     * Gent the VendorInterface by id
     *
     * @param $vendorId
     * @return null|\Omnyfy\Vendor\Api\Data\VendorInterface
     */
    public function getVendor($vendorId){
        try {
            return $this->_vendorRepository->getById($vendorId);
        } Catch(\Exception $exception){
            return null;
        }
    }
    /**
     * Get the LocationInterface by id
     *
     * @param $locationId
     * @return null|\Omnyfy\Vendor\Api\Data\LocationInterface
     */
    public function getLocation($locationId){
        try {
            return $this->_locationRepository->getById($locationId);
        } catch (\Exception $exception){
            return null;
        }
    }

    /**
     * Get the VendorTypeInterface by vendor type id
     *
     * @param $typeId
     * @return null|\Omnyfy\Vendor\Api\Data\VendorTypeInterface
     */
    public function getVendorType($typeId) {
        try {
            $vendorType = $this->_typeRepository->getById($typeId);
            return $vendorType;
        } catch(\Exception $exception){
            return null;
        }
    }
    /**
     * Check vendor template that is required to display
     *
     * @param $typeId
     * @return int|null|string
     * 0 - Display the Vendor details and link to the vendor page.
     * 1 - Display the Location details and link to the location page.
     */
    public function isVendorTemplate($typeId){
        if($type = $this->getVendorType($typeId))
            return $type->getSearchBy();
        return 0;
    }
}
