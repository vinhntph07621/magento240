<?php
/**
 * Project: MCM
 * User: jing
 * Date: 2019-04-02
 * Time: 17:33
 */

namespace Omnyfy\Mcm\Plugin;

use Magento\Backend\Model\Menu\Builder;
use Magento\Backend\Model\Menu;

class AdminMenuBuilder
{
    protected $menuItemFactory;

    protected $backendSession;

    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Backend\Model\Menu\ItemFactory $menuItemFactory
    )
    {
        $this->backendSession = $backendSession;
        $this->menuItemFactory = $menuItemFactory;
    }

    public function afterGetResult(Builder $subject, Menu $menu) {
        $vendorInfo = $this->backendSession->getVendorInfo();

        if (!empty($vendorInfo)) {
            $parent = "Omnyfy_Mcm::withdrawal_management";
            $item = $this->menuItemFactory->create([
                'data' => [
                    'parent_id' => $parent,
                    'id' => 'Omnyfy_Mcm::vendor_new_withdrawal',
                    'title' => 'New Withdraw',
                    'resource' => 'Omnyfy_Mcm::vendor_new_withdrawal',
                    'action' => 'omnyfy_mcm/vendorwithdrawal/newwithdrawal'
                ]
            ]);

            $menu->add($item, $parent);
        }

        return $menu;
    }
}
 