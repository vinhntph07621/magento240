<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 29/01/2020
 * Time: 5:20 PM
 */

namespace Omnyfy\VendorDashBoard\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Generate extends \Omnyfy\Core\Command\Command
{
    protected $appState;

    protected $helperData;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\VendorDashBoard\Helper\Data $helperData,
        $name = null
    )
    {
        $this->appState = $state;
        $this->helperData = $helperData;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('omnyfy:dashboard:generate');
        $this->setDescription('Automatic generate of vendor dashboard');
        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            return;
        }
        try {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        } catch (\Exception $e) {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        }

        try {
            $output->writeln('Start');

            $this->helperData->generateDashBoards();

            $output->writeln('Done');
        } catch (\Exception $exception){
        }
    }
}