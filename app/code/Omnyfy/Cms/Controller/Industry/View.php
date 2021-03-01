<?php

/**
 * Project: CMS M2.
 * User: abhay
 * Date: 3/05/18
 * Time: 11:00 AM
 */

namespace Omnyfy\Cms\Controller\Industry;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;

class View extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $resultForwardFactory;
    protected $countryRepository;

    public function __construct(
    Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\Registry $coreRegistry, \Omnyfy\Cms\Model\Industry $industryRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->industryRepository = $industryRepository;

        parent::__construct($context);
    }

    public function execute() {
        $industry = $this->_initIndustry();
        if (empty($industry)) {
            //404
            $this->_forward('index', 'noroute', 'cms');
            return;
        }
        return $this->resultPageFactory->create();
    }

    protected function _initIndustry() {
        $industryId = $this->getRequest()->getParam('id');

        if (empty($industryId))
            return false;

        try {
            $industry = $this->industryRepository->load($industryId);
            $this->_coreRegistry->register('current_industry', $industry);

            if ($industryId != $industry->getId()) {
                return false;
            }

            if (!$industry->getStatus()) {
                return false;
            }

            return $industry;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }
    }

}
