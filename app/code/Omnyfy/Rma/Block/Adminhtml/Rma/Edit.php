<?php
/**
 * Project: RMA per vendor
 * Author: seth
 * Date: 21/2/20
 * Time: 2:39 pm
 **/

namespace Omnyfy\Rma\Block\Adminhtml\Rma;


class Edit extends \Mirasvit\Rma\Block\Adminhtml\Rma\Edit
{
    /**
     * @var \Omnyfy\Rma\Helper\Data
     */
    protected $helper;

    protected $registry;

    protected $context;

    protected $policyInterface;

    protected $adminSession;

    public function __construct(
        \Mirasvit\Rma\Api\Service\Resolution\ResolutionManagementInterface $resolutionManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Helper\Rma\Url $rmaUrl,
        \Mirasvit\Rma\Helper\Order\Creditmemo $creditmemoHelper,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $orderInvoiceCollectionFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Authorization\PolicyInterface $policyInterface,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Omnyfy\Rma\Helper\Data $helper,
        array $data = []
    ) {
        $this->resolutionManagement          = $resolutionManagement;
        $this->rmaManagement                 = $rmaManagement;
        $this->rmaUrl                        = $rmaUrl;
        $this->creditmemoHelper              = $creditmemoHelper;
        $this->orderInvoiceCollectionFactory = $orderInvoiceCollectionFactory;
        $this->wysiwygConfig                 = $wysiwygConfig;
        $this->registry                      = $registry;
        $this->context                       = $context;
        $this->policyInterface               = $policyInterface;
        $this->adminSession                  = $adminSession;
        $this->helper                        = $helper;

        parent::__construct($resolutionManagement,
            $rmaManagement,
            $rmaUrl,
            $creditmemoHelper,
            $orderInvoiceCollectionFactory,
            $wysiwygConfig,
            $registry,
            $context,
            $policyInterface,
            $adminSession,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $vendorId = $this->helper->getVendorId();
        if ($vendorId) {
            $this->buttonList->remove('order_creditmemo_manual');
        }
        $this->buttonList->remove('order_exchange');

        return $this;
    }
}