<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Annotations;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use SQLI\EzPlatformAdminUiExtendedBundle\Annotations\Annotation\EntityProperty;
use SQLI\EzPlatformAdminUiExtendedBundle\Annotations\Annotation\SQLIClassAnnotation;
use SQLI\EzPlatformAdminUiExtendedBundle\Annotations\Annotation\SQLIPropertyAnnotation;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SQLIAnnotationManager
{
    /**
     * Classname of annotation
     * @var string
     */
    private $annotation;
    /** @var array */
    private $directories;
    /** @var Reader */
    private $annotationReader;
    /**
     * The Kernel root directory
     * @var string
     */
    private $rootDir;

    public function __construct( $annotation, $directories, $rootDir, Reader $annotationReader )
    {
        $this->annotation       = $annotation;
        $this->directories      = $directories;
        $this->rootDir          = $rootDir;
        $this->annotationReader = $annotationReader;
    }

    /**
     * Returns all PHP classes annotated with annotation specified in service declaration (see services.yml)
     * @example service : sqli_admin_annotation_entities
     *
     * @return array
     * @throws \ReflectionException
     */
    public function getAnnotatedClasses()
    {
        $annotations = $this->getSQLIAnnotations();

        // Only annotation in service declaration will be kept
        if( array_key_exists( $this->annotation, $annotations ) )
        {
            return $annotations[$this->annotation];
        }

        return [];
    }

    /**
     * Return all PHP classes annotated with an SQLIClassAnnotation
     * For each class, all properties will be defined
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function getSQLIAnnotations()
    {
        $annotatedClasses = [];

        // Scan all files into directories defined in configuration
        foreach( $this->directories as $entitiesMapping )
        {
            $directory = $entitiesMapping['directory'];
            $namespace = $entitiesMapping['namespace'];
            if( is_null( $namespace ) )
            {
                $namespace = str_replace( '/', '\\', $directory );
            }

            $path   = $this->rootDir . '/../src/' . $directory;
            $finder = new Finder();
            $finder->depth( 0 )->files()->in( $path );

            /** @var SplFileInfo $file */
            foreach( $finder as $file )
            {
                $className      = $file->getBasename( '.php' );
                $classNamespace = "$namespace\\$className";
                // Create reflection class from generated namespace to read annotation
                $class = new \ReflectionClass( $classNamespace );

                // Search if $class use an SQLIClassAnnotation
                $classAnnotation = $this
                    ->annotationReader
                    ->getClassAnnotation( $class, SQLIClassAnnotation::class );
                // Check if $class use Doctrine\Entity annotation
                $classDoctrineAnnotation = $this
                    ->annotationReader
                    ->getClassAnnotation( $class, Entity::class );

                if( !$classAnnotation || !$classDoctrineAnnotation )
                {
                    // No SQLIClassAnnotation or isn't an entity, ignore her
                    continue;
                }

                // Prepare properties
                $properties         = [];
                $compoundPrimaryKey = [];

                $reflectionProperties = $class->getProperties();
                /** @var \ReflectionProperty $reflectionProperty */
                foreach( $reflectionProperties as $reflectionProperty )
                {
                    // Accessibility of each property
                    $accessibility = "public"; // public
                    if( $reflectionProperty->isPrivate() )
                    {
                        $accessibility = "private"; // private
                    }
                    elseif( $reflectionProperty->isProtected() )
                    {
                        $accessibility = "protected"; // protected
                    }

                    // Try to get an SQLIPropertyAnnotation
                    $visible            = true;
                    $readonly           = false;
                    $propertyAnnotation = $this
                        ->annotationReader
                        ->getPropertyAnnotation( $reflectionProperty, SQLIPropertyAnnotation::class );

                    // Check if a visibility information defined on entity's property thanks to 'visible' annotation
                    if( $propertyAnnotation instanceof EntityProperty && !$propertyAnnotation->isVisible() )
                    {
                        $visible = false;
                    }
                    // Check if property must be only in readonly
                    if( $propertyAnnotation instanceof EntityProperty && $propertyAnnotation->isReadonly() )
                    {
                        $readonly = true;
                    }
                    $properties[$reflectionProperty->getName()] = [
                        'accessibility' => $accessibility,
                        'visible'       => $visible,
                        'readonly'      => $readonly,
                    ];

                    // Build primary key from Doctrine\Id annotation
                    if( $this->annotationReader->getPropertyAnnotation( $reflectionProperty, Id::class ) )
                    {
                        $compoundPrimaryKey[] = $reflectionProperty->getName();
                    }
                }

                /** @var SQLIClassAnnotation $classAnnotation */
                $annotationClassname = substr( strrchr( get_class( $classAnnotation ), '\\' ), 1 );

                $annotatedClasses[$annotationClassname][$classNamespace] =
                    [
                        'classname'   => $className,
                        'annotation'  => $classAnnotation,
                        'properties'  => $properties,
                        'primary_key' => $compoundPrimaryKey,
                    ];
            }
        }

        return $annotatedClasses;
    }
}