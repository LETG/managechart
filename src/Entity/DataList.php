<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use app\ChromePhp;

/**
 * DataList
 *
 * @ORM\Table(name="data_list")
 * @ORM\Entity(repositoryClass="App\Repository\DataListRepository")
 */

class DataList
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
     * @ORM\Column(name="nameData", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nameData;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateData", type="datetime")
     * @Assert\DateTime()
     */
    private $dateData;

    /**
     * @var string
     *
     * @ORM\Column(name="requestData", type="text")
     * @Assert\NotBlank()
     */
    private $requestData;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataSource")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $dataSource;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Series", mappedBy="dataList", cascade={"persist", "remove"})
     */
    private $series;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Flag", mappedBy="dataList", cascade={"persist", "remove"})
     */
    private $flag;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AttributSpatial", mappedBy="dataList", cascade={"persist", "remove"})
     */
    private $attributsSpatiaux;

    /**
     * Attributs non stockes en BDD
     */

    private $data;              /* Donnees de la requete sous forme de tableau ligne par ligne */

    private $dataColumn;        /* Donnees de la requete sous forme de tableau colone par colone */

    private $fields;            /* Tous les champs de la requete */

    private $dataByParameter;   /* Toutes les donnees de la requete sous forme de tableau indexe */

    private $parameter;         /* Tous les parametres */


    public function __construct() {

        $this->dateData = new \DateTime;
        $this->attributsSpatiaux = new ArrayCollection();
    }


    /**
     * Execute la requete
     */
    public function executeQuery($attributsSpatiauxURL = "default", $editCreate = false, $test = false) {

        /* Connexion a la BDD */
        try {
            $bdd = $this->dataSource->connect();
        } catch (Exception $e) {
            throw $e;
        }

        /* Recuperation de la requete */
        $request = substr($this->requestData, 0, strlen($this->requestData));

        /* Si attributs spatiaux, on essaye de les insérer dans la requête */
        if ($attributsSpatiauxURL != "default" || !$this->attributsSpatiaux->isEmpty())
        {
            try {
                $attributsSpatiauxInQuery = $this->insertAttributsInQuery($attributsSpatiauxURL, $editCreate);
            } catch (Exception $e) {
                throw $e;
            }
            
			/**
			 * Algorithme d'insertion des attributs spatiaux dans une requête :
			 * - On recherche la clause FROM dans la requête, on part de cette clause FROM et on essaye d'insérer les attributs spatiaux en avançant mot par mot
			 *   jusqu'à ce qu'il n'y ai plus d'erreur de syntaxe dans la requête
			 * - Les attributs spatiaux ne seront jamais insérés entre une parenthèse ouvrante et une parenthèse fermante (sous-requête par exemple)
			 */
			 
            $request = str_replace("\n", " ", $request); // On remplace les retours à la ligne par des espaces dans la requête

			$positionFROM = stripos($request, "from");
			
			if($positionFROM) {
				
				$debutRequete = substr($request, 0, $positionFROM);
				$finRequete = substr($request, $positionFROM) . " "; // On rajoute un espace pour être sûr d'arriver à la fin de la requête

				$request = $debutRequete . $attributsSpatiauxInQuery . $finRequete;
				
				/* On définit un nombre max de tour pour essayé d'insérer les attributs spatiaux afin de ne pas tomber dans une boucle infinie */
				$nbMaxIteration = 30;
				$iterationCourante = 0;
				
				while(!$bdd->testQuery($request) and $iterationCourante < $nbMaxIteration)
				{
					$positionEspace = stripos($finRequete, " ");
					$debutFinRequete = substr($finRequete, 0, $positionEspace+1); // +1 car on veut prendre l'espace
					$finRequete = substr($finRequete, $positionEspace+1); // +1 car on ne veut pas prendre l'espace
					
					if(isset($finRequete[0]) and $finRequete[0] == "(") {
						$positionParentheseFermante = strripos($finRequete, ")"); // On récupère la position de la DERNIERE parenthèse fermante
						$debutFinRequete = $debutFinRequete . substr($finRequete, 0, $positionParentheseFermante+1);
						$finRequete = substr($finRequete, $positionParentheseFermante+1);
					}
					
					$debutRequete = $debutRequete . $debutFinRequete;
					
					$request = $debutRequete . $attributsSpatiauxInQuery . $finRequete;
					$iterationCourante++;
				}
			}
        }
        
        if ($test) {
            $request = $request . ' LIMIT 100';
            echo '<div class="alert alert-block alert-info">' . $request . '</div>';
        }

        $request = $request . ' ;';
        $echoRequest = "";

        /* Recuperation du resultat de la requete */
        try {
            $result = $bdd->query($request);
        } catch (Exception $e) {
            throw new \Exception($echoRequest . $e->getMessage());
        }

        if (!$result) {
            throw new \Exception($echoRequest . "Une erreur s'est produite.\n");
        }

        /* Initialisation des attributs */
        $this->data = $bdd->getData($result);
        $this->dataColumn = $bdd->getAllColumns($result);
        $this->fields = $bdd->getListFields($result);
        $this->dataByParameter = $this->decoupeData();
        $this->parameter = array_keys($this->dataByParameter);

        $bdd->free($result);

        return $echoRequest;
    }


    /**
     * Retourne la selection des attributs spatiaux
     * 1. Si on est en visualisation de test et $attributsSpatiauxURL = "default"
     * 2. Si on est en edition/creation et $this->attributsSpatiaux est vide
     * 3. Si on est en visualisation de prod et $attributsSpatiauxURL n'est pas vide
     */
    protected function insertAttributsInQuery($attributsSpatiauxURL, $editCreate) {

        $request = "";

        /* Cas 1 */
        if ($attributsSpatiauxURL == "default")
        {
            /* Ajout de la selection de l'attribut dans la requete */
            foreach ($this->attributsSpatiaux->toArray() as $attributSpatial)
            {
                $request .= $this->insertAttributInQuery(
                    $attributSpatial->getNameAttribut(),
                    $attributSpatial->getValueAttribut(),
                    $attributSpatial->getTypeAttribut(),
                    $attributSpatial->getKeywordAttribut());
            }
        }

        /* Cas 2 - 3 */
        else {

            $attributsSpatiauxURL = explode(",", $attributsSpatiauxURL);

            /* Cas 2 */
            if ($editCreate)
            {
                /* Parcours du tableau et insertion d'attributs dans la requete */
                foreach ($attributsSpatiauxURL as $attributSpatial)
                {
                    $attributSpatial = explode("@", $attributSpatial);
                    $request .= $this->insertAttributInQuery($attributSpatial[0], $attributSpatial[1], $attributSpatial[2], $attributSpatial[3]);
                }
            }

            /* Cas 3 */
            else
            {
                if ($this->attributsSpatiaux->toArray())
                {
                    /* Generation d'erreur si pas assez d'attributs spatiaux */
                    if (count($this->attributsSpatiaux->toArray()) > count($attributsSpatiauxURL)) {
                        throw new \Exception('Pas assez d\'attributs dans la requete', 24);
                    }

                    $indexArrayValueAs = 0;

                    foreach ($this->attributsSpatiaux as $attributSpatial)
                    {
                        /* Si null dans $attributsSpatiauxURL */
                        if ($attributsSpatiauxURL[$indexArrayValueAs] == "") {
                            $msgError = "La valeur de l\"attribut " . $attributSpatial->getNameAttribut() . " en position " . $indexArrayValueAs . " est nulle";
                            throw new \Exception($msgError, 42);
                        } else {
                            $request .= $this->insertAttributInQuery(
                                $attributSpatial->getNameAttribut(),
                                $attributsSpatiauxURL[$indexArrayValueAs],
                                $attributSpatial->getTypeAttribut(),
                                $attributSpatial->getKeywordAttribut());
                        }

                        $indexArrayValueAs++;
                    }
                }
            }
        }

        return $request;
    }


    /**
     * Retourne une chaine de caractere à ajouter à la requête
     */
    protected function insertAttributInQuery($nameAttribut, $valueAttribut, $typeAttribut, $keywordAttribut) {

         $res =  " ".$keywordAttribut ." ". $nameAttribut . " " . "=";

        /* Ajout de guillemet en fonction du type */
        if ($typeAttribut == "numeric") {
            $res .= $valueAttribut;
        } else {
            $res .= '\'' . pg_escape_string($valueAttribut) . '\'';
        }

        $res .= " ";
        return $res;
    }


    /**
     * Retourne un tableau indexe par les parametres de la troisieme colonne
     */
    protected function decoupeData() {

        $res = array();
        $allColumns = $this->dataColumn;

        /* x column */
        $xValue = $allColumns[0];

        /* y column */
        $yValue = null;

        if (count($allColumns) > 1) {
            $yValue = $allColumns[1];
        }

        /* y id */
        $idY = null;

        if (count($allColumns) > 2) {
            $idY = $allColumns[2];
        }

        /* unit y (optional) */
        $unitY = null;

        if (count($allColumns) > 3) {
            $unitY = $allColumns[3];
        }

        for ($i=0; $i < count($yValue); $i++)
        { 
            if ($unitY != null) {
                $res[$idY[$i] . "@" . $unitY[$i]][] = array($xValue[$i], $yValue[$i]);
            } else {
                $res[$idY[$i]][] = array($xValue[$i], $yValue[$i]);
            }
        }

        return $res;
    }


    public function getField($index) {
        return $this->fields[$index];
    }


    public function getListDataByParameter() {
        return $this->dataByParameter;
    }


    public function getDataByParameter($parameter) {
        $listkeysData = array_keys($this->dataByParameter);
        return $this->dataByParameter[$listkeysData[$parameter]];        
    }


    /**
     * Set dataSource
     *
     * @param \App\Entity\DataSource $dataSource
     * @return DataList
     */
    public function setDataSource(\App\Entity\DataSource $dataSource) {
        $this->dataSource = $dataSource;
        return $this;
    }


    /**
     * Get dataSource
     *
     * @return \App\Entity\DataSource 
     */
    public function getDataSource() {
        return $this->dataSource;
    }


    /**
     * Add series
     *
     * @param \App\Entity\Series $series
     * @return DataList
     */
    public function addSeries(\App\Entity\Series $series) {
        $this->series[] = $series;
        return $this;
    }


    /**
     * Remove series
     *
     * @param \App\Entity\Series $series
     */
    public function removeSeries(\App\Entity\Series $series) {
        $this->series->removeElement($series);
    }


    /**
     * Get series
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSeries() {
        return $this->series;
    }

////////////////////////////////////////////////////////////////////////////

    /**
     * Add flag
     *
     * @param \App\Entity\Flag $flag
     * @return DataList
     */
    public function addFlag(\App\Entity\Flag $flag) {
        $this->flag[] = $flag;
        return $this;
    }


    /**
     * Remove flag
     *
     * @param \App\Entity\Flag $flag
     */
    public function removeFlag(\App\Entity\Flag $flag) {
        $this->flag->removeElement($flag);
    }


    /**
     * Get flag
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlag() {
        return $this->flag;
    }    

////////////////////////////////////////////////////////////////////////////

    /**
     * Add attributsSpatiaux
     *
     * @param \App\Entity\AttributSpatial $attributsSpatiaux
     * @return DataList
     */
    public function addAttributsSpatiaux(\App\Entity\AttributSpatial $attributsSpatiaux) {
        $this->attributsSpatiaux[] = $attributsSpatiaux;
        return $this;
    }
    

    /**
     * Remove attributsSpatiaux
     *
     * @param \App\Entity\AttributSpatial $attributsSpatiaux
     */
    public function removeAttributsSpatiaux(\App\Entity\AttributSpatial $attributsSpatiaux) {
        $this->attributsSpatiaux->removeElement($attributsSpatiaux);
    }


    /**
     * Get attributsSpatiaux
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributsSpatiaux() {
        return $this->attributsSpatiaux;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }
    

    /**
     * Set nameData
     *
     * @param string $nameData
     *
     * @return DataList
     */
    public function setNameData($nameData) {
        $this->nameData = $nameData;
        return $this;
    }
    

    /**
     * Get nameData
     *
     * @return string
     */
    public function getNameData() {
        return $this->nameData;
    }
    

    /**
     * Set dateData
     *
     * @param \DateTime $dateData
     *
     * @return DataList
     */
    public function setDateData($dateData) {
        $this->dateData = $dateData;
        return $this;
    }
    

    /**
     * Get dateData
     *
     * @return \DateTime
     */
    public function getDateData() {
        return $this->dateData;
    }
    

    /**
     * Set requestData
     *
     * @param string $requestData
     *
     * @return DataList
     */
    public function setRequestData($requestData) {
        $this->requestData = $requestData;
        return $this;
    }
    

    /**
     * Get requestData
     *
     * @return string
     */
    public function getRequestData() {
        return $this->requestData;
    }
    

    /**
     * Gets the Attributs non stockes en BDD.
     *
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }
    

    /**
     * Gets the value of dataColumn.
     *
     * @return mixed
     */
    public function getColumn($column) {
        return $this->dataColumn[$column];
    }
    

    /**
     * Gets the value of fields.
     *
     * @return mixed
     */
    public function getFields() {
        return $this->fields;
    }
    

    /**
     * Gets the value of parameter.
     *
     * @return mixed
     */
    public function getParameter() {
        return $this->parameter;
    }

}
