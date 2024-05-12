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
use Doctrine\ORM\EntityRepository;
use App\Repository\DataSourceRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Form\EventListener\AddUserDateFields;
use Doctrine\ORM\EntityManagerInterface;

class DataListType extends ActionFormType
{
    /**
	 * @param FormBuilderInterface $builder
     * @param array $options
     */
	public function buildForm(FormBuilderInterface $builder, array $options) {
                $data = $builder->getData();
                $datasource = $data->getDatasource();
                // var_dump($data);
                $user = $this->security->getUser();
                $userCre = $this->security->getUser()->getUserCre();
                if($userCre == 1){
                    $userCre = $this->security->getUser()->getId();
                }
                
		$builder
			->add('nameData',			TextType::class,					array(
				'label' => 'formDataList.name'
			));
                if ($datasource){
			$builder->add('dataSource', 		EntityType::class,				array(
				'label' => 'formDataList.dataSource',
				'class' => DataSource::class,
                                'query_builder' => function (EntityRepository $er) use ($datasource, $userCre) {
                                        if ($datasource) {
                                            return $er->createQueryBuilder('d')
                                                ->where('d.userCre = :id')
                                                ->setParameter('id', $userCre);
                                        } 
                                    },
				'choice_label' => 'getUniqueName'
			));
                } else {
                    	$builder->add('dataSource', 		EntityType::class,				array(
				'label' => 'formDataList.dataSource',
				'class' => DataSource::class,
                                'query_builder' => function(DataSourceRepository $ttr) use ($userCre) {
                                    return $ttr->findDataSourceListForUserCre($userCre);
                                    
                                },
                                'choice_label' => 'getUniqueName'
			));                    
                }
		$builder->add('requestData',		TextareaType::class,				array(
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
