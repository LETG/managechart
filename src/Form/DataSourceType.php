<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Bdd\Controller\AvailableType;

use App\Form\Type\ActionFormType;

class DataSourceType extends ActionFormType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('nameBDD',		TextType::class,				array(
				'label' => 'formDataSource.name'
			))
			->add('nameCon',		TextType::class,				array(
				'label' => 'formDataSource.nameCon'
			))
			->add('descriptionBDD',	TextareaType::class,			array(
				'label' => 'formDataSource.description'
			))
			->add('hostBDD',		TextType::class,				array(
				'label' => 'formDataSource.host'
			))
			->add('portBDD',		IntegerType::class,			array(
				'label' => 'formDataSource.port'
			))
			->add('loginBDD',		TextType::class,				array(
				'label' => 'formDataSource.login'
			))
			->add('passwordBDD',	PasswordType::class,			array(
				'label' => 'formDataSource.password'
			))
			->add('typeBDD',		ChoiceType::class,	array(
				'label' => 'formDataSource.type',
				'choices' => array_flip(AvailableType::$types)
			))
			->add('typeStrBDD',		HiddenType::class);
                
                $builder->addEventSubscriber($this->addUserDate);
	}

	/**
	 * @param OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array('data_class' => 'App\Entity\DataSource'));
	}

	/**
	 * @return string
	 */
	public function getBlockPrefix() {
		return 'mc_datasourcesbundle_datasource';
	}
}
