<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Bdd\Controller\Bdd;
use App\Controller\EncryptController;

/**
 * DataSource
 *
 * @ORM\Table(name="data_source")
 * @ORM\Entity(repositoryClass="App\Repository\DataSourceRepository")
 */
class DataSource
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nameBDD", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nameBDD;

    /**
     * @var string
     *
     * @ORM\Column(name="descriptionBDD", type="text")
     */
    private $descriptionBDD;

    /**
     * @var string
     *
     * @ORM\Column(name="hostBDD", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $hostBDD;

    /**
     * @var int
     *
     * @ORM\Column(name="portBDD", type="integer")
     * @Assert\NotBlank()
     */
    private $portBDD;

    /**
     * @var string
     *
     * @ORM\Column(name="loginBDD", type="string", length=1024)
     */
    private $loginBDD;

    /**
     * @var string
     *
     * @ORM\Column(name="passwordBDD", type="string", length=1024)
     */
    private $passwordBDD;

    /**
     * @var int
     *
     * @ORM\Column(name="typeBDD", type="integer")
     * @Assert\NotBlank()
     */
    private $typeBDD;

    /**
     * @var string
     *
     * @ORM\Column(name="typeStrBDD", type="string", length=255)
     */
    private $typeStrBDD;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateBDD", type="datetime")
     * @Assert\Type("\DateTimeInterface")
     */
    private $dateBDD;

    /**
     * @var string
     *
     * @ORM\Column(name="nameCon", type="string", length=255)
     */
    private $nameCon;


    public function __construct() {
        $this->dateBDD = new \Datetime;
    }

    /**
     * Connexion a la base de donnee
     */
    public function connect()
    {
        /* Déchiffrement des identifiants */
        $this->setLoginBDD(EncryptController::dechiffrer($this->getLoginBDD()));
        $this->setPasswordBDD(EncryptController::dechiffrer($this->getPasswordBDD()));

        try {
            $bddClass = new Bdd($this);
        } catch(Exception $e) {
            throw $e;
        }

        /* Chiffrement des identifiants */
        $this->setLoginBDD(EncryptController::chiffrer($this->getLoginBDD()));
        $this->setPasswordBDD(EncryptController::chiffrer($this->getPasswordBDD()));

        $bdd = $bddClass->getBdd();

        /* Connexion */
        try {
            $bdd->connect();
        } catch (Exception $e) {
            throw $e;
        }

        return $bdd;
    }

    /**
     * Connexion a la base de donnee sans cryptage des identifiants
     */
    public function testConnect()
    {
        try {
            $bddClass = new Bdd($this);
        } catch (Exception $e) {
            throw $e;
        }

        $bdd = $bddClass->getBdd();

        try {
            $bdd->connect();
        } catch (Exception $e) {
            throw $e;
        }

        return $bdd;
    }

    /**
     * Concatenation de nameCon, nameBDD et TypeStrBDD
     */
    public function getUniqueName()
    {
        return sprintf('%s - %s - %s', $this->nameCon, $this->nameBDD, $this->typeStrBDD);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nameBDD
     *
     * @param string $nameBDD
     *
     * @return DataSource
     */
    public function setNameBDD($nameBDD)
    {
        $this->nameBDD = $nameBDD;

        return $this;
    }

    /**
     * Get nameBDD
     *
     * @return string
     */
    public function getNameBDD()
    {
        return $this->nameBDD;
    }

    /**
     * Set descriptionBDD
     *
     * @param string $descriptionBDD
     *
     * @return DataSource
     */
    public function setDescriptionBDD($descriptionBDD)
    {
        $this->descriptionBDD = $descriptionBDD;

        return $this;
    }

    /**
     * Get descriptionBDD
     *
     * @return string
     */
    public function getDescriptionBDD()
    {
        return $this->descriptionBDD;
    }

    /**
     * Set hostBDD
     *
     * @param string $hostBDD
     *
     * @return DataSource
     */
    public function setHostBDD($hostBDD)
    {
        $this->hostBDD = $hostBDD;

        return $this;
    }

    /**
     * Get hostBDD
     *
     * @return string
     */
    public function getHostBDD()
    {
        return $this->hostBDD;
    }

    /**
     * Set portBDD
     *
     * @param integer $portBDD
     *
     * @return DataSource
     */
    public function setPortBDD($portBDD)
    {
        $this->portBDD = $portBDD;

        return $this;
    }

    /**
     * Get portBDD
     *
     * @return int
     */
    public function getPortBDD()
    {
        return $this->portBDD;
    }

    /**
     * Set loginBDD
     *
     * @param string $loginBDD
     *
     * @return DataSource
     */
    public function setLoginBDD($loginBDD)
    {
        $this->loginBDD = $loginBDD;

        return $this;
    }

    /**
     * Get loginBDD
     *
     * @return string
     */
    public function getLoginBDD()
    {
        return $this->loginBDD;
    }

    /**
     * Set passwordBDD
     *
     * @param string $passwordBDD
     *
     * @return DataSource
     */
    public function setPasswordBDD($passwordBDD)
    {
        $this->passwordBDD = $passwordBDD;

        return $this;
    }

    /**
     * Get passwordBDD
     *
     * @return string
     */
    public function getPasswordBDD()
    {
        return $this->passwordBDD;
    }

    /**
     * Set typeBDD
     *
     * @param integer $typeBDD
     *
     * @return DataSource
     */
    public function setTypeBDD($typeBDD)
    {
        $this->typeBDD = $typeBDD;

        return $this;
    }

    /**
     * Get typeBDD
     *
     * @return int
     */
    public function getTypeBDD()
    {
        return $this->typeBDD;
    }

    /**
     * Set typeStrBDD
     *
     * @param string $typeStrBDD
     *
     * @return DataSource
     */
    public function setTypeStrBDD($typeStrBDD)
    {
        $this->typeStrBDD = $typeStrBDD;

        return $this;
    }

    /**
     * Get typeStrBDD
     *
     * @return string
     */
    public function getTypeStrBDD()
    {
        return $this->typeStrBDD;
    }

    /**
     * Set dateBDD
     *
     * @param \DateTime $dateBDD
     *
     * @return DataSource
     */
    public function setDateBDD($dateBDD)
    {
        $this->dateBDD = $dateBDD;

        return $this;
    }

    /**
     * Get dateBDD
     *
     * @return \DateTime
     */
    public function getDateBDD()
    {
        return $this->dateBDD;
    }

    /**
     * Set nameCon
     *
     * @param string $nameCon
     *
     * @return DataSource
     */
    public function setNameCon($nameCon)
    {
        $this->nameCon = $nameCon;

        return $this;
    }

    /**
     * Get nameCon
     *
     * @return string
     */
    public function getNameCon()
    {
        return $this->nameCon;
    }
}
