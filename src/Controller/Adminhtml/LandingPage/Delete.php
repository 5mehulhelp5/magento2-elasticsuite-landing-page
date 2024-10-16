<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Controller\Adminhtml\LandingPage;

use Exception;
use Actiview\ElasticsuiteLandingPages\Api\LandingPageRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Actiview_ElasticsuiteLandingPages::LandingPage_delete';

    public function __construct(
        Context $context,
        private LandingPageRepositoryInterface $landingPageRepository,
    ) {
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        try {
            $id = (int) $this->getRequest()->getParam('entity_id');
            $landingPage = $this->landingPageRepository->get($id);

            if ($landingPage->getId()) {
                $this->landingPageRepository->delete($landingPage);
                $this->messageManager->addSuccessMessage(__('The landing page has been deleted.'));
            } else {
                $this->messageManager->addErrorMessage(__('The landing page does not exist.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var Redirect $redirect */
        $redirect = $this->resultRedirectFactory->create();

        return $redirect->setPath('*/*');
    }
}
