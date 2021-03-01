<?php


namespace Omnyfy\VendorAuth\Observer\Webapi\Controller;

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
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
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
        $startTime = microtime(true);

        \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug("Controller Observe");
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
        ];

        if (key_exists($route,$routes))
            return $routes[$route];

        return null;
    }
}
