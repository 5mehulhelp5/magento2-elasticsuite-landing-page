<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Observer;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Model\LandingPage\UrlRewriteGenerator;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class ProcessUrlRewriteAfterSave implements ObserverInterface
{
    public function __construct(
        private UrlPersistInterface $urlPersist,
        private UrlRewriteGenerator $urlRewriteGenerator,
    ) {}

    public function execute(Observer $observer): void
    {
        /** @var LandingPageInterface $landingPage */
        $landingPage = $observer->getEvent()->getLandingPage();

        if (
            $landingPage->dataHasChangedFor(LandingPageInterface::URL_KEY)
            || $landingPage->dataHasChangedFor(LandingPageInterface::STORE_ID)
        ) {
            $urls = $this->urlRewriteGenerator->generate($landingPage);
            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID   => $landingPage->getId(),
                UrlRewrite::ENTITY_TYPE => UrlRewriteGenerator::ENTITY_TYPE,
            ]);
            $this->urlPersist->replace($urls);
        }
    }
}
