<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 6/4/17
 * Time: 11:19 AM
 */
namespace Omnyfy\Vendor\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Hello extends Command
{
    protected function configure()
    {
        $this->setName('omnyfy:vendor:hello');
        $this->setDescription('Say Hello');
        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        $output->writeln('Hello! It\'s me.');
    }
}