<?php

namespace Omnyfy\Mcm\Model\Api;

use Omnyfy\Mcm\Api\GetTransactionFeeInterface;
use Omnyfy\Mcm\Helper\Data as FeeHelper;
use Omnyfy\Mcm\Model\Calculation\Calculator\CalculatorInterface;
use Psr\Log\NullLogger;

/**
 * Class CreateBooking
 * @package Omnyfy\Booking\Model
 */
class GetTransactionFee implements GetTransactionFeeInterface
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonResultFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    protected $quoteRepository;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @var FeeHelper
     */
    protected $helper;

    protected $priceHelper;

    /**
     * GetTransactionFee constructor.
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        CalculatorInterface $calculator,
        FeeHelper $helper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->jsonHelper = $jsonHelper;
        $this->quoteRepository = $quoteRepository;
        $this->calculator = $calculator;
        $this->helper = $helper;
        $this->priceHelper  = $priceHelper;
    }

    /**
     * @api
     * @param int $quoteId
     * @return int $transactionFee
     */
    public function getTransactionFee($quoteId)
    {
        $quote = $this->quoteRepository->get($quoteId);

        if ($quote->getMcmTransactionFeeInclTax() > 0) {
            return $this->priceHelper->currency(number_format($quote->getMcmTransactionFeeInclTax(),2),false,false);
        }
        else {
            return 0;
        }
    }
}
