<?php

namespace Omnyfy\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Queue extends AbstractHelper
{
    const TABLE_NAME = 'omnyfy_core_queue';

    protected $resource;

    public function __construct(
        Context $context,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->resource = $resource;
        parent::__construct($context);
    }

    public function sendMsgToQueue($topic, $message)
    {
        $conn = $this->resource->getConnection('core_write');

        $queueTable = $this->resource->getTableName('omnyfy_core_queue');

        $conn->insert($queueTable, [
            'id' => new \Zend_Db_Expr('NULL'),
            'topic' => strtolower($topic),
            'message' => $message,
            'status' => 'pending',
        ]);
    }

    public function sendDataToQueue($topic, $data)
    {
        $this->sendMsgToQueue($topic, json_encode($data));
    }

    public function takeMsgFromQueue($topic) {
        $conn = $this->resource->getConnection('core_write');

        $queueTable = $this->resource->getTableName('omnyfy_core_queue');

        $select = $conn->select()->from($queueTable)
            ->where('topic=?', $topic)
            ->where('status=?', 'pending')
            ->order('id')
            ->limit(1)
        ;

        $row = $conn->fetchRow($select);

        if (empty($row)) {
            return false;
        }

        $this->updateQueueMsgStatus($row['id'], 'processing');

        return $row;
    }

    public function takeDataFromQueue($topic, &$id)
    {
        $row = $this->takeMsgFromQueue($topic);
        if (empty($row)) {
            return false;
        }

        if (!isset($row['id']) || empty($row['id'])) {
            return false;
        }

        try {
            if (!isset($row['message']) || empty($row['message'])) {
                $this->updateQueueMsgStatus($row['id'], 'blocking');
                return false;
            }

            $id = intval($row['id']);
            $data = json_decode($row['message'], true);
            return $data;
        }
        catch(\Exception $e) {
            $this->updateQueueMsgStatus($row['id'], 'blocking');
        }

        return false;
    }

    public function updateQueueMsgStatus($id, $status)
    {
        $conn = $this->resource->getConnection('core_write');

        $queueTable = $this->resource->getTableName('omnyfy_core_queue');

        $conn->update($queueTable, ['status' => strtolower($status)], $conn->quoteInto('id=?', $id));
    }

    public function putDelayItemsBack($topic)
    {
        $conn = $this->resource->getConnection('core_write');

        $queueTable = $this->resource->getTableName('omnyfy_core_queue');

        $conn->update($queueTable,
            ['status' => 'pending'],
            [
                'topic=?' => $topic,
                'status=?' => 'delay'
            ]
        );
    }
}