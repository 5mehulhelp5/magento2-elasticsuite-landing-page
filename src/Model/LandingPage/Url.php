<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model\LandingPage;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Controller\LandingPage\View;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Url
{
    public function __construct(
        private UrlFinderInterface $urlFinder,
        private UrlInterface $urlBuilder,
        private StoreManagerInterface $storeManager
    ) {}

    public function getUrl(LandingPageInterface $landingPage): string
    {
        return $this->getUrlRewrite($landingPage) ?: View::getUrlPath($landingPage);
    }

    private function getUrlRewrite(LandingPageInterface $landingPage): ?string
    {
        $url = null;

        $rewrite = $this->urlFinder->findOneByData([
            UrlRewrite::ENTITY_ID => $landingPage->getId(),
            UrlRewrite::ENTITY_TYPE => \Actiview\ElasticsuiteLandingPages\Model\LandingPage\UrlRewriteGenerator::ENTITY_TYPE,
            UrlRewrite::STORE_ID => $this->storeManager->getStore()->getId(),
        ]);

        if ($rewrite) {
            $url = $this->urlBuilder->getDirectUrl($rewrite->getRequestPath());
        }

        return $url;
    }

}
