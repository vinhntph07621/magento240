<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 19/6/17
 * Time: 10:32 AM
 */

namespace Omnyfy\Vendor\Api\Data;

interface VendorInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    const VENDOR_ADMIN_ROLE = 'Vendor Admin';

    const VENDOR_USER_ROLE = 'Vendor User';

    const NAME = 'name';

    const STATUS = 'status';

    const ADDRESS = 'address';

    const PHONE = 'phone';

    const EMAIL = 'email';

    //const FAX = 'fax';

    //const SOCIAL_MEDIA = 'social_media';

    const DESCRIPTION = 'description';

    //const ABN = 'abn';

    const LOGO = 'logo';

    const BANNER = 'banner';

    const TYPE_ID = 'type_id';

    const ATTRIBUTE_SET_ID = 'attribute_set_id';

    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    const BASE_LOGO_PATH = 'omnyfy/vendor/logo';

    const BASE_BANNER_PATH = 'omnyfy/vendor/banner';

    const BASE_MEDIA_PATH = 'omnyfy/vendor/media';

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
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

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
     * @return string|null
     */
    public function getAddress();

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress($address);

    /**
     * @return string|null
     */
    public function getPhone();

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone);

    /**
     * @return string|null
     */
    public function getEmail();

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

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
    public function getLogo();

    /**
     * @param string $logo
     * @return $this
     */
    public function setLogo($logo);

    /**
     * @return string|null
     */
    public function getBanner();

    /**
     * @param string $banner
     * @return $this
     */
    public function setBanner($banner);

    /**
     * @return int|null
     */
    public function getTypeId();

    /**
     * @param int $typeId
     * @return $this
     */
    public function setTypeId($typeId);

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
     * @return \Omnyfy\Vendor\Api\Data\VendorExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Omnyfy\Vendor\Api\Data\VendorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes($extensionAttributes);
}