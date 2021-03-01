<?php
/**
 * Project: Core
 * User: jing
 * Date: 2019-08-22
 * Time: 14:21
 */
namespace Omnyfy\Core\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;

class Email extends AbstractHelper
{
    protected $_transportBuilder;

    protected $_fileDriver;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Omnyfy\Core\Model\Mail\TransportBuilder $transportBuilder,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        $this->_transportBuilder = $transportBuilder;
        $this->_fileDriver = $fileDriver;
        parent::__construct($context);
    }

    public function sendEmail($templateId, $vars, $from, $to, $area, $storeId, $attachments = [], $cc = [])
    {
        $recipient = $this->parseRecipient($to);

        if (empty($recipient)) {
            //Throw exception
            throw new LocalizedException(__('Empty recipient specified. Unable to send Email.'));
        }

        $builder = $this->_transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateVars($vars)
            ->setTemplateOptions([
                'area' => $area,
                'store' => $storeId
            ])
            ->setFrom($from)
            ->addTo($recipient['email'], $recipient['name'])
        ;

        $cc = $this->parseRecipient($cc);
        if (!empty($cc)) {
            $builder->addCc($cc['email'], $cc['name']);
        }

        if (!empty($attachments)) {
            foreach($attachments as $file) {
                if (!$this->_fileDriver->isFile($file)) {
                    continue;
                }
                $fileName = str_replace(basename($file), '', $file);
                $builder->addAttachment($file, $fileName);
            }
        }

        $transport = $builder->getTransport();
        $transport->sendMessage();
    }

    protected function parseRecipient($to)
    {
        if (is_string($to)) {
            return ['email' => $to,  'name' => ''];
        }

        if (is_array($to) ) {
            if (array_key_exists('name', $to) && array_key_exists('email', $to)) {
                return $to;
            }

            foreach($to as $_sub) {
                return $this->parseRecipient($_sub);
            }
        }

        return false;
    }
}
 