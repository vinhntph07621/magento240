<?php

namespace Omnyfy\Postcode\Api\Data;

interface PostcodeInterface
{

    const COUNTRY_ID    = 'country_id';
    const REGION_CODE   = 'region_code';
    const POSTCODE      = 'postcode';
    const SUBURB        = 'suburb';
    const LATITUDE      = 'latitude';
    const LONGITUDE     = 'longitude';
    const TIMEZONE      = 'timezone_override';

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     */
    public function setId($id);

    /**
     * Get country id
     *
     * @return string|null
     */
    public function getCountryId();

    /**
     * Set country id
     *
     * @param string $countryId
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     */
    public function setCountryId($countryId);

    /**
     * Get region code
     *
     * @return string|null
     */
    public function getRegionCode();

    /**
     * Set region code
     *
     * @param string $region
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     */
    public function setRegionCode($region);

    /**
     * Get postcode
     *
     * @return string|null
     */
    public function getPostcode();

    /**
     * Set postcode
     *
     * @param string $postcode
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     */
    public function setPostcode($postcode);

    /**
     * Get suburb
     *
     * @return string|null
     */
    public function getSuburb();

    /**
     * Set suburb
     *
     * @param string $suburb
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     */
    public function setSuburb($suburb);

    /**
     * Get latitude
     *
     * @return float|null
     */
    public function getLatitude();

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     */
    public function setLatitude($latitude);

    /**
     * Get longitude
     *
     * @return float|null
     */
    public function getLongitude();

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     */
    public function setLongitude($longitude);

    /**
     * Get timezone
     *
     * @return string|null
     */
    public function getTimezone();

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     */
    public function setTimezone($timezone);

}
