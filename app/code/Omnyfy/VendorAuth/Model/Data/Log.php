<?php


namespace Omnyfy\VendorAuth\Model\Data;

use Omnyfy\VendorAuth\Api\Data\LogInterface;

class Log extends \Magento\Framework\Api\AbstractExtensibleObject implements LogInterface
{

    /**
     * Get log_id
     * @return string|null
     */
    public function getLogId()
    {
        return $this->_get(self::LOG_ID);
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
     * @return string|null
     */
    public function getLoggedinVendorId()
    {
        return $this->_get(self::LOGGEDIN_VENDOR_ID);
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
     * Retrieve existing extension attributes object or create a new one.
     * @return \Omnyfy\VendorAuth\Api\Data\LogExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Omnyfy\VendorAuth\Api\Data\LogExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Omnyfy\VendorAuth\Api\Data\LogExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get module
     * @return string|null
     */
    public function getModule()
    {
        return $this->_get(self::MODULE);
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
     * @return string|null
     */
    public function getRoute()
    {
        return $this->_get(self::ROUTE);
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
     * @return string|null
     */
    public function getController()
    {
        return $this->_get(self::CONTROLLER);
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
     * @return string|null
     */
    public function getAction()
    {
        return $this->_get(self::ACTION);
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
     * @return string|null
     */
    public function getRequestedEntityId()
    {
        return $this->_get(self::REQUESTED_ENTITY_ID);
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

    public function getDate(){
        return $this->_get(self::DATE);
    }

    public function setDate($date){
        return $this->setData(self::DATE, $date);
    }
}
