<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-03
 * Time: 13:39
 */

namespace Omnyfy\Vendor\Api\Data;

interface VendorTypeInterface
{
    const TYPE_NAME = 'type_name';

    const SEARCH_BY ='search_by';

    const VIEW_MODE = 'view_mode';

    const STATUS = 'status';

    const VENDOR_ATTR_SET_ID = 'vendor_attribute_set_id';

    const LOCATION_ATTR_SET_ID = 'location_attribute_set_id';

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
    public function getTypeName();

    /**
     * @param string $name
     * @return $this
     */
    public function setTypeName($name);

    /**
     * @return string|null
     */
    public function getSearchBy();

    /**
     * @param string $searchBy
     * @return $this
     */
    public function setSearchBy($searchBy);

    /**
     * @return string|null
     */
    public function getViewMode();

    /**
     * @param string $viewMode
     * @return $this
     */
    public function setViewMode($viewMode);

    /**
     * @return string|null
     */
    public function getStatus();

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return int|null
     */
    public function getVendorAttributeSetId();

    /**
     * @param int $vendorAttributeSetId
     * @return $this
     */
    public function setVendorAttributeSetId($vendorAttributeSetId);

    /**
     * @return int|null
     */
    public function getLocationAttributeSetId();

    /**
     * @param int $locationAttributeSetId
     * @return $this
     */
    public function setLocationAttributeSetId($locationAttributeSetId);
}