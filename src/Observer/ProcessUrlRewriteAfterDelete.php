<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Observer;

use Magento\Framework\Event\Observer;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Model\LandingPage\UrlRewriteGenerator;
use Magento\Framework\Event\ObserverInterface;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class ProcessUrlRewriteAfterDelete implements ObserverInterface
{
    public function __construct(
        private UrlPersistInterface $urlPersist,
    ) {}

    public function execute(Observer $observer): void
    {
        /** @var LandingPageInterface $landingPage */
        $landingPage = $observer->getEvent()->getLandingPage();

        if ($landingPage->isDeleted()) {
            $this->urlPersist->deleteByData(
                [
                    UrlRewrite::ENTITY_ID => $landingPage->getId(),
                    UrlRewrite::ENTITY_TYPE => UrlRewriteGenerator::ENTITY_TYPE,
                ]
            );
        }
    }
}
