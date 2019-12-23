<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Controller;

use ReflectionException;
use SQLI\EzPlatformAdminUiExtendedBundle\Annotations\Annotation\Entity;
use SQLI\EzPlatformAdminUiExtendedBundle\Form\EditElementType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EntitiesController extends Controller
{
    /**
     * Display all entities annotated with SQLIAdmin\Entity
     *
     * @return Response
     * @throws ReflectionException
     */
    public function listAllEntitiesAction()
    {
        $this->denyAccessUnlessGranted( 'ez:sqli_admin:list_entities' );

        $params['classes'] = $this->get( 'sqli_admin_entities' )->getAnnotatedClasses();

        return $this->render( 'SQLIEzPlatformAdminUiExtendedBundle:Entities:listAllEntities.html.twig', $params );
    }

    /**
     * Display an entity (lines in SQL table)
     *
     * @param $fqcn
     * @return Response
     * @throws ReflectionException
     */
    public function showEntityAction( $fqcn, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ez:sqli_admin:entity_show' );

        // Entity informations and all elements
        $params = $this->get( 'sqli_admin_entities' )->getEntity( $fqcn );
        // Change current page on PagerFanta
        $params['elements']->setCurrentPage( $request->get( 'page', 1 ) );

        return $this->render( 'SQLIEzPlatformAdminUiExtendedBundle:Entities:showEntity.html.twig', $params );
    }

    /**
     * Remove an element
     *
     * @param $fqcn
     * @param $compound_id string Compound primary key in JSON string
     * @return Response
     * @throws ReflectionException
     */
    public function removeElementAction( $fqcn, $compound_id )
    {
        $this->denyAccessUnlessGranted( 'ez:sqli_admin:entity_remove_element' );

        $removeSuccessfull = false;

        // Check if class annotation allow deletion
        $entity = $this->get( 'sqli_admin_entities' )->getEntity( $fqcn, false );

        if( array_key_exists( 'class', $entity ) && array_key_exists( 'annotation', $entity['class'] ) )
        {
            $entityAnnotation = $entity['class']['annotation'];
            // Check if annotation exists
            if( $entityAnnotation instanceof Entity )
            {
                // Check if deletion is allowed
                if( $entityAnnotation->isDelete() )
                {
                    // Try to decode compound Id
                    $compound_id = json_decode( $compound_id, true );

                    // If valid compound Id, remove element
                    if( !empty( $compound_id ) )
                    {
                        $this->get( 'sqli_admin_entities' )->remove( $fqcn, $compound_id );
                        $removeSuccessfull = true;
                    }
                }
            }
        }

        if( $removeSuccessfull )
        {
            // Display success notification
            $this
                ->get( 'EzSystems\EzPlatformAdminUi\Notification\FlashBagNotificationHandler' )
                ->success( $this
                               ->get( 'translator' )
                               ->trans( 'entity.element.deleted', [], 'sqli_admin' ) );
        }
        else
        {
            // Display error notification
            $this
                ->get( 'EzSystems\EzPlatformAdminUi\Notification\FlashBagNotificationHandler' )
                ->error( $this
                             ->get( 'translator' )
                             ->trans( 'entity.element.cannot_delete', [], 'sqli_admin' ) );
        }

        // Redirect to entity homepage (list of elements)
        return $this->redirectToRoute( 'sqli_ez_platform_admin_ui_extended_entity_homepage',
                                       [ 'fqcn' => $fqcn ] );
    }

    /**
     * Show edit form and save modifications
     *
     * @param string  $fqcn FQCN
     * @param string  $compound_id Json format
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws ReflectionException
     */
    public function editElementAction( $fqcn, $compound_id, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ez:sqli_admin:entity_edit_element' );

        $updateSuccessfull = false;

        // Check if class annotation allow modification
        $entity = $this->get( 'sqli_admin_entities' )->getEntity( $fqcn, false );

        if( array_key_exists( 'class', $entity ) && array_key_exists( 'annotation', $entity['class'] ) )
        {
            $entityAnnotation = $entity['class']['annotation'];
            // Check if annotation exists
            if( $entityAnnotation instanceof Entity )
            {
                // Check if modification is allowed
                if( $entityAnnotation->isUpdate() )
                {
                    // Try to decode compound Id
                    $compound_id = json_decode( $compound_id, true );

                    // If valid compound Id, update element
                    if( !empty( $compound_id ) )
                    {
                        // Find element
                        $element = $this->get( 'sqli_admin_entities' )->findOneBy( $fqcn, $compound_id );

                        // Build form according to element and entity informations
                        $form = $this->createForm( EditElementType::class, $element, [ 'entity' => $entity ] );
                        $form->handleRequest( $request );

                        if( $form->isSubmitted() && $form->isValid() )
                        {
                            // Form is valid, update element
                            $this->get( 'doctrine.orm.entity_manager' )->persist( $element );
                            $this->get( 'doctrine.orm.entity_manager' )->flush();

                            $updateSuccessfull = true;
                        }
                        else
                        {
                            // Display form
                            $params['form'] = $form->createView();
                            $params['fqcn'] = $fqcn;

                            return $this
                                ->render( 'SQLIEzPlatformAdminUiExtendedBundle:Entities:editElement.html.twig',
                                          $params );
                        }
                    }
                }
            }
        }

        if( $updateSuccessfull )
        {
            // Display success notification
            $this
                ->get( 'EzSystems\EzPlatformAdminUi\Notification\FlashBagNotificationHandler' )
                ->success( $this
                               ->get( 'translator' )
                               ->trans( 'entity.element.updated', [], 'sqli_admin' ) );
        }
        else
        {
            // Display error notification
            $this
                ->get( 'EzSystems\EzPlatformAdminUi\Notification\FlashBagNotificationHandler' )
                ->success( $this
                               ->get( 'translator' )
                               ->trans( 'entity.element.cannot_update', [], 'sqli_admin' ) );
        }

        // Redirect to entity homepage (list of elements)
        return $this->redirectToRoute( 'sqli_ez_platform_admin_ui_extended_entity_homepage',
                                       [ 'fqcn' => $fqcn ] );
    }

    /**
     * Show edit form and save modifications
     *
     * @param string  $fqcn FQCN
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws ReflectionException
     */
    public function createElementAction( $fqcn, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ez:sqli_admin:entity_edit_element' );

        $updateSuccessfull = false;

        // Check if class annotation allow modification
        $entity = $this->get( 'sqli_admin_entities' )->getEntity( $fqcn, false );

        if( array_key_exists( 'class', $entity ) && array_key_exists( 'annotation', $entity['class'] ) )
        {
            $entityAnnotation = $entity['class']['annotation'];
            // Check if annotation exists
            if( $entityAnnotation instanceof Entity )
            {
                // Check if modification is allowed
                if( $entityAnnotation->isUpdate() )
                {
                    // New element
                    $element = new $fqcn();

                    // Build form according to element and entity informations
                    $form = $this->createForm( EditElementType::class, $element, [ 'entity' => $entity ] );
                    $form->handleRequest( $request );

                    if( $form->isSubmitted() && $form->isValid() )
                    {
                        // Form is valid, update element
                        $this->get( 'doctrine.orm.entity_manager' )->persist( $element );
                        $this->get( 'doctrine.orm.entity_manager' )->flush();

                        $updateSuccessfull = true;
                    }
                    else
                    {
                        // Display form
                        $params['form'] = $form->createView();
                        $params['fqcn'] = $fqcn;

                        return $this
                            ->render( 'SQLIEzPlatformAdminUiExtendedBundle:Entities:createElement.html.twig',
                                      $params );
                    }
                }
            }
        }

        if( $updateSuccessfull )
        {
            // Display success notification
            $this
                ->get( 'EzSystems\EzPlatformAdminUi\Notification\FlashBagNotificationHandler' )
                ->success( $this
                               ->get( 'translator' )
                               ->trans( 'entity.element.created', [], 'sqli_admin' ) );
        }
        else
        {
            // Display error notification
            $this
                ->get( 'EzSystems\EzPlatformAdminUi\Notification\FlashBagNotificationHandler' )
                ->success( $this
                               ->get( 'translator' )
                               ->trans( 'entity.element.cannot_create', [], 'sqli_admin' ) );
        }

        // Redirect to entity homepage (list of elements)
        return $this->redirectToRoute( 'sqli_ez_platform_admin_ui_extended_entity_homepage',
                                       [ 'fqcn' => $fqcn ] );
    }
}
