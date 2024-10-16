<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

class LandingPageSearchResults extends SearchResults implements LandingPageSearchResultsInterface
{
}
