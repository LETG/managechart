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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

class AdminEditFormType extends AbstractType
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
			]);
	}
	
	
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity(['fields' => ['username']]),
                new UniqueEntity(['fields' => ['email']])
            ]
        ]);
    }
	
	public function getBlockPrefix() {
		return 'mc_user_admin_edit';
	}
}
