<?php

declare(strict_types=1);

namespace Actiview\ElasticsuiteLandingPages\Api\Data;

/**
 * @method getId()
 * @method getEntityId()
 * @method getTitle()
 * @method getUrlKey()
 * @method getStoreId()
 * @method getStoreIds()
 * @method getRuleCondition()
 * @method getMetaTitle()
 * @method getMetaKeywords()
 * @method getMetaDescription()
 * @method getContentHeading()
 * @method getData($key = '', $index = null)
 * @method setData($key, $value = '')
 * @method setStoreId($value = '')
 * @method setStoreIds($value = '')
 * @method isDeleted()
 * @method checkUrlKey(string $urlKey, int $storeId)
 * @method dataHasChangedFor(string $field)
 */
interface LandingPageInterface
{
    public const CACHE_TAG = 'landing_page';

    public const ENTITY_ID = 'entity_id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const IS_ACTIVE = 'is_active';
    public const TITLE = 'title';
    public const URL_KEY = 'url_key';
    public const STORE_ID = 'store_id';
    public const RULE_CONDITION = 'rule_condition';

    public const TABLE_NAME = 'elasticsuite_landing_page';
    public const STORE_TABLE_NAME = 'elasticsuite_landing_page_store';
}
