<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor;

use Actiview\ElasticsuiteLandingPages\Model\ResourceModel\LandingPage\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class LandingPageStoreFilter implements CustomFilterInterface
{
    /** @inheritDoc */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        /** @var Collection $collection */
        $collection->addStoreFilter($filter->getValue(), false);

        return true;
    }
}
