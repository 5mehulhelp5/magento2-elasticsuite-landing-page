<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model\ResourceModel;

use Smile\ElasticsuiteCatalogRule\Model\RuleFactory;
use Smile\ElasticsuiteCatalogRule\Model\Rule;
use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Magento\Backend\Model\Validator\UrlKey\CompositeUrlKey as CompositeUrlKeyValidator;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;

class LandingPage extends AbstractDb
{
    public const MAIN_TABLE = 'elasticsuite_landing_page';

    /** @inheritdoc */
    protected $_idFieldName = LandingPageInterface::ENTITY_ID;

    public function __construct(
        Context $context,
        private CompositeUrlKeyValidator $compositeUrlKeyValidator,
        private FilterManager $filterManager,
        private RuleFactory $ruleFactory,
        $connectionName = null,
    ) {
        parent::__construct($context, $connectionName);
    }

    /** @inheritdoc */
    protected function _construct()
    {
        $this->_init(LandingPageInterface::TABLE_NAME, LandingPageInterface::ENTITY_ID);
    }

    /** @inheritdoc */
    public function getConnection(): AdapterInterface
    {
        /** @var AdapterInterface $connection */
        $connection =  parent::getConnection();

        return $connection;
    }

    /** @inheritdoc */
    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->getStoreIdsFromId((int) $object->getId());
            $object->setStoreId($stores);
            $object->setStoreIds($stores);
        }

        return parent::_afterLoad($object);
    }

    /** @inheritdoc */
    protected function _beforeSave(AbstractModel $object)
    {
        $this->setGeneratedUrlKey($object);
        $this->validateNewUrlKey($object);
        $this->saveRuleCondition($object);

        return parent::_beforeSave($object);
    }

    /** @inheritdoc */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveStoreRelation($object);

        return parent::_afterSave($object);
    }

    public function getStoreIdsFromId(int $id): array
    {
        /** @var AdapterInterface $connection */
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable(LandingPageInterface::STORE_TABLE_NAME), LandingPageInterface::STORE_ID)
            ->where(LandingPageInterface::ENTITY_ID . ' = ?', (int) $id)
        ;

        return $connection->fetchCol($select);
    }

    /**
     * Set a generated URL Key base on title, if URL Key is empty
     */
    private function setGeneratedUrlKey(AbstractModel $object): void
    {
        $urlKey = $object->getData(LandingPageInterface::URL_KEY);

        if ($urlKey === '' || $urlKey === null) {
            $urlKey = $this->filterManager->translitUrl($object->getTitle());
            $object->setData(LandingPageInterface::URL_KEY, $urlKey);
        }
    }

    private function saveRuleCondition(AbstractModel $object): void
    {
        /** @var Rule $rule */
        $rule = $this->ruleFactory->create();
        $data = $object->getData(LandingPageInterface::RULE_CONDITION);

        if ($data instanceof Rule) {
            $rule = $data;
        } elseif (is_array($data)) {
            $rule->getConditions()->loadArray($data);
        }

        $object->setData(LandingPageInterface::RULE_CONDITION, $rule->getConditions()->asArray());
        $this->_serializeField($object, LandingPageInterface::RULE_CONDITION);
    }

    private function saveStoreRelation(AbstractModel $object): void
    {
        $storeLinks = [];
        $storeIds = $object->getStoreId();

        if (!is_array($storeIds) || !count($storeIds)) {
            $storeIds = [0];
        }

        if (in_array(0, $storeIds)) {
            $storeIds = [0];
        }

        $deleteCondition = [
            LandingPageInterface::ENTITY_ID . " = ?" => $object->getId(),
            LandingPageInterface::STORE_ID . " NOT IN (?)" => $storeIds,
        ];

        foreach ($storeIds as $storeId) {
            $storeLinks[] = [
                LandingPageInterface::ENTITY_ID => (int) $object->getId(),
                LandingPageInterface::STORE_ID => (int) $storeId,
            ];
        }

        $this->getConnection()->delete($this->getTable(LandingPageInterface::STORE_TABLE_NAME), $deleteCondition);
        $this->getConnection()->insertOnDuplicate(
            $this->getTable(LandingPageInterface::STORE_TABLE_NAME),
            $storeLinks,
            array_keys(current($storeLinks))
        );
    }

    /**
     * Check if landing page URL key exist for specific store, return id if landing page exists
     */
    public function checkUrlKey(string $urlKey, int $storeId): int
    {
        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->getLoadByUrlKeySelect($urlKey, $stores, 1);
        $select->reset(Select::COLUMNS)
            ->columns('landingpage.' . LandingPageInterface::ENTITY_ID)
            ->order('landingpage_store.store_id DESC')
            ->limit(1);

        return (int) $this->getConnection()->fetchOne($select);
    }

    private function getLoadByUrlKeySelect(string $urlKey, array $store, int $isActive = 0): Select
    {
        $select = $this->getConnection()->select()
            ->from(['landingpage' => LandingPageInterface::TABLE_NAME])
            ->join(
                ['landingpage_store' => LandingPageInterface::STORE_TABLE_NAME],
                'landingpage.' . LandingPageInterface::ENTITY_ID . ' = landingpage_store.' . LandingPageInterface::ENTITY_ID,
                []
            )
            ->where('landingpage.url_key = ?', $urlKey)
            ->where('landingpage_store.store_id IN (?)', $store);

        if ($isActive !== null) {
            $select->where('landingpage.is_active = ?', $isActive);
        }

        return $select;
    }

    private function validateNewUrlKey(AbstractModel $object): void
    {
        $urlKey = (string) $object->getData(LandingPageInterface::URL_KEY);

        $errors = $this->compositeUrlKeyValidator->validate($urlKey);

        if (!empty($errors)) {
            throw new LocalizedException($errors[0]);
        }

        if (!$this->isValidUrlKey($urlKey)) {
            throw new LocalizedException(__(
                "The page URL key can't use capital letters or disallowed symbols. "
                . "Remove the disallowed letters and symbols and try again."
            ));
        }
    }

    private function isValidUrlKey(string $urlKey = ''): bool
    {
        return (bool) preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $urlKey);
    }
}
