<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Ui\Component\Listing\Column;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder as ActionUrlBuilder;
use Magento\Framework\UrlInterface;

class Actions extends Column
{
    public const LANDING_URL_PATH_EDIT = 'landingpages/landingpage/edit';
    public const LANDING_URL_PATH_DELETE = 'landingpages/landingpage/delete';
    public const LANDING_URL_PATH_DUPLICATE = 'landingpages/landingpage/duplicate';

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private ActionUrlBuilder $actionUrlBuilder,
        private UrlInterface $urlBuilder,
        private StoreManagerInterface $storeManager,
        array $components = [],
        array $data = [],
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /** @inheritDoc */
    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $name = $this->getData('name');
            $item[$name]['edit'] = $this->getEditUrl($item);
            $item[$name]['preview'] = $this->getPreviewUrl($item);
            $item[$name]['duplicate'] = $this->getDuplicateUrl($item);
            $item[$name]['delete'] = $this->getDeleteUrl($item);
        }

        return $dataSource;
    }

    private function getDeleteUrl(array $item): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(
                self::LANDING_URL_PATH_DELETE,
                [
                    'entity_id' => $item[LandingPageInterface::ENTITY_ID],
                ]
            ),
            'post' => true,
            'label' => __('Delete'),
            'confirm' => [
                'title' => __('Delete ${ $.$data.title }'),
                'message' => __('Are you sure you wan\'t to delete a ${ $.$data.title } record?'),
                '__disableTmpl' => false,
            ]
        ];
    }

    private function getPreviewUrl(array $item): array
    {
        static $store = null;

        if (!$store) {
            $stores = $this->storeManager->getStores();
            $store = array_shift($stores);
        }

        return [
            'href' => $this->actionUrlBuilder->getUrl(
                $item[LandingPageInterface::URL_KEY],
                $store->getId(),
                $store->getCode(),
            ),
            'label' => __('View'),
            'target' => '_blank',
        ];
    }

    private function getEditUrl(array $item): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(self::LANDING_URL_PATH_EDIT, [
                'entity_id' => $item[LandingPageInterface::ENTITY_ID],
            ]),
            'label' => __('Edit')
        ];
    }

    private function getDuplicateUrl(array $item): array
    {
        return [
            'href' => $this->urlBuilder->getUrl(
                self::LANDING_URL_PATH_DUPLICATE,
                [
                    'entity_id' => $item[LandingPageInterface::ENTITY_ID],
                ]
            ),
            'label' => __('Duplicate')
        ];
    }
}
