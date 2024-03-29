<?php

namespace App\Form\Chart;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Chart\AvailableChoice\AvailableTypeSerie;
use App\Chart\AvailableChoice\AvailableColorSerie;
use App\Chart\AvailableChoice\AvailableNumberofSerie;
use App\Chart\AvailableChoice\AvailableDashStyleSerie;
use App\Entity\DataList;
use App\Entity\Series;
use App\Repository\DataListRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Symfony\Component\Security\Core\Security;

class SeriesType extends AbstractType
{
      public function __construct(Security $security) {
        $this->security = $security;
      }

	/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
    	$builder
    		->add('titleSerie',		TextType::class,		array(
    			'label' => 'formSerie.title'
    		))
    		->add('unitSerie',		TextType::class,		array(
    			'label' => 'formSerie.unit',
    			'required' => false
    		))
    		->add('dataList',		EntityType::class,	array(
    			'label' => 'formSerie.dataList',
    			'class' => DataList::class,
    			'choice_label' => 'nameData',
    			'placeholder' => 'formSerie.dataListChoice',
                'required' => true,
                'query_builder' => function(DataListRepository $dr) {
                    return $dr->createQueryBuilder('d')
                            ->where('d.userCre = :id')
                            ->orderBy('d.nameData', 'ASC')
                            ->setParameter('id', $this->security->getUser()->getId());
                }
    		))
            ->add('typeSerie',      ChoiceType::class,   array(
                'label' => 'formSerie.type',
                //'placeholder' => 'Choisir une option',
                'choices' => array_flip(AvailableTypeSerie::$typesSerie),
            ))
    		->add('colorSerie',		ChoiceType::class,	array(
    			'label' => 'formSerie.color',
    			'choices' => AvailableColorSerie::$colorSerie
    		))
    		->add('markerSerie',	CheckboxType::class,	array(
    			'label' => 'formSerie.marker',
    			'required' => false
    		))
    		->add('dashStyleSerie',	ChoiceType::class,	array(
    			'label' => 'formSerie.dashStyle',
    			'choices' => array_flip(AvailableDashStyleSerie::$dashStyleSerie)
    		))        
            ->add('yaxisOrder',      TextType::class,     array(
                'label' => 'formSerie.yaxisOrder',
                'required' => false
             ))
            ->add('size',  ChoiceType::class, array(
                    'label' => 'formSerie.size',
                    'required' => false,
                    'choices' =>  AvailableTypeSerie::$sizetype,
                    'choice_translation_domain' => true
            )) 
            ->add('innersize',  ChoiceType::class, array(
                    'label' => 'formSerie.innersize',
                    'required' => false,
                    'choices' =>  AvailableTypeSerie::$sizetype,
                    'choice_translation_domain' => true
            ))
            ->add('colsize',  TextType::class, array(
                    'label' => 'formSerie.colsize',
                    'required' => false
            ));
        
        // Permet de valider les couleurs ajoutées dynamiquement avec le color picker.
		$builder->get('colorSerie')->resetViewTransformers();

    	/* Ajout du champ parameterDataList auto-complété par un dataList en argument */
    	$formModifier = function(FormInterface $form, $dataList) {
    		/* Si dataList n'est pas null = auto-complétion */
    		if ($dataList != null) {
    			$parameter = $dataList->getParameter();
    			$form
    				->add('parameterDataList',	ChoiceType::class,	array(
    					'label' => 'formSerie.parameter',
    					'choices' => array_flip($parameter)
    				));
    		} else {
    			$form
    				->add('parameterDataList',	ChoiceType::class,	array(
    					'label' => 'formSerie.parameter',
    					'choices' => array()
    				));
    		}
    	};

    	/* Listener sur PRE_SET_DATA (liaison entre l'objet et le formulaire)
    	   La serie est vide à la création et non à l'édition */
    	$builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($formModifier) {
    		$data = $event->getData();
    		/* Si édition d'une serie */
    		if ($data != null && $data->getDataList() != null) {
    			$data->getDataList()->executeQuery();
    			$formModifier($event->getForm(), $data->getDataList());
    		} else {
    			$formModifier($event->getForm(), null);
    		}
    	});

		/* Listener sur POST_SUBMIT de dataList */
		$builder->get('dataList')->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($formModifier) {
			/* $event->getData() est vide, on récupère $event->getForm()->getData() */
			$dataList = $event->getForm()->getData();
			$dataList->executeQuery('default', false,false);//Rajout d'un false pour avoir le 3eme parametres

			$formModifier($event->getForm()->getParent(), $dataList);
		});
        array_push(AvailableNumberofSerie::$nbSerie, 'Série');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults(array(
			'data_class' => Series::class,
			'allow_extra_fields' => true
		));
    }

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return 'mc_chartbundle_series';
    }
}
