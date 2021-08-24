<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\DataSource;
use App\Form\DataSourceType;
use App\Bdd\Controller\AvailableType;
use App\Controller\EncryptController;

class DataSourcesController extends AbstractController
{
    /**
     * @isGranted("ROLE_ADMIN")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(DataSource::class);
        $list_dataSources = $repository->findAll();

        // On vérifie si la clé de chiffrement existe
        $keyExists = false;
        if (file_exists(EncryptController::$keyFile) && file_exists(EncryptController::$ivFile)) {
            $keyExists = true;
        }

        return $this->render('data_sources/index.html.twig', array(
            'list_dataSources' => $list_dataSources,
            'privateKey' => $keyExists
        ));
    }

    /**
     * @isGranted("ROLE_ADMIN")
     */
    public function new(Request $request, TranslatorInterface $translator)
    {
        $dataSource = new DataSource;
        $form = $this->createForm(DataSourceType::class, $dataSource);

        // On vérifie si la clé de chiffrement existe
        $keyExists = false;
        if (file_exists(EncryptController::$keyFile) && file_exists(EncryptController::$ivFile)) {
            $keyExists = true;
        }

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /* Ajout typeStrBDD et chifrement des identifiants */
                $dataSource->setTypeStrBDD(AvailableType::$types[$dataSource->getTypeBDD()]);
                $dataSource->setLoginBDD(EncryptController::chiffrer($dataSource->getLoginBDD()));
                $dataSource->setPasswordBDD(EncryptController::chiffrer($dataSource->getPasswordBDD()));

                $em = $this->getDoctrine()->getManager();
                $em->persist($dataSource);
                $em->flush();

                return $this->redirect($this->generateUrl('data_sources_registration_confirmed', array('id' => $dataSource->getId())));
            }
        }


        dump($form);
        return $this->render('data_sources/form.html.twig', array(
            'form' => $form->createView(),
            'title' => $translator->trans('datasources.new'),
            'privateKey' => $keyExists
        ));
    }

    /**
     * Connexion
     */
    public function connect()
    {
        $nameBDD = $_POST['nameBDD'];
        $host = $_POST['host'];
        $port = $_POST['port'];
        $login = $_POST['login'];
        $password = $_POST['password'];
        $type = $_POST['type'];

        $dataSource = new DataSource();

        $dataSource->setNameBDD($nameBDD);
        $dataSource->setHostBDD($host);
        $dataSource->setPortBDD($port);
        $dataSource->setLoginBDD($login);
        $dataSource->setPasswordBDD($password);
        $dataSource->setTypeBDD($type);

        try {
            $bdd = $dataSource->testConnect();
        } catch (Exception $e) {
            return new Response($e);
        }

        return new Response('Connexion Reussi');
    }

    /**
     * @isGranted("ROLE_ADMIN")
     */
    public function confirmed(DataSource $dataSource)
    {
        return $this->render('data_sources/confirmed.html.twig', array('dataSource' => $dataSource));
    }

    /**
     * @isGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, TranslatorInterface $translator, DataSource $dataSource)
    {
        $dataSource->setLoginBDD(EncryptController::dechiffrer($dataSource->getLoginBDD()));
        $form = $this->createForm(DataSourceType::class, $dataSource);

        // On vérifie si la clé de chiffrement existe
        $keyExists = false;
        if (file_exists(EncryptController::$keyFile) && file_exists(EncryptController::$ivFile)) {
            $keyExists = true;
        }

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $dataSource->setTypeStrBDD(AvailableType::$types[$dataSource->getTypeBDD()]);
                $dataSource->setLoginBDD(EncryptController::chiffrer($dataSource->getLoginBDD()));
                $dataSource->setPasswordBDD(EncryptController::chiffrer($dataSource->getPasswordBDD()));

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return $this->redirect($this->generateUrl('data_sources_registration_confirmed', array('id' => $dataSource->getId())));
            }
        }

        return $this->render('data_sources/form.html.twig', array(
            'form' => $form->createView(),
            'title' => $translator->trans('datasources.edit'),
            'privateKey' => $keyExists
        ));
    }

    /**
     * @isGranted("ROLE_ADMIN")
     */
    public function delete(DataSource $dataSource)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($dataSource);
        $em->flush();

        return $this->redirect($this->generateUrl('data_sources_list'));
    }

    /**
     * @isGranted("ROLE_ADMIN")
     */
    public function show(DataSource $dataSource)
    {
        // On vérifie si la clé de chiffrement existe
        $keyExists = false;
        if (file_exists(EncryptController::$keyFile) && file_exists(EncryptController::$ivFile)) {
            $keyExists = true;
        }

        return $this->render('data_sources/show.html.twig', array(
            'dataSource' => $dataSource,
            'privateKey' => $keyExists
            ));
    }
}
