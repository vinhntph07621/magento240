<?php


namespace Omnyfy\VendorAuth\Observer\Backend\Controller;

class ActionPredispatch implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    private $responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var
     */
    protected $actionFlag;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_message;

    /**
     * @var \Omnyfy\VendorAuth\Model\Vendor
     */
    protected $_vendorAuthVendor;

    /**
     * @var \Omnyfy\VendorAuth\Api\Data\LogInterface
     */
    protected $_log;

    /**
     * @var \Omnyfy\VendorAuth\Api\LogRepositoryInterface
     */
    protected $_logRepository;

    /**
     * RestrictWebsite constructor.
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\App\Http\Context $context,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Omnyfy\VendorAuth\Model\Vendor $vendorAuthVendor,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Omnyfy\VendorAuth\Api\LogRepositoryInterface $logRepository,
        \Omnyfy\VendorAuth\Api\Data\LogInterface $log
    )
    {
        $this->_response = $response;
        $this->_urlFactory = $urlFactory;
        $this->_context = $context;
        $this->_actionFlag = $actionFlag;
        $this->_storeManager = $storeManager;
        $this->authSession = $authSession;
        $this->session = $session;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->_actionFlag = $actionFlag;
        $this->_message = $messageManager;
        $this->_vendorAuthVendor = $vendorAuthVendor;
        $this->_redirectFactory = $redirectFactory;
        $this->_log = $log;
        $this->_logRepository = $logRepository;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    )
    {
        //check if it is a vendor user
        $vendorSession = $this->session->getVendorInfo();

        if (empty($vendorSession))
            return;

        $vendorId = $vendorSession['vendor_id'];

        if ($vendorId == 0 || empty($vendorId))
            return;

        /**
         * @var \Magento\Framework\App\Request\Http $request
         */
        $request = $observer->getEvent()->getRequest();
        $module = $request->getControllerModule();
        $route = $request->getRouteName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        //$fullName = $request->getFullActionName();

        //Block customers to view details page
        if ($route == 'customer' && $action == 'edit'){
            $redirectionUrl = $this->url->getUrl('access/denied/index');

            $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
        }

        //check if route needs to be protected.
        if (!$routeArray = $this->isProtected($route))
            return;


        //Check id the controller needs to be protected.
        if (!key_exists($controller, $routeArray))
            return;

        $controllerArray = $routeArray[$controller];

        //Check if the action needs to be protected.
        if (!$this->isActionProtected($action))
            return;

        //Get the vendor entities.
        $ids = [];

        //If vendor entices managed in separate table
        if (key_exists('table_suffix', $controllerArray)) {
            $ids = $this->_vendorAuthVendor->getVendorEntityFromType($controllerArray['table_suffix'], $vendorId);
        }

        /** If the Omnyfy vendor vendor*/
        if ($route == 'omnyfy_vendor' && ($controller == 'vendor' || $controller == 'vendor_store')) {
            $ids = [$vendorId];
        }

        //If vendor_id is added to the table row
        if (key_exists('table', $controllerArray)) {
            $ids = $this->_vendorAuthVendor->getVendorEntityFromTable($controllerArray['table'], $controllerArray['column'], $vendorId);
        }

        //Get the entity id passed in URI
        if (key_exists('entity_id', $controllerArray)) {
            $idName = $controllerArray['entity_id'];
            $id = $request->get($idName);
        }

        if (empty($id) && ($action == 'save' || $action == 'edit')){
           return;
        }

        if (empty($ids) || !in_array($id, $ids)) {
            try{
                $logModel = $this->_log->setData( [
                    "loggedin_vendor_id" => $vendorId,
                    "module" => $module,
                    "route" => $route,
                    "controller" => $controller,
                    "action" => $action,
                    "requested_entity_id" => $id
                    ]
                );
                $this->_logRepository->save($logModel);

            } catch (\Exception $exception){}

            if ($action == 'save'){
                sleep(10);
                exit(1);
            }

            $this->_message->addErrorMessage(__('You are not authorised to perform %1 action .',$action));
            $redirectionUrl = $this->url->getUrl('access/denied/index');

            $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
        }

        return;
    }

    protected function isActionProtected($action){
        $isProtected = false;

        $actionsToProtected = [
            'save',
            'edit',
            'delete',
            'massDelete',
            'deleteWebsitePost',
            'inlineEdit',
            'addComment',
            'massDisable',
            'massEnable',
            'restore',
            'cancel',
            'hold',
            'unhold',
            'post',
            'saverole',
            'massOnTheFly',
            'importPost',
            'stock',
        ];

        if (in_array($action, $actionsToProtected)) {
            $isProtected = true;
        }

        return $isProtected;
    }

    public function isProtected($route){

        $routes = [
            'customer' => [
                'index' =>[
                    'table_suffix' => 'customer',
                    'entity_id' => 'id'
                ],
            ],
            'sales' => [
                'invoice' =>[
                    'table_suffix' => 'invoice',
                    'entity_id' => 'id'
                ],
                'order' =>[
                    'table_suffix' => 'order',
                    'entity_id' => 'id'
                ],
            ],
            'catalog' =>[
                'product' =>[
                    'table_suffix' => 'product',
                    'entity_id' => 'id'
                ],
            ],
            'omnyfy_vendor' =>[
                'vendor' =>[
                    'entity_id' => 'id'
                ],
                'location' =>[
                    'table' => 'omnyfy_vendor_location_entity',
                    'column' => 'entity_id',
                    'entity_id' => 'id'
                ],
                'vendor_store' =>[
                    'entity_id' => 'id'
                ],
            ],
            'omnyfy_mcm' => [
                'fees' => [
                    'table' => 'omnyfy_mcm_fees_and_charges',
                    'column' => 'id',
                    'entity_id' => 'id'
                ],
            ],
        ];

        if (key_exists($route,$routes))
            return $routes[$route];

        return null;
    }
}
