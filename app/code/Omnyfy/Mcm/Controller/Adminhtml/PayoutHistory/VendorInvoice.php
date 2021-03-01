<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\PayoutHistory;

use Magento\Framework\App\Filesystem\DirectoryList;
use Omnyfy\Mcm\Model\VendorPayoutInvoiceFactory;
use Omnyfy\Mcm\Model\VendorPayoutInvoice\VendorPayoutInvoiceOrderFactory;
use Omnyfy\Mcm\Model\VendorOrderFactory;
use Omnyfy\Mcm\Helper\Data as HelperData;
use Omnyfy\Mcm\Plugin\Vendor\Model\Vendor;
use Omnyfy\Vendor\Model\Config;

class VendorInvoice extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::payout_history';
    protected $adminTitle = 'Payout History';

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_rootDirectory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    protected $_abstractPdf;

    protected $vendorPayoutInvoiceFactory;

    protected $vendorPayoutInvoiceOrderFactory;

    protected $_mcmHelper;

    protected $vendorOrderFactory;

    protected $vendorHelper;

    protected $vendorConfig;

    /**
     * Y coordinate
     *
     * @var int
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Omnyfy\Mcm\Model\VendorPayoutInvoice\Pdf\AbstractPdf $abstractPdf,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        VendorPayoutInvoiceFactory $vendorPayoutInvoiceFactory,
        VendorPayoutInvoiceOrderFactory $vendorPayoutInvoiceOrderFactory,
        VendorOrderFactory $vendorOrderFactory,
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        Config $vendorConfig,
        HelperData $helper
    ) {
        $this->_rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->_abstractPdf = $abstractPdf;
        $this->fileFactory = $fileFactory;
        $this->vendorPayoutInvoiceFactory = $vendorPayoutInvoiceFactory;
        $this->vendorPayoutInvoiceOrderFactory = $vendorPayoutInvoiceOrderFactory;
        $this->vendorOrderFactory = $vendorOrderFactory;
        $this->vendorHelper = $vendorHelper;
        $this->vendorConfig = $vendorConfig;
        $this->_mcmHelper = $helper;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute() {
        $error = 1;
        $invoiceId = $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoiceData = $this->vendorPayoutInvoiceFactory->create()->load($invoiceId);
            if (!empty($invoiceData->getData())) {

                $payoutOrderData = $this->vendorPayoutInvoiceOrderFactory->create()
                    ->getCollection()
                    ->addFieldToFilter('vendor_id', $invoiceData->getVendorId())
                    ->addFieldToFilter('invoice_id', $invoiceId)
                    ->getFirstItem();

                $vendorOrderData = $this->vendorOrderFactory->create();

                $error = 0;
                try {
                    $pdf = new \Zend_Pdf();
                    $this->_abstractPdf->_setPdf($pdf);
                    $style = new \Zend_Pdf_Style();
                    $this->_setFontBold($style, 10);
                    $page = $this->_abstractPdf->newPage();

                    /* Add address */
                    //$this->_abstractPdf->insertAddress($page);

                    /* Add head */
                    $this->_abstractPdf->insertOrder(
                            $page, $invoiceData
                    );

                    /* Add document text and number */

                    /* Add table */
                    $this->_drawHeader($page);

                    /* Add body */
                    $this->insertInvoiceBody($page, $invoiceData);

                    /* Add totals */
                    //$this->_setFontBold($page, 12);
                    $this->_abstractPdf->insertTotals($page, $invoiceData);

                    $this->_abstractPdf->insertMessage($page, $invoiceData);

                    //*/
                    $fileName = 'vendor_invoice_' . time() . '.pdf';
                    return $this->fileFactory->create(
                                    $fileName, $pdf->render(), \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR, 'application/pdf'
                    );
                } catch (\Exception $exception) {
                    $error = 1;
                    $this->messageManager->addErrorMessage(__($exception->getMessage()));
                    $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
                    $resultRedirect->setPath('omnyfy_mcm/payouthistory/index');
                    return $resultRedirect;
                }
            }
        }
        if ($error) {
            $this->messageManager->addErrorMessage(__("This Invoice doesn't exist."));
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('omnyfy_mcm/payouthistory/index');
            return $resultRedirect;
        }
    }

    protected function insertInvoiceBody($page, $invoiceData) {
        /* Add body */
        $lines = [];
        foreach ($invoiceData->getAllInvoiceOrders() as $key => $order) {
            $lines[$key][] = ['text' => __($order->getOrderIncrementId()), 'feed' => 75, 'align' => 'right'];
            $lines[$key][] = ['text' => __($this->currency($order->getOrderTotalInclTax())), 'feed' => 140, 'align' => 'right'];
            $lines[$key][] = ['text' => __($this->currency($order->getShippingTotalForOrder())), 'feed' => 200, 'align' => 'right'];
            $lines[$key][] = ['text' => __($this->currency($order->getOrderTotalTax())), 'feed' => 315, 'align' => 'right'];
            $lines[$key][] = ['text' => __($this->currency($order->getFeesTotalInclTax())), 'feed' => 375, 'align' => 'right'];
            $lines[$key][] = ['text' => __($this->currency($order->getFeesTotalTax())), 'feed' => 485, 'align' => 'right'];
            $lines[$key][] = ['text' => __($this->currency($order->getOrderTotalInclTax() - $order->getFeesTotalInclTax())), 'feed' => 565, 'align' => 'right'];
        }
        $lineBlock = ['lines' => $lines, 'height' => 20];
        $this->_abstractPdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->_abstractPdf->y -= 20;
    }

    /**
     * Set font as regular
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7) {
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
    protected function _setFontBold($object, $size = 7) {
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
    protected function _setFontItalic($object, $size = 7) {
        $font = \Zend_Pdf_Font::fontWithPath(
                        $this->_rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/FreeSerifItalic.ttf')
        );
        $object->setFont($font, $size);
        return $font;
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
        $docHeader = $this->_abstractPdf->getDocHeaderCoordinates();
        $page->drawText($text, 35, $docHeader[1] - 15, 'UTF-8');
    }

    /**
     * Draw header for order table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page) {
        /* Add table head */
        $this->_abstractPdf->y -= 15;
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));

        $page->setLineWidth(1);

        $page->drawRectangle(30, (int) $this->_abstractPdf->y, 570, (int) $this->_abstractPdf->y - 30);
        //die('test');
        $this->_abstractPdf->y -= 12;

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Order ID'), 'feed' => 70, 'align' => 'right'];
        $lines[0][] = ['text' => __('Order Total'), 'feed' => 140, 'align' => 'right']; //'align' => 'right'
        $lines[1][] = ['text' => __('(incl. tax)'), 'feed' => 140, 'align' => 'right']; //'align' => 'right'
        $lines[0][] = ['text' => __('Shipping'), 'feed' => 200, 'align' => 'right'];
        $lines[1][] = ['text' => __('(incl. tax)'), 'feed' => 200, 'align' => 'right'];
        $lines[0][] = ['text' => __('Tax Included in Order'), 'feed' => 315, 'align' => 'right'];
        $lines[0][] = ['text' => __('Fees Total'), 'feed' => 375, 'align' => 'right'];
        $lines[1][] = ['text' => __('(incl. tax)'), 'feed' => 375, 'align' => 'right'];
        $lines[0][] = ['text' => __('Tax Included in Fees'), 'feed' => 485, 'align' => 'right'];
        $lines[0][] = ['text' => __('Payout Amount'), 'feed' => 565, 'align' => 'right'];

        $lineBlock = ['lines' => $lines, 'height' => 10];

        $this->_abstractPdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_abstractPdf->y -= 20;
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
        $this->_abstractPdf->y = 800;

        return $page;
    }

    public function currency($value) {
        return $this->_mcmHelper->formatToBaseCurrency($value);
    }

}
