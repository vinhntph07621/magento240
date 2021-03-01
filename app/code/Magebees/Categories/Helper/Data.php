<?php
namespace Magebees\Categories\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_systemStore;	
	protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
       	\Magento\Store\Model\System\Store $systemStore,
		\Magento\Store\Model\StoreManagerInterface $storeManager
       
    ) {
		$this->_systemStore = $systemStore;
    	$this->storeManager = $storeManager;
        parent::__construct($context);
    	
	}

	public function getStoreData()
    {
		return $this->_systemStore->getStoreValuesForForm(false, true);
	}
	
	public function getStoreone()
	{
		 if ($this->storeManager->hasSingleStore()) {
			return true;
		 }else{
		 	return false;
		 }
	}


		
}
