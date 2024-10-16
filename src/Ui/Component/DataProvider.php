<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Ui\Component;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Actiview\ElasticsuiteLandingPages\Model\ResourceModel\LandingPage\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
