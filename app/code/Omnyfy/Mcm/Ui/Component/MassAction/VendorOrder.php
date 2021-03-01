<?php
/**
 * Project: MCM
 * User: jing
 * Date: 2019-03-25
 * Time: 16:20
 */
namespace Omnyfy\Mcm\Ui\Component\MassAction;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Backend\Model\Session as BackendSession;

class VendorOrder extends \Magento\Ui\Component\Action
{
    protected $urlBuilder;

    protected $request;

    protected $backendSession;

    public function __construct(
        ContextInterface $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        BackendSession $backendSession,
        array $components = [],
        array $data = [],
        $actions = null)
    {
        parent::__construct($context, $components, $data, $actions);

        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->backendSession = $backendSession;
    }

    public function prepare() {
        parent::prepare();

        $config = $this->getConfiguration();
        $params = [];
        $vendorId = $this->request->getParam('vendor_id');
        if (empty($vendorId)) {
            $vendorId = $this->backendSession->getCurrentVendorId();
        }
        if (!empty($vendorId)) {
            $params['vendor_id'] = $vendorId;
        }
        $config['url'] = $this->urlBuilder->getUrl($config['urlPath'], $params);
        $this->setData('config', $config);
    }
}
 