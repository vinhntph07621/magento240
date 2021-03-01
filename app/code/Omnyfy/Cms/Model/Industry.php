<?php
/**
 * Project: CMS Industry M2.
 * User: abhay
 * Date: 01/05/17
 * Time: 2:30 PM
 */

namespace Omnyfy\Cms\Model;

use Omnyfy\Cms\Model\Url;

/**
 * Country model
 *
 * @method \Omnyfy\Cms\Model\ResourceModel\Country _getResource()
 * @method \Omnyfy\Cms\Model\ResourceModel\Country getResource()
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getIdentifier()
 * @method $this setIdentifier(string $value)
 */
class Industry extends \Magento\Framework\Model\AbstractModel {

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'omnyfy_cms_industry';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'cms_industry';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Omnyfy\Cms\Model\Url $url
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, Url $url, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
    ) {
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('Omnyfy\Cms\Model\ResourceModel\Industry');
    }

    /**
     * Check if country identifier exist for specific store
     * return country id if country exists
     *
     * @param string $identifier
     * @return int
     */
    public function checkIdentifier($identifier) {
        return $this->load($identifier)->getId();
    }

    /**
     * Retrieve catgegory url route path
     * @return string
     */
    public function getUrl() {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_INDUSTRY);
    }

    /**
     * Retrieve industry url
     * @return string
     */
    public function getIndustryUrl() {
        return $this->_url->getUrl($this, URL::CONTROLLER_INDUSTRY);
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false) {
        return $plural ? 'Industries' : 'Industry';
    }

    public function getTitle($plural = false) {
        return $this->getIndustryName();
    }

    /**
     * Retrieve all industry image url
     * @return string
     */
    public function getIndustryImage($field) {
        if ($file = $this->getData($field)) {
            $image = $this->_url->getMediaUrl($file);
        } else {
            $image = false;
        }
        return $image;
    }

}
