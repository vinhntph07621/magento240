<?php


namespace Omnyfy\VendorAuth\Api\Data;

interface LogInterface
{

    const CONTROLLER = 'controller';
    const DATE = 'date';
    const LOGGEDIN_VENDOR_ID = 'loggedin_vendor_id';
    const MODULE = 'module';
    const ROUTE = 'route';
    const ACTION = 'action';
    const REQUESTED_ENTITY_ID = 'requested_entity_id';
    const LOG_ID = 'log_id';


    /**
     * Get log_id
     * @return string|null
     */
    public function getLogId();

    /**
     * Set log_id
     * @param string $logId
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setLogId($logId);

    /**
     * Get loggedin_vendor_id
     * @return string|null
     */
    public function getLoggedinVendorId();

    /**
     * Set loggedin_vendor_id
     * @param string $loggedinVendorId
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setLoggedinVendorId($loggedinVendorId);

    /**
     * Get module
     * @return string|null
     */
    public function getModule();

    /**
     * Set module
     * @param string $module
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setModule($module);

    /**
     * Get route
     * @return string|null
     */
    public function getRoute();

    /**
     * Set route
     * @param string $route
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setRoute($route);

    /**
     * Get controller
     * @return string|null
     */
    public function getController();

    /**
     * Set controller
     * @param string $controller
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setController($controller);

    /**
     * Get action
     * @return string|null
     */
    public function getAction();

    /**
     * Set action
     * @param string $action
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setAction($action);

    /**
     * Get requested_entity_id
     * @return string|null
     */
    public function getRequestedEntityId();

    /**
     * Set requested_entity_id
     * @param string $requestedEntityId
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setRequestedEntityId($requestedEntityId);

    /**
     * Get date
     * @return string|null
     */
    public function getDate();

    /**
     * Set date
     * @param string $date
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    public function setDate($date);
}