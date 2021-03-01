<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-16
 * Time: 11:48
 */
namespace Omnyfy\Vendor\Model;


use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class VendorAttributeGroupRepository implements \Omnyfy\Vendor\Api\VendorAttributeGroupRepositoryInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeGroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var \Omnyfy\Vendor\Model\Vendor\Attribute\GroupFactory
     */
    protected $groupFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group
     */
    protected $groupResource;

    /**
     * @param \Magento\Eav\Api\AttributeGroupRepositoryInterface $groupRepository
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group $groupResource
     * @param Vendor\Attribute\GroupFactory $groupFactory
     */
    public function __construct(
        \Magento\Eav\Api\AttributeGroupRepositoryInterface $groupRepository,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group $groupResource,
        \Omnyfy\Vendor\Model\Vendor\Attribute\GroupFactory $groupFactory
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupResource = $groupResource;
        $this->groupFactory = $groupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Eav\Api\Data\AttributeGroupInterface $group)
    {
        return $this->groupRepository->save($group);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this->groupRepository->getList($searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function get($groupId)
    {
        /** @var \Omnyfy\Vendor\Model\Vendor\Attribute\Group $group */
        $group = $this->groupFactory->create();
        $this->groupResource->load($group, $groupId);
        if (!$group->getId()) {
            throw new NoSuchEntityException(__('Group with id "%1" does not exist.', $groupId));
        }
        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($groupId)
    {
        $this->delete(
            $this->get($groupId)
        );
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Eav\Api\Data\AttributeGroupInterface $group)
    {
        /** @var \Omnyfy\Vendor\Model\Vendor\Attribute\Group $group */
        if ($group->hasSystemAttributes()) {
            throw new StateException(
                __('Attribute group that contains system attributes can not be deleted')
            );
        }
        return $this->groupRepository->delete($group);
    }
}
 