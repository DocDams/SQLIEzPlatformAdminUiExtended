<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use SQLI\EzPlatformAdminUiExtendedBundle\Annotations\Annotation\Entity;
use SQLI\EzPlatformAdminUiExtendedBundle\Annotations\SQLIAnnotationManager;

class EntityHelper
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var SQLIAnnotationManager */
    private $annotationManager;

    public function __construct( EntityManagerInterface $entityManager, SQLIAnnotationManager $annotationManager )
    {
        $this->entityManager     = $entityManager;
        $this->annotationManager = $annotationManager;
    }

    /**
     * Get all classes annotated with SQLIClassAnnotation interface
     *
     * @return array
     * @throws \ReflectionException
     */
    public function getAnnotatedClasses()
    {
        $annotatedClasses = $this->annotationManager->getAnnotatedClasses();

        foreach( $annotatedClasses as $annotatedFQCN => &$annotatedClass )
        {
            $annotatedClass['count'] = $this->count( $annotatedFQCN );
        }

        return $annotatedClasses;
    }

    /**
     * Get a class annotated with SQLIClassAnnotation interface from her FQCN
     *
     * @param $fqcn
     * @return mixed|null
     * @throws \ReflectionException
     */
    public function getAnnotatedClass( $fqcn )
    {
        $annotatedClasses = $this->getAnnotatedClasses();

        return array_key_exists( $fqcn, $annotatedClasses ) ? $annotatedClasses[$fqcn] : null;
    }

    /**
     * Get an entity with her information and elements
     *
     * @param string $fqcn
     * @param bool   $fetchElements
     * @return mixed
     * @throws \ReflectionException
     */
    public function getEntity( $fqcn, $fetchElements = true )
    {
        $annotatedClass['fqcn']  = $fqcn;
        $annotatedClass['class'] = $this->getAnnotatedClass( $fqcn );

        // Prepare a filter (only properties flagged as visible or without this annotation) for findAll
        $filteredColums = [];
        foreach( $annotatedClass['class']['properties'] as $propertyName => $propertyInfos )
        {
            if( $propertyInfos['visible'] )
            {
                $filteredColums[] = $propertyName;
            }
        }

        if( $fetchElements )
        {
            // Get all elements
            /** @var Entity $classAnnotation */
            $classAnnotation = $annotatedClass['class']['annotation'];
            $pager           = new Pagerfanta( new ArrayAdapter( $this->findAll( $fqcn, $filteredColums ) ) );
            $pager->setMaxPerPage( $classAnnotation->getMaxPerPage() );

            $annotatedClass['elements'] = $pager;
        }

        return $annotatedClass;
    }

    /**
     * Retrieve all lines in SQL table
     *
     * @param string     $entityClass FQCN
     * @param array|null $filteredColums
     * @return array
     */
    public function findAll( $entityClass, $filteredColums = null )
    {
        /** @var $repository EntityRepository */
        $repository   = $this->entityManager->getRepository( $entityClass );
        $queryBuilder = $repository->createQueryBuilder( 'entity' );

        // In case of filtering columns
        if( is_array( $filteredColums ) )
        {
            array_walk( $filteredColums, function( &$columnName )
            {
                $columnName = "entity.$columnName";
            } );
            $select = implode( ",", $filteredColums );

            // Change SELECT clause
            $queryBuilder->select( $select );
        }

        // Return results as array (ignore accessibility of properties)
        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * Count number of element for an entity
     *
     * @param string $entityClass FQCN
     * @return integer
     */
    public function count( $entityClass )
    {
        return $this->entityManager->getRepository( $entityClass )->count( [] );
    }

    /**
     * Remove an element
     * $findCriteria = ['columnName' => 'value']
     *
     * @param string $entityClass FQCN
     * @param array  $findCriteria
     */
    public function remove( $entityClass, $findCriteria )
    {
        $element = $this->findOneBy( $entityClass, $findCriteria );
        if( !is_null( $element ) )
        {
            $this->entityManager->remove( $element );
            $this->entityManager->flush();
        }
    }

    /**
     * Find one element
     *
     * @param $entityClass
     * @param $findCriteria
     * @return object|null
     */
    public function findOneBy( $entityClass, $findCriteria )
    {
        return $this->entityManager->getRepository( $entityClass )->findOneBy( $findCriteria );
    }
}