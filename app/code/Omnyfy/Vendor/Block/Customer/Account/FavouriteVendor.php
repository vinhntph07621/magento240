<?php
/**
 * Project: favourite vendor.
 * User: seth
 * Date: 09/09/19
 * Time: 2:27 PM
 */
namespace Omnyfy\Vendor\Block\Customer\Account;

use Magento\Framework\View\Element\Template;

class FavouriteVendor extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Omnyfy\Vendor\Helper\Session 
     */
    protected $_sessionHelper;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\FavouriteVendor\CollectionFactory
     */
    protected $_favouriteCollectionFactory;

    /**
     * @var \Omnyfy\Vendor\Helper\Media 
     */
    protected $_vendorMedia;

    /**
     * @var \Omnyfy\Vendor\Api\VendorRepositoryInterface 
     */
    protected $_vendorRepository;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory
     */
    protected $_vendorCollectionFactory;

    /**
     * FavouriteVendor constructor.
     * @param Template\Context $context
     * @param \Omnyfy\Vendor\Helper\Session $sessionHelper
     * @param \Omnyfy\Vendor\Model\Resource\FavouriteVendor\CollectionFactory $favouriteCollectionFactory
     * @param \Omnyfy\Vendor\Helper\Media $vendorMedia
     * @param \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository
     * @param \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Omnyfy\Vendor\Helper\Session $sessionHelper,
        \Omnyfy\Vendor\Model\Resource\FavouriteVendor\CollectionFactory $favouriteCollectionFactory,
        \Omnyfy\Vendor\Helper\Media $vendorMedia,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        array $data = [])
    {
        $this->_sessionHelper = $sessionHelper;
        $this->_favouriteCollectionFactory = $favouriteCollectionFactory;
        $this->_vendorMedia = $vendorMedia;
        $this->_vendorRepository = $vendorRepository;
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get list of favourite vendors.
     * @return \Omnyfy\Vendor\Model\Resource\Vendor\Collection
     */
    public function getFavouriteVendors() {
        $customerId = $this->_sessionHelper->getCustomer()->getId();
        $favouriteCollection = $this->_favouriteCollectionFactory->create();
        $favouriteCollection->addFieldToFilter('customer_id', $customerId);
        $vendorIds = $favouriteCollection->getColumnValues('vendor_id');

        $vendorCollection = $this->_vendorCollectionFactory->create();
        $vendorCollection->addFieldToSelect('*');
        $vendorCollection->addFieldToFilter('entity_id', ['in' => $vendorIds]);
        
        return $vendorCollection;
    }

    /**
     * Get vendor link.
     * @param $vendorId
     * @return string
     */
    public function getVendorLink($vendorId){
        return $this->getUrl('shop/brands/view/id/' . $vendorId);
    }

    /**
     * @param $vendorId
     * @return bool|string
     */
    public function getImage($vendorId){
        try {
            $vendor = $this->_vendorRepository->getById($vendorId);
            if ($vendor) {
                return $this->_vendorMedia->getVendorLogoUrl($vendor);
            }
        }catch(\Exception $exception){
            $this->_logger->debug($exception->getMessage());
        }
        return "";
    }

    /**
     * @return mixed
     */
    public function getCustomerId() {
        return $this->_sessionHelper->getCustomer()->getId();
    }
}