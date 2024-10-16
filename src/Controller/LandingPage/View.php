<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Controller\LandingPage;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Api\LandingPageRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Title;

class View implements HttpGetActionInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly PageFactory $pageFactory,
        private readonly LandingPageRepositoryInterface $landingPageRepository,
        private readonly Registry $registry,
        private readonly StoreManagerInterface $storeManager,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function execute(): Page
    {
        $landingPage = $this->landingPageRepository->get($this->getLandingPageId());

        $rootCategoryId = $this->getCurrentStore()->getRootCategoryId();
        $category = $this->categoryRepository->get($rootCategoryId, $this->getCurrentStore()->getId());

        $this->registry->register('current_category', $category);
        $this->registry->register('current_landing_page', $landingPage);

        $page = $this->pageFactory->create();
        $page->addPageLayoutHandles([
            'type' => $category->hasChildren() ? 'layered' : 'layered_without_children',
        ], 'catalog_category_view');

        $page->getConfig()->getTitle()->set($landingPage->getTitle());
        $page->getConfig()->setMetaTitle($landingPage->getMetaTitle() ?: $landingPage->getTitle());
        $page->getConfig()->setKeywords($landingPage->getMetaKeywords());
        $page->getConfig()->setDescription($landingPage->getMetaDescription());
        $page->getConfig()->setPageLayout('2columns-left');
        $page->getConfig()->addBodyClass('catalog-category-view');

        $pageMainTitleBlock = $page->getLayout()->getBlock('page.main.title');
        if ($pageMainTitleBlock) {
            /** @var Title $pageMainTitleBlock */
            $pageMainTitleBlock->setPageTitle($landingPage->getContentHeading() ?: $landingPage->getTitle());
        }

        return $page;
    }

    public static function getUrlPath(LandingPageInterface $landingPage): string
    {
        return 'landingpages/landingpage/view/id/' . $landingPage->getId();
    }

    private function getLandingPageId(): int
    {
        return (int) $this->request->getParam('entity_id') ?: (int) $this->request->getParam('id');
    }

    private function getCurrentStore(): StoreInterface
    {
        return $this->storeManager->getStore();
    }
}
