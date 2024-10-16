<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Controller\Adminhtml\LandingPage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Actiview_ElasticsuiteLandingPages::LandingPage';

    public function __construct(
        Context $context,
        private readonly PageFactory $pageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Actiview_ElasticsuiteLandingPages::LandingPage');
        $page->getConfig()->getTitle()->prepend(__('Landing Pages'));

        return $page;
    }
}
