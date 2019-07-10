SQLI eZPlatform Admin UI Extended Bundle
========================================

[SQLI](http://www.sqli.com) eZPlatform AdminUI Extended is a bundle that add a new tab in admin top bar and allow to display and CRUD entities

Installation
------------

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

### Assets

Generate assets :
```bash
php bin/console assetic:dump
php bin/console cache:clear
```

### Parameters

Configure directories (and namespaces if not according to PSR-0 rules) entities to lookup :

```yml
sqli_ez_platform_admin_ui_extended:
    entities:
        - { directory: 'Acme/AcmeBundle/Entity/Doctrine' }
        - { directory: 'Acme/AcmeBundle2/Entity/Doctrine', namespace: 'Acme\AcmeBundle2NoPSR0\ORM\Doctrine' }
```
Use "~" if the namespace of your classes observ PSR-0 rules or specify directory which contains them.

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
     * @SQLIAdmin\EntityProperty(description="Describe property of your entity",readonly=true)
     */
    private $text;
    
    /**
     * @var string
     * @ORM\Column(name="select",type="int")
     * @SQLIAdmin\EntityProperty(choices={"First choice": 1, "Second choice": 2})
     */
    private $select;
    
    // ...
    public function getId()
    {
        return $this->id;
    }
    
    public function getData() : ?string
    {
        return $this->data;
    }
    
    public function getText() : string 
    {
        return $this->text ?: '';
    }
    
    public function getSelect() : int
    {
        return $this->select;
    }
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
- **readonly** Disallow modifications in edit form
- **choices** An hash relayed to [ChoiceType](https://symfony.com/doc/current/reference/forms/types/choice.html#choices) 


### Supported types

List of supported Doctrine types :
- string
- text
- integer
- float
- decimal
- boolean
- date
- datetime

**NOTICE** : Be careful if you choose to specify the return type on getters : in creation mode, getters will return 'null' so please provide a default value or nullable in type of return (see getter in above class example)
