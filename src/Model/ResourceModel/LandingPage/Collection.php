<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Model\ResourceModel\LandingPage;

use Actiview\ElasticsuiteLandingPages\Api\Data\LandingPageInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Actiview\ElasticsuiteLandingPages\Model\LandingPage;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    /** @inheritDoc */
    protected function _construct()
    {
        $this->_init(LandingPage::class, \Actiview\ElasticsuiteLandingPages\Model\ResourceModel\LandingPage::class);
        $this->_map['fields']['page_id'] = 'main_table.entity_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /** @inheritDoc */
    protected function _afterLoad()
    {
        $this->loadStores();

        return parent::_afterLoad();
    }

    /** @return self */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id' || $field === 'store_ids') {
            return $this->addStoreFilter($condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param array|string|null $store
     */
    public function addStoreFilter($store, bool $withAdmin = true): self
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
            $this->setFlag('store_filter_added', true);
        }

        return $this;
    }

    /**
     * @param array|string|null $store
     */
    private function performAddStoreFilter($store, bool $withAdmin = true): self
    {
        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFieldToFilter('store', ['in' => $store]);

        return $this;
    }

    private function loadStores(): void
    {
        $itemIds = $this->getColumnValues(LandingPageInterface::ENTITY_ID);

        if (count($itemIds)) {
            $result = [];
            $connection = $this->getConnection();

            $select = $connection->select()
                ->from(['entity_store' => $this->getTable(LandingPageInterface::STORE_TABLE_NAME)])
                ->where('entity_store.' . LandingPageInterface::ENTITY_ID . ' IN (?)', $itemIds)
            ;

            foreach ($connection->fetchAll($select) as $item) {
                $result[$item[LandingPageInterface::ENTITY_ID]][] = $item[LandingPageInterface::STORE_ID];
            }

            /** @var LandingPageInterface $item */
            foreach ($this as $item) {
                $entityId = $item->getData(LandingPageInterface::ENTITY_ID);

                if (!isset($result[$entityId])) {
                    $result[$entityId] = [0];
                }

                $item->setData('store_id', $result[$entityId]);
                $item->setData('store_ids', $result[$entityId]);
            }
        }
    }

    /** @inheritDoc */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable(LandingPageInterface::STORE_TABLE_NAME, LandingPageInterface::ENTITY_ID);

        parent::_renderFiltersBefore();
    }

    private function joinStoreRelationTable(string $tableName, string $linkField): void
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = store_table.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
    }
}
