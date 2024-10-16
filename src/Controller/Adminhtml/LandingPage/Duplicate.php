<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Controller\Adminhtml\LandingPage;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;

class Duplicate implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Actiview_ElasticsuiteLandingPages::LandingPage_save';

    public function __construct(
        private readonly RequestInterface $request,
        private readonly ManagerInterface $messageManager,
        private readonly RedirectFactory $resultRedirectFactory,
    ) {}

    /**
     * @todo Implement duplication
     */
    public function execute(): ResultInterface
    {
        $id = $this->request->getParam('entity_id');
        $redirect = $this->resultRedirectFactory->create();

        $this->messageManager->addNoticeMessage(__('Duplication is not yet implemented.'));

        return $redirect->setPath('*/*/');
    }
}
