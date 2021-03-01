<?php
/**
 * Project: Mcm.
 * User: jing
 * Date: 2018-12-20
 * Time: 15:02
 */

namespace Omnyfy\Mcm\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessPayoutAmount extends Command
{
    protected $appState;

    protected $queueHelper;

    protected $eventManager;

    protected $shipmentRepository;

    protected $vendorOrderFactory;

    protected $payoutHelper;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Omnyfy\Mcm\Model\VendorOrderFactory $vendorOrderFactory,
        \Omnyfy\Mcm\Helper\Payout $payoutHelper
    )
    {
        $this->appState = $state;
        $this->queueHelper = $queueHelper;
        $this->eventManager = $eventManager;
        $this->shipmentRepository = $shipmentRepository;
        $this->vendorOrderFactory = $vendorOrderFactory;
        $this->payoutHelper = $payoutHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('omnyfy:mcm:process_payout_amount');
        $this->setDescription('Process Payout Amount for mcm orders');
        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        if (!$this->lock()) {
            return;
        }

        try{
            $code = $this->appState->getAreaCode();
        }
        catch(\Exception $e) {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        }

        $output->writeln('Start to process');

        $i = $done = $failed = $invalid = 0;

        // Update the payout amount of the mcm order
        $vendorOrdersMcm = $this->vendorOrderFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('payout_calculated', 0);

        foreach ($vendorOrdersMcm as $vendorOrderMcm) {
            // If there is a record in omnyfy_mcm_shipping_calculation use that for order
            $doesShippingMcmCalculationExist = $this->payoutHelper->doesShippingCalculationExist($vendorOrderMcm);
            if ($doesShippingMcmCalculationExist) {
                $orderTotalIncludingShippingPayout = $this->payoutHelper->getOrderPayoutShippingAmount($vendorOrderMcm);
                $vendorOrderTotalIncTax = ($vendorOrderMcm->getBaseGrandTotal() + ($orderTotalIncludingShippingPayout - $vendorOrderMcm->getShippingDiscountAmount()));
                $vendorOrderMcm->setPayoutShipping($orderTotalIncludingShippingPayout - $vendorOrderMcm->getShippingDiscountAmount());
            } else {
                // fallback for existing
                $vendorOrderTotalIncTax = ($vendorOrderMcm->getBaseGrandTotal() + ($vendorOrderMcm->getBaseShippingAmount() + $vendorOrderMcm->getBaseShippingTax() - $vendorOrderMcm->getShippingDiscountAmount()));
            }

            $vendorOrderTotalFees = $vendorOrderMcm->getTotalCategoryFee() + $vendorOrderMcm->getTotalSellerFee() + $vendorOrderMcm->getDisbursementFee();

            $vendorOrderTotalFeeTax = $vendorOrderMcm->getTotalCategoryFeeTax() + $vendorOrderMcm->getTotalSellerFeeTax() + $vendorOrderMcm->getDisbursementFeeTax();
            $vendorOrderFeeTotalIncTax = $vendorOrderTotalFees + $vendorOrderTotalFeeTax;

            $vendorOrderMcm->setPayoutAmount($vendorOrderTotalIncTax - $vendorOrderFeeTotalIncTax);
            $vendorOrderMcm->setPayoutCalculated(1);

            try {
                $vendorOrderMcm->save();
                $i++;
            } catch (\Exception $e) {
                $this->objectManager->get('Psr\Log\LoggerInterface')->critical('Error updating payout amount mcm order_id' . $vendorOrderMcm->getId()
                    . $e->getMessage()
                );
            }
        }

        $done++;
        $output->writeln('Done. Got '. $i . ' payout amounts processed.');
        $output->writeln('Succeeded: ' . $done);
    }
}