<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageSearchResultsInterface;

interface LandingPageRepositoryInterface
{
    public function get(int $id): LandingPageInterface;

    public function getList(SearchCriteriaInterface $criteria): LandingPageSearchResultsInterface;

    public function save(LandingPageInterface $entity): LandingPageInterface;

    public function delete(LandingPageInterface $entity): bool;

    public function deleteById(int $id): bool;
}
