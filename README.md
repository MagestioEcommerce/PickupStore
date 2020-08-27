# Extension Store Pickup for Magento 2.3.+

[![N|Solid](https://magestio.com/wp-content/uploads/logo_web_r.png)](https://magestio.com)

### Characteristics

* Add multiple stores
* Disable the store pickup on certain postal codes.
* Add different prices for each store

### Requirements
* Compatible with Magento 2.3.+
* [Magestio_Core](https://github.com/MagestioEcommerce/core) is required to use any of Magestio extensions.

### Enable extension

```
php bin/magento module:enable Magestio_Core
php bin/magento module:enable Magestio_PickupStore
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
php bin/magento setup:static-content:deploy
```

### Soporte técnico

* Web: [https://magestio.com/](https://magestio.com/)