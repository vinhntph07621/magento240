<?php


namespace Omnyfy\VendorAuth\Model;

use Omnyfy\VendorAuth\Api\Data\LogInterface;

class Log extends \Magento\Framework\Model\AbstractModel implements LogInterface
{

    protected $_eventPrefix = 'omnyfy_vendorauth_log';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorAuth\Model\ResourceModel\Log');
    }

    /**
     * Get log_id
     * @return string
     */
    public function getLogId()
    {
        return $this->getData(self::LOG_ID);
    }

    /**
     * Set log_id
     * @param string $logId
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setLogId($logId)
    {
        return $this->setData(self::LOG_ID, $logId);
    }

    /**
     * Get loggedin_vendor_id
     * @return string
     */
    public function getLoggedinVendorId()
    {
        return $this->getData(self::LOGGEDIN_VENDOR_ID);
    }

    /**
     * Set loggedin_vendor_id
     * @param string $loggedinVendorId
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setLoggedinVendorId($loggedinVendorId)
    {
        return $this->setData(self::LOGGEDIN_VENDOR_ID, $loggedinVendorId);
    }

    /**
     * Get module
     * @return string
     */
    public function getModule()
    {
        return $this->getData(self::MODULE);
    }

    /**
     * Set module
     * @param string $module
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setModule($module)
    {
        return $this->setData(self::MODULE, $module);
    }

    /**
     * Get route
     * @return string
     */
    public function getRoute()
    {
        return $this->getData(self::ROUTE);
    }

    /**
     * Set route
     * @param string $route
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setRoute($route)
    {
        return $this->setData(self::ROUTE, $route);
    }

    /**
     * Get controller
     * @return string
     */
    public function getController()
    {
        return $this->getData(self::CONTROLLER);
    }

    /**
     * Set controller
     * @param string $controller
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setController($controller)
    {
        return $this->setData(self::CONTROLLER, $controller);
    }

    /**
     * Get action
     * @return string
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * Set action
     * @param string $action
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }

    /**
     * Get requested_entity_id
     * @return string
     */
    public function getRequestedEntityId()
    {
        return $this->getData(self::REQUESTED_ENTITY_ID);
    }

    /**
     * Set requested_entity_id
     * @param string $requestedEntityId
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setRequestedEntityId($requestedEntityId)
    {
        return $this->setData(self::REQUESTED_ENTITY_ID, $requestedEntityId);
    }

    /**
     * Get date
     * @return string
     */
    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    /**
     * Set date
     * @param string $date
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }
}