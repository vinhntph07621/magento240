<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 5/4/17
 * Time: 9:57 AM
 */

namespace Omnyfy\Vendor\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Omnyfy\Vendor\Api\Data\VendorInterface;

class Vendor extends AbstractExtensibleModel implements VendorInterface, IdentityInterface
{
    const CACHE_TAG = 'omnyfy_vendor_vendor';

    const ENTITY = 'omnyfy_vendor_vendor';

    protected $_eventPrefix = 'omnyfy_vendor';

    protected $_eventObject = 'vendor';

    protected $interfaceAttributes = [
        VendorInterface::NAME,
        VendorInterface::STATUS,
        VendorInterface::ADDRESS,
        VendorInterface::PHONE,
        VendorInterface::EMAIL,
        VendorInterface::LOGO,
        VendorInterface::BANNER,
        VendorInterface::TYPE_ID,
        VendorInterface::ATTRIBUTE_SET_ID
    ];

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Resource\Vendor');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        $this->setData(self::NAME, $name);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
    }

    public function getPolicyContents()
    {
        $labels = [
            'shipping_policy' => 'Shipping Policy',
            'return_policy' => 'Returns',
            'payment_policy' => 'Payment',
            'marketing_policy' => 'Marketing & Privacy',
        ];

        $result = [];
        foreach ($labels as $key => $label) {
            $result[$key] = [
                'label' => $label,
                'content' => $this->getData($key)
            ];
        }
        return $result;
    }

    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    public function setAddress($address)
    {
        $this->setData(self::ADDRESS, $address);
        return $this;
    }

    public function getPhone()
    {
        return $this->getData(self::PHONE);
    }

    public function setPhone($phone)
    {
        $this->setData(self::PHONE, $phone);
        return $this;
    }

    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    public function setEmail($email)
    {
        $this->setData(self::EMAIL, $email);
        return $this;
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        $this->setData(self::DESCRIPTION, $description);
        return $this;
    }

    public function getLogo()
    {
        return $this->getData(self::LOGO);
    }

    public function setLogo($logo)
    {
        $this->setData(self::LOGO, $logo);
        return $this;
    }

    public function getBanner()
    {
        return $this->getData(self::BANNER);
    }

    public function setBanner($banner)
    {
        $this->setData(self::BANNER, $banner);
        return $this;
    }

    public function getTypeId()
    {
        return $this->getData(self::TYPE_ID);
    }

    public function setTypeId($typeId)
    {
        return $this->setData(self::TYPE_ID, $typeId);
    }

    public function getAttributeSetId()
    {
        return $this->getData(self::ATTRIBUTE_SET_ID);
    }

    public function setAttributeSetId($attributeSetId)
    {
        return $this->setData(self::ATTRIBUTE_SET_ID, $attributeSetId);
    }

    public function getExtensionAttributes()
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            return $this->extensionAttributesFactory->create('Omnyfy\Vendor\Api\Data\VendorInterface');
        }
        return $extensionAttributes;
    }

    public function setExtensionAttributes($extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    public function getWebsiteIds()
    {
        if (!$this->hasWebsiteIds()) {
            $ids = $this->_getResource()->getWebsiteIds($this);
            $this->setWebsiteIds($ids);
        }
        return $this->getData('website_ids');
    }
}