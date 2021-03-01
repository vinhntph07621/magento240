<?php
/**
 * Project: Get Vendor Phone value.
 * Author: seth
 * Date: 14/2/20
 * Time: 11:51 am
 **/

namespace Omnyfy\Vendor\Ui\Component\Listing\Column;

use \Omnyfy\Vendor\Api\VendorRepositoryInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Telephone
 * @package Omnyfy\Vendor\Ui\Component\Listing\Column
 */
class Telephone extends Column
{
    /**
     * @var VendorRepositoryInterface
     */
    protected $_vendorRepository;

    /**
     * Telephone constructor.
     * 
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param VendorRepositoryInterface $vendorRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        VendorRepositoryInterface $vendorRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_vendorRepository = $vendorRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {

                if (isset($item['entity_id'])) {
                    $vendor = $this->getVendorById($item['entity_id']);
                    $item[$fieldName] = $vendor->getData('phone');
                } else {
                    $item[$fieldName] = '';
                }
            }
        }
        return $dataSource;
    }

    public function getVendorById($id){
        try {
            return $this->_vendorRepository->getById($id);
        } catch (\Exception $exception){
            return null;
        }
    }

}