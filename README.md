# Actiview_ElasticsuiteLandingPages

Create landing pages with a selection of your product catalog using conditions. Add content for Search Engine Optimization (SEO) purposes. 

## Installation

```sh
composer require actiview/magento2-elasticsuite-landing-pages
bin/magento module:enable Actiview_ElasticsuiteLandingPages
bin/magento setup:db-schema:upgrade
bin/magento setup:db-data:upgrade
bin/magento cache:clean
```

## Usage

After installation go to your Magento 2 backend and navigate to "Content" → "Landing Pages" → "Manage". Here you can create, list, edit and delete all your Landing Pages. 

## Credits

This module builds on the fantastic [Elasticsuite module](https://github.com/Smile-SA/elasticsuite) by [Smile](https://smile.eu/).

## Elasticsuite version compatibility

This module is compatible with Smile Elasticsuite >= 2.10.

## Hyvä compatibility

This module is fully compatible with Hyvä.
