# SQLI eZPlatform Admin UI Extended Bundle
[SQLI](http://www.sqli.com) eZPlatform AdminUI Extended is a bundle that add a new tab in admin top bar and allow to display and CRUD entities

## Installation
### Install with composer
```
composer require sqli/ezplatform_adminui_extended:dev-master
```

### Register the bundle

Activate the bundle in `app/AppKernel.php`

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new SQLI\EzPlatformAdminUiExtendedBundle\SQLIEzPlatformAdminUiExtendedBundle(),
    ];
}
```

### Add routes

In `app/config/routing.yml` :

```yml
# SQLI Admin routes
_sqli_admin:
    resource: "@SQLIEzPlatformAdminUiExtendedBundle/Resources/config/routing.yml"
    prefix: /
```

### Assets

Generate assets :
```bash
php bin/console assetic:dump
php bin/console cache:clear
```