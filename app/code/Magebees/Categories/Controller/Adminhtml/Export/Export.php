<?php
namespace Magebees\Categories\Controller\Adminhtml\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\DriverInterface;

class Export extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
 
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }
 
        
    public function execute()
    {
        $data = $this->getRequest()->getPost()->toarray();
        $include_sku = $data['product_sku'];
        $data_store_id = $data['store_id'];
        $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
        $extvardir = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $exportdir = '/export';
        $extvardir->create($exportdir);
        $extvardir->changePermissions($exportdir, DriverInterface::WRITEABLE_DIRECTORY_MODE);
        
        $export_file_name = "exportcategory_".date('m-d-Y_h-i-s', time()).".csv";
        $header = [];
        $header['store_code'] = '';
        $header['category_id'] = '';
        $header['name'] = '';
        $header['category_name'] = '';
        $header['is_active'] = '';
        $header['include_in_navigation_menu'] = '';
        $header['image'] = '';
        $header['description'] = '';
        $header['cms_block'] = '';
        $header['display_mode'] = '';
        $header['is_anchor'] = '';
        $header['available_sort_by'] = '';
        $header['default_sort_by'] = '';
        $header['price_step'] = '';
        $header['url_key'] = '';
        $header['page_title'] = '';
        $header['meta_keywords'] = '';
        $header['meta_description'] = '';
        $header['use_parent_category'] = '';
        $header['custom_design'] = '';
        $header['page_layout'] = '';
        $header['custom_layout'] = '';
        $header['apply_to_products'] = '';
        $header['active_from'] = '';
        $header['active_to'] = '';
        $header['sku'] = '';
        $filePath = $filesystem->getDirectoryRead(DirectoryList::VAR_DIR)
            ->getAbsolutePath("export/").$export_file_name;
        $files = fopen($filePath, "a");
        fputCsv($files, array_keys($header));
        fclose($files);
        
        $categories =  $this->_objectManager->create('Magento\Catalog\Model\Category')->getCollection()->addAttributeToSort('path', 'asc');
        
        foreach ($categories as $cat_data) {
            $cat_path = $cat_data->getData('path');
            $cat_path_data = explode('/', $cat_path);
            $cat_path_name_first = [];
            for ($i=2; $i<count($cat_path_data); $i++) {
                $cat_path_id = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($cat_path_data[$i]);
                $cat_path_name  = $cat_path_id->getData('name');
                $cat_path_name_first[] = $cat_path_name;
            }
            $cat_path_name_final = '';
            for ($i=0; $i<count($cat_path_name_first); $i++) {
                if ($i < count($cat_path_name_first)-1) {
                    $cat_path_name_final .= trim($cat_path_name_first[$i])."/";
                } else {
                    $cat_path_name_final .= trim($cat_path_name_first[$i]);
                }
            }
                 
            if ($cat_path_data[0] != "" && $cat_data->getData('level') > 1) {
                if ($data_store_id == 0) {
                    $allStores = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStores();
            
                    $store_view_data  = [];
                    $store_view_id  = [];
                    $store_view_root = [];
                    foreach ($allStores as $_eachStoreId => $val) {
                        $_storeId = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStore($_eachStoreId);
                        $store_view_data[] = $_storeId->getCode();
                        $store_view_id[] = $_storeId->getId();
                        $store_view_root[] = $_storeId->getRootCategoryId();
                    }
                } else {
                    $store_view_id  = [];
                    $store_view_data  = [];
                    $store_view_root  = [];
                    $_storeId = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStore($data_store_id);
                    $store_view_data[] = $_storeId->getCode();
                    $store_view_id[] = $_storeId->getId();
                    $store_view_root[] = $_storeId->getRootCategoryId();
                }
                
                for ($i=0; $i<count($store_view_id); $i++) {
                    $category_id = $this->_objectManager->create('Magento\Catalog\Model\Category');
                    $category_id->setStoreId($store_view_id[$i]);
                    $category_id->load($cat_data->getData('entity_id'));
                
                    $productid = '';
                    if ($include_sku) {
                        $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($cat_data->getData('entity_id'));
                        $productCollection = $category->getProductCollection();
                        $d =0;
                        $productid = '';
                        foreach ($productCollection as $_product) {
                            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($_product->getId());
                            if ($d < count($productCollection)-1) {
                                $productid .= $_product->getSku()."|";
                            } else {
                                $productid .= $_product->getSku();
                            }
                            $d++;
                        }
                    }
                    
                    $path = explode('/', $category_id->getData('path'));
                    if ($path[1] == $store_view_root[$i]) {
                        $paths = Count($path);
                        if ($paths > 3) {
                            $cat_path_name_first = [];
                            for ($c=1; $c<count($path); $c++) {
                                $cat_path_id = $this->_objectManager->create('Magento\Catalog\Model\Category')->setStoreId($store_view_id[$i])->load($path[$c]);
                                $cat_path_name  = $cat_path_id->getData('name');
                                $cat_path_name_first[] = $cat_path_name;
                            }
                            $cat_path_name_finals = '';
                            for ($d=1; $d<count($cat_path_name_first); $d++) {
                                if ($d < count($cat_path_name_first)-1) {
                                    $cat_path_name_finals .= trim($cat_path_name_first[$d])."/";
                                } else {
                                    $cat_path_name_finals .= trim($cat_path_name_first[$d]);
                                }
                            }
                        } else {
                            $cat_path_name_finals = trim($category_id->getData('name'));
                        }
                    
                        $sort_by = '';
                        $available_sort_bys = $category_id->getData('available_sort_by');
                        if (!empty($available_sort_bys)) {
                            $y =0;
                            foreach ($available_sort_bys as $available_sort_by) {
                                if ($y < count($available_sort_bys)-1) {
                                    $sort_by .= $available_sort_by."|";
                                } else {
                                    $sort_by .= $available_sort_by;
                                }
                                $y++;
                            }
                        }
                        $row = [
								"store_code"=>$store_view_data[$i],
                                "category_id"=>$category_id->getId(),
                                "name"=>$category_id->getName(),
                                "category_name"=>$cat_path_name_finals,
                                "is_active"=>$category_id->getData('is_active'),
                                "include_in_navigation_menu"=>$category_id->getData('include_in_menu'),
                                "image"=>$category_id->getData('image'),
                                "description"=>$category_id->getData('description'),
                                "cms_block"=>$category_id->getData('landing_page'),
                                "display_mode"=>$category_id->getData('display_mode'),
                                "is_anchor"=>$category_id->getData('is_anchor'),
                                "available_sort_by"=>$sort_by,
                                "default_sort_by"=>$category_id->getData('default_sort_by'),
                                "price_step"=>$category_id->getData('filter_price_range'),
                                "url_key"=>$category_id->getData('url_key'),
                                "page_title"=>$category_id->getData('meta_title'),
                                "meta_keywords"=>$category_id->getData('meta_keywords'),
                                "meta_description"=>$category_id->getData('meta_description'),
                                "use_parent_category"=>$category_id->getData('custom_use_parent_settings'),
                                "custom_design"=>$category_id->getData('custom_design'),
                                "page_layout"=>$category_id->getData('page_layout'),
                                "custom_layout"=>$category_id->getData('custom_layout_update'),
                                "apply_to_products"=>$category_id->getData('custom_apply_to_products'),
                                "active_from"=>$category_id->getData('custom_design_from'),
                                "active_to"=>$category_id->getData('custom_design_to'),
                                "sku"=>$productid,
                            ];
                            $productid = '';
                        
                            $file = fopen($filePath, "a");
                            fputcsv($file, $row);
                            fclose($file);
                    }
                }
            }
        }
        $download_path = $this->getUrl('*/*/downloadexportedfile', ["file"=>$export_file_name]);
        $result = "";
        $result = "<div class='message message-success success'><div data-ui-id='messages-message-success'>Generated csv File : <b style='font-size:12px'><a href='".$download_path."' target='_blank'>".$export_file_name."</a></b></div></div>";
        $this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Categories::export');
    }
}
