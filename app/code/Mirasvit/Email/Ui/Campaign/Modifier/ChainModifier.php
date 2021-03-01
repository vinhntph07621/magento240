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
 * @package   mirasvit/module-email
 * @version   2.1.44
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Campaign\Modifier;

use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Mirasvit\Email\Api\Data\ChainInterface;
use Mirasvit\Email\Api\Data\QueueInterface;
use Mirasvit\Email\Api\Repository\QueueRepositoryInterface;

class ChainModifier implements ModifierInterface
{
    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var PoolInterface
     */
    private $modifierPool;

    /**
     * ChainModifier constructor.
     * @param QueueRepositoryInterface $queueRepository
     * @param UrlInterface $urlBuilder
     * @param PoolInterface|null $modifierPool
     */
    public function __construct(
        QueueRepositoryInterface $queueRepository,
        UrlInterface $urlBuilder,
        PoolInterface $modifierPool = null
    ) {
        $this->queueRepository = $queueRepository;
        $this->urlBuilder = $urlBuilder;
        $this->modifierPool = $modifierPool;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $data['delete_url'] = $this->urlBuilder->getUrl('email/chain/delete', [
            '_current' => 1,
            ChainInterface::ID => $data[ChainInterface::ID]
        ]);

        $data['duplicate_url'] = $this->urlBuilder->getUrl('email/chain/duplicate', [
            '_current'         => 1,
            ChainInterface::ID => $data[ChainInterface::ID],
        ]);

        $data['report'] = ['pendingCount' => $this->countPendingEmails($data[ChainInterface::ID])];

        return $data;
    }

    /**
     * @param int $chainId
     *
     * @return int
     */
    private function countPendingEmails($chainId)
    {
        $queues = $this->queueRepository->getCollection();
        $queues->addFieldToFilter(ChainInterface::ID, $chainId)
            ->addFieldToFilter(QueueInterface::STATUS, QueueInterface::STATUS_PENDING);

        return $queues->count();
    }
}
