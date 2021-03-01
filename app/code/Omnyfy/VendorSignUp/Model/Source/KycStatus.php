<?php
/**
 * Project: Vendor SignUp
 * User: jing
 * Date: 6/9/19
 * Time: 4:45 pm
 */
namespace Omnyfy\VendorSignUp\Model\Source;

class KycStatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const STATUS_PENDING = 'pending';

    const STATUS_CHECKING = 'pending_check';

    const STATUS_APPROVING = 'approved_kyc_check';

    const STATUS_APPROVED = 'approved';

    public function toValuesArray()
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_CHECKING => __('Checking'),
            self::STATUS_APPROVING => __('Approving'),
            self::STATUS_APPROVED => __('Approved'),
        ];
    }

    public function toOptionArray()
    {
        $result = [];
        foreach($this->toValuesArray() as $key => $value) {
            $result[] = ['value' => $key, 'label' => $value];
        }
        return $result;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
 