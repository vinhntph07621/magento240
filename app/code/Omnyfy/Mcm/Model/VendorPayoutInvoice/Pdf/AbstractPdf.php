<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile

namespace Omnyfy\Mcm\Model\VendorPayoutInvoice\Pdf;

use Magento\Framework\App\Filesystem\DirectoryList;
use Omnyfy\Mcm\Helper\Data as HelperData;

/**
 *  PDF abstract model
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractPdf extends \Magento\Framework\DataObject {

    /**
     * Y coordinate
     *
     * @var int
     */
    public $y;

    /**
     * Item renderers with render type key
     * model    => the model name
     * renderer => the renderer model
     *
     * @var array
     */
    public $_renderers = [];

    /**
     * Predefined constants
     */
    const XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID = 'sales_pdf/invoice/put_order_id';
    const XML_PATH_SALES_PDF_SHIPMENT_PUT_ORDER_ID = 'sales_pdf/shipment/put_order_id';
    const XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID = 'sales_pdf/creditmemo/put_order_id';

    /**
     * Zend PDF object
     *
     * @var \Zend_Pdf
     */
    public $_pdf;

    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    public $_paymentData;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    public $string;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    public $_localeDate;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $_scopeConfig;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    public $_mediaDirectory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    public $_rootDirectory;

    /**
     * @var Config
     */
    public $_pdfConfig;

    /**
     * @var \Magento\Sales\Model\Order\Pdf\Total\Factory
     */
    public $_pdfTotalFactory;

    /**
     * @var \Magento\Sales\Model\Order\Pdf\ItemsFactory
     */
    public $_pdfItemsFactory;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    public $inlineTranslation;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    public $addressRenderer;

    /**
     * @var \Omnyfy\Vendor\Model\Config
     */
    public $_vendorConfigHelper;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Config $pdfConfig
     * @param Total\Factory $pdfTotalFactory
     * @param ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Omnyfy\Vendor\Model\Config $vendorConfigHelper,
        HelperData $helper,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->_paymentData = $paymentData;
        $this->_localeDate = $localeDate;
        $this->string = $string;
        $this->_scopeConfig = $scopeConfig;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->_pdfConfig = $pdfConfig;
        $this->_pdfTotalFactory = $pdfTotalFactory;
        $this->_pdfItemsFactory = $pdfItemsFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->regionFactory = $regionFactory;
        $this->countryFactory = $countryFactory;
        $this->_vendorConfigHelper = $vendorConfigHelper;
        $this->_mcmHelper = $helper;
        parent::__construct($data);
    }

    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param  string $string
     * @param  \Zend_Pdf_Resource_Font $font
     * @param  float $fontSize Font size in points
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize) {
        $drawingString = '"libiconv"' == ICONV_IMPL ? iconv(
                        'UTF-8', 'UTF-16BE//IGNORE', $string
                ) : @iconv(
                        'UTF-8', 'UTF-16BE', $string
        );

        $characters = [];
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = ord($drawingString[$i++]) << 8 | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = array_sum($widths) / $font->getUnitsPerEm() * $fontSize;
        return $stringWidth;
    }

    /**
     * Calculate coordinates to draw something in a column aligned to the right
     *
     * @param  string $string
     * @param  int $x
     * @param  int $columnWidth
     * @param  \Zend_Pdf_Resource_Font $font
     * @param  int $fontSize
     * @param  int $padding
     * @return int
     */
    public function getAlignRight($string, $x, $columnWidth, \Zend_Pdf_Resource_Font $font, $fontSize, $padding = 5) {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + $columnWidth - $width - $padding;
    }

    /**
     * Calculate coordinates to draw something in a column aligned to the center
     *
     * @param  string $string
     * @param  int $x
     * @param  int $columnWidth
     * @param  \Zend_Pdf_Resource_Font $font
     * @param  int $fontSize
     * @return int
     */
    public function getAlignCenter($string, $x, $columnWidth, \Zend_Pdf_Resource_Font $font, $fontSize) {
        $width = $this->widthForStringUsingFontSize($string, $font, $fontSize);
        return $x + round(($columnWidth - $width) / 2);
    }

    /**
     * Insert logo to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param null $store
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function insertLogo(&$page, $store = null) {
        $this->y = $this->y ? $this->y : 815;
        $image = $this->_scopeConfig->getValue(
                'sales/identity/logo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store
        );
        if ($image) {
            $imagePath = '/sales/store/logo/' . $image;
            if ($this->_mediaDirectory->isFile($imagePath)) {
                $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
                $top = 830;
                //top border of the page
                $widthLimit = 270;
                //half of the page width
                $heightLimit = 270;
                //assuming the image is not a "skyscraper"
                $width = $image->getPixelWidth();
                $height = $image->getPixelHeight();

                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width = $widthLimit;
                }

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 25;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 10;
            }
        }
    }

    /**
     * Insert address to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param null $store
     * @return void
     */
    public function insertAddress(&$page, $store = null) {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $font = $this->_setFontRegular($page, 10);
        $page->setLineWidth(0);
        $this->y = $this->y ? $this->y : 815;
        $top = 815;
        foreach (explode(
                "\n", $this->_scopeConfig->getValue(
                        'sales/identity/address', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store
                )
        ) as $value) {
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(
                            trim(strip_tags($_value)), $this->getAlignRight($_value, 130, 440, $font, 10), $top, 'UTF-8'
                    );
                    $top -= 10;
                }
            }
        }
        $this->y = $this->y > $top ? $top : $this->y;
    }

    public function MOdetails() {
        
    }

    /**
     * Format address
     *
     * @param  string $address
     * @return array
     */
    public function _formatAddress($address) {
        $return = [];
        foreach (explode('|', $address) as $str) {
            foreach ($this->string->split($str, 45, true, true) as $part) {
                if (empty($part)) {
                    continue;
                }
                $return[] = $part;
            }
        }
        return $return;
    }

    /**
     * Calculate address height
     *
     * @param  array $address
     * @return int Height
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function _calcAddressHeight($address) {
        $y = 0;
        foreach ($address as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 40, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $y += 15;
                }
            }
        }
        return $y;
    }

    /**
     * Insert order to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool $putOrderId
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function insertOrder(&$page, $invoiceData) {

        $moName = $this->_scopeConfig->getValue(
            'general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $storeCity = $this->_scopeConfig->getValue(
            'general/store_information/city', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $storeRegionName = $this->getRegionName($this->_scopeConfig->getValue(
            'general/store_information/region_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
        $storePostcode = $this->_scopeConfig->getValue(
            'general/store_information/postcode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $storeCountryName = $this->getCountryName($this->_scopeConfig->getValue(
            'general/store_information/country_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
        $storePhone = $this->_scopeConfig->getValue(
            'general/store_information/phone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $storeVAT = $this->_vendorConfigHelper->getMoAbn();

        $storeAddressLine1 = $this->_scopeConfig->getValue(
            'general/store_information/street_line1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $storeAddressLine2 = $this->_scopeConfig->getValue(
            'general/store_information/street_line2', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $storeAddress = [
            $this->_scopeConfig->getValue(
                'general/store_information/street_line1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            $this->_scopeConfig->getValue(
                'general/store_information/street_line2', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            ($storeCity ? $storeCity . ', ' : '') . ($storeRegionName ? $storeRegionName . ', ' : '') . $storePostcode,
            $storeCountryName,
            $storePhone ? 'T: ' . $storePhone : ''
        ];

        $storeVAT = $this->_vendorConfigHelper->getMoAbn();
        $lines[0][] = ['text' => $moName, 'feed' => 35, 'font' => 'bold', 'font_size' => 25];

        $this->y -= 15;
        $lineBlock = ['lines' => $lines, 'height' => 10];
        $page = $this->drawLineBlocks($page, [$lineBlock]);

        $lines2[0][] = ['text' => 'Invoice to: ', 'feed' => 35, 'font' => 'bold', 'font_size' => 10];
        $lines2[1][] = ['text' => $invoiceData->getVendorName(), 'feed' => 35, 'font' => 'normal', 'font_size' => 10];

        $lines2[0][] = ['text' => 'Invoice from: ', 'feed' => 570, 'font' => 'bold', 'font_size' => 10, 'align' => 'right'];
        $lines2[1][] = ['text' => $moName, 'feed' => 570, 'font' => 'normal', 'font_size' => 10, 'align' => 'right'];
        $lines2[2][] = ['text' => 'ABN: ' . $storeVAT, 'feed' => 570, 'font' => 'normal', 'font_size' => 10, 'align' => 'right'];
        $lines2[3][] = ['text' => '', 'feed' => 570, 'font' => 'normal', 'font_size' => 10, 'align' => 'right'];
        $lines2[4][] = ['text' => $storeAddressLine1 . ' ' . $storeAddressLine2, 'feed' => 570, 'font' => 'normal', 'font_size' => 10, 'align' => 'right'];
        $lines2[5][] = ['text' => $storeCity . ' ' . $storePostcode . ' ' . $storeRegionName, 'feed' => 570, 'font' => 'normal', 'font_size' => 10, 'align' => 'right'];
        $lines2[6][] = ['text' => $storeCountryName, 'feed' => 570, 'font' => 'normal', 'font_size' => 10, 'align' => 'right'];

        $this->y -= 30;
        $lineBlock2 = ['lines' => $lines2, 'height' => 10];
        $page = $this->drawLineBlocks($page, [$lineBlock2]);

        $lines3[0][] = ['text' => 'Tax Invoice', 'feed' => 35, 'font' => 'bold', 'font_size' => 12];
        $lines3[1][] = ['text' => 'Invoice #: ' . $invoiceData->getIncrementId(), 'feed' => 35, 'font' => 'normal', 'font_size' => 10];
        $lines3[2][] = ['text' => 'Invoice Date: ' . $this->getDateWithFormat($invoiceData->getCreatedAt(), 'd M Y'), 'feed' => 35, 'font' => 'normal', 'font_size' => 10];

        $this->y -= 30;
        $lineBlock3 = ['lines' => $lines3, 'height' => 10];
        $page = $this->drawLineBlocks($page, [$lineBlock3]);


        $this->insertDocumentNumber($page, __('Invoice # ') . $invoiceData->getIncrementId()); //$invoice->getIncrementId()

        $vendorAddress = [
            $invoiceData->getVendorName(),
            $invoiceData->getVendorAddress(),
            $invoiceData->getVendorPhone() ? 'T: ' . $invoiceData->getVendorPhone() : '',
            $invoiceData->getVendorAbn() ? 'ABN: ' . $invoiceData->getVendorAbn() : ''
        ];
        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;
    }

    /**
     * Insert title and number for concrete document type
     *
     * @param  \Zend_Pdf_Page $page
     * @param  string $text
     * @return void
     */
    public function insertDocumentNumber(\Zend_Pdf_Page $page, $text) {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page, 10);
        $docHeader = $this->getDocHeaderCoordinates();
        $page->drawText($text, 35, $docHeader[1] - 15, 'UTF-8');
    }

    /**
     * Sort totals list
     *
     * @param  array $a
     * @param  array $b
     * @return int
     */
    public function _sortTotalsList($a, $b) {
        if (!isset($a['sort_order']) || !isset($b['sort_order'])) {
            return 0;
        }

        if ($a['sort_order'] == $b['sort_order']) {
            return 0;
        }

        return $a['sort_order'] > $b['sort_order'] ? 1 : -1;
    }

    /**
     * Return total list
     *
     * @return \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal[] Array of totals
     */
    public function _getTotalsList() {
        $totals = $this->_pdfConfig->getTotals();
        usort($totals, [$this, '_sortTotalsList']);
        $totalModels = [];
        foreach ($totals as $totalInfo) {
            $class = empty($totalInfo['model']) ? null : $totalInfo['model'];
            $totalModel = $this->_pdfTotalFactory->create($class);
            $totalModel->setData($totalInfo);
            $totalModels[] = $totalModel;
        }

        return $totalModels;
    }

    /**
     * Insert totals to pdf page
     *
     * @param  \Zend_Pdf_Page $page
     * @param  \Magento\Sales\Model\AbstractModel $source
     * @return \Zend_Pdf_Page
     */
    public function insertTotals($page, $source) {
        $lines[0][0] = ['text' => __('Order Total (incl. tax):'), 'feed' => 430, 'align' => 'right', 'font' => 'bold'];
        $lines[0][1] = ['text' => __($this->currency($source->getOrdersTotalInclTax())), 'feed' => 560, 'align' => 'right', 'font' => 'bold'];
        $lines[1][0] = ['text' => __('Tax Included in Order:'), 'feed' => 430, 'align' => 'right', 'font' => 'bold'];
        $lines[1][1] = ['text' => __($this->currency($source->getOrdersTotalTax())), 'feed' => 560, 'align' => 'right', 'font' => 'bold'];
        $this->y -= 30;
        $lineBlock = ['lines' => $lines, 'height' => 20];
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0)); //black
        $page->setLineWidth(0.5);
        $page->drawLine(300, $this->y, 560, $this->y);

        $lines2[0][] = ['text' => __('Fees Total Payable (incl. tax):'), 'feed' => 430, 'align' => 'right', 'font' => 'bold'];
        $lines2[0][] = ['text' => __($this->currency($source->getFeesTotalInclTax())), 'feed' => 560, 'align' => 'right', 'font' => 'bold'];
        $lines2[1][] = ['text' => __('Tax Included in Fees:'), 'feed' => 430, 'align' => 'right', 'font' => 'bold'];
        $lines2[1][] = ['text' => __($this->currency($source->getFeesTotalTax())), 'feed' => 560, 'align' => 'right', 'font' => 'bold'];
        $this->y -= 30;
        $lineBlock = ['lines' => $lines2, 'height' => 20];
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0)); //black
        $page->setLineWidth(0.5);
        $page->drawLine(300, $this->y, 560, $this->y);

        $lines3[0][] = ['text' => __('Total Earnings (incl. tax):'), 'feed' => 430, 'align' => 'right', 'font' => 'bold'];
        $lines3[0][] = ['text' => __($this->currency($source->getTotalEarningInclTax())), 'feed' => 560, 'align' => 'right', 'font' => 'bold'];

        $this->y -= 30;
        $lineBlock = ['lines' => $lines3, 'height' => 20];
        $page = $this->drawLineBlocks($page, [$lineBlock]);

        $page->drawLine(30, $this->y, 570, $this->y);

        return $page;
    }

    /**
     * Insert totals to pdf page
     *
     * @param  \Zend_Pdf_Page $page
     * @param  \Magento\Sales\Model\AbstractModel $source
     * @return \Zend_Pdf_Page
     */
    public function insertMessage($page, $source) {
        $moName = $this->_scopeConfig->getValue(
            'general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $lines1[0][] = ['text' => 'Invoice paid via Stripe', 'feed' => 35, 'font' => 'bold', 'font_size' => 12];

        $this->y -= 30;
        $lineBlock1 = ['lines' => $lines1, 'height' => 15];
        $page = $this->drawLineBlocks($page, [$lineBlock1]);

        $lines[0][] = ['text' => $moName . ', on your behalf, charged the customer for the above orders.', 'feed' => 35, 'font' => 'normal', 'font_size' => 10];
        $lines[1][] = ['text' => __('The total amount for the orders in this invoice has been released from Stripe.'), 'feed' => 35, 'font' => 'normal', 'font_size' => 10];

        $this->y -= 30;
        $lineBlock = ['lines' => $lines, 'height' => 15];
        $page = $this->drawLineBlocks($page, [$lineBlock]);

        return $page;
    }

    /**
     * Parse item description
     *
     * @param  \Magento\Framework\DataObject $item
     * @return array
     */
    public function _parseItemDescription($item) {
        $matches = [];
        $description = $item->getDescription();
        if (preg_match_all('/<li.*?>(.*?)<\/li>/i', $description, $matches)) {
            return $matches[1];
        }

        return [$description];
    }

    /**
     * Before getPdf processing
     *
     * @return void
     */
    public function _beforeGetPdf() {
        $this->inlineTranslation->suspend();
    }

    /**
     * After getPdf processing
     *
     * @return void
     */
    public function _afterGetPdf() {
        $this->inlineTranslation->resume();
    }

    /**
     * Format option value process
     *
     * @param  array|string $value
     * @param  \Magento\Sales\Model\Order $order
     * @return string
     */
    public function _formatOptionValue($value, $order) {
        $resultValue = '';
        if (is_array($value)) {
            if (isset($value['qty'])) {
                $resultValue .= sprintf('%d', $value['qty']) . ' x ';
            }

            $resultValue .= $value['title'];

            if (isset($value['price'])) {
                $resultValue .= " " . $order->formatPrice($value['price']);
            }
            return $resultValue;
        } else {
            return $value;
        }
    }

    /**
     * Initialize renderer process
     *
     * @param string $type
     * @return void
     */
    public function _initRenderer($type) {
        $rendererData = $this->_pdfConfig->getRenderersPerProduct($type);
        foreach ($rendererData as $productType => $renderer) {
            $this->_renderers[$productType] = ['model' => $renderer, 'renderer' => null];
        }
    }

    /**
     * Retrieve renderer model
     *
     * @param  string $type
     * @return \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _getRenderer($type) {
        if (!isset($this->_renderers[$type])) {
            $type = 'default';
        }

        if (!isset($this->_renderers[$type])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We found an invalid renderer model.'));
        }

        if (is_null($this->_renderers[$type]['renderer'])) {
            $this->_renderers[$type]['renderer'] = $this->_pdfItemsFactory->get($this->_renderers[$type]['model']);
        }

        return $this->_renderers[$type]['renderer'];
    }

    /**
     * Public method of public @see _getRenderer()
     *
     * Retrieve renderer model
     *
     * @param  string $type
     * @return \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
     */
    public function getRenderer($type) {
        return $this->_getRenderer($type);
    }

    /**
     * Draw Item process
     *
     * @param  \Magento\Framework\DataObject $item
     * @param  \Zend_Pdf_Page $page
     * @param  \Magento\Sales\Model\Order $order
     * @return \Zend_Pdf_Page
     */
    public function _drawItem(\Magento\Framework\DataObject $item, \Zend_Pdf_Page $page, \Magento\Sales\Model\Order $order) {
        $type = $item->getOrderItem()->getProductType();
        $renderer = $this->_getRenderer($type);
        $renderer->setOrder($order);
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);

        $renderer->draw();

        return $renderer->getPage();
    }

    /**
     * Set font as regular
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    public function _setFontRegular($object, $size = 7) {
        $font = \Zend_Pdf_Font::fontWithPath(
                        $this->_rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/FreeSerif.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    public function _setFontBold($object, $size = 7) {
        $font = \Zend_Pdf_Font::fontWithPath(
                        $this->_rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/FreeSerifBold.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    public function _setFontItalic($object, $size = 7) {
        $font = \Zend_Pdf_Font::fontWithPath(
                        $this->_rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/FreeSerifItalic.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set PDF object
     *
     * @param  \Zend_Pdf $pdf
     * @return $this
     */
    public function _setPdf(\Zend_Pdf $pdf) {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Retrieve PDF object
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Zend_Pdf
     */
    public function _getPdf() {
        if (!$this->_pdf instanceof \Zend_Pdf) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please define the PDF object before using.'));
        }

        return $this->_pdf;
    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    public function newPage(array $settings = []) {
        $pageSize = !empty($settings['page_size']) ? $settings['page_size'] : \Zend_Pdf_Page::SIZE_A4;
        $page = $this->_getPdf()->newPage($pageSize);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;

        return $page;
    }

    /**
     * Draw lines
     *
     * Draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  \Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Zend_Pdf_Page
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function drawLineBlocks(\Zend_Pdf_Page $page, array $draw, array $pageSettings = []) {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __('We don\'t recognize the draw line data. Please define the "lines" array.')
                );
            }
            $lines = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = [$column['text']];
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = \Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = [$column['text']];
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 15) {
                            $page = $this->newPage($pageSettings);
                        }

                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                } else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                            default:
                                break;
                        }
                        $page->drawText($part, $feed, $this->y - $top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }

        return $page;
    }

    /**
     * Get region name by id
     * @param type $regionId
     * @return type
     */
    public function getRegionName($regionId = null) {
        if ($regionId) {
            $region = $this->regionFactory->create()->load($regionId);
            return $region->getName();
        } else {
            return null;
        }
    }

    /**
     * Get Country name by id
     * @param type $countryId
     * @return type
     */
    public function getCountryName($countryId = null) {
        if ($countryId) {
            $country = $this->countryFactory->create()->loadByCode($countryId);
            return $country->getName();
        } else {
            return null;
        }
    }

    public function getDateWithFormat($date, $format = 'Y-m-d H:i:s') {
        return $date ? $this->_localeDate->date(new \DateTime($date))->format($format) : '';
    }

    public function currency($value) {
        return $this->_mcmHelper->formatToBaseCurrency($value);
    }

}
