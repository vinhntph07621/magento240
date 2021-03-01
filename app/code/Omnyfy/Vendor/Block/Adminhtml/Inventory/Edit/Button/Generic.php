<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 30/1/18
 * Time: 2:48 PM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Inventory\Edit\Button;

class Generic implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    protected $context;

    protected $registry;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\Context $context,
        \Magento\Framework\Registry $registry
    )
    {
        $this->context = $context;
        $this->registry = $registry;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }
}