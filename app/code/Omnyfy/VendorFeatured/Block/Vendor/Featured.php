<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 7/10/2019
 * Time: 5:09 PM
 */

namespace Omnyfy\VendorFeatured\Block\Vendor;

use Magento\Framework\View\Element\Template;
use Omnyfy\Vendor\Api\LocationRepositoryInterface;

class Featured extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\CollectionFactory
     */
    protected $_featuredCollectionFactory;

    /**
     * @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory
     */
    protected $_vendorTagCollectionFactory;

    /**
     * @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\CollectionFactory
     */
    protected $_tagCollectionFactory;

    /**
     * @var \Omnyfy\Vendor\Helper\Media
     */
    protected $_vendorMedia;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\CollectionFactory
     */
    protected $_vendorCollectionFactory;

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
     * @var \Omnyfy\VendorReview\Helper\Vendor
     */
    protected $_vendorReview;

    /**
     * Featured constructor.
     * @param Template\Context $context
     * @param \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\CollectionFactory $featuredCollectionFactory
     * @param \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory $vendorTagCollectionFactory
     * @param \Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\CollectionFactory $tagCollectionFactory
     * @param \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory
     * @param \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository
     * @param LocationRepositoryInterface $locationRepository
     * @param \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $typeRepository
     * @param \Omnyfy\Vendor\Helper\Media $media
     * @param \Omnyfy\VendorReview\Helper\Vendor $vendorReview
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\CollectionFactory $featuredCollectionFactory ,
        \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag\CollectionFactory $vendorTagCollectionFactory,
        \Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\CollectionFactory $tagCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\Vendor\Api\LocationRepositoryInterface $locationRepository,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $typeRepository,
        \Omnyfy\Vendor\Helper\Media $media,
        \Omnyfy\VendorReview\Helper\Vendor $vendorReview,
        array $data = [])
    {
        $this->_featuredCollectionFactory = $featuredCollectionFactory;
        $this->_vendorTagCollectionFactory = $vendorTagCollectionFactory;
        $this->_tagCollectionFactory = $tagCollectionFactory;
        $this->_vendorMedia = $media;
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_vendorRepository = $vendorRepository;
        $this->_locationRepository = $locationRepository;
        $this->_typeRepository = $typeRepository;
        $this->_vendorReview = $vendorReview;

        parent::__construct($context, $data);
    }


    /**
     * Return all the Tags
     *
     * @return \Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\Collection
     */
    public function getTags(){
        /** @var \Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag\Collection $tagCollection */
        $tagCollection = $this->_tagCollectionFactory->create();
        return $tagCollection;
    }

    /**
     * Return all the featured vendors and location
     *
     * @param null $tagId
     * @return \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\Collection
     */
    public function getFeaturedVendors($tagId = null){
        /** @var  \Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeatured\Collection $vendorCollection */
        $vendorCollection = $this->_featuredCollectionFactory->create();

        /** Only if tagId is provided otherwise return all the featured vendors. */
        if ($tagId) {
            $vendorCollection->joinTags();
            $vendorCollection->addFieldToFilter('tag.vendor_tag_id', ['eq' => $tagId]);
        }

        return $vendorCollection;
    }

    public function getVendorMedia($vendor){
        return $this->_vendorMedia->getVendorLogoUrl($vendor);
    }

    public function getVendor($vendorId){
        try {
            return $this->_vendorRepository->getById($vendorId);
        } Catch(\Exception $exception){
            return null;
        }
    }

    public function getLocation($locationId){
        try {
            return $this->_locationRepository->getById($locationId);
        } catch (\Exception $exception){
            return null;
        }
    }

    public function getStarSummery($vendor){
        return $this->_vendorReview->getStarSummary($vendor);
    }

    public function getReviewSummaryCount($vendor){
        return $this->_vendorReview->getReviewSummaryCount($vendor);
    }

    public function getVendorType($typeId) {
        try {
            $vendorType = $this->_typeRepository->getById($typeId);
            return $vendorType;
        } catch(\Exception $exception){
            return null;
        }
    }

    /**
     * @param $typeId
     * @return int|null|string
     * 0 - Vendor
     * 1 - Location
     */
    public function isVendorTemplate($typeId){
        if($type = $this->getVendorType($typeId))
            return $type->getSearchBy();
        return 0;
    }
}