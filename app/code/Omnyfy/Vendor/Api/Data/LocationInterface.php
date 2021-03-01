<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 4/7/17
 * Time: 2:17 PM
 */
namespace Omnyfy\Vendor\Api\Data;

interface LocationInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    const VENDOR_ID = 'vendor_id';

    const VENDOR_NAME = 'vendor_name';

    const LOCATION_NAME = 'location_name';

    const PRIORITY = 'priority';

    const LATITUDE = 'latitude';

    const LONGITUDE = 'longitude';

    const DESCRIPTION = 'description';

    const STATUS = 'status';

    const IS_WAREHOUSE = 'is_warehouse';

    const VENDOR_TYPE_ID = 'vendor_type_id';

    const ATTRIBUTE_SET_ID = 'attribute_set_id';

    const LOCATION_CONTACT_NAME = 'location_contact_name';

    const LOCATION_CONTACT_PHONE = 'location_contact_phone';

    const LOCATION_CONTACT_EMAIL = 'location_contact_email';

    const LOCATION_COMPANY_NAME = 'location_company_name';

    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    const BASE_LOGO_PATH = 'omnyfy/location/logo';

    const BASE_BANNER_PATH = 'omnyfy/location/banner';

    const BASE_MEDIA_PATH = 'omnyfy/location/media';

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int|null
     */
    public function getVendorId();

    /**
     * @param int $vendorId
     * @return $this
     */
    public function setVendorId($vendorId);

    /**
     * @return string|null
     */
    public function getLocationName();

    /**
     * @param string $name
     * @return $this
     */
    public function setLocationName($name);

    /**
     * @return int|null
     */
    public function getPriority();

    /**
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority);

    /**
     * @return float|null
     */
    public function getLatitude();

    /**
     * @param float $latitude
     * @return $this
     */
    public function setLatitude($latitude);

    /**
     * @return float|null
     */
    public function getLongitude();

    /**
     * @param float $longitude
     * @return $this
     */
    public function setLongitude($longitude);

    /**
     * @return string|null
     */
    public function getVendorName();

    /**
     * @param string $vendorName
     * @return $this
     */
    public function setVendorName($vendorName);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string|null
     */
    public function getLink();

    /**
     * @return int|null
     */
    public function getIsWarehouse();

    /**
     * @param int $isWarehouse
     * @return $this
     */
    public function setIsWarehouse($isWarehouse);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return int|null
     */
    public function getVendorTypeId();

    /**
     * @param int $vendorTypeId
     * @return $this
     */
    public function setVendorTypeId($vendorTypeId);

    /**
     * @return int|null
     */
    public function getAttributeSetId();

    /**
     * @param int $attributeSetId
     * @return $this
     */
    public function setAttributeSetId($attributeSetId);

    /**
     * @return string|null
     */
    public function getLocationContactName();

    /**
     * @param string $locationContactName
     * @return $this
     */
    public function setLocationContactName($locationContactName);

    /**
     * @return string|null
     */
    public function getLocationContactPhone();

    /**
     * @param string $locationContactPhone
     * @return $this
     */
    public function setLocationContactPhone($locationContactPhone);

    /**
     * @return string|null
     */
    public function getLocationContactEmail();

    /**
     * @param string $locationContactEmail
     * @return $this
     */
    public function setLocationContactEmail($locationContactEmail);

    /**
     * @return string|null
     */
    public function getLocationCompanyName();

    /**
     * @param string $locationCompanyName
     * @return $this
     */
    public function setLocationCompanyName($locationCompanyName);

    /**
     * @return \Omnyfy\Vendor\Api\Data\LocationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Omnyfy\Vendor\Api\Data\LocationExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes($extensionAttributes);
}
