<?php

namespace App\Bdd\Controller;

interface InterfaceBDD {
	/** 
	 * Connexion a la BDD 
	 */
	public function connect();

	/** 
	 * Execute la requete en argument
	 */
	public function query($querySQL);
	
	/**
	 * Test la requete en argument (retourne TRUE si la requête s'exécute ou FALSE si une erreur survient)
	 */
	public function testQuery($querySQL);

	/**
	 * Retourne le resultat sous forme de tableau
	 */
	public function getData($result);

	/** 
	 * Retourne les noms des champs sous forme de tableau
	 */
	public function getListFields($result);

	/**
	 * Retourne les valeurs par colonne du resultat en argument
	 */
	public function getAllColumns($result);

	/**
	 * Retourne les valeurs de la colonne en argument
	 */
	public function getColumn($result, $column);

	/**
	 * Vide la memoire
	 */
	public function free($result);
}
