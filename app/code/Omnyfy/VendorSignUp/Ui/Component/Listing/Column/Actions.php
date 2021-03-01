<?php

namespace Omnyfy\VendorSignUp\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;


class Actions extends Column
{

    const URL_PATH_VIEW = 'omnyfy_vendorsignup/signup/view';
    const URL_PATH_APPROVE = 'omnyfy_vendorsignup/signup/approve';
    const URL_PATH_REJECT = 'omnyfy_vendorsignup/signup/reject';
    const URL_PATH_DELETE = 'omnyfy_vendorsignup/signup/delete';
    const URL_PATH_EDIT = 'omnyfy_vendorsignup/signup/edit';


    protected $urlBuilder;


    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
	
	public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                //print_r($item);
                #if ($item['status'] != 1) {
                    $item[$this->getData('name')]['view'] = [
                        'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_VIEW,
                                [
                                    'id' => $item['id']
                                ]
                            ),
                        'label' => __('View'),
                        'hidden' => false
                    ];
               # }
			   if ($item['status'] != 1) {
					$item[$this->getData('name')]['approve'] = [
						'href' => $this->urlBuilder->getUrl(
									static::URL_PATH_APPROVE,
									[
										'id' => $item['id']
									]
								),
						'label' => __('Approve'),
						'hidden' => false,
						'confirm' => [
							'title' => __('Approve ${ $.$data.business_name }'),
							'message' => __('Do you want to approve the signup request from "${ $.$data.business_name }"?')
						]
					];
			   }
			   if ($item['status'] == 0) {
					$item[$this->getData('name')]['reject'] = [
						'href' => $this->urlBuilder->getUrl(
									static::URL_PATH_REJECT,
									[
										'id' => $item['id']
									]
								),
						'label' => __('Reject'),
						'hidden' => false,
						'confirm' => [
							'title' => __('Reject ${ $.$data.business_name }'),
							'message' => __('Do you want to reject the signup request from "${ $.$data.business_name }"?')
						]
					];
			   }
			   $item[$this->getData('name')]['delete'] = [
					'href' => $this->urlBuilder->getUrl(
								static::URL_PATH_DELETE,
								[
									'id' => $item['id']
								]
							),
					'label' => __('Delete'),
					'hidden' => false,
					'confirm' => [
						'title' => __('Delete ${ $.$data.business_name }'),
						'message' => __('Are you sure you want to delete the signup request from "${ $.$data.business_name }"?')
					]
				];
				$item[$this->getData('name')]['view'] = [
                    'href' => $this->urlBuilder->getUrl(
								static::URL_PATH_VIEW,
								[
									'id' => $item['id']
								]
							),
                    'label' => __('View'),
                    'hidden' => false,
                ];
				/* $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl(
								static::URL_PATH_EDIT,
								[
									'id' => $item['id']
								]
							),
                    'label' => __('Edit'),
                    'hidden' => false,
                ]; */
            }
        }

        return $dataSource;
    }
}