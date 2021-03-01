<?php
/**
 * Project: Core
 * User: jing
 * Date: 2/9/19
 * Time: 12:09 pm
 */
namespace Omnyfy\Core\Model\Mail;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Mail\MessageInterface;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    public function addAttachment($filePath, $fileNameInEmail, $ics = null)
    {
        $type = strtolower($this->loadFileType($filePath));

        switch($type) {
            case 'pdf':
                $this->message->createAttachment(
                    file_get_contents($filePath),
                    'application/pdf',
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    $fileNameInEmail
                );
                break;
            case 'csv':
                $this->message->createAttachment(
                    file_get_contents($filePath),
                    'text/csv',
                    \Zend_Mime::DISPOSITION_INLINE,
                    \Zend_Mime::ENCODING_BASE64,
                    $fileNameInEmail
                );
                break;
            case 'ics':
                $this->message->createAttachment(
                    $ics,
                    'text/calendar',
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    $fileNameInEmail);
                break;
        }

        return $this;
    }

    public function addContentAttachment($content, $type, $nameInEmail, $disposition=\Zend_Mime::DISPOSITION_ATTACHMENT)
    {
        $this->message->createAttachment(
            $content,
            $type,
            $disposition,
            \Zend_Mime::ENCODING_BASE64,
            $nameInEmail
        );

        return $this;
    }

    protected function loadFileType($filePath)
    {
        return pathinfo($filePath, PATHINFO_EXTENSION);
    }

    protected function prepareMessage()
    {
        $template = $this->getTemplate();
        $types = [
            TemplateTypesInterface::TYPE_TEXT => MessageInterface::TYPE_TEXT,
            TemplateTypesInterface::TYPE_HTML => MessageInterface::TYPE_HTML,
        ];

        $body = $template->processTemplate();
        $this->message->setMessageType($types[$template->getType()])
            ->setBody($body)
            ->setSubject(html_entity_decode($template->getSubject(), ENT_QUOTES));

        if (array_key_exists('attachments', $this->templateVars)) {
            $attachments = $this->templateVars['attachments'];
            if (!empty($attachments)) {
                foreach($attachments as $attachment) {
                    if (!array_key_exists('content', $attachment)
                        || !array_key_exists('type', $attachment)
                        || !array_key_exists('name', $attachment)
                    ) {
                        continue;
                    }
                    $disposition = isset($attachment['disposition']) ? $attachment['disposition'] : \Zend_Mime::DISPOSITION_ATTACHMENT;
                    if ($disposition !== \Zend_Mime::DISPOSITION_ATTACHMENT && $disposition !== \Zend_Mime::DISPOSITION_INLINE) {
                        $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT;
                    }
                    $this->addContentAttachment(
                        $attachment['content'],
                        $attachment['type'],
                        $attachment['name'],
                        $disposition
                    );
                }
            }
        }
        return $this;
    }
}
 