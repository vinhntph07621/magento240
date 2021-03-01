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

class ImportLocation extends Command
{
    protected $csvProcessor;

    protected $locationCollectionFactory;

    protected $vendorCollectionFactory;

    protected $locationResource;

    protected $appState;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Location $locationResource,
        \Magento\Framework\File\Csv $csvProcessor
    )
    {
        $this->appState = $state;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        $this->locationResource = $locationResource;
        $this->csvProcessor = $csvProcessor;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('omnyfy:vendor:import_location');
        $this->setDescription('Import location information');
        $this->addArgument('filename', InputArgument::REQUIRED, 'Filename (abcd.csv)');
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
        
        $output->writeln('Import Location information');

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
        $csvRows = $this->csvProcessor->getData($filename);

        $zendDbExprNull = new \Zend_Db_Expr('NULL');
        $locationData = [];
        foreach($csvRows as $key => $row) {
            if (0== $key) continue;

            if (empty($row[0])) {
                $output->writeln('Location Name missing on line '. ($key +1));
                continue;
            }

            if (empty($row[1])) {
                $output->writeln('Vendor Id missing on line '.($key + 1));
                continue;
            }
            $locationName = $row[0];
            $vendorId = $row[1];

            if (!in_array($vendorId, $vendors)) {
                $output->writeln('Invalid Vendor ID '. $vendorId . ' for location '.$locationName . ' on line '.($key + 1));
                continue;
            }

            $locationData[] = [
                'location_id' => $zendDbExprNull,
                'vendor_id' => $vendorId,
                'name' => $locationName,
                'is_active' => 1
            ];
        }

        $this->locationResource->bulkSave($locationData);

        $output->writeln('Done');
    }
}
