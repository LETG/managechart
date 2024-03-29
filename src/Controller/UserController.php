<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Form\User\AdminEditFormType;
use App\Form\User\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * Affiche l'utilisateur
     * @isGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function show(User $user)
    {
        /* Si l'utilisateur n'est pas admin, affiche son profile */
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') && $user->getId() != $this->getUser()->getId()) {
            throw new AccessDeniedHttpException("Accès interdit");
        } else {
            return $this->render('users/show.html.twig', array('user' => $user));
        }
    }

    /**
     * Affiche la liste des utilisateurs
     * @isGranted("ROLE_ADMIN")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('users/index.html.twig', array('listUsers' => $users));
    }

    /**
    * Création d'utilisateurs
    * @isGranted("ROLE_ADMIN")
    */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('user_list'));
        }

        return $this->render('users/register.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
    * Edition d'utilisateurs
    * @isGranted("ROLE_ADMIN")
    */
    public function edit(User $user, Request $request)
    {
        $form = $this->createForm(AdminEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('user_list'));
        }

        return $this->render('users/edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * Suppression d'utilisateurs
     * @isGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function delete(User $user)
    {
        /* l'utilisateur ne peut pas supprimer son compte */
        if ($user === $this->getUser()) {
            $translator = $this->get('translator');
            $msgError = $translator->trans('userlistcontroller.deleteitself.msgError');

            $this->get('session')->getFlashBag()->add('deleteItSelf', $msgError);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('user_list'));
    }
}
