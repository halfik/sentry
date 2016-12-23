<?php

namespace Netinteractive\Sentry\Facades;

/**
 * Part of the Sentry package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    2.0.0
 * @author     Netinteractive LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Netinteractive LLC
 * @link       http://cartalyst.com
 */

use Netinteractive\Elegant\Db\ConnectionResolverInterface;
use Netinteractive\Elegant\Db\Connection;
use PDO;

/**
 * Class ConnectionResolver
 * @package Netinteractive\Sentry\Facades
 */
class ConnectionResolver implements ConnectionResolverInterface
{

	/**
	 * The PDO instance.
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * The PDO driver name.
	 *
	 * @var string
	 */
	protected $driver;

	/**
	 * The table prefix.
	 *
	 * @var string
	 */
	protected $tablePrefix = '';

	/**
	 * The default connection name.
	 *
	 * @var string
	 */
	protected $defaultConnection;

	/**
	 * The database connection.
	 *
	 * @var \Netinteractive\Elegant\Db\Connection
	 */
	protected $connection;

	/**
	 * Create a new connection resolver.
	 *
	 * @param  \PDO $pdo
	 * @param  string $driverName
	 * @param  string $tablePrefix
	 * @return void
	 */
	public function __construct(PDO $pdo, $driverName, $tablePrefix = '')
	{
		$this->pdo         = $pdo;
		$this->driverName  = $driverName;
		$this->tablePrefix = $tablePrefix;
	}

	/**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return \Netinteractive\Elegant\Db\Connection
	 */
	public function connection($name = null)
	{
		return $this->getConnection();
	}

	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	public function getDefaultConnection()
	{
		return $this->getConnection();
	}

	/**
	 * Set the default connection name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultConnection($name)
	{
		$this->defaultConnection = $name;
	}

	/**
	 * Returns the database connection.
	 *
	 * @return \Netinteractive\Elegant\Db\Connection
	 * @throws \InvalidArgumentException
	 */
	public function getConnection()
	{
		if ($this->connection === null)
		{
			$connection = null;

			// We will now provide the query grammar to the connection.
			switch ($this->driverName)
			{
				case 'mysql':
					$connection = '\Netinteractive\Elegant\Db\MySqlConnection';
					break;

				case 'pgsql':
					$connection = '\Netinteractive\Elegant\Db\PostgresConnection';
					break;

				case 'sqlsrv':
					$connection = '\Netinteractive\Elegant\Db\SqlServerConnection';
					break;

				case 'sqlite':
					$connection = '\Netinteractive\Elegant\Db\SQLiteConnection';
					break;

				default:
					throw new \InvalidArgumentException("Cannot determine grammar to use based on {$this->driverName}.");
					break;
			}
			
			$this->connection = new $connection($this->pdo, '', $this->tablePrefix);
		}

		return $this->connection;
	}

}
