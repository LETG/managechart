<?php

namespace App\Bdd\Controller;

/**
 * Implements InterfaceBDD
 */
abstract class AbstractBDD implements InterfaceBDD
{
	protected $host;
	protected $port;
	protected $dbname;
	protected $login;
	protected $password;
	protected $connection;

	public static $msgGetTimestamp;

	public function __construct($host, $port, $dbname, $login, $password) {
		$this->host = $host;
		$this->port = $port;
		$this->dbname = $dbname;
		$this->login = $login;
		$this->password = $password;
	}


    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    public function getDbname()
    {
        return $this->dbname;
    }

    public function setDbname($dbname)
    {
        $this->dbname = $dbname;

        return $this;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }
}
