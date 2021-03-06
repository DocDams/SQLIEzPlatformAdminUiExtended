<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle\Form;

use SQLI\EzPlatformAdminUiExtendedBundle\Classes\Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class FilterType extends AbstractType
{
    /** @var TranslatorInterface */
    private $translator;

    public function __construct( TranslatorInterface $translator )
    {
        $this->translator = $translator;
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $classInformations = $options['class_informations'];

        $builder->add( "column_name", ChoiceType::class,
                       [
                           'choices' => array_combine( array_keys( $classInformations['properties'] ),
                                                       array_keys( $classInformations['properties'] ) ),
                       ] );

        $builder->add( "operand", ChoiceType::class,
                       [
                           'choices' => Filter::OPERANDS_MAPPING,
                       ] );
        $builder->add( "value", TextType::class,
                       [
                           'attr' =>
                               [
                                   'placeholder' => $this->translator->trans( "entity.field.placeholder.value",
                                                                              [],
                                                                              "sqli_admin" )
                               ],
                       ] );

        $builder->add( 'filter', SubmitType::class,
                       [
                           "label" => $this->translator->trans( "entity.button.label.filter",
                                                                [],
                                                                "sqli_admin" ),
                       ] );
    }

    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setRequired( 'class_informations' );
    }
}