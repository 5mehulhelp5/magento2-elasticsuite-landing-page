<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model\LandingPage;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterfaceFactory;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Api\LandingPageRepositoryInterface;
use Actiview\ElasticsuiteLandingPages\Model\ResourceModel\LandingPage\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private array $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        private RequestInterface $request,
        private LandingPageRepositoryInterface $landingPageRepository,
        private LandingPageInterfaceFactory $landingPageFactory,
        private DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }

    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $entity = $this->getCurrentEntity();
        $this->loadedData[$entity->getId()] = $entity->getData();

        return $this->loadedData;
    }

    private function getCurrentEntity(): LandingPageInterface
    {
        $id = $this->getLandingPageId();
        $landingPage = $this->landingPageFactory->create();

        if ($id) {
            try {
                $landingPage = $this->landingPageRepository->get($id);
            } catch (NoSuchEntityException) {
                // Do nothing
            }
        }

        $data = $this->dataPersistor->get('landing_page');

        if (!empty($data)) {
            $landingPage->setData($data);
        }

        $this->dataPersistor->clear('landing_page');

        return $landingPage;
    }

    private function getLandingPageId(): int
    {
        return (int) $this->request->getParam($this->getRequestFieldName());
    }
}
