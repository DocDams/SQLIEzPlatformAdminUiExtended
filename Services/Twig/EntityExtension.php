<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Services\Twig;

class EntityExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction( 'sqli_admin_attribute', [
                $this,
                'attributeValue'
            ], array( 'is_safe' => [ 'all' ] ) ),
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
            if( $object[$property_name] instanceof \DateTime )
            {
                // Datetime doesn't have a __toString method
                return date_format( $object[$property_name], "c" );
            }
            else
            {
                return strval( $object[$property_name] );
            }
        }
        catch( \ErrorException $exception )
        {
            // If property instance of an object which not implements a __toString method it will display an error
            return "<span title='{$exception->getMessage()}' class='alert alert-danger'>ERROR</span>";
        }
    }
}