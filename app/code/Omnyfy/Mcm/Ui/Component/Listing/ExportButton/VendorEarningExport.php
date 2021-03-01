<?php

namespace Omnyfy\Mcm\Ui\Component\Listing\ExportButton;

/**
 * Class VendorEarningExport
 */
class VendorEarningExport extends \Magento\Ui\Component\ExportButton {

    /**
     * Component name
     */
    const NAME = 'exportButton';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $components
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\UiComponent\ContextInterface $context, \Magento\Framework\UrlInterface $urlBuilder, \Magento\Framework\App\Request\Http $request, array $components = [], array $data = []
    ) {
        parent::__construct($context, $urlBuilder, $components, $data);
        $this->_urlBuilder = $urlBuilder;
        $this->_request = $request;
    }

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName() {
        return static::NAME;
    }

    /**
     * @return void
     */
    public function prepare() {
        
        $vendorId = $this->_request->getParam('vendor_id');
        if ($vendorId) {
            $config = $this->getData('config');
            if (isset($config['options'])) {
                $options = [];
                foreach ($config['options'] as $option) {
                    $option['url'] = $this->urlBuilder->getUrl($option['url'], ["vendor_id" => $vendorId]);
                    $options[] = $option;
                }
                $config['options'] = $options;
                $this->setData('config', $config);
            }
        }
        parent::prepare();
    }

}
