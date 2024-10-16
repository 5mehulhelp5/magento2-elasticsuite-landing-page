<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Controller\Adminhtml\LandingPage;

use Exception;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterfaceFactory;
use Actiview\ElasticsuiteLandingPages\Api\LandingPageRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Actiview_ElasticsuiteLandingPages::LandingPage_save';

    public function __construct(
        Context $context,
        private readonly PageFactory $pageFactory,
        private readonly Registry $registry,
        private LandingPageRepositoryInterface $landingPageRepository,
        private LandingPageInterfaceFactory $landingPageFactory,
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $landingPage = $this->landingPageFactory->create();
        $id = (int) $this->getRequest()->getParam('entity_id');

        if ($id) {
            try {
                $landingPage = $this->landingPageRepository->get($id);
            } catch (Exception) {
                $this->messageManager->addErrorMessage(__('This landing page no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->registry->register('current_landing_page', $landingPage);

        $page = $this->pageFactory->create();
        $page->setActiveMenu('Actiview_ElasticsuiteLandingPages::LandingPage');
        $title = $id
            ? __('Edit Landing Page')
            : __('New Landing Page');
        $page->getConfig()->getTitle()->prepend($title);

        return $page;
    }
}
