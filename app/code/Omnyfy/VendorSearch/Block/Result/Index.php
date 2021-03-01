<?php


namespace Omnyfy\VendorSearch\Block\Result;

class Index extends \Magento\Framework\View\Element\Template
{
    /** @var \Omnyfy\Vendor\Api\SearchRepositoryInterface  */
    protected $_vendorSearchRepository;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder  */
    protected $_searchCriteriaBuilder;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\Vendor\Api\SearchRepositoryInterface $vendorSearchRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->_vendorSearchRepository = $vendorSearchRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;

        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ])->addCrumb('search', ['label' => __('Vendors')]);
        }
        return $this;
    }

    public function getSearchFormHtml(){
        return $this->getChildHtml('vendor.search.form.container', false);
    }

    public function getLayeredNavigation(){
        return $this->getChildHtml('vendor.search.navigation.container', false);
    }

    public function getSearchSummery(){
        return $this->getChildHtml('vendor.search.summery.container', false);
    }

    public function getSearchResult(){
        return $this->getChildHtml('vendor.search.result.container', false);
    }

    public function getSearchResultMap(){
        return $this->getChildHtml('vendor.search.result.map.container', false);
    }

    public function getSearchResultFilter(){
        return $this->getChildHtml('vendor.search.result.filter.container', false);
    }
}
