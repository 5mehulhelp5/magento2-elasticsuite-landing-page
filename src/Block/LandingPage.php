<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Block;

use Magento\Cms\Block\BlockByIdentifierFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template\Context;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Model\Template\Filter;
use Throwable;

class LandingPage extends Template implements IdentityInterface
{
    private ?LandingPageInterface $currentLandingPage = null;

    public function __construct(
        Context $context,
        private readonly Registry $registry,
        private readonly BlockByIdentifierFactory $blockByIdentifierFactory,
        private readonly Filter $filter,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getLandingPage(): LandingPageInterface
    {
        if (!$this->currentLandingPage) {
            $this->currentLandingPage = $this->registry->registry('current_landing_page');
        }

        return $this->currentLandingPage;
    }

    /** @inheritDoc */
    public function getIdentities()
    {
        return [LandingPageInterface::CACHE_TAG . '_' . $this->getLandingPage()->getId()];
    }

    public function getWysiwygContent(string $position): string
    {
        $this->validatePosition($position);
        $landingPage = $this->getLandingPage();
        $field = 'content_' . $position;
        $content = $landingPage->getData($field) ?? '';

        return $this->filter->filter($content);
    }

    public function getCmsBlockContent(string $position): string
    {
        $this->validatePosition($position);
        $landingPage = $this->getLandingPage();
        $field = 'cms_block_' . $position;

        try {
            return $this->blockByIdentifierFactory->create()
                ->setIdentifier($landingPage->getData($field))
                ->toHtml()
            ;
        } catch (Throwable) {
            $content = '';
        }

        return $content;
    }

    private function validatePosition(string $position): void
    {
        if (!in_array($position, ['top', 'bottom'])) {
            throw new LocalizedException(__('Invalid CMS Block position'));
        }
    }
}
