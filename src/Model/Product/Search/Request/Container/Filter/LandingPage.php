<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model\Product\Search\Request\Container\Filter;

use Magento\Framework\Registry;
use Smile\ElasticsuiteCore\Api\Search\Request\Container\FilterInterface;

/**
 * Filter landing pages with conditions set on that landing page.
 */
class LandingPage implements FilterInterface
{
    public function __construct(
        private Registry $registry,
    ) {}

    /** @inheritDoc */
    public function getFilterQuery()
    {
        $landingPage = $this->registry->registry('current_landing_page');

        if (!$landingPage) {
            return null;
        }

        return $landingPage->getRuleCondition()->getSearchQuery();
    }
}
