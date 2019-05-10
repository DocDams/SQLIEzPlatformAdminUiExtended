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

### Parameters

Configure directories and namespaces entities to lookup :

```yml
parameters:
    sqli_ez_platform_admin_ui_extended.entities.directories:
        Acme/AcmeBundle/Entity/Doctrine: ~
```

Annotations on entities :

```php
<?php
namespace Acme\AcmeBundle\Entity\Doctrine;

use SQLI\EzPlatformAdminUiExtendedBundle\Annotations\Annotation as SQLIAdmin;

/**
* Class MyEntity
 * 
 * @package Acme\AcmeBundle\Entity\Doctrine
 * @ORM\Table(name="my_entity")
 * @ORM\Entity(repositoryClass="Acme\AcmeBundle\Repository\Doctrine\MyEntityRepository")
 * @SQLIAdmin\Entity(update=true,create=true,delete=false,description="Describe your entity")
 */
class MyEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="data",type="string")
     * @SQLIAdmin\EntityProperty(visible=false)
     */
    private $data;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="text",type="string")
     * @SQLIAdmin\EntityProperty(description="Describe property of your entity")
     */
    private $text;
    
    // ...
}
```

Class annotation `Entity` has following properties :
- **description** Description
- **update** Allow update of a line in table
- **delete** Allow deletion of a line in table
- **create** Allow creation of new line in table

Property annotation `EntityProperty` has following properties :
- **description** Description
- **visible** Display column

### Assets

Generate assets :
```bash
php bin/console assetic:dump
php bin/console cache:clear
```