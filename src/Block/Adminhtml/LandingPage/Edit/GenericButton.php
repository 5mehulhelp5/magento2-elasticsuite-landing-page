<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Block\Adminhtml\LandingPage\Edit;

use Magento\Backend\Block\Widget\Context;
use Actiview\ElasticsuiteLandingPages\Api\LandingPageRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    public function __construct(
        protected Context $context,
        protected LandingPageRepositoryInterface $repository
    ) {}

    public function getId(): ?int
    {
        $id = (int) $this->context->getRequest()->getParam('entity_id');

        if (!$id) {
            return null;
        }

        try {
            return (int) $this->repository->get($id)->getId();
        } catch (NoSuchEntityException) {
            // Do nothing
        }

        return null;
    }

    protected function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
