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
 * @package   mirasvit/module-rewards
 * @version   3.0.21
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\RewardsCustomerAccount\Block;

use Mirasvit\Rewards\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

/**
 * Added rewards link to top menu(customer account menu)
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var string
     */
    protected $_template = 'Mirasvit_RewardsCustomerAccount::link.phtml';

    public function __construct(Data $helper, Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('rewards/account');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return $this->helper->getPointsName();
    }
}
