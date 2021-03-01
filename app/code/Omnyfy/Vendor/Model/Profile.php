<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 6/6/17
 * Time: 5:18 PM
 */

namespace Omnyfy\Vendor\Model;

use Omnyfy\Vendor\Api\Data\ProfileInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Profile extends AbstractModel implements ProfileInterface, IdentityInterface
{
    const CACHE_TAG = 'omnyfy_vendor_profile';

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Resource\Profile');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    protected function _afterLoad()
    {
        $updates = $this->loadUpdates();

        if (!empty($updates)) {
            foreach($updates as $key => $val) {
                if ($key == $this->getIdFieldName()) {
                    continue;
                }
                $this->setData($key, $val);
            }
        }

        return parent::_afterLoad();
    }

    public function loadUpdates()
    {
        $updateString = $this->getUpdate();
        if (empty($updateString)) {
            return [];
        }

        $updates = json_decode($updateString, true);
        if (empty($updates)) {
            return [];
        }

        return $updates;
    }
}