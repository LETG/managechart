<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\DataSource;

class EncryptController extends AbstractController
{
    public static $keyFile = __DIR__ . '/../Resources/privateKey/key.txt';
    public static $ivFile = __DIR__ . '/../Resources/privateKey/iv.txt';


    /**
     * Chiffrement d'un message en clair
     */
    public static function chiffrer($clair)
    {
        $chiffre = false;

        if (file_exists(self::$keyFile) && file_exists(self::$ivFile))
        {
            $key = base64_decode(file_get_contents(self::$keyFile));
            $iv = base64_decode(file_get_contents(self::$ivFile));

            if ($key && $iv) {
                $chiffre = openssl_encrypt($clair, "AES256", $key, 0, $iv);
            }
        }

        // On encode en base64 pour qu'il n'y ai pas de problème d'encodage lors du stockage en BDD
        return base64_encode($chiffre);
    }


    /**
     * Déchiffrement d'un message chiffré
     */
    public static function dechiffrer($chiffre)
    {
        $clair = false;

        if (file_exists(self::$keyFile) && file_exists(self::$ivFile))
        {
            $key = base64_decode(file_get_contents(self::$keyFile));
            $iv = base64_decode(file_get_contents(self::$ivFile));

            if ($key && $iv) {
                $clair = openssl_decrypt(base64_decode($chiffre), "AES256", $key, 0, $iv);
            }
        }

        return $clair;
    }


    /**
     * Rechiffre tous les identifiants et mots de passe en base de données
     * @isGranted("ROLE_ADMIN")
     */
    public function rechiffrerBDDAction()
    {
        // Si il existe déjà une clé privée, on en crée une nouvelle puis on rechiffre la base de données
        if (file_exists(self::$keyFile) && file_exists(self::$ivFile))
        {
            $oldKey = base64_decode(file_get_contents(self::$keyFile));
            $oldIv = base64_decode(file_get_contents(self::$ivFile));

            file_put_contents(self::$keyFile, base64_encode(openssl_random_pseudo_bytes(32)));
            file_put_contents(self::$ivFile, base64_encode(openssl_random_pseudo_bytes(16)));

            $em = $this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository('McDataSourcesBundle:DataSource');
            $listDataSources = $repository->findAll();

            foreach ($listDataSources as $dataSource)
            {
                $clairLogin = openssl_decrypt(base64_decode($dataSource->getLoginBDD()), "AES256", $oldKey, 0, $oldIv);
                $clairPassword = openssl_decrypt(base64_decode($dataSource->getPasswordBDD()), "AES256", $oldKey, 0, $oldIv);

                $dataSource->setLoginBDD(self::chiffrer($clairLogin));
                $dataSource->setPasswordBDD(self::chiffrer($clairPassword));

                $em->persist($dataSource);
            }

            $em->flush();
        }
        // Si il n'existe pas de clé privée, on en crée une mais on ne rechiffre pas la BD car on considère qu'elle est vide quand il n'existe pas de clé privée.
        // Si il existe déjà des identifiants chiffrés en base de données mais pas de clé privée, il faut générer une nouvelle clé puis réenregistrer les identifiants via le formulaire de modification pour qu'ils soient chiffrés avec la nouvelle clé
        else
        {
            file_put_contents(self::$keyFile, base64_encode(openssl_random_pseudo_bytes(32))); // 32 octets = 256 bits
            file_put_contents(self::$ivFile, base64_encode(openssl_random_pseudo_bytes(16)));
        }

        return $this->redirectToRoute('data_sources_list');
    }
}
