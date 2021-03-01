<?php
/**
 * Project: Omnyfy Core.
 * User: jing
 * Date: 25/7/18
 * Time: 6:34 PM
 */
namespace Omnyfy\Core\Command;

class Command extends \Symfony\Component\Console\Command\Command
{
    protected $_appName;

    protected function getLockFileName()
    {
        if (empty($this->_appName)) {
            $_dir = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\App\Filesystem\DirectoryList::class);
            $this->_appName = basename($_dir->getRoot());
        }
        $lockFileName = $this->_appName . '_' . str_replace('omnyfy:', '', $this->getName());
        $lockFileName = str_replace(':', '_', $lockFileName);
        $lockFileName .= '.lock';
        return $lockFileName;
    }

    protected function lock()
    {
        $lockFile = sys_get_temp_dir() . '/'
            . $this->getLockFileName();
        if (file_exists($lockFile)) {
            $pid = file_get_contents($lockFile);
            if (posix_getsid($pid) !== false) {
                return false;
            }
        }
        $flag = file_put_contents($lockFile, getmypid());
        return (false === $flag ? false : true);
    }
}