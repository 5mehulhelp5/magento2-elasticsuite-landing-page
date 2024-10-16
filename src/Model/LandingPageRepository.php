<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageSearchResultsInterface;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageSearchResultsInterfaceFactory;
use Actiview\ElasticsuiteLandingPages\Api\LandingPageRepositoryInterface;
use Actiview\ElasticsuiteLandingPages\Model\ResourceModel\LandingPage as ResourceLandingPage;
use Actiview\ElasticsuiteLandingPages\Model\ResourceModel\LandingPage\CollectionFactory as LandingPageCollectionFactory;
use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class LandingPageRepository implements LandingPageRepositoryInterface
{
    public function __construct(
        private readonly ResourceLandingPage $resource,
        private readonly LandingPageFactory $landingPageFactory,
        private readonly LandingPageCollectionFactory $landingPageCollectionFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
        private readonly LandingPageSearchResultsInterfaceFactory $searchResultsFactory,
    ) {}

    /** @inheritdoc */
    public function save(LandingPageInterface $entity): LandingPageInterface
    {
        try {
            $this->resource->save($entity); // @phpstan-ignore-line
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(
                __('Could not save the Landing Page: %1', $e->getMessage()),
                $e
            );
        } catch (Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Landing Page: %1', __('Something went wrong while saving the Landing Page.')),
                $exception
            );
        }

        return $entity;
    }

    /** @inheritdoc */
    public function get(int $id): LandingPageInterface
    {
        $landingPage = $this->landingPageFactory->create();
        $this->resource->load($landingPage, $id);
        if (!$landingPage->getId()) {
            throw new NoSuchEntityException(__('Landing Page with id "%1" does not exist.', $id));
        }

        return $landingPage;
    }

    /** @inheritdoc */
    public function getList(SearchCriteriaInterface $criteria): LandingPageSearchResultsInterface
    {
        $collection = $this->landingPageCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems()); // @phpstan-ignore-line
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /** @inheritdoc */
    public function delete(LandingPageInterface $entity): bool
    {
        try {
            $landingPageModel = $this->landingPageFactory->create();
            $this->resource->load($landingPageModel, $entity->getEntityId());
            $this->resource->delete($landingPageModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Landing Page: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /** @inheritdoc */
    public function deleteById(int $id): bool
    {
        return $this->delete($this->get($id));
    }
}
