<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Controller\Adminhtml\LandingPage;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Api\LandingPageRepositoryInterface;
use Actiview\ElasticsuiteLandingPages\Model\LandingPageFactory;
use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Throwable;

class Save implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Actiview_ElasticsuiteLandingPages::LandingPage_save';

    public function __construct(
        private readonly RequestInterface $request,
        private readonly ManagerInterface $messageManager,
        private readonly RedirectFactory $resultRedirectFactory,
        private readonly LandingPageRepositoryInterface $repository,
        private readonly LandingPageFactory $modelFactory,
        private readonly DataPersistorInterface $dataPersistor,
    ) {}

    public function execute(): ResultInterface
    {
        $id = $this->request->getParam('entity_id');
        $data = $this->request->getPostValue();
        $redirect = $this->resultRedirectFactory->create();

        $model = $this->modelFactory->create();
        if ($id) {
            $model = $this->repository->get((int) $id);
        }

        $model->setData($data);
        $ruleConditionPost = $this->request->getParam('rule_condition', []);
        $model->getRuleCondition()->loadPost($ruleConditionPost);

        try {
            $model = $this->repository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the landing page.'));
            $this->dataPersistor->clear('landing_page');
            return $this->processResultRedirect($model, $redirect);
        } catch (LocalizedException $e) {
            /** @var Exception $e */
            $e = $e->getPrevious() ?: $e;
            $this->messageManager->addExceptionMessage($e);
        } catch (Throwable) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the landing page.'));
        }

        $this->dataPersistor->set('landing_page', $data);

        return $redirect->setPath('*/*/edit', ['entity_id' => $id, '_current' => true]);
    }

    private function processResultRedirect(LandingPageInterface $model, Redirect $redirect): Redirect
    {
        if ($this->request->getParam('back', false) === 'duplicate') {
            return $redirect->setPath('*/*/duplicate', ['entity_id' => $model->getId(), '_current' => true]);
        } elseif ($this->request->getParam('back')) {
            return $redirect->setPath('*/*/edit', ['entity_id' => $model->getId(), '_current' => true]);
        }

        return $redirect->setPath('*/*/');
    }
}
