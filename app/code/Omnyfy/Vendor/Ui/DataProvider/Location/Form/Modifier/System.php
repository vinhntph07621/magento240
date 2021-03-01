<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-07-23
 * Time: 16:37
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Location\Form\Modifier;

use Magento\Framework\UrlInterface;

class System extends AbstractModifier
{
    const KEY_SUBMIT_URL = 'submit_url';

    protected $locator;

    protected $urlBuilder;

    protected $vendorUrls = [
        self::KEY_SUBMIT_URL => 'omnyfy_vendor/location/save',
    ];

    public function __construct(
        \Omnyfy\Vendor\Model\Locator\LocationLocatorInterface $locator,
        UrlInterface $urlBuilder,
        array $vendorUrls = []
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->vendorUrls = array_replace_recursive($this->vendorUrls, $vendorUrls);
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    public function modifyData(array $data)
    {
        $model = $this->locator->getLocation();

        $parameters = [
            'id' => $model->getId(),
            'store' => $model->getStoreId(),
        ];

        $submitUrl = $this->urlBuilder->getUrl($this->vendorUrls[self::KEY_SUBMIT_URL], $parameters);

        return array_replace_recursive(
            $data,
            [
                'config' => [
                    self::KEY_SUBMIT_URL => $submitUrl,
                ]
            ]
        );
    }

}
 