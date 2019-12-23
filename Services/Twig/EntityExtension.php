<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Services\Twig;

use SQLI\EzPlatformAdminUiExtendedBundle\Services\EntityHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EntityExtension extends \Twig_Extension
{
    protected $container;
    /** @var EntityHelper */
    private $entityHelper;

    public function __construct( ContainerInterface $container, EntityHelper $entityHelper )
    {
        $this->container    = $container;
        $this->entityHelper = $entityHelper;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction( 'sqli_admin_attribute',
                                      [
                                          $this,
                                          'attributeValue'
                                      ], array( 'is_safe' => [ 'all' ] ) ),
            new \Twig_SimpleFunction( 'bundle_exists',
                                      [
                                          $this,
                                          'bundleExists'
                                      ] ),
        ];
    }

    /**
     * Get value of a property
     *
     * @param $object
     * @param $property_name
     * @return false|string
     */
    public function attributeValue( $object, $property_name )
    {
        try
        {
            return $this->entityHelper->attributeValue( $object, $property_name );
        }
        catch( \ErrorException $exception )
        {
            // If property instance of an object which not implements a __toString method it will display an error
            return "<span title='{$exception->getMessage()}' class='alert alert-danger'>ERROR</span>";
        }
    }

    /**
     * Check if a bundle is declared
     *
     * @param $bundleName
     * @return bool
     */
    public function bundleExists( $bundleName )
    {
        return array_key_exists( $bundleName, $this->container->getParameter( 'kernel.bundles' ) );
    }
}