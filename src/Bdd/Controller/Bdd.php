<?php

namespace App\Bdd\Controller;

use App\Entity\DataSource;

/**
 * Gere l'instanciation de la Bdd
 */
class Bdd
{
	protected $bdd;
	protected $type;
	protected $msgIncorrectTypeBDD;

	public function __construct(DataSource $dataSource) {
		$this->type = $dataSource->getTypeBDD();

		try {
			$this->instanciateBDD(
				$this->type, 
				$dataSource->getHostBDD(),
				$dataSource->getPortBDD(),
				$dataSource->getNameBDD(),
				$dataSource->getLoginBDD(),
				$dataSource->getPasswordBDD()
			);
		} catch (Exception $e) {
			throw $e;
		}
	}

	/**
	 * Instancie $this->bdd
	 */
	protected function instanciateBDD($type, $host, $port, $dbname, $login, $password) {
		switch ($type) {
			case 0:
				$this->bdd = new PostgreBDD($host, $port, $dbname, $login, $password);
				break;
			case 1:
				$this->bdd = new MySQLBDD($host, $port, $dbname, $login, $password);
				break;

			default:
				instanciateMsgIncorrectTypeBDD($type);
				throw new \Exception($this->msgIncorrectTypeBDD);
				break;
		}
	}

	/**
	 * Instancie le message d'erreur retourne par instanciateBDD
	 */
	protected function instanciateMsgIncorrectTypeBDD($errorType) {
		$this->msgIncorrectTypeBDD = "Erreur, type de base de donnees non reconnu.\n";
		$this->msgIncorrectTypeBDD .= "Types disponibles :\n";

		foreach (\Mc\BddBundle\Controller\AvailableType::$types as $index => $type) {
			$this->msgIncorrectTypeBDD .= $index . " : " . $type . "\n";
		}

		$this->msgIncorrectTypeBDD .= "Type donne en argument : " . $errorType . "\n";
		$this->msgIncorrectTypeBDD .= "(Attention a ce que le type donne soit un int)";
	}

	/**
	 * Retourne un message indnquant comment recuperer un UNIX Timestamp
	 */
	public static function getMsgTimestamp() {
		$msg = array();

		$msg[] = "PostgreSQL : " . PostgreBDD::$msgGetTimestamp;
		$msg[] = "MySQL : " . MySQLBDD::$msgGetTimestamp;

		return $msg;
	}

	public function getBdd()
	{
		return $this->bdd;
	}

	public function setBdd($bdd)
	{
		$this->bdd = $newBdd;
	}
}
