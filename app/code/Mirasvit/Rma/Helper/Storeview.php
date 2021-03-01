<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.1.18
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Helper;

use Mirasvit\Core\Service\SerializeService as Serializer;

class Storeview extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $context;

    private $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->context      = $context;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * @param string $object
     * @param string $field
     * @param string $value
     * @return void
     */
    public function setStoreViewValue($object, $field, $value)
    {
        $storeId = (int)$object->getStoreId();
        $serializedValue = $object->getData($field);
        $arr = $this->unserialize($serializedValue);

        if ($storeId === 0) {
            $arr[0] = $value;
        } else {
            $arr[$storeId] = $value;
            if (!isset($arr[0])) {
                $arr[0] = $value;
            }
        }
        $object->setData($field, Serializer::encode($arr));
    }

    /**
     * @param object $object
     * @param string $field
     * @return string
     */
    public function getStoreViewValue($object, $field)
    {
        $storeId = $object->getStoreId();
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $serializedValue = $object->getData($field);
        $arr = $this->unserialize($serializedValue);
        $defaultValue = null;
        if (isset($arr[0])) {
            $defaultValue = $arr[0];
        }

        if (isset($arr[$storeId])) {
            $localizedValue = $arr[$storeId];
        } else {
            $localizedValue = $defaultValue;
        }

        return $localizedValue;
    }

    /**
     * @param string $string
     * @return array
     */
    public function unserialize($string)
    {
        try {
            $result = Serializer::decode($string);
            if (!$result) {
                $result = [0 => $string];
            }

            return $result;
        } catch (\Exception $e) {
            return [0 => $string];
        }
    }
}
