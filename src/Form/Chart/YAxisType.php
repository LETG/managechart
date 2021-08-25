<?php

namespace App\Form\Chart;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Chart\AvailableChoice\AvailableTypeAxis;
use App\Entity\YAxis;

class YAxisType extends AbstractType
{
	/**
	 * @param FormBuilderInterface $builder
     * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('titleYAxis', TextType::class, array(
				'label' => 'formYAxis.title',
				'required' => false
			))
			->add('typeYAxis', ChoiceType::class, array(
				'label' => 'formYAxis.type',
				'choices' => AvailableTypeAxis::$typesAxis
			))
			->add('flag', CollectionType::class, array(
				'label' => 'formYAxis.flag',
				'entry_type' => FlagType::class,
				'allow_add' => true,
				'allow_delete' => true,
				'by_reference' => false
			))
			->add('series', CollectionType::class, array(
				'label' => 'formYAxis.series',
				'entry_type' => SeriesType::class,
				'allow_add' => true,
				'allow_delete' => true,
				'by_reference' => false
			))
			->add('top', ChoiceType::class, array(
				'label' => 'formYAxis.top',
				'choices' => AvailableTypeAxis::$top
				//,'required' => false
				,'data' => '0%'
			))
			->add('height', ChoiceType::class, array(
				'label' => 'formYAxis.type',
				'choices' => AvailableTypeAxis::$height,
				//'required' => false,
				'data' => '100%'
			))
			->add('opposite', CheckboxType::class, array(
				'label' => 'formYAxis.opposite',
				'required' => false
			))
			->add('orderY', TextType::class, array(
                'label' => 'formYAxis.orderY',
                'required' => false
             ));
	}

	/**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
        	'data_class' => YAxis::class,
        	'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return 'mc_chartbundle_yaxis';
    }
}
