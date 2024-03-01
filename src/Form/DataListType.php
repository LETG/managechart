<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use App\Entity\AttributSpatial;
use App\Entity\DataList;
use App\Entity\DataSource;

use App\Form\Type\ActionFormType;

class DataListType extends ActionFormType
{
	/**
	 * @param FormBuilderInterface $builder
     * @param array $options
     */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('nameData',			TextType::class,					array(
				'label' => 'formDataList.name'
			))
			->add('dataSource', 		EntityType::class,				array(
				'label' => 'formDataList.dataSource',
				'class' => DataSource::class,
				'choice_label' => 'getUniqueName'
			))
			->add('requestData',		TextareaType::class,				array(
				'label' => 'formDataList.request'
			))
			->add('attributsSpatiaux',	CollectionType::class, 			array(
				'label' 		=> 'formDataList.attributes',
				'entry_type' 			=> AttributSpatialType::class,
  				'allow_add'    	=> true,
  				'allow_delete' 	=> true
			));
                
                $builder->addEventSubscriber($this->addUserDate);
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array('data_class' => DataList::class));
	}

	/**
     * @return string
     */
    public function getBlockPrefix() {
        return 'mc_datalistbundle_datalist';
    }
}
