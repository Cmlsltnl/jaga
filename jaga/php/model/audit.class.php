<?php

class Audit {

	// NO UPDATE OR DELETE DUNCTIONALITY REQUIRED (DOES NOT EXTEND ORM)
	// ORM utilizes this class to log all CRUD operations
	
	public $auditID;
	public $channelID;
	public $auditDateTime;
	public $auditUserID;
	public $auditIP;
	public $auditAction;
	public $auditObject;
	public $auditObjectID;
	public $auditOldValue;
	public $auditNewValue;
	public $auditResult;
	public $auditNote;
	
	public function __construct($auditID = 0) {
	
		if ($auditID != 0) {
		
			$core = Core::getInstance();
			$query = "SELECT * FROM jaga_Audit WHERE auditID = :auditID LIMIT 1";
			$statement = $core->database->prepare($query);
			$statement->execute(array(':auditID' => $auditID));
			if (!$row = $statement->fetch()) { die('Audit entry does not exist.'); }
			foreach ($row AS $key => $value) { if (!is_int($key)) { $this->$key = $value; } }
			
		} else {

			$this->auditID = 0;
			if (isset($_SESSION['channelID'])) { $this->channelID = $_SESSION['channelID']; } else { $this->channelID = 0; }
			$this->auditDateTime = date('Y-m-d H:i:s');
			if (isset($_SESSION['userID'])) { $this->auditUserID = $_SESSION['userID']; } else { $this->auditUserID = 0; }
			$this->auditIP = $_SERVER['REMOTE_ADDR'];
			$this->auditAction = '';
			$this->auditObject = '';
			$this->auditObjectID = 0;
			$this->auditOldValue = '';
			$this->auditNewValue = '';
			$this->auditResult = '';
			$this->auditNote = '';

		}
	}
	
	public static function createAuditEntry($ioa) { // Instance of Audit Object

		$objectName = get_class($ioa);
		$auditVariableArray = get_object_vars($ioa);
		$auditPropertyArray = array_keys($auditVariableArray);
		$query = "INSERT INTO `jaga_Audit` (" . implode(', ', $auditPropertyArray) . ") VALUES (:" . implode(', :', $auditPropertyArray) . ")";
		$core = Core::getInstance();
		$statement = $core->database->prepare($query);
		foreach ($auditVariableArray AS $property => $value) { $attribute = ':' . $property; $statement->bindValue($attribute, $value); }
		if (!$statement->execute()){ die("Audit::createAuditEntry(\$ioa) => There was a problem saving to the audit trail."); }
		$auditID = $core->database->lastInsertId();
		return $auditID;
		
	}

	public function getAuditTrailArray() {

		$query = "SELECT * FROM jaga_Audit ORDER BY auditDateTime DESC LIMIT 500";
		$core = Core::getInstance();
		$statement = $core->database->query($query);
		$auditTrailArray = array();
		while ($row = $statement->fetch()) { $auditTrailArray[] = $row; }
		return $auditTrailArray;

	}

	
}

?>