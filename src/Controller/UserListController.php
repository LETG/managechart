<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Mc\UserBundle\Entity\Account;
use Mc\UserBundle\Form\Type\AdminEditFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserListController extends AbstractController
{
    /**
     * Affiche l'utilisateur
     * @isGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function show(Account $user)
    {
        /* Si l'utilisateur n'est pas admin, affiche son profile */
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') && $user->getId() != $this->getUser()->getId()) {
            throw new AccessDeniedHttpException("Accès interdit");
        } else {
            return $this->render('McUserBundle:UserList:show.html.twig', array('user' => $user));
        }
    }

    /**
     * Affiche la liste des utilisateurs
     * @isGranted("ROLE_ADMIN")
     */
    public function index()
    {
        $userManager = $this->get('fos_user.user_manager');
        $users = $userManager->findUsers();

        return $this->render('McUserBundle:UserList:index.html.twig', array('listUsers' => $users));
    }

    /**
    * Edition d'utilisateurs
    * @isGranted("ROLE_ADMIN")
    */
    public function edit($id, Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array('id'=>$id));

        $form = $this->createForm(new AdminEditFormType('Mc\UserBundle\Entity\Account'), $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $userManager = $this->get('fos_user.user_manager');
                $userManager->updateUser($user);

                return $this->redirect($this->generateUrl('user_list'));
        }

        return $this->render('McUserBundle:UserEdit:edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));
    }

    /**
     * Suppression d'utilisateurs
     * @isGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function delete(Account $user)
    {
        /* l'utilisateur ne peut pas supprimer son compte */
        if ($user === $this->getUser()) {
            $translator = $this->get('translator');
            $msgError = $translator->trans('userlistcontroller.deleteitself.msgError');

            $this->get('session')->getFlashBag()->add('deleteItSelf', $msgError);
        } else {
            $userManager = $this->get('fos_user.user_manager');
            $userManager->deleteUser($user);
        }
        return $this->redirect($this->generateUrl('user_list'));
    }
}
