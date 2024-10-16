<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Block\Adminhtml\LandingPage;

use Magento\Backend\Block\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Magento\Backend\Block\AbstractBlock;
use Magento\Framework\Data\Form;
use Smile\ElasticsuiteCatalogRule\Block\Product\Conditions;

class RuleCondition extends AbstractBlock
{
    public function __construct(
        Context $context,
        private FormFactory $formFactory,
        private Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /** @inheritDoc */
    protected function _toHtml()
    {
        return $this->getForm()->toHtml();
    }

    private function getLandingPage(): LandingPageInterface
    {
        return $this->registry->registry('current_landing_page');
    }

    private function getForm(): Form
    {
        /** @var Conditions $ruleConditionRenderer */
        $ruleConditionRenderer = $this->getLayout()->createBlock(Conditions::class);

        $form = $this->formFactory->create(['data' => [
            'html_id' => LandingPageInterface::RULE_CONDITION,
        ]]);

        $form
            ->addField(
                LandingPageInterface::RULE_CONDITION,
                'text',
                [
                    'name' => LandingPageInterface::RULE_CONDITION,
                    'label' => __('Products to display'),
                    'container_id' => LandingPageInterface::RULE_CONDITION
                ]
            )
            ->setValue($this->getLandingPage()->getRuleCondition())
            ->setRenderer($ruleConditionRenderer)
        ;

        return $form;
    }
}
