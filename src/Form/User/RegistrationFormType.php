<?php

namespace App\Form\User;

use App\Entity\User;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

use App\Form\Type\ActionFormType;

use App\Entity\DataSource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RegistrationFormType extends ActionFormType
{
	public function buildForm(FormBuilderInterface $builder, array $options) 
	{
		$builder
			->add('username', TextType::class, [
				'constraints' => [
					new NotBlank(),
					new Length(['min' => 2, 'max' => 180]),
				],
			])
            ->add('email', EmailType::class, [
				'constraints' => [
					new NotBlank(),
					new Length(['min' => 2, 'max' => 180]),
					new Email(),
				],
			])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('roles', ChoiceType::class, [
                    'constraints' => [
                            new NotBlank()
                    ],
                    'choices' => array_flip([
                            'ROLE_ADMIN' => 'Admin',
                            'ROLE_SCIENTIFIC_PLUS' => 'Scientific+',
                            'ROLE_SCIENTIFIC' => 'Scientific'
                    ]),
                    'multiple' => true,
            ])
            ->add('dataSource', EntityType::class, array(
                    'label' => 'formDataList.dataSource',
                    'class' => DataSource::class,
                    'choice_label' => 'getUniqueName',
                    'placeholder'   => 'Nom de la base de donnée',
                    'required'      => false
            ))
            ->addEventSubscriber($this->addUserDate);
	}
        
	
	
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity(['fields' => ['username']]),
                new UniqueEntity(['fields' => ['email']])
            ]
        ]);
    }
	
	public function getBlockPrefix(): string {
		return 'mc_user_registration';
	}
}
