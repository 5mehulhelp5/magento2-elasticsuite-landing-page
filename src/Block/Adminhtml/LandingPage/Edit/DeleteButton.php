<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Block\Adminhtml\LandingPage\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s', '%s', {'data': {}})",
                    __('Are you sure you want to do this?'),
                    $this->getDeleteUrl()
                ),
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * URL to send delete requests to.
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl('*/*/delete', ['entity_id' => $this->getId()]);
    }
}
