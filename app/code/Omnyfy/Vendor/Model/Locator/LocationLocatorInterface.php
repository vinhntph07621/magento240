<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-07-23
 * Time: 16:40
 */
namespace Omnyfy\Vendor\Model\Locator;

interface LocationLocatorInterface
{
    public function getLocation();

    public function getStore();

    public function getWebsiteIds();

    public function getBaseCurrencyCode();
}
 