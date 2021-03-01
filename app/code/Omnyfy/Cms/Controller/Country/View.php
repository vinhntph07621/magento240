<?php

/**
 * Project: CMS M2.
 * User: abhay
 * Date: 27/3/18
 * Time: 03:00 PM
 */

namespace Omnyfy\Cms\Controller\Country;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;

class View extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;
    protected $resultForwardFactory;
    protected $countryRepository;

    public function __construct(
    Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory, \Magento\Framework\Registry $coreRegistry, \Omnyfy\Cms\Model\Country $countryRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->countryRepository = $countryRepository;

        parent::__construct($context);
    }

    public function execute() {
        $country = $this->_initCountry();
        if (empty($country)) {
            //404
            $this->_forward('index', 'noroute', 'cms');
            return;
        }
        return $this->resultPageFactory->create();
    }

    protected function _initCountry() {
        $countryId = $this->getRequest()->getParam('id');

        if (empty($countryId))
            return false;

        try {
            $country = $this->countryRepository->load($countryId);
			$country->setVisitiors($country->getVisitiors()+1);
			$country->save();
			
            $this->_coreRegistry->register('current_country', $country);

            if ($countryId != $country->getId()) {
                return false;
            }

            if (!$country->getStatus()) {
                return false;
            }

            return $country;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }
    }

}
