<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Controller;

use Magento\Framework\App\Action\Forward;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterfaceFactory;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Router implements RouterInterface
{
    public function __construct(
        private LandingPageInterfaceFactory $landingPageFactory,
        private ActionFactory $actionFactory,
        private StoreManagerInterface $storeManager,
    ) {}

    public function match(RequestInterface $request): ?ActionInterface
    {
        $urlKey = trim($request->getPathInfo(), '/');

        /** @var LandingPageInterface $landingPage */
        $landingPage = $this->landingPageFactory->create();
        $id = $landingPage->checkUrlKey($urlKey, (int) $this->storeManager->getStore()->getId());

        if (!$id) {
            return null;
        }

        $request
            ->setModuleName('landingpages')
            ->setControllerName('landingpage')
            ->setActionName('view')
            ->setParam('entity_id', $id)
            ->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $urlKey)
        ;

        return $this->actionFactory->create(Forward::class);
    }
}
