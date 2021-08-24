<?php

namespace App\Bdd\Controller;

/**
 * extends AbstractBDD
 */
class MySQLBDD extends AbstractBDD
{
	public static $msgGetTimestamp = "UNIX_TIMESTAMP('2007-11-30 10:30:19')";

	public function __construct($host, $port, $dbname, $login, $password) {
		parent::__construct($host, $port, $dbname, $login, $password);
	}

	/**
	 * Connexion a la BDD 
	 */
	public function connect() {
		$this->connection = mysqli_connect($this->host, $this->login, $this->password, $this->dbname, $this->port);

		if (!$this->connection) {
			throw new \Exception('Erreur de connexion (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
		}
		mysqli_set_charset($this->connection, "utf8");
	}

	/**
	 * Execute la requete en argument
	 */
	public function query($querySQL) {
		if ($result = mysqli_query($this->connection, $querySQL)) {
			return $result;
		}
		throw new \Exception(mysqli_error($this->connection));
		
	}
	
	/**
	 * Test la requete en argument (retourne TRUE si la requête s'exécute ou FALSE si une erreur survient)
	 */
	public function testQuery($querySQL) {

		// On ignore les messages d'erreur avec l'opérateur de contrôle d'erreur "@" car on ne veut pas que le script s'interrompt
		$result = @mysqli_query($this->connection, $querySQL);
		
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Retourne le resultat sous forme de tableau
	 */
	public function getData($result) {
		$data = array();

		while ($row = mysqli_fetch_row($result)) {
			$data[] = $row;
		}
		mysqli_data_seek($result, 0);
		return $data;
	}

	/**
	 * Retourne les noms des champs sous forme de tableau
	 */
	public function getListFields($result) {
		$listFields = mysqli_fetch_fields($result);
		$res = array();

		foreach ($listFields as $field) {
			$res[] = $field->name;
		}
		mysqli_data_seek($result, 0);
		return $res;
	}

	/**
	 * Retourne les valeurs par colonne du resultat en argument
	 */
	public function getAllColumns($result) {
		$columns = array();

		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			for ($i=0; $i < count($row); $i++) { 
				$columns[$i][] = $row[$i];
			}
		}
		mysqli_data_seek($result, 0);
		return $columns;
	}

	/**
	 * Retourne les valeurs de la colonne en argument
	 */
	public function getColumn($result, $column) {
		$res = array();

		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
		 	$res[] = $row[$column];
		}
		mysqli_data_seek($result, 0);
		return $res; 
	}

	/**
	 * Vide la memoire
	 */
	public function free($result) {
		if (is_resource($result)) {
			mysqli_free_result($result);
		}
	}
}
