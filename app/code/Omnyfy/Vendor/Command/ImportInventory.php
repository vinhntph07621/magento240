<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 18/4/17
 * Time: 2:10 PM
 */

namespace Omnyfy\Vendor\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ImportInventory extends Command
{
    protected $csvProcessor;

    protected $inventoryResource;

    protected $productCollectionFactory;

    protected $locationCollectionFactory;

    protected $vendorCollectionFactory;

    protected $vendorResource;

    protected $appState;

    protected $config;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Omnyfy\Vendor\Model\Resource\Inventory $inventoryResource,
        \Magento\Framework\File\Csv $csvProcessor,
        \Omnyfy\Vendor\Model\Config $config
    )
    {
        $this->appState = $state;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        $this->vendorResource = $vendorResource;
        $this->inventoryResource = $inventoryResource;
        $this->csvProcessor = $csvProcessor;
        $this->config = $config;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('omnyfy:vendor:import_inventory');
        $this->setDescription('Import product location relation');
        $this->addArgument('filename', InputArgument::REQUIRED, 'Filename (abcd.csv)');
        $this->addArgument('qty', InputArgument::OPTIONAL, 'Default Qty');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('Someone else is running this command.');
            return;
        }

        try{
            $code = $this->appState->getAreaCode();
        }
        catch(\Exception $e) {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        }
        
        $output->writeln('Import Location product relation');

        //load all location
        $locations = [];
        $locationCollection = $this->locationCollectionFactory->create();
        foreach($locationCollection as $location) {
            $locations[$location->getId()] = $location->getVendorId();
        }

        //load all vendor
        $vendorCollection = $this->vendorCollectionFactory->create();
        $vendors = $vendorCollection->getAllIds();

        $filename = $input->getArgument('filename');
        $output->writeln('Loading file '.$filename);
        $defaultQty = $input->getArgument('qty');
        $defaultQty = empty($defaultQty) ? 0 : intval($defaultQty);
        $csvRows = $this->csvProcessor->getData($filename);

        $productIdsToLocation = [];
        $productIdsToVendor = [];
        $skus = [];
        foreach($csvRows as $key => $row) {
            if (0== $key) continue;

            if (empty($row[0])) {
                $output->writeln('SKU missing on line '. ($key +1));
                continue;
            }

            if (empty($row[1])) {
                $output->writeln('Location Id missing on line '.($key + 1));
                continue;
            }
            $sku = $row[0];
            $locationId = $row[1];
            $qty = isset($row[2]) ? intval($row[2]) : $defaultQty;

            if (!array_key_exists($locationId, $locations)) {
                $output->writeln('Location '. $locationId . ' not exist on line '.($key + 1));
                continue;
            }
            $vendorId = $locations[$locationId];
            if (!in_array($vendorId, $vendors)) {
                $output->writeln('Invalid Vendor ID '. $vendorId . ' for location '.$locationId . ' on line '.($key + 1));
                continue;
            }

            $skus[$sku] = [
                'location_id' => $locationId,
                'vendor_id' => $vendorId,
                'qty' => $qty,
                'line' => $key + 1
            ];
        }

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addFieldToFilter('sku', ['in' => array_keys($skus)]);

        $skuToIds = [];
        foreach($productCollection as $product) {
            $skuToIds[$product->getSku()] = $product->getId();
        }
        $productCollection->clear();
        reset($locations);
        reset($vendors);

        $zendDbExprNull = new \Zend_Db_Expr('NULL');
        foreach($skus as $sku => $arr) {
            if (!array_key_exists($sku, $skuToIds)) {
                $output->writeln('Product '.$sku.' in line '. $arr['line'] . ' not exist');
                continue;
            }

            $locationId = $arr['location_id'];
            $vendorId = $arr['vendor_id'];
            $productId = $skuToIds[$sku];

            if ($this->config->isVendorShareProducts()) {
                $productIdsToVendor[$productId] = $vendorId;
            }
            else {
                if (!array_key_exists($skuToIds[$sku], $productIdsToVendor) ) {
                    $productIdsToVendor[$productId] = $vendorId;
                }
                else{
                    $output->writeln('Product '.$sku. ' in line '. $arr['line'] . ' already assigned to Vendor '. $productIdsToVendor[$productId]);
                    continue;
                }
            }

            $productIdsToLocation[] = [
                'inventory_id' => $zendDbExprNull,
                'product_id' => $productId,
                'location_id' => $locationId,
                'qty' => $arr['qty']
            ];
        }

        $this->inventoryResource->bulkSave($productIdsToLocation);

        $productIdToVendorId = [];
        foreach($productIdsToVendor as $productId => $vendorId) {
            $productIdToVendorId[] = [
                'product_id' => $productId,
                'vendor_id' => $vendorId
            ];
        }
        $this->vendorResource->saveProductRelation($productIdToVendorId);

        $output->writeln('Done');
    }
}
