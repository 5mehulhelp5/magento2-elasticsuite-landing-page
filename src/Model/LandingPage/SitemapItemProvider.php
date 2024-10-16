<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model\LandingPage;

use Actiview\ElasticsuiteLandingPages\Model\ResourceModel\LandingPage\Collection;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;

class SitemapItemProvider implements ItemProviderInterface
{
    public function __construct(
        private SitemapItemInterfaceFactory $itemFactory,
        private Collection $landingPageCollection,
    ) {}

    /** @inheritDoc */
    public function getItems($storeId)
    {
        $collection = $this->landingPageCollection
            ->addStoreFilter($storeId)
            ->getItems()
        ;

        return array_map(fn($item) => $this->itemFactory->create([
            'url' => $item->getUrlKey(),
            'updatedAt' => $item->getUpdatedAt(),
            'priority' => '0.8',
            'changeFrequency' => 'weekly',
        ]), $collection);
    }
}
