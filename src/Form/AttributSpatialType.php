<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\AttributSpatial;

class AttributSpatialType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
     * @param array $options
     */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('nameAttribut',		TextType::class,				array(
				'label' => 'formAttributes.name'
			))
			->add('valueAttribut',		TextType::class,				array(
				'label' => 'formAttributes.value'
			))
			->add('typeAttribut',		ChoiceType::class,			array(
				'label' => 'formAttributes.type',
				'choices' => array('numeric' => 'formAttributes.typeNumeric', 'string' => 'formAttributes.typeString')
			))
            ->add('keywordAttribut',	ChoiceType::class,			array(
				'label' => 'formAttributes.keyword',
				'choices' => array(' ' => ' ', 'AND' => 'AND', 'OR' => 'OR' , 'WHERE' => 'WHERE')
			));
	}

	/**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array('data_class' => AttributSpatial::class));
    }

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return 'mc_datalistbundle_attributspatial';
    }
}
