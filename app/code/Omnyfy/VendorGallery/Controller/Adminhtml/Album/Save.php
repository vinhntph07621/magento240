<?php
namespace Omnyfy\VendorGallery\Controller\Adminhtml\Album;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Omnyfy\VendorGallery\Model\Item;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Omnyfy_VendorGallery::vendor_gallery_update';

    /**
     * @var \Omnyfy\VendorGallery\Model\AlbumFactory
     */
    protected $albumFactory;

    /**
     * @var \Omnyfy\VendorGallery\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Omnyfy\VendorGallery\Model\Album\Item\Processor
     */
    protected $itemProcessor;

    /**
     * @var \Omnyfy\VendorGallery\Model\ResourceModel\ItemFactory
     */
    protected $itemResourceFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Omnyfy\VendorGallery\Model\AlbumFactory $albumFactory
     * @param \Omnyfy\VendorGallery\Model\ItemFactory $itemFactory
     * @param \Omnyfy\VendorGallery\Model\ResourceModel\ItemFactory $itemResourceFactory
     * @param \Omnyfy\VendorGallery\Model\Album\Item\Processor $itemProcessor
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        \Omnyfy\VendorGallery\Model\AlbumFactory $albumFactory,
        \Omnyfy\VendorGallery\Model\ItemFactory $itemFactory,
        \Omnyfy\VendorGallery\Model\ResourceModel\ItemFactory $itemResourceFactory,
        \Omnyfy\VendorGallery\Model\Album\Item\Processor $itemProcessor,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->albumFactory = $albumFactory;
        $this->itemFactory = $itemFactory;
        $this->itemProcessor = $itemProcessor;
        $this->itemResourceFactory = $itemResourceFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            $data = $this->getRequest()->getPostValue();
            $id = (int)$this->getRequest()->getParam('id');

            try {
                $model = $this->albumFactory->create();

                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong album is specified.'));
                    }
                }

                $model->addData($data);

                $this->_session->setPageData($model->getData());

                $model->save();
                if (!empty($data['location_ids'])) {
                    $model->saveAlbumLocation($data['location_ids']);
                }
                if (isset($data['album']['media_gallery'])) {
                    $itemIdsToDelete = [];
                    $itemIdToUpdate = [];
                    foreach ($data['album']['media_gallery']['images'] as &$item) {

                        if (empty($item['entity_id']) && $item['removed'] == 1) {
                            continue;
                        }

                        $item = $this->processData($item, $model, $data);

                        if (empty($item['entity_id'])) {
                            // Add new item
                            unset($item['entity_id']);
                            $modelItem = $this->itemFactory->create();
                            $modelItem->addData($item)->save();
                        } else {
                            if ($item['removed'] == 1) {
                                // Add id to remove
                                $itemIdsToDelete[] = $item['entity_id'];
                            } else {
                                // Add item data to update
                                $itemDataToUpdate = $item;
                                unset($itemDataToUpdate['entity_id']);
                                $itemIdToUpdate[$item['entity_id']] = $itemDataToUpdate;
                            }
                        }
                    }
                    $itemResource = $this->itemResourceFactory->create();
                    $itemResource->deleteItemByIds($itemIdsToDelete);
                    $itemResource->updateItemsData($itemIdToUpdate);
                }

                $this->messageManager->addSuccessMessage(__('You saved the album id %1.', $model->getId()));
                $this->_session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (NoSuchEntityException $e1) {
                $this->messageManager->addErrorMessage('Invalid album type selected');
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('*/*/new');
                }
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $el) {
                $this->messageManager->addErrorMessage($el->getMessage());

                if (!empty($id)) {
                    $this->_redirect('*/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('*/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    $e->getMessage()
                );
                $this->logger->critical($e);
                $this->_session->setPageData($data);
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('*/*/new');
                }
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * @param array $item
     * @param \Omnyfy\VendorGallery\Model\Album $model
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processData($item, $model, $data)
    {
        $item['album_id'] = $model->getId();
        $item['status'] = $item['disabled'] == 0 ? 1 : 0;

        if ($item['media_type'] == 'image') {
            $item['type'] = Item::TYPE_IMAGE;
            $item['url'] = $itemName  = $this->itemProcessor->addImage(
                $model,
                $item['file']
            );
            $item['is_thumbnail'] = $data['album']['thumbnail'] == $item['file'] ? 1 : 0;
        } else {
            $item['type'] = Item::TYPE_VIDEO;
            $item['preview_image'] = $itemName  = $this->itemProcessor->addImage(
                $model,
                $item['file']
            );
            $item['is_thumbnail'] = $data['album']['thumbnail'] == $item['preview_image'] ? 1 : 0;
            $item['url'] = $item['video_url'];
        }
        return $item;
    }
}
