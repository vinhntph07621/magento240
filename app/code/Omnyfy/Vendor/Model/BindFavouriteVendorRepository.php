<?php
/**
 * Project: apcd.
 * User: seth
 * Date: 6/9/19
 * Time: 2:33 PM
 */
namespace Omnyfy\Vendor\Model;

class BindFavouriteVendorRepository implements \Omnyfy\Vendor\Api\BindFavouriteVendorRepositoryInterface
{
    /**
     * @var \Omnyfy\Vendor\Model\Resource\FavouriteVendor
     */
    protected $vendorResource;

    /**
     * BindFavouriteVendorRepository constructor.
     * @param \Omnyfy\Vendor\Model\Resource\FavouriteVendor $vendorResource
     */
    public function __construct(
        \Omnyfy\Vendor\Model\Resource\FavouriteVendor $vendorResource
    )
    {
        $this->vendorResource = $vendorResource;
    }

    public function save($customerId, $vendorId)
    {
        if (empty($customerId)) {
            return ['error' => true, 'message' => 'For logged in customer only.'];
        }
        
        if (empty($vendorId)) {
            return ['error' => true, 'message' => 'Invalid request'];
        }

        try {
            $action = 'add';
            $this->vendorResource->saveFavouriteBrokersId($customerId, $vendorId);
            $message = 'Vendor successfully added.';
            $isSuccess = true;

            return ['success' => $isSuccess, 'action' => $action, 'message' => $message];
        }
        catch (\Magento\Framework\Exception\NoSuchEntityException $exception)
        {
            return ['error' => true, 'message' => $exception->getMessage()];
        }
        catch (\Exception $exception)
        {
            return ['error' => true, 'message' => $exception->getMessage()];
        }
    }

    public function delete($customerId, $vendorId)
    {
        if (empty($customerId)) {
            return ['error' => true, 'message' => 'For logged in customer only.'];
        }

        if (empty($vendorId)) {
            return ['error' => true, 'message' => 'Invalid request'];
        }

        try {
            $action = 'remove';
            $this->vendorResource->removeFavouriteBrokersId($customerId, $vendorId);
            $message = 'Vendor successfully removed.';
            $isSuccess = true;

            return ['success' => $isSuccess, 'action' => $action, 'message' => $message];
        }
        catch (\Magento\Framework\Exception\NoSuchEntityException $exception)
        {
            return ['error' => true, 'message' => $exception->getMessage()];
        }
        catch (\Exception $exception)
        {
            return ['error' => true, 'message' => $exception->getMessage()];
        }
    }
}
