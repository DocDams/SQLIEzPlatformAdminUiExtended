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
            if( $propertyInfos['visible'] )
            {
                $builder->add( $propertyName );
            }
        }

        $builder
            ->add( 'submit', SubmitType::class,
                   [
                       'label' => 'form.button.label.submit',
                       'translation_domain' => 'forms',
                   ]);
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setRequired( 'entity' );
    }
}