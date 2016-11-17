<?php

	/**
	 * Class used for builds/versions
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 ** @version 3.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package thebuggenie
	 * @subpackage main
	 */

	/**
	 * Class used for builds/versions
	 *
	 * @package thebuggenie
	 * @subpackage main
	 */
	class TBGBuild extends TBGVersionItem 
	{
		
		static protected $_b2dbtablename = 'TBGBuildsTable';
		
		/**
		 * This builds edition
		 *
		 * @var TBGEdition
		 * @Class TBGEdition
		 */
		protected $_edition = null;

		/**
		 * This builds project
		 *
		 * @var TBGProject
		 * @Class TBGProject
		 */
		protected $_project = null;
		
		/**
		 * Whether this build is released or not
		 * 
		 * @var boolean
		 */
		protected $_isreleased = null;
		
		/**
		 * The builds release date
		 * 
		 * @var integer
		 */
		protected $_release_date = null;
		
		/**
		 * Project builds cache
		 * 
		 * @var array
		 */
		static protected $_project_builds = null;

		/**
		 * Edition builds cache
		 * 
		 * @var array
		 */
		static protected $_edition_builds = null;
		
		/**
		 * Get all builds for a specific project
		 * 
		 * @param integer $project_id The project ID
		 * 
		 * @return array
		 */
		public static function getByProjectID($project_id)
		{
			if (self::$_project_builds === null)
			{
				self::$_project_builds = array();
			}
			if (!array_key_exists($project_id, self::$_project_builds))
			{
				self::$_project_builds[$project_id] = array();
				if ($res = B2DB::getTable('TBGBuildsTable')->getByProjectID($project_id))
				{
					while ($row = $res->getNextRow())
					{
						$build = TBGContext::factory()->TBGBuild($row->get(TBGBuildsTable::ID), $row);
						self::$_project_builds[$project_id][$build->getID()] = $build;
					}
				}
			}
			return self::$_project_builds[$project_id];
		}

		/**
		 * Get all builds for a specific edition
		 * 
		 * @param integer $edition_id The edition ID
		 * 
		 * @return array
		 */
		public static function getByEditionID($edition_id)
		{
			if (self::$_edition_builds === null)
			{
				self::$_edition_builds = array();
			}
			if (!array_key_exists($edition_id, self::$_edition_builds))
			{
				self::$_edition_builds[$edition_id] = array();
				if ($res = B2DB::getTable('TBGBuildsTable')->getByEditionID($project_id))
				{
					$build = TBGContext::factory()->TBGBuild($row->get(TBGBuildsTable::ID), $row);
					self::$_edition_builds[$edition_id][$build->getID()] = $build;
				}
			}
			return self::$_edition_builds[$edition_id];
		}
		
		/**
		 * Class constructor
		 *
		 * @param B2DBRow $row
		 */
		public function _construct(B2DBRow $row, $foreign_key = null)
		{
			try
			{
				if ($this->_edition && is_numeric($this->_edition))
				{
					$this->_edition = TBGContext::factory()->TBGEdition($row->get(TBGBuildsTable::EDITION), $row);
				}
				elseif ($this->_project && is_numeric($this->_project))
				{
					$this->_project = TBGContext::factory()->TBGProject($row->get(TBGBuildsTable::PROJECT), $row);
				}
			}
			catch (Exception $e) {}
		}
		
		public function _postSave($is_new)
		{
			if ($is_new)
			{
				TBGContext::setPermission("canseebuild", $this->getID(), "core", 0, TBGContext::getUser()->getGroup()->getID(), 0, true);
				TBGEvent::createNew('core', 'TBGBuild::createNew', $this)->trigger();
			}
		}

		/**
		 * Returns the name and the version, nicely formatted
		 * 
		 * @return string
		 */
		public function getPrintableName()
		{
			return $this->_name . ' (' . $this->getVersion() . ')';
		}
		
		/**
		 * Returns the edition
		 *
		 * @return TBGEdition
		 */
		public function getEdition()
		{
			return $this->_edition;
		}

		public function setEdition(TBGEdition $edition)
		{
			$this->_edition = $edition;
		}
		
		/**
		 * Returns the project
		 *
		 * @return TBGProject
		 */
		public function getProject()
		{
			return ($this->_project !== null || !is_object($this->_edition)) ? $this->_project : $this->_edition->getProject();
		}

		public function setProject(TBGProject $project)
		{
			$this->_project = $project;
		}
		
		/**
		 * Whether this build is under an edition
		 * 
		 * @return bool
		 */
		public function isEditionBuild()
		{
			return !is_null($this->_edition);
		}

		/**
		 * Whether this build is under a project
		 * 
		 * @return bool
		 */
		public function isProjectBuild()
		{
			return !is_null($this->_project);
		}
		
		/**
		 * Returns the parent object
		 * 
		 * @return TBGVersionItem
		 */
		public function getParent()
		{
			return ($this->isProjectBuild()) ? $this->getProject() : $this->getEdition();
		}
		
		/**
		 * Make the build the default for it's edition or project
		 */
		public function setDefault()
		{
			if ($this->isEditionBuild())
			{
				B2DB::getTable('TBGBuildsTable')->clearDefaultsByEditionID($this->getParent()->getID());
			}
			else
			{
				B2DB::getTable('TBGBuildsTable')->clearDefaultsByProjectID($this->getParent()->getID());
			}
			$res = B2DB::getTable('TBGBuildsTable')->setDefaultBuild($this->getID());
			$this->_isdefault = true;
		}
		
		/**
		 * Delete this build
		 */
		protected function _preDelete()
		{
			B2DB::getTable('TBGIssueAffectsBuildTable')->deleteByBuildID($this->getID());
		}
		
		/**
		 * Adds this build to all open issues in this edition or project
		 * Returns true if any issues were updated, false if not
		 * 
		 * @param integer $limit_status Limit to only a specific status type
		 * @param integer $limit_category Limit to only a specific category
		 * @param integer $limit_issuetype Limit to only a specific issue type
		 * 
		 * @return boolean
		 */
		public function addToOpenParentIssues($limit_status = null, $limit_category = null, $limit_issuetype = null)
		{
			if ($this->isEditionBuild())
			{
				$res = B2DB::getTable('TBGIssueAffectsEditionTable')->getOpenAffectedIssuesByEditionID($this->getParent()->getID(), $limit_status, $limit_category, $limit_issuetype);
			}
			else
			{
				$res = TBGIssuesTable::getTable()->getOpenAffectedIssuesByProjectID($this->getParent()->getID(), $limit_status, $limit_category, $limit_issuetype);
			}
			
			$retval = false;
			if ($res)
			{
				while ($row = $res->getNextRow())
				{
					$issue_id = $row->get(TBGIssuesTable::ID);
					if (B2DB::getTable('TBGIssueAffectsBuildTable')->setIssueAffected($issue_id, $this->getID()))
					{
						$retval = true;
					}
				}
			}
			return $retval;
		}
		
		/**
		 * Whether or not the current user can access the build
		 * 
		 * @return boolean
		 */
		public function hasAccess()
		{
			return (($this->getProject() instanceof TBGProject && $this->getProject()->canSeeAllBuilds()) || TBGContext::getUser()->hasPermission('canseebuild', $this->getID()));
		}
		
	}
