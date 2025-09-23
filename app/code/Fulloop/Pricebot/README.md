# Mage2 Module Fulloop Pricebot

    ``fulloop/module-pricebot``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Web scraper to gather products price from the competitor websites

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Fulloop`
 - Enable the module by running `php bin/magento module:enable Fulloop_Pricebot`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require fulloop/module-pricebot`
 - enable the module by running `php bin/magento module:enable Fulloop_Pricebot`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - Console Command
	- import

 - Controller
	- frontend > fulloop_pricebot/index/index

 - API Endpoint
	- POST - Fulloop\Pricebot\Api\ProductsManagementInterface > Fulloop\Pricebot\Model\ProductsManagement

 - API Endpoint
	- GET - Fulloop\Pricebot\Api\ProductsManagementInterface > Fulloop\Pricebot\Model\ProductsManagement


## Attributes



