<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Chart\AvailableChoice\AvailableTypeAxis;
use App\Chart\AvailableChoice\AvailablePolarType;
use App\Chart\AvailableChoice\AvailableTypeSerie;
use App\Chart\AvailableChoice\AvailableHeatmapType;
use App\Entity\Chart;
use App\Form\Chart\YAxisType;

use App\Form\Type\ActionFormType;

class ChartType extends ActionFormType
{
	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
    	$builder
    		->add('nameChart',        TextType::class,		array(
    			'label' => 'formChart.name'
                ,'required' => true
    		))
    		->add('creditsChart',     TextType::class,		array(
    			'label' => 'formChart.credits'
                ,'required' => true
    		))
    		->add('titleChart',       TextType::class,		array(
    			'label' => 'formChart.title',
    			'required' => false
    		))
    		->add('urlcreditsChart',  TextType::class,		array(
    			'label' => 'formChart.urlcredits',
    			'required' => false
    		))
    		->add('subtitleChart',    TextType::class,		array(
    			'label' => 'formChart.subtitle',
    			'required' => false
    		))
    		->add('legendChart',      CheckboxType::class,	array(
    			'label' => 'formChart.legend',
    			'required' => false
    		))
    		->add('tooltipChart',      CheckboxType::class,	array(
    			'label' => 'formChart.tooltip',
    			'required' => false
    		))
                ->add('publicChart',      CheckboxType::class,	array(
    			'label' => 'formChart.public',
    			'required' => true
    		))
            ->add('xAxisTitle',       TextType::class,        array(
                'label' => 'formChart.xAxisTitle',
                'required' => false
            ))
            ->add('xAxisUnit',        TextType::class,       array(
                'label' => 'formChart.xAxisUnit',
                'required' => false
            ))
            ->add('exportPrintChart', CheckboxType::class,   array(
                'label' => 'formChart.exportPrint',
                'required' => false
           ))
            ->add('exportCSVChart',   CheckboxType::class,   array(
                'label' => 'formChart.exportCSV',
                'required' => false
            ))
            ->add('list_yAxis',       CollectionType::class, array(
                'label' => 'formChart.listYAxis',
                'entry_type' => YAxisType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__nameyaxis__'
            ));

    	$entity = $builder->getData();
    	/* Si le chart est de type highchart */
    	if ($entity->getTypeChart() == 'simplechart') {
    		$builder
                ->add('xAxisType',      ChoiceType::class,   array(
                    'label' => 'formChart.xAxisType',
                    'choices' => array_flip(AvailableTypeAxis::$typesAxis),
                    'choice_translation_domain' => true,
                ))
                ->add('invertedChart',  CheckboxType::class, array(
                    'label' => 'formChart.inverted',
                    'required' => false
                ))
                ->add('typestacked',      ChoiceType::class,   array(
                    'label' => 'formChart.typestacked',
                    'choices' => array_flip(AvailableTypeSerie::$typesStack),
                    'placeholder' => 'Choisir une option',
                    'required' => false,
                    'choice_translation_domain' => true,
                )); 
              }

       else if ($entity->getTypeChart() == 'dynamicchart'){
                $builder
                 ->add('xAxisType',      ChoiceType::class,   array(
                    'label' => 'formChart.xAxisType',
                    'choices' => array_flip(AvailableTypeAxis::$typesAxis),
                    'choice_translation_domain' => true,
                ))
                ->add('invertedChart',  CheckboxType::class, array(
                    'label' => 'formChart.inverted',
                    'required' => false
                ));

              }  

        else if ($entity->getTypeChart() == 'piechart') {
            $builder
                ->add('xAxisType',      ChoiceType::class,   array(
                    'label' => 'formChart.xAxisType',
                    'choices' => array_flip(AvailableTypeAxis::$typesAxis),
                    'choice_translation_domain' => true
                ))
                ->add('invertedChart',  CheckboxType::class, array(
                    'label' => 'formChart.inverted',
                    'required' => false
                ));
                
        }
    	else if ($entity->getTypeChart() == 'polarchart') {
            $builder
                ->add('xAxisType',      ChoiceType::class,   array(
                    'label' => 'formChart.xAxisType',
                    'choices' => array_flip(AvailableTypeAxis::$typesAxis),
                    'choice_translation_domain' => true
                ))
                ->add('invertedChart',  CheckboxType::class, array(
                    'label' => 'formChart.inverted',
                    'required' => false
                ))
                ->add('polarType',      ChoiceType::class,   array(
                    'label' => 'formChart.polarType',
                    'choices' => array_flip(AvailablePolarType::$polarTypes),
                    'choice_translation_domain' => true
                ));
        }
        else if ($entity->getTypeChart() == 'simplechart')  {
            $builder
                ->add('gapSizeChart', IntegerType::class,  array(
                    'label' => 'formChart.gapSize'
                ));
         }

        else if ($entity->getTypeChart() == 'heatmapchart') {
            $builder
                ->add('xAxisType',      ChoiceType::class,   array(
                    'label' => 'formChart.xAxisType',
                    'choices' => array_flip(AvailableTypeAxis::$typesAxis),
                    'choice_translation_domain' => true
                ))
                ->add('invertedChart',  CheckboxType::class, array(
                    'label' => 'formChart.inverted',
                    'required' => false
                ));
                /*->add('heatmapType',      ChoiceType::class,   array(
                    'label' => 'formChart.heatmapType',
                    'placeholder' => 'Choisir une option',
                    'choices' => AvailableHeatmapType::$heatTypes,
                    'choice_translation_domain' => true
                ));*/
        } 
       
        $builder->addEventSubscriber($this->addUserDate);
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => Chart::class,
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return 'mc_chartbundle_chart';
    }
}
