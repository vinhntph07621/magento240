<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Omnyfy\VendorReview\Model\ReviewFactory;
use Omnyfy\VendorReview\Model\RatingFactory;

/**
 * Reviews admin controller
 */
abstract class Vendor extends Action
{
    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Review model factory
     *
     * @var \Omnyfy\VendorReview\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * Rating model factory
     *
     * @var \Omnyfy\VendorReview\Model\RatingFactory
     */
    protected $ratingFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
     * @param \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ReviewFactory $reviewFactory,
        RatingFactory $ratingFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'pending':
                return $this->_authorization->isAllowed('Omnyfy_VendorReview::pending');
                break;
            default:
                return $this->_authorization->isAllowed('Omnyfy_VendorReview::vendor_reviews_all');
                break;
        }
    }
}
