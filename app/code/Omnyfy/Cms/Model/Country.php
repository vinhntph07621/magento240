<?php

/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
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
class Country extends \Magento\Framework\Model\AbstractModel {

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'omnyfy_cms_country';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'cms_country';

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
        $this->_init('Omnyfy\Cms\Model\ResourceModel\Country');
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
        return $this->_url->getUrlPath($this, URL::CONTROLLER_COUNTRY);
    }

    /**
     * Retrieve country url
     * @return string
     */
    public function getCountryUrl() {
        return $this->_url->getUrl($this, URL::CONTROLLER_COUNTRY);
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false) {
        return $plural ? 'Countries' : 'Country';
    }

    public function getTitle($plural = false) {
        return $this->getCountryName();
    }

    /**
     * Retrieve all country image url
     * @return string
     */
    public function getCountryImage($field) {
        if ($file = $this->getData($field)) {
            $image = $this->_url->getMediaUrl($file);
        } else {
            $image = false;
        }
        return $image;
    }

}
