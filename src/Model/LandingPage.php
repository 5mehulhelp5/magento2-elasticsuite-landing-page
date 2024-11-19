<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Actiview\ElasticsuiteLandingPages\Model\ResourceModel\LandingPage as LandingPageResource;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Smile\ElasticsuiteCatalogRule\Model\Rule;
use Smile\ElasticsuiteCatalogRule\Model\RuleFactory;

class LandingPage extends AbstractModel implements LandingPageInterface, IdentityInterface
{
    /** @inheritdoc */
    protected $_eventPrefix = 'landing_page';

    /** @inheritdoc */
    protected $_eventObject = 'landing_page';

    /** @inheritdoc */
    protected $_idFieldName = LandingPageInterface::ENTITY_ID;

    public function __construct(
        Context $context,
        Registry $registry,
        private RuleFactory $ruleFactory,
        private SerializerInterface $serializer,
        private LandingPageResource $landingPageResource,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = [],
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /** @inheritdoc */
    protected function _construct(): void
    {
        $this->_init(LandingPageResource::class);
    }

    /** @inheritdoc */
    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }

    public function checkUrlKey(string $urlKey, int $storeId): int
    {
        return $this->landingPageResource->checkUrlKey($urlKey, $storeId);
    }

    public function getRuleCondition(): Rule
    {
        $rule = $this->getData(self::RULE_CONDITION);

        if (!$rule instanceof Rule) {
            $ruleData = $rule;
            $rule = $this->ruleFactory->create();

            if (is_string($ruleData)) {
                $ruleData = $this->serializer->unserialize($ruleData);
            }

            if (is_array($ruleData)) {
                $rule->getConditions()->loadArray($ruleData);
            }

            $this->setData(self::RULE_CONDITION, $rule);
        }

        return $this->getData(self::RULE_CONDITION);
    }
}
