<?php

// secure against direct execution
if(!defined("JUDOINTRANET")) {die("Cannot be executed directly! Please use index.php.");}


/**
 * class ProtocolCorrection implements the representation of a protocol correction object
 */
class ProtocolCorrection extends Object {

	/*
	 * class-variables
	 */
	private $protocol;
	private $modified;
	private $pid;
	private $uid;
	private $finished;
	
	/*
	 * getter/setter
	 */
	public function get_protocol(){
		return $this->protocol;
	}
	public function set_protocol($protocol) {
		$this->protocol = $protocol;
	}
	public function get_modified(){
		return $this->modified;
	}
	public function set_modified($modified) {
		$this->modified = $modified;
	}
	public function get_pid(){
		return $this->pid;
	}
	public function set_pid($pid) {
		$this->pid = $pid;
	}
	public function get_uid(){
		return $this->uid;
	}
	public function set_uid($uid) {
		$this->uid = $uid;
	}
	public function get_finished(){
		return $this->finished;
	}
	public function set_finished($finished) {
		$this->finished = $finished;
	}
	
	/*
	 * constructor/destructor
	 */
	public function __construct($arg,$uid=null) {
		
		// parent constructor
		parent::__construct();
		
		// check uid
		if(is_null($uid)) {
			$uid = $_SESSION['user']->get_id();
		}
		$this->set_uid($uid);
		
		// check if user has allready corrected
		if(ProtocolCorrection::hasCorrected($arg->get_id(),$uid) === true) {
			$this->getFromDb($arg->get_id());
		} else {
			$this->set_protocol($arg->get_protocol());
			$this->set_finished(0);
		}
		
	}
	
	/*
	 * methods
	 */
	/**
	 * getFromDb gets the protocol correction text for the given protocolid
	 * 
	 * @param int $id id of the protocolentry
	 * @return void
	 */
	private function getFromDb($id) {
		
		// get db-object
		$db = Db::newDb();
		
		// prepare sql-statement
		$sql = "SELECT p.protocol,p.modified,p.finished
				FROM protocol_correction AS p
				WHERE p.pid = $id
				AND p.uid=".$this->get_uid();
		
		// execute
		$result = $db->query($sql);
		
		// fetch result
		list($protocol,$modified,$finished) = $result->fetch_array(MYSQL_NUM);
		
		// set variables to object
		$this->set_pid($id);
		$this->set_protocol($protocol);
		$this->set_modified($modified);
		$this->set_finished($finished);
		
		// close db
		$db->close();
	}
	
	
	
	
	
	
	
	
	/**
	 * hasCorrected checks if the actual user has already corrected this protocol
	 * 
	 * @param int $id id of the protocol to be checked
	 * @param int $uid uid of the user to be checked
	 * @return bool true if user has checked, false otherwise
	 */
	public static function hasCorrected($id,$uid=null) {
		
		// get db-object
		$db = Db::newDb();
		
		// check uid
		if(is_null($uid)) {
			$uid = $_SESSION['user']->get_id();
		}
		
		// prepare sql-statement
		$sql = "SELECT *
				FROM protocol_correction
				WHERE pid = $id
				AND uid=".$uid;
		
		// execute
		$result = $db->query($sql);
		
		// close db
		$db->close();
		
		// check result
		if($result->num_rows == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	
	
	
	
	
	
	
	/**
	 * update updates the fields of $this with the given values
	 * 
	 * @param array $correction array containig the changed values
	 * @return void
	 */
	public function update($correction) {
		
		// walk through array
		foreach($correction as $name => $value) {
			
			// check $name
			if($name == 'protocol') {
				$this->set_protocol($value);
			} elseif($name == 'modified') {
				$this->set_modified($value);
			} elseif($name == 'pid') {
				$this->set_pid($value);
			} elseif($name == 'finished') {
				$this->set_finished($value);
			}
		}
	}
	
	
	
	
	
	
	
	
	/**
	 * writeDb writes the actual values of $this to db
	 * 
	 * @param string $action indicates new correction or update existing
	 * @return void
	 */
	public function writeDb($action='new') {
		
		// get db-object
		$db = Db::newDb();
		
		// check action
		if($action == 'new') {
		
			// insert
			// prepare sql-statement
			$sql = "INSERT INTO protocol_correction
						(uid,
						pid,
						protocol,
						modified,
						finished,
						valid)
					VALUES (".$db->real_escape_string($this->get_uid()).","
						.$db->real_escape_string($this->get_pid()).",'"
						.$db->real_escape_string($this->get_protocol())."','"
						.$db->real_escape_string(date('Y-m-d H:i:s'))."',"
						.$db->real_escape_string($this->get_finished()).","
						.$db->real_escape_string(1).")";
			
			// execute;
			$db->query($sql);
		} elseif($action == 'update') {
			
			// update
			// prepare sql-statement
			$sql = "UPDATE protocol_correction
					SET
						protocol='".$db->real_escape_string($this->get_protocol())."',
						modified='".$db->real_escape_string(date('Y-m-d H:i:s'))."',
						finished=".$db->real_escape_string($this->get_finished()).",
						valid=".$db->real_escape_string(1)."
					WHERE uid = ".$db->real_escape_string($this->get_uid())."
					AND pid = ".$this->get_pid();
			
			// execute
			$db->query($sql);
		} else {
			
			// error
			$errno = $GLOBALS['Error']->error_raised('DbActionUnknown','write_protocol_correction',$action);
			throw new Exception('DbActionUnknown',$errno);
		}
		
		// close db
		$db->close();
	}
	
	
	
	
	
	
	
	
	/**
	 * listCorrections returns an array of all corrections of this protocol
	 * 
	 * @param int $id id of the protocol to be checked
	 * @return array list of all corrections of the given protocol id
	 */
	public static function listCorrections($pid) {
		
		// get db-object
		$db = Db::newDb();
		
		// prepare sql-statement
		$sql = "SELECT *
				FROM protocol_correction
				WHERE pid = ".$pid;
		
		// execute
		$result = $db->query($sql);
		
		// get result
		$corrections = array();
		while($correction = $result->fetch_array(MYSQL_ASSOC)) {
			$corrections[] = $correction;
		} 
		
		// close db
		$db->close();
		
		// return
		return $corrections;
	}

	
}