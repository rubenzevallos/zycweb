<?php

	/**
	 * B2DB Row Base class
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 2.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package B2DB
	 * @subpackage core
	 */

	/**
	 * B2DB Row Base class
	 *
	 * @package B2DB
	 * @subpackage core
	 */
	abstract class BaseB2DBRow
	{
		protected $_fields = array();
		
		/**
		 * Criteria
		 *
		 * @var B2DBStatement
		 */
		protected $_statement = null;
		
		/**
		 * Constructor
		 * 
		 * @param B2DBstatement $statement
		 */
		public function __construct($row, $statement)
		{
			foreach ($row as $key => $val)
			{
				$this->_fields[$key] = $val;
			}
			$this->_statement = $statement;
		}

		public function getJoinedTables()
		{
			return $this->_statement->getCriteria()->getForeignTables();
		}
		
		public function get($column, $foreign_key = null, $debug = false)
		{
			if ($this->_statement == null)
			{
				throw new B2DBException('Statement did not execute, cannot return unknown value for column ' . $column);
			}
			else
			{
				if ($foreign_key !== null)
				{
					foreach ($this->_statement->getCriteria()->getForeignTables() as $aft)
					{
						if ($aft['original_column'] == $foreign_key)
						{
							$columnname = $this->_statement->getCriteria()->getColumnName($column);
							$column = $aft['jointable']->getB2DBAlias() . '.' . $columnname;
							break;
						}
					}
				}
				else
				{
					$column = $this->_statement->getCriteria()->getSelectionColumn($column, null, $debug);
					//if ($debug) var_dump($column);
					if ($debug) var_dump('selection alias: ' . $this->_statement->getCriteria()->getSelectionAlias($column));
					if ($debug) echo 'fields: ';
					if ($debug) var_dump($this->_fields);
				}
				if (isset($this->_fields[$this->_statement->getCriteria()->getSelectionAlias($column)]))
				{
					return $this->_fields[$this->_statement->getCriteria()->getSelectionAlias($column)];
				}
				else
				{
					return '';
				}
			}
		}
		
		/**
		 * Return the associated B2DBCriteria
		 * 
		 * @return B2DBCriteria
		 */
		public function getCriteria()
		{
			return $this->_statement->getCriteria();
		}
		
	}
	