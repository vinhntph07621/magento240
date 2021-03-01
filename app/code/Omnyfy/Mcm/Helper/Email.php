<?php
namespace Omnyfy\Mcm\Helper;
 
/**
 * Mcm Email helper
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
 
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
 
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
 
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
 
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
     
    /**
     * @var string
    */
    protected $temp_id;
 
    /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder; 
    }
 
    /**
     * Return store 
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }
 
    /**
     * Template generate with template file and tempaltes variables values                
     * @param  Mixed $emailTemplateVariables 
     * @param  Mixed $senderInfo             
     * @param  Mixed $receiverInfo           
     * @return void
     */
    public function generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo,$area = 'admin')
    {
		if($area == 'admin'){
			$area = \Magento\Framework\App\Area::AREA_ADMINHTML;
		}else{
			$area = \Magento\Framework\App\Area::AREA_FRONTEND;
		}
		
		if (empty($senderInfo)) {
            $sendFrom = 'general';
        }else{
			$sendFrom = $senderInfo;
		}
        $template =  $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
                ->setTemplateOptions(
                    [
                        'area' => $area,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($sendFrom)
                ->addTo($receiverInfo['email'],$receiverInfo['name']);
        return $this;        
    }
 
    /**
     * Function use to send emails                  
     * @param  Mixed $emailTemplateVariables 
     * @param  Mixed $senderInfo             
     * @param  Mixed $receiverInfo           
     * @return void
     */
    /* your send mail method*/
    public function mcmMailSend($templateId, $emailTemplateVariables,$senderInfo,$receiverInfo)
    {
 
        $this->temp_id = $templateId;
        $this->inlineTranslation->suspend();    
        $this->generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo);    
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();        
        $this->inlineTranslation->resume();
    }
 
}