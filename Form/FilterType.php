<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Form;

use SQLI\EzPlatformAdminUiExtendedBundle\Classes\Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $classInformations = $options['class_informations'];

        $builder->add( "column_name", ChoiceType::class,
                       [
                           'choices' => array_combine( array_keys( $classInformations['properties'] ), array_keys( $classInformations['properties'] ) ),
                       ] );

        $builder->add( "operand", ChoiceType::class,
                       [
                           'choices' => Filter::OPERANDS_MAPPING,
                       ] );
        $builder->add( "value", TextType::class,
                       [
                           'attr' => [ 'placeholder' => "Valeur" ],
                       ] );

        $builder->add( 'filter', SubmitType::class,
                       [
                           "label" => "Filtrer",
                       ] );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setRequired( 'class_informations' );
    }
}