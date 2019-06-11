<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditElementType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $element = $options['entity'];

        foreach( $element['class']['properties'] as $propertyName => $propertyInfos )
        {
            // If property can be visible, add it to formbuilder
            if( $propertyInfos['visible'] )
            {
                $params = [];
                if( $propertyInfos['readonly'] )
                {
                    // Readonly attribute for this attribute
                    $params = [ 'attr' => [ 'readonly' => true, 'class' => 'bg-transparent' ] ];
                }
                $builder->add( $propertyName, null, $params );
            }
        }

        $builder
            ->add( 'submit', SubmitType::class,
                   [
                       'label'              => 'form.button.label.submit',
                       'translation_domain' => 'forms',
                       'attr'               => [ 'class' => 'd-none' ],
                   ] );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setRequired( 'entity' );
    }
}