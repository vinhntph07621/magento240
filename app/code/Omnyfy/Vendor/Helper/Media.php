<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-04
 * Time: 14:58
 */
namespace Omnyfy\Vendor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Omnyfy\Booking\Api\Data\ProviderInterface;

class Media extends AbstractHelper
{
    /**
     * Support media uploader value with doesn't include path prefix
     *
     * @param $vendor
     * @param $type
     * @return bool|string
     */
    public function getVendorMediaUrl($vendor, $type)
    {
        $value = $this->getVendorMediaPath($vendor, $type);

        if (empty($value)) {
            return false;
        }

        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $value;
    }

    public function getLocationMediaUrl($location, $type)
    {
        $value = $this->getLocationMediaPath($location, $type);

        if (empty($value)) {
            return false;
        }

        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $value;
    }

    public function getVendorMediaPath($vendor, $type)
    {
        if (empty($vendor) || empty($vendor->getId())) {
            return false;
        }

        switch($type) {
            case 'banner':
                $value = $vendor->getBanner();
                if (substr($value, 0, 20) !== \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_BANNER_PATH) {
                    $value = \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_BANNER_PATH . '/' . $value;
                }
                break;
            case 'logo':
                $value = $vendor->getLogo();
                if (substr($value, 0, 18) !== \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_LOGO_PATH) {
                    $value = \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_LOGO_PATH . '/' . $value;
                }
                break;

            default:
                $value = $vendor->getData($type);
        }

        if (empty($value)) {
            return false;
        }

        switch($type) {
            case 'banner':
                if (substr($value, 0, 20) !== \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_BANNER_PATH) {
                    $value = \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_BANNER_PATH . '/' . $value;
                }
                break;
            case 'logo':
                if (substr($value, 0, 18) !== \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_LOGO_PATH) {
                    $value = \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_LOGO_PATH . '/' . $value;
                }
                break;
            default:
                if (substr($value, 0, 19) !== \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_MEDIA_PATH) {
                    $value = \Omnyfy\Vendor\Api\Data\VendorInterface::BASE_MEDIA_PATH . '/' . $value;
                }
                break;
        }

        $value = str_replace('//', '/', $value);
        $value = ltrim($value, '/');

        return $value;
    }

    public function getLocationMediaPath($location, $code)
    {
        if (empty($location) || empty($location->getId())) {
            return false;
        }

        $value = $location->getData($code);

        if (empty($value)) {
            return false;
        }

        if (substr($value, 0, 21) !== \Omnyfy\Vendor\Api\Data\LocationInterface::BASE_MEDIA_PATH) {
            $value = \Omnyfy\Vendor\Api\Data\LocationInterface::BASE_MEDIA_PATH . '/' . $value;
        }

        $value = str_replace('//', '/', $value);
        $value = ltrim($value, '/');

        return $value;
    }

    public function getVendorLogoUrl($vendor)
    {
        return $this->getVendorMediaUrl($vendor, 'logo');
    }

    public function getVendorBannerUrl($vendor)
    {
        return $this->getVendorMediaUrl($vendor, 'banner');
    }

    public function getVendorLogoPath($vendor)
    {
        return $this->getVendorMediaPath($vendor, 'logo');
    }

    public function getVendorBannerPath($vendor)
    {
        return $this->getVendorMediaPath($vendor, 'banner');
    }

    /**
     * @param $provider
     * @return string
     */
    public function getProviderImage($provider) {
        if ($image = $provider->getPhoto()) {
            $mediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
            return $mediaUrl . ProviderInterface::BASE_PHOTO_PATH . '/' . $image;
        }
        return '';
    }
}
 