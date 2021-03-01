<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 30/1/18
 * Time: 9:59 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Inventory\Edit\Button;
use Magento\Framework\AuthorizationInterface;

class StockReport extends Generic
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * StockReport constructor.
     * @param \Magento\Framework\View\Element\UiComponent\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\Context $context,
        \Magento\Framework\Registry $registry,
        AuthorizationInterface $authorization
    )
    {
        $this->authorization = $authorization;
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        //add role for locations stock report
        if (!$this->authorization->isAllowed('Omnyfy_Vendor::stock_report')) {
            return [];
        }
        return [
            'label' => __('Stock report'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('omnyfy_vendor/location/report')),
            'class' => 'stockreport',
            'sort_order' => 20
        ];
    }
}
