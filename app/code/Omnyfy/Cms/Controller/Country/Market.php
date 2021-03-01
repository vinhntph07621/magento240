<?php
/**
 * Project: CMS M2.
 * User: abhay
 * Date: 26/4/18
 * Time: 03:00 PM
 */

namespace Omnyfy\Cms\Controller\Country;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;

class Market extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $resultForwardFactory;

    protected $countryRepository;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
		\Magento\Framework\Registry $coreRegistry,
        \Omnyfy\Cms\Model\Country $countryRepository
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
		$this->_coreRegistry = $coreRegistry;
        $this->countryRepository = $countryRepository;

        parent::__construct($context);
    }

    public function execute()
    { 
		return $this->resultPageFactory->create();
    }
}