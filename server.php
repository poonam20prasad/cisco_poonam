<?php

 class Server {
	var $errors = array();
	var $data = array();
	var $msg ="";
	var $db = null;
	
	/*********** DataBase Functions **********/
	function dbConnect(){
		// connect to the database
		$this->db = mysqli_connect('localhost', 'root', '', 'ciscotest');
		if (mysqli_connect_errno()) {
            $this->msg .= "Database Connection Failed". mysqli_connect_errno().":".mysqli_connect_error();
            return null;
        }
		return $this->db;
	}

	function dbClose(){
		mysql_close($this->getConnection()); // Closing Connection
		$this->db = null;
	}
	
	function getConnection(){
		if($this->db==null)
			$this->db = $this->dbConnect();
		return $this->db;
	}
	
	function dbInsert($query){
		// Perform a query, check for error
        if (!mysqli_query($this->getConnection(), $query)) {
          $this->msg .= "Data Insertion Failed" . mysqli_errno($this->getConnection()).":".mysqli_error($this->getConnection());
		  return 0;
        } 
		
		$insert_id = mysqli_insert_id($this->getConnection());
		return $insert_id;
	}
	
	function dbUpdate($query){
		// Perform a query, check for error
        if (!mysqli_query($this->getConnection(), $query)) {
          $this->msg .= "Data Insertion Failed" . mysqli_errno($this->getConnection()).":".mysqli_error($this->getConnection());
		  return 0;
        }
		return 1;
	}
	
	function dbSelectCount($query){
		if ($result=mysqli_query($this->getConnection(), $query)){
			// Return the number of rows in result set
			$rowcount=mysqli_num_rows($result);
			// Free result set
			mysqli_free_result($result);
			return $rowcount;
		}
		return 0;
	}
	
	function dbSelect($query){
		if ($result=mysqli_query($this->getConnection(), $query)){
			// Return the number of rows in result set
			$rowcount=mysqli_num_rows($result);
			
			if($rowcount>0)
				return $result;
		}
		return null;
	}
	
	
	/********** Validation Functions **********/
	
	function isValidNumber($number, $size, $decimal){
		if($number==0){
			return true;
		}
		if(!is_numeric($number)) {//check emtpy
			$this->msg .= "Sorry, Number is Empty.";
			return false;
		}
		
		if($decimal==0 && !filter_var($number, FILTER_SANITIZE_NUMBER_INT)){//check int
			$this->msg .= "Sorry, Number is not Integral.";
			return false;
		} 
		else if(!filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT)) {//check float
			$this->msg .= "Sorry, Number is not Decimal.";
			return false;
		}
		if(strlen($number)>$size){//check length
			$this->msg .= "Sorry, Larger Length.";
			return false;
		} 
		return true;
	}
	
	function isValidString($string, $length){
		if(empty($string)){//check emtpy
			$this->msg .= "Sorry, Field is Empty.";
			return false;
		}
		if(!filter_var($string, FILTER_SANITIZE_STRING)){//string validation
			$this->msg .= "Sorry, Not Valid String.";
			return false;
		} 
		if(strlen($string)>$length && $length !=-1) {//check length
			$this->msg .= "Sorry, Larger Length.";
			return false;
		} 
		return true;
	}
	
	/********** Controller Functions ***********/
	
	function getAction(){
		$flag = false;
		//var_dump($this->data);
		//die;
		if(isset($this->data['action'])){
			if($this->data['action']=="deleteRouter"){
				$flag = $this->updateRouter();
			} else if($this->data['action']=="updateRouter"){
				$flag = $this->updateRouter();
			} else if($this->data['action']=="addRouter"){
				$flag = $this->addRouter();
			} else if($this->data['action']=="update_order"){
				$flag = $this->updateOrder();
			} else if($this->data['action']=="add_status"){
				$flag = $this->addOrderStatus();
			} else if($this->data['action']=="add_message"){
				$flag = $this->addOrderMessage();
			} else if($this->data['action']=="router_list"){
				$flag = $this->getRouterList();
			}		
			echo json_encode(array(
					'status' => $flag?'success':'error',
					'message'=> $this->msg
				));
		}
	}
	
	/********** Model Functions **********/
	
	function getRouterList($status=0) {	
		if(isset($this->data['status']))
			$status = $this->data['status'];
		$sql = "SELECT * FROM tblrouter";
		if($status>0)
			$sql.=" where Status=".$status;
		return $this->dbSelect($sql);
	}
	
	function getRouterCount($status=0) {	
		$sql = "SELECT * FROM tblrouter";
		if($status>0)
			$sql.=" where Status=".$status;
		return $this->dbSelectCount($sql);
	}
		
	function getRouterDetails($key='') {		
		$sql = "SELECT * FROM tblrouter ";			
		if(strlen($key)>0)
			$sql.=" where Loopback='$key'";
		$result = $this->dbSelect($sql);
		$orders = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		
		return $orders;
	}
	
	function isUniqueRouter(){
		$query = "SELECT * FROM tblrouter WHERE";
		
		if(isset($this->data["sapid"]))
			$query .= " Sapid = '".$this->data["sapid"]."' AND";
		if(isset($this->data["hostname"]))
			$query .= " Hostname = '".$this->data["hostname"]."' AND";
		if(isset($this->data["ipaddress"]))
			$query .= " Loopback = '".$this->data["ipaddress"]."' AND";
		if(isset($this->data["macaddress"]))
			$query .= " MacAddress = '".$this->data["macaddress"]."' AND";
		if(isset($this->data["type"]))
			$query .= " Type = '".$this->data["type"]."' AND";
		if(isset($this->data["status"]))
			$query .= " Status = ".$this->data["status"];
		else
			$query .= " Status = 1";

		
		//die($query);
		if($this->dbSelectCount($query)>0){				
			return false;
		} 	
		return true;
	}
	
	function addRouter() {
		
		$Sapid = $this->data["sapid"];
		$Hostname = $this->data["hostname"];
		$Loopback = $this->data["ipaddress"];
		$MacAddress = $this->data["macaddress"];
		$Type = $this->data["type"];
		$Status = 1;
		
		if($this->isUniqueRouter()){
			$query = "INSERT INTO tblrouter (Sapid, Hostname, Loopback, MacAddress, Type, Status) VALUES('$Sapid','$Hostname', '$Loopback', '$MacAddress', '$Type',$Status)";
			if($this->dbInsert($query)>0){
				$this->msg = "Status updated";			
			} 
			$this->msg .= "Sorry, there was an error in saving your data.";	
		} else{
			$this->msg .= "Sorry, Data is not unique.";	
		}
		return false;		
	}	
	
	function addRouterdata($Sapid,$Hostname,$Loopback,$MacAddress,$Type,$Status) {
		
			$query = "INSERT INTO tblrouter (Sapid, Hostname, Loopback, MacAddress, Type, Status) VALUES('$Sapid','$Hostname', '$Loopback', '$MacAddress', '$Type',$Status)";
			if($this->dbInsert($query)>0){
				$this->msg = "Status updated";			
			} 					
	}
	
	function UpdateRouter() {
		$query = "UPDATE tblrouter SET";
		
		if(isset($this->data["sapid"]))
			$query .= " Sapid = '".$this->data["sapid"]."',";
		if(isset($this->data["hostname"]))
			$query .= " Hostname = '".$this->data["hostname"]."',";
		if(isset($this->data["ipaddress"]))
			$query .= " Loopback = '".$this->data["ipaddress"]."',";
		if(isset($this->data["macaddress"]))
			$query .= " MacAddress = '".$this->data["macaddress"]."',";
		if(isset($this->data["type"]))
			$query .= " Type = '".$this->data["type"]."',";
		if(isset($this->data["status"]))
			$query .= " Status = ".$this->data["status"];
		else
			$query .= " Status = 1";

		$query .= " WHERE  Rid= ".$this->data["rid"];
		//die($query);
		if($this->dbUpdate($query)>0){				
			return true;				
		} else {
			$this->msg .= "Sorry, there was an error in saving your data.";
		}	
		return false;	
	}
	
	public function generateSapid($length=18)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		$query = "SELECT * FROM `tblrouter` WHERE `Sapid` LIKE '$randomString' and Status=1 ";
		$data=$this->dbSelect($query);
		
		if($data!=NULL){
			return self::generateSapid(18);
		}
		else {
			return $randomString;
		}
		
	}
	
	public function generatehostname($length=14)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		$query = "SELECT * FROM `tblrouter` WHERE `Hostname` LIKE '$randomString' and Status=1 ";
		$data=$this->dbSelect($query);
		
		if($data!=NULL){
			return self::generatehostname(14);
		}
		else {
			return $randomString;
		}
		
	}
	
	
	public function generatemacaddress($length=17)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		$query = "SELECT * FROM `tblrouter` WHERE `MacAddress` LIKE '$randomString' and Status=1 ";
		$data=$this->dbSelect($query);
		
		if($data!=NULL){
			return self::generatemacaddress(17);
		}
		else {
			return $randomString;
		}
		
	}
	
	public function generateip()
	{
		//$ip=long2ip(rand(0, "4294967295"));
		$ip = "".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);
		$query = "SELECT * FROM `tblrouter` WHERE `Loopback` LIKE '$ip' and Status=1 ";
		$data=$this->dbSelect($query);
		if($data!=NULL){
			return self::generateip();
		}
		else {
			return $ip;
		}	

	}
	
	function getDirFileList(){
		if(isset($this->data["dir"])){
			$sort = 0;
			if(isset($this->data["dirsort"]))
				$sort=	$this->data["dirsort"];
			return scandir($this->data["dir"], $sort);
		}
		return false;		
	}
	
	function getDirContents($dir) {
				$files = scandir($dir);
				foreach ($files as $key => $value) {
					$path = $dir . DIRECTORY_SEPARATOR . $value;
					if (!is_dir($path)) {
						$path;
						
					} else if ($value != "." && $value != "..") {
						getDirContents($path);
					}
				}
			}
	
	
	function getBaseUrl() 
	{
		// output: /myproject/index.php
		$currentPath = $_SERVER['PHP_SELF']; 
		
		// output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
		$pathInfo = pathinfo($currentPath); 
		
		// output: localhost
		$hostName = $_SERVER['HTTP_HOST']; 
		
		// output: http://
		$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
		
		// return: http://localhost/myproject/
		return $protocol.$hostName.$pathInfo['dirname']."/";
	}
	
	function regularPolygon($img,$x,$y,$radius,$sides,$color)
	{
		$points = array();
		for($a = 0;$a <= 360; $a += 360/$sides)
		{
			$points[] = $x + $radius * cos(deg2rad($a));
			$points[] = $y + $radius * sin(deg2rad($a));
		}
		//print_r($points);die;
		return imagepolygon($img,$points,$sides,$color);
	} 
	
 }
 
//phpinfo();

if($_REQUEST || $_POST || $_GET){
	$objServer=new Server();
	$objServer->data=isset($_REQUEST)?$_REQUEST:isset($_POST)?$_POST:$_GET;
	$objServer->getAction(); 
}
?>