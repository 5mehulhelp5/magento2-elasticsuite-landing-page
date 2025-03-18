<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Controller\Adminhtml\LandingPage;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterfaceFactory;
use Actiview\ElasticsuiteLandingPages\Api\LandingPageRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class Duplicate implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Actiview_ElasticsuiteLandingPages::LandingPage_save';

    public function __construct(
        private readonly RequestInterface $request,
        private readonly ManagerInterface $messageManager,
        private readonly RedirectFactory $resultRedirectFactory,
        private readonly LandingPageRepositoryInterface $repository,
        private readonly LandingPageInterfaceFactory $modelFactory,
    ) {}

    public function execute(): ResultInterface
    {
        $redirect = $this->resultRedirectFactory->create();

        try {
            $id = $this->request->getParam('entity_id');
            $model = $this->repository->get((int) $id);
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('This landing page no longer exists.'));
            return $redirect->setPath('*/*/');
        }

        $data = $model->getData();
        $newModel = $this->modelFactory->create(['data' => $data]);
        $urlKey = $model->getUrlKey() . '-' . uniqid();
        $newModel->setCreatedAt(null);
        $newModel->setUpdatedAt(null);
        $newModel->setId(null);
        $newModel->setUrlKey($urlKey);
        $this->repository->save($newModel);

        $this->messageManager->addSuccessMessage(__('This landing page is duplicated.'));

        return $redirect->setPath('*/*/');
    }
}
