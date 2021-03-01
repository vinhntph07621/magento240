<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 12/9/19
 * Time: 5:38 pm
 */
namespace Omnyfy\Vendor\Model\Resource;

class Helper extends \Magento\Eav\Model\ResourceModel\Helper
{
    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param string $modulePrefix
     */
    public function __construct(\Magento\Framework\App\ResourceConnection $resource, $modulePrefix = 'Omnyfy_Vendor')
    {
        parent::__construct($resource, $modulePrefix);
    }
}
 