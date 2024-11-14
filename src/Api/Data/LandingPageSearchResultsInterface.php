<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface LandingPageSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return LandingPageInterface[]
     */
    public function getItems();

    /**
     * @param LandingPageInterface[] $items
     */
    public function setItems(array $items);
}
