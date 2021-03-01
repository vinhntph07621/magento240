<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-autocomplete
 * @version   1.2.4
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchAutocomplete\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\SearchAutocomplete\Model\Result;

class Suggest extends Action
{
    /**
     * @var Result
     */
    private $result;

    /**
     * Suggest constructor.
     * @param Result $result
     * @param Context $context
     */
    public function __construct(
        Result $result,
        Context $context
    ) {
        $this->result = $result;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->result->init();

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->setHeader('cache-control', 'max-age=86400, public, s-maxage=86400', true);
        $response->representJson(\Zend_Json::encode(
            $this->result->toArray()
        ));
    }
}
