<?php
namespace Omnyfy\Stripe\Controller\Adminhtml\Vendor;

use Omnyfy\Stripe\Helper\Data;

class Index extends \Omnyfy\Vendor\Controller\Adminhtml\Vendor\Index
{
    const STRIPE_AUTH_CODE_COOKIE = 'stripe_auth_code';
    /**
     * @var Data
     */
    protected $stripeHelper;

    /**
     * Index constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param Data $stripeHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        Data $stripeHelper
    ) {
        $this->stripeHelper = $stripeHelper;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NotFoundException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute()
    {
        if (!empty($this->stripeHelper->getCookie(self::STRIPE_AUTH_CODE_COOKIE))) {
            try {
                $this->stripeHelper->handleStripeAccountAuthCode($this->stripeHelper->getCookie(self::STRIPE_AUTH_CODE_COOKIE));
                $this->messageManager->addSuccessMessage(
                    __('Your account Stripe was successfully created. To manage your account, go to your profile and check your "My bank and payout" details.')
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
            $this->stripeHelper->deleteCookie(self::STRIPE_AUTH_CODE_COOKIE);
        }
        return parent::execute();
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->_auth->isLoggedIn()) {
            if (!empty($this->getRequest()->getParam('code'))) {
                $this->stripeHelper->setCookie(
                    self::STRIPE_AUTH_CODE_COOKIE,
                    $this->getRequest()->getParam('code')
                );
            }
        }
        return parent::dispatch($request);
    }
}
