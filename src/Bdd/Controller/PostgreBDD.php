<?php

namespace App\Bdd\Controller;

/**
 * extends AbstractBDD
 */
class PostgreBDD extends AbstractBDD
{
	public static $msgGetTimestamp = "EXTRACT(EPOCH FROM '2007-11-30 10:30:19')";

	public function __construct($host, $port, $dbname, $login, $password) {
		parent::__construct($host, $port, $dbname, $login, $password);
	}

	/**
	 * Connexion a la BDD 
	 */
	public function connect() {
		try {
			$param = 'host=' . $this->host . ' port=' . $this->port . ' dbname=' . $this->dbname . ' user=' . $this->login . ' password=' . $this->password;
			$this->connection = pg_connect($param);
		} catch (Exception $e) {
			throw $e;
		}

		if (!$this->connection) {
			throw new \Exception("Une erreur s'est produite.\n");
		}
		pg_set_client_encoding($this->connection, "UNIQUE");
	}

	/**
	 * Execute la requete en argument
	 */
	public function query($querySQL) {
		try {
			return pg_query($this->connection, $querySQL);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	/**
	 * Test la requete en argument (retourne TRUE si la requête s'exécute ou FALSE si une erreur survient)
	 */
	public function testQuery($querySQL) {
		
		// On ignore les messages d'erreur avec l'opérateur de contrôle d'erreur "@" car on ne veut pas que le script s'interrompt
		$result = @pg_query($this->connection, $querySQL);
		
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

		while ($row = pg_fetch_row($result)) {
			$data[] = $row;
		}
		return $data;
	}

	/**
	 * Retourne les noms des champs sous forme de tableau
	 */
	public function getListFields($result) {
		$listFields = array();

		$nbFields = pg_num_fields($result);
		for ($i=0; $i < $nbFields; $i++) { 
			$listFields[] = pg_field_name($result, $i);
		}
		return $listFields;
	}

	/**
	 * Retourne les valeurs par colonne du resultat en argument
	 */
	public function getAllColumns($result) {
		$columns = array();

		for ($i=0; $i < pg_num_fields($result); $i++) { 
			$columns[] = pg_fetch_all_columns($result, $i);
		}
		return $columns;
	}

	/**
	 * Retourne les valeurs de la colonne en argument
	 */
	public function getColumn($result, $column) {
		return pg_fetch_all_columns($result, $column);
	}

	/**
	 * Vide la memoire
	 */
	public function free($result) {
		pg_free_result($result);
	}
}
