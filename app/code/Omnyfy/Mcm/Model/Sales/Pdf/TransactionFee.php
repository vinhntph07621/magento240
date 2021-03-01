<?php

namespace Omnyfy\Mcm\Model\Sales\Pdf;

/**
 * Class Fee
 * @package Omnyfy\Mcm\Model\Sales\Pdf
 */
class TransactionFee extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal {

    public function getTotalsForDisplay() {
        $amount = $this->getOrder()->formatPriceTxt($this->getAmount());
        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix() . $amount;
        }

        $title = __($this->getTitle());
        if ($this->getTitleSourceField()) {
            $label = $title . ' (' . $this->getTitleDescription() . '):';
        } else {
            $label = $title . ':';
        }

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $total = ['amount' => $amount, 'label' => $label, 'font_size' => $fontSize];
        return [$total];
    }

}
