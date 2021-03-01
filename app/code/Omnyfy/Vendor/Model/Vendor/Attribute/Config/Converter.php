<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-16
 * Time: 17:04
 */
namespace Omnyfy\Vendor\Model\Vendor\Attribute\Config;

class Converter implements \Magento\Framework\Config\ConverterInterface
{
    public function convert($source)
    {
        $result = [];
        /** @var DOMNode $groupNode */
        foreach ($source->documentElement->childNodes as $groupNode) {
            if ($groupNode->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $groupName = $groupNode->attributes->getNamedItem('name')->nodeValue;
            /** @var DOMNode $groupAttributeNode */
            foreach ($groupNode->childNodes as $groupAttributeNode) {
                if ($groupAttributeNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $groupAttributeName = $groupAttributeNode->attributes->getNamedItem('name')->nodeValue;
                $result[$groupName][] = $groupAttributeName;
            }
        }
        return $result;
    }
}
 