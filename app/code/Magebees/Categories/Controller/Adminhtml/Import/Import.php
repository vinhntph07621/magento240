<?php
namespace Magebees\Categories\Controller\Adminhtml\Import;

use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;
class Import extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;
    protected $resultPageFactory;
    protected $mappings = [];
    protected $_categoryCache = [];
 
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
		LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
		$this->_logger = $logger;
        parent::__construct($context);
    }
 
        
    public function execute()
    {
        $error = array();
		$formdata = $this->getRequest()->getPost()->toarray();
			
			
		$csvresult = array();
		
        $files =  $this->getRequest()->getFiles();
        if (isset($files['filename']['name']) && $files['filename']['name'] != '') {
            try {
                $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'filename']);
                $allowed_ext_array = ['csv'];
                $uploader->setAllowedExtensions($allowed_ext_array);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::VAR_DIR);
                $result = $uploader->save($mediaDirectory->getAbsolutePath('import/'));
                $path = $mediaDirectory->getAbsolutePath('import');
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $this->_redirect('*/*/index');
                return;
            }
                    
            $filename = $path.$result['file'];
            $handle = fopen($path.$result['file'], 'r');
            $data = fgetcsv($handle, filesize($filename));
            if (!$this->mappings) {
                $this->mappings = $data;
            }
                                
            $storeData = [];
            $rootData = [];
            $subcategory = [];
            $maincategory = [];
            $root_category = '';
            $rootIds = '';
            
			$store_id = $formdata['store_id'];
			if($store_id){
				$storedata = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStore($store_id);
				$rootId = $storedata->getRootCategoryId();
			}else{
				$storeManager=$this->_objectManager->create('Magento\Store\Model\StoreManagerInterface');
				$rootId = $storeManager->getStore()->getRootCategoryId();
			}
			if(isset($formdata['pointer_next']) && $formdata['pointer_next']!=1){
				$flag = false;
				fseek($handle,$formdata['pointer_next']);
			}else{
				$flag = true;
			}

			$import_behavior = $formdata['import_behavior'];
			$count = 1;          		       
            while ($data = fgetcsv($handle, filesize($filename))) {
				if($count > 50){
					break;
				}
			
            	if (!empty($data)) {
				   try {
						    
                        foreach ($data as $key => $value) {
                            $importData[$this->mappings[$key]] = addslashes($value);
                        }  
						
						if (isset($importData['category_id'])) {
							$category_id = $importData['category_id'];
						}else{
							$category_id = '';
						}	
											
						if($import_behavior == "delete"){			
							try {
								if($category_id){
									$this->_objectManager->create('Magento\Catalog\Model\Category')->load($category_id)->delete();
								}	
							} catch (\Exception $e) {
						
							}
							
						}else{
													
						
							if (isset($importData['category_name'])) {
								$rootCategory = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($rootId);
								$root_category =  trim($rootCategory->getData('name'));

								if (isset($importData['category_name'])) {
									$categories = $importData['category_name'];
								}	
								if (isset($importData['is_active'])) {
									$is_active = $importData['is_active'];
								}
								if (isset($importData['url_key'])) {
									$url_key = $importData['url_key'];
								}
								if (isset($importData['description'])) {
									$description = stripslashes($importData['description']);
								}
								if (isset($importData['page_title'])) {
									$page_title = stripslashes($importData['page_title']);
								}
								if (isset($importData['meta_keywords'])) {
									$meta_keywords = stripslashes($importData['meta_keywords']);
								}
								if (isset($importData['meta_description'])) {
									$meta_description = stripslashes($importData['meta_description']);
								}
								if (isset($importData['include_in_navigation_menu'])) {
									$include_in_navigation_menu = $importData['include_in_navigation_menu'];
								}
								if (isset($importData['display_mode'])) {
									$display_mode = $importData['display_mode'];
								}
								if (isset($importData['available_sort_by'])) {
									$available_sort_by = explode("|", $importData['available_sort_by']);
								}
								if (isset($importData['cms_block'])) {
									$cms_block = $importData['cms_block'];
								}
								if (isset($importData['is_anchor'])) {
									$is_anchor = $importData['is_anchor'];
								}
								if (isset($importData['default_product_listing'])) {
									$default_product_listing = $importData['default_product_listing'];
								}

								if (isset($importData['image'])) {
									$image = $importData['image'];
								}
								if (isset($importData['price_step'])) {
									$price_step = $importData['price_step'];
								}
								if (isset($importData['use_parent_category'])) {
									$use_parent_category = $importData['use_parent_category'];
									if ($use_parent_category == 0) {
										if (isset($importData['apply_to_products'])) {
											$apply_to_products = $importData['apply_to_products'];
										}
										if (isset($importData['custom_design'])) {
											$custom_design = $importData['custom_design'];
										}
										if (isset($importData['active_from'])) {
											$active_from = $importData['active_from'];
										}
										if (isset($importData['active_to'])) {
											$active_to = $importData['active_to'];
										}
										if (isset($importData['page_layout'])) {
											$page_layout = $importData['page_layout'];
										}
										if (isset($importData['custom_layout'])) {
											$custom_layout = stripslashes($importData['custom_layout']);
										}
									}
								}

								if (isset($importData['sku'])) {
									$product_sku = $importData['sku'];
								}
								$_categoryCache = [];
								if ($categories=="") {
									return [];
								}
								$rootPath = '1/'.$rootId;
								if (empty($this->_categoryCache[1])) {
									$collection = $this->_objectManager->create('Magento\Catalog\Model\Category')->getCollection()->addAttributeToSelect('name');
									$collection->getSelect()->where("path like '".$rootPath."/%'");
									foreach ($collection as $cat) {
										$pathArr = explode('/', $cat->getPath());
										$namePath = '';
										for ($i=2, $l=sizeof($pathArr); $i<$l; $i++) {
											$name = $collection->getItemById($pathArr[$i])->getName();
											$namePath .= (empty($namePath) ? '' : '/').trim($name);
										}
										$cat->setNamePath($namePath);
									}

									$cache = [];
									foreach ($collection as $cat) {
										$cache[strtolower($cat->getNamePath())] = $cat;
										$cat->unsNamePath();
									}
									$this->_categoryCache[1] = $cache;
								}

								$cache =& $this->_categoryCache[1];
								$catIds = [];
								$path = $rootPath;
								$namePath = '';	

								
								if ($category_id != "") {
									$cats = $this->_objectManager->create('Magento\Catalog\Model\Category')->setStoreId($store_id)->load($category_id);
									$catsdata = count($cats->getData());
									if($catsdata > 2){
										if (isset($importData['name'])) {
											$name = stripslashes($importData['name']);
											$subcategory['name'] =  trim($name);
										}

										if (isset($is_active)) {
											$subcategory['is_active'] =  $is_active;
										}
										if (isset($url_key)) {
											$subcategory['url_key'] =  $url_key;
										}
										if (isset($description)) {
											$subcategory['description'] =  $description;
										}
										if (isset($page_title)) {
											$subcategory['meta_title'] =  $page_title;
										}
										if (isset($meta_keywords)) {
											$subcategory['meta_keywords'] =  $meta_keywords;
										}
										if (isset($meta_description)) {
											$subcategory['meta_description'] =  $meta_description;
										}
										if (isset($include_in_navigation_menu)) {
											$subcategory['include_in_menu'] =  $include_in_navigation_menu;
										}
										if (isset($display_mode)) {
											$subcategory['display_mode'] =  $display_mode;
										}
										if (isset($cms_block)) {
											$subcategory['landing_page'] =  $cms_block;
										}
										if (isset($available_sort_by)) {
											$subcategory['available_sort_by'] =  $available_sort_by;
										}
										if (isset($default_product_listing)) {
											$subcategory['default_sort_by'] =  $default_product_listing;
										}
										if (isset($is_anchor)) {
											$subcategory['is_anchor'] =  $is_anchor;
										}

										if (isset($image)) {
											$this->getCategoryImageFile($image);
											$subcategory['image'] =  $image;
										}
										if (isset($price_step)) {
											$subcategory['filter_price_range'] =  $price_step;
										}
										if (isset($use_parent_category)) {
											$subcategory['custom_use_parent_settings']=     $use_parent_category;
										}
										if (isset($apply_to_products)) {
											$subcategory['custom_apply_to_products'] =  $apply_to_products;
										}
										if (isset($custom_design)) {
											$subcategory['custom_design'] =  $custom_design;
										}
										if (isset($active_from)) {
											$subcategory['custom_design_from'] =  $active_from;
										}
										if (isset($active_to)) {
											$subcategory['custom_design_to'] =  $active_to;
										}
										if (isset($page_layout)) {
											$subcategory['page_layout'] =  $page_layout;
										}
										if (isset($custom_layout)) {
											$subcategory['custom_layout_update'] =  $custom_layout;
										}
										$cats->addData($subcategory);
										$cats->save();
										if (isset($product_sku)) {
											if ($product_sku != "") {
												$product_data = explode("|", $product_sku);
												for ($e=0; $e<count($product_data); $e++) {
													$product_id = $this->_objectManager->create('Magento\Catalog\Model\Product')->getIdBySku(trim($product_data[$e]));
													if ($product_id != "") {
														$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
														$newCategories = $origCats = $product->getCategoryIds();

														if (!in_array($category_id, $origCats)) {
															$newCategories = array_merge($origCats, [$category_id]);
															$product->setCategoryIds($newCategories)->save();
														}
													}
												}
											}
										}
									}else{
										 $error[] = $importData['category_name']." >> ".$category_id." Category Id does not exist in your store.";									
									}
								} 

								if (!$category_id) {
									foreach (explode('/', $categories) as $catName) {
										$namePath .= (empty($namePath) ? '' : '/').strtolower($catName);
										if (empty($cache[$namePath])) {
											$catName = stripslashes($catName);
											if ($store_id != 0) {
												$cat = $this->_objectManager->create('Magento\Catalog\Model\Category')
														->setStoreId(0)
														->setName(trim($catName))
														->setIsActive(1)
														->setPath($path)->save();
												$cache[$namePath] = $cat;
												$cate_id = $cache[$namePath]->getId();
												$cat = $this->_objectManager->create('Magento\Catalog\Model\Category')
													->setStoreId($store_id)->load($cate_id)
													->setName(trim($catName));
											} else {
												$cat = $this->_objectManager->create('Magento\Catalog\Model\Category')
													->setStoreId($store_id)
													->setName(trim($catName))
													->setPath($path);
											}
											if (isset($is_active)) {
												$maincategory['is_active'] =  $is_active;
											}
											if (isset($url_key)) {
												$maincategory['url_key'] =  $url_key;
											}
											if (isset($description)) {
												$maincategory['description'] =  $description;
											}
											if (isset($page_title)) {
												$maincategory['meta_title'] =  $page_title;
											}
											if (isset($meta_keywords)) {
												$maincategory['meta_keywords'] =  $meta_keywords;
											}
											if (isset($meta_description)) {
												$maincategory['meta_description'] =  $meta_description;
											}

											if (isset($include_in_navigation_menu)) {
												$maincategory['include_in_menu'] =  $include_in_navigation_menu;
											}
											if (isset($display_mode)) {
												$maincategory['display_mode'] =  $display_mode;
											}
											if (isset($cms_block)) {
												$maincategory['landing_page'] =  $cms_block;
											}
											if (isset($default_product_listing)) {
												$maincategory['default_sort_by'] =  $default_product_listing;
											}
											if (isset($available_sort_by)) {
												$maincategory['available_sort_by'] =  $available_sort_by;
											}

											if (isset($is_anchor)) {
												$maincategory['is_anchor'] =  $is_anchor;
											}

											if (isset($image)) {
												$this->getCategoryImageFile($image);
												$maincategory['image'] =  $image;
											}
											if (isset($price_step)) {
												$maincategory['filter_price_range'] =  $price_step;
											}
											if (isset($use_parent_category)) {
												$maincategory['custom_use_parent_settings']=    $use_parent_category;
											}
											if (isset($apply_to_products)) {
												$maincategory['custom_apply_to_products'] =  $apply_to_products;
											}
											if (isset($custom_design)) {
												$maincategory['custom_design'] =  $custom_design;
											}
											if (isset($active_from)) {
												$maincategory['custom_design_from'] =  $active_from;
											}
											if (isset($active_to)) {
												$maincategory['custom_design_to'] =  $active_to;
											}
											if (isset($page_layout)) {
												$maincategory['page_layout'] =  $page_layout;
											}
											if (isset($custom_layout)) {
												$maincategory['custom_layout_update'] =  $custom_layout;
											}
											$cat->addData($maincategory);
											$cat->save();

											$cache[$namePath] = $cat;
											$cate_id = $cache[$namePath]->getId();

											if (isset($product_sku)) {
												if ($product_sku != "") {
													$product_data = explode("|", $product_sku);
													for ($e=0; $e<count($product_data); $e++) {
														$product_id = $this->_objectManager->create('Magento\Catalog\Model\Product')->getIdBySku(trim($product_data[$e]));
														if ($product_id != "") {
															$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
															$newCategories = $origCats = $product->getCategoryIds();
															if (!in_array($cate_id, $origCats)) {
																$newCategories = array_merge($origCats, [$cate_id]);
																$product->setCategoryIds($newCategories)->save();
															}
														}
													}
												}
											}
										}
										$catId = $cache[$namePath]->getId();
										$path .= '/'.$catId;
									}
								}
								
							}
							
						}
						
						if ($count == 50) {
							$csvresult['count'] = $count;
							$csvresult['pointer_last'] = ftell($handle);
							$next = fgets($handle);
							if(!empty($next)){
								$csvresult['no_more'] = false;
							}else{
								if($import_behavior == 'delete'){
									$this->messageManager->addSuccess(__('Categories Deleted Successfully.'));
								}else{
									$this->messageManager->addSuccess(__('Categories Imported Successfully.'));
								}
                         								
								$csvresult['no_more'] = true;
							}
						
							$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($csvresult));
							return;

						}			

                    } catch (\Exception $e) {
                        $error[] = $importData['category_name']." >> ".$e->getMessage();
                    }
                }
				
			 	$count++;	
            }
			if($import_behavior == 'delete'){
				$this->messageManager->addSuccess(__('Categories Deleted Successfully.'));
			}else{
				$this->messageManager->addSuccess(__('Categories Imported Successfully.'));
			}

			$csvresult['count'] = $count-1;
			$csvresult['pointer_last'] = ftell($handle);
			$csvresult['no_more'] = true;
			
        }
		
		
        if (!empty($error)) {
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import_export_categories.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($error);
			$this->messageManager->addError(__('There are some issues while importing the categories. Please check "var/log/import_export_categories.log" file.')); 
          
        }
		$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($csvresult));
		return;

    }
    
    function getCategoryImageFile($filename)
    {
        if ($filename) {
            $cat_dir = "pub/media/catalog/category";
            if (!is_dir($cat_dir)) {
                mkdir($cat_dir, 0755, true);
            }
            
            $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
            $reader = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $filePath = "";
            if (file_exists($reader->getAbsolutePath("import/category/".$filename))) {
                $fileUrl = $reader->getAbsolutePath("import/category/".$filename);
                $filePath = $reader->getAbsolutePath("catalog/category/".$filename);
                rename($fileUrl, $filePath);
            }
            return  $filePath;
        } else {
            return  false;
        }
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magebees_Categories::import');
    }
}
