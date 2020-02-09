[frugue.com](https://frugue.com) (Magento 2).

## How to update all `frugue/*` packages 
```              
sudo service cron stop     
sudo service nginx stop      
sudo service php7.2-fpm stop 
bin/magento maintenance:enable
composer remove frugue/configurable
composer remove frugue/core  
composer remove frugue/ru
composer remove frugue/shipping  
composer remove frugue/store
rm -rf composer.lock
composer clear-cache
composer require frugue/configurable:*
composer require frugue/core:*
composer require frugue/ru:* 
composer require frugue/shipping:*  
composer require frugue/store:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/code
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme TemplateMonster/theme007 \
	-f en_US de_DE fr_FR ru_RU
bin/magento maintenance:disable 
sudo service php7.2-fpm start
sudo service nginx start
sudo service cron start
```