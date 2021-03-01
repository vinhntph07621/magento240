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
 * @package   mirasvit/module-rewards
 * @version   3.0.21
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $config;
    private $storeManager;
    private $localeCurrency;
    protected $context;

    public function __construct(
        \Mirasvit\Rewards\Model\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->config                 = $config;
        $this->storeManager           = $storeManager;
        $this->localeCurrency         = $localeCurrency;
        $this->context                = $context;
        parent::__construct($context);
    }

    /**
     * @var \Magento\Store\Model\Store|bool|\Magento\Store\Api\Data\StoreInterface
     */
    protected $_currentStore;

    /**
     * Sets current store for translation.
     *
     * @param \Magento\Store\Model\Store|\Magento\Store\Api\Data\StoreInterface $store
     *
     * @return void
     */
    public function setCurrentStore($store)
    {
        $this->_currentStore = $store;
    }

    /**
     * Returns current store.
     *
     * @return \Magento\Store\Api\Data\StoreInterface|\Magento\Store\Model\Store
     */
    public function getCurrentStore()
    {
        if (!$this->_currentStore) {
            $this->_currentStore = $this->storeManager->getStore();
        }

        return $this->_currentStore;
    }

    /**
     * @return \Mirasvit\Rewards\Model\Config
     */
    private function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getPointsName()
    {
        $unit = $this->getConfig()->getGeneralPointUnitName();
        $unit = str_replace(['(', ')'], '', $unit);

        return $unit;
    }

    /**
     * @param float $points
     * @param int $storeId
     * @return string
     */
    public function getPointUnitName($points, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->getCurrentStore()->getId();
        }
        $unit = $this->getConfig()->getGeneralPointUnitName($storeId);

        if ($points == 1) {
            $unit = preg_replace("/\([^)]+\)/", '', $unit);
        } else {
            $unit = str_replace(['(', ')'], '', $unit);
        }

        return $unit;
    }

    /**
     * @param float $points
     * @param int $storeId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    public function formatPoints($points, $storeId = null)
    {
        $unit = $this->getPointUnitName($points, $storeId);
        $currencyCode = $this->getCurrentStore()->getCurrentCurrency()->getCode();
        $points = $this->localeCurrency->getCurrency($currencyCode)
            ->toCurrency($points, ['display' => \Zend_Currency::NO_SYMBOL, 'precision' => 0]);

        return $points . ' ' . $unit;
    }

    /**
     * @param float $points
     * @param int $storeId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    public function backendGridFormatPoints($points, $storeId = null)
    {
        $unit = $this->getPointUnitName($points, $storeId);

        return $points . ' ' . $unit;
    }

    /**
     * @param float $points
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    public function formatPointsWithCutUnitName($points)
    {
        $result = $this->formatPoints($points);
        $regexp = '/p{P}/';
        $result = trim(preg_replace($regexp, ' ', $result));
        if (count(explode(' ', $result)) > 2) {
            $regexp = '/[^\p{Lu}]/u';
            return $points .' '. preg_replace($regexp, '', ucwords(strtolower($result)));
        }

        return $result;
    }
}
