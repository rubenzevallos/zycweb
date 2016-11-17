<?php

/* *********************************************************************
 *
 *	paCRUD - PHP Ajax CRUD Framework é um framework para
 *	desenvolvimento rápido de sistemas de informação web.
 *	Copyright (C) 2010 Emerson Casas Salvador <salvaemerson@gmail.com>
 *	e Odair Rubleski <orubleski@gmail.com>
 *
 *	This file is part of paCRUD.
 *
 *	paCRUD is free software; you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation; either version 3, or (at your option)
 *	any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 * ******************************************************************* */

/*
este arquivo contém as classe de login
*/

class pacrudLogin {
	private $password;
	private $logged;
	private $ident;
	private $connection;
	private $username;
	private $err;
	private $session;

	function __construct($ident,$connection,$username,$password) {
		$this->ident = $ident;
		$this->connection = $connection;
		$this->logged = false;
		$this->err = '';
		if ($username == '') {
			if (isset($_SESSION[$ident.'_pacrudUserName'])) {
				$this->username = $_SESSION[$ident.'_pacrudUserName'];
			}
			if (isset($_SESSION[$ident.'_pacrudUserPassword'])) {
				$this->password = $_SESSION[$ident.'_pacrudUserPassword'];
			}
		}
		else {
			$this->username = $username;
			$this->password = $password;
		}
		if ($this->username != '') {
			$this->session = true;
		}
		else {
			$this->session = false;
		}
	}
	
	function createUser($newUsername,$newPassword,$newEnabled) {
		return true;
	}
	
	function retrieveUser($username) {
		return true;
	}
	
	function updateUser($oldUsername,$newUsername,$newPassword,$newEnabled) {
		return true;
	}
	
	function deleteUser($oldUsername) {
		return true;
	}
	
	function getErr() {
		return $this->err;
	}
	
	function isSession() {
		return $this->session;
	}
	
	function login() {
		global $pacrudText;
		global $pacrudConfig;
		if ($this->connection->getConnection()) {
		    $sql = 'SELECT password FROM '.pGetSchema($pacrudConfig['loginSchema'],'').'syslogin '.
				   'WHERE enabled=TRUE '.
				   'AND username='.pFormatSql($this->username,'string').';';
			$password = $this->connection->sqlQuery($sql);		    
		    if ($password != '' and $this->password == $password) {
				$this->logged = true;
				$this->sessionRegister();
			}
			else {
				$this->logged = false;
				$this->err = $pacrudText[21];
			}
		}
		else {
			$this->logged = false;
			$this->err = $this->connection->getErr();
		}
		return $this->logged;
	}

	function logoff() {
		return $this->sessionUnRegister();
	}
	
	private function sessionRegister() {
		$_SESSION[$this->ident.'_pacrudUserName'] = $this->username;
		$_SESSION[$this->ident.'_pacrudUserPassword'] = $this->password;
		if (isset($_SESSION[$this->ident.'_pacrudUserName']) and isset($_SESSION[$this->ident.'_pacrudUserPassword'])) {
			$this->sesseion = true;
		}
		else {
			$this->sesseion = false;
		}
		return $this->session;
	}
	
	private function sessionUnRegister() {
		if (isset($_SESSION[$this->ident.'_pacrudUserName'])) {
			unset($_SESSION[$this->ident.'_pacrudUserName']);
		}
		if (isset($_SESSION[$this->ident.'_pacrudUserPassword'])) {
			unset($_SESSION[$this->ident.'_pacrudUserPassword']);
		}
		session_write_close();
		if (isset($_SESSION[$this->ident.'_pacrudUserName']) and isset($_SESSION[$this->ident.'_pacrudUserPassword'])) {
			$this->sesseion = true;
			return false;
		}
		else {
			$this->sesseion = false;
			return true;
		}
	}
}
