<?php

namespace Omnyfy\Mcm\Api;

interface GetTransactionFeeInterface
{
    /**
     * @api
     * @param int $quoteId
     * @return int $transactionFee
     */
    public function getTransactionFee($quoteId);
}