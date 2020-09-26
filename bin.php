<?php

 class Bin {
	var $errors = array();
	var $data = array();
	var $msg ="";
	var $db = null;
	
	/*********** DataBase Functions **********/
	function dbConnect(){
		// connect to the database
		$this->db = mysqli_connect('localhost', 'root', '', 'glassco');
		//$this->db = mysqli_connect('localhost', 'glassco1', 'ofW4;1-0ANMa6a', 'glassco1_glassco');
		
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
		//$this->dbClose();
		/*if($insert_id>0)
			return true;
		return false;*/
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
	
	/********** Email Functions **********/
	function mailConnect(){
		// connect to the database
		ini_set("SMTP","mail.glasscoghy.in");
		ini_set("smtp_port","587");
		ini_set("auth_username","info@glasscoghy.in");
		ini_set("auth_password","mousejr@45");
		ini_set("sendmail_from","info@glasscoghy.in");

		//mail('poonam20prasad@gmail.com', 'This is a test subject line', 'The complete body of the message', 'poonamprasad212');
	}	
	
	function sendEmail($mailto, $subject, $message, $file_count, $filename, $attachments){
		$this->mailConnect();
		
		$from_mail = "info@glasscoghy.in"; //from email using site domain.
		$from_name = "Glass Co"; //from email using site domain.
		$replyto = "poonamprasad212@gmail.com"; //from email using site domain.
		
		if($file_count == 0) //if attachment not exists
			return $this->sendPlainMail($mailto, $subject, $message, $from_mail, $from_name, $replyto);
		else if($file_count == 1) //if attachment not exists
			return $this->sendSingleAttachmentMail($mailto, $subject, $message, $from_mail, $from_name, $replyto, $filename);
		else //otherwise
			return $this->sendMultiAttachmentMail($mailto, $subject, $message, $from_mail, $from_name, $replyto, $file_count, $attachments);
	}
	
	function sendPlainMail($mailto, $subject, $message, $from_mail, $from_name, $replyto){
		//send plain email
		$header = "From: ".$from_name." <".$from_mail.">\r\n";
        $header .= "Reply-To: ".$replyto."\r\n";
        $header .= "X-Mailer: PHP/" . phpversion();
		
		if (mail($mailto, $subject, $message, $header)) {
            return true; // Or do something here
        } else {
          return false;
        }
	}
	
	function sendSingleAttachmentMail($mailto, $subject, $message, $from_mail, $from_name, $replyto, $filename){
		$file = "orders/".$filename;
        $content = file_get_contents( $file);
        $content = chunk_split(base64_encode($content));
        $uid = md5(uniqid(time()));
        $name = basename($file);
        
        // header
        $header = "From: ".$from_name." <".$from_mail.">\r\n";
        $header .= "Reply-To: ".$replyto."\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
        
        // message & attachment
        $nmessage = "--".$uid."\r\n";
        $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
        $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $nmessage .= $message."\r\n\r\n";
        $nmessage .= "--".$uid."\r\n";
        $nmessage .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
        $nmessage .= "Content-Transfer-Encoding: base64\r\n";
        $nmessage .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
        $nmessage .= $content."\r\n\r\n";
        $nmessage .= "--".$uid."--";
        
        if (mail($mailto, $subject, $nmessage, $header)) {
            return true; // Or do something here
        } else {
          return false;
        }
	}
	
	function sendMultiAttachmentMail($mailto, $subject, $message, $from_mail, $from_name, $replyto, $file_count, $attachments){
		$uid = md5(uniqid(time()));
        
		//header
		$header = "From: ".$from_name." <".$from_mail.">\r\n";
        $header .= "Reply-To: ".$replyto."\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n"; //mime type specification
        
		// message & attachment
        
        //message text
		$body = "--$uid\r\n";
		$body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
		$body .= "Content-Transfer-Encoding: base64\r\n\r\n"; 
		$body .= chunk_split(base64_encode($message)); 

		//attachments
		for ($x = 0; $x < $file_count; $x++){       
			if(!empty($attachments['name'][$x])){
				
				if($attachments['error'][$x]>0) //exit script and output error if we encounter any
				{
					$mymsg = array( 
					1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini", 
					2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", 
					3=>"The uploaded file was only partially uploaded", 
					4=>"No file was uploaded", 
					6=>"Missing a temporary folder" ); 
					$this->msg .=$mymsg[$attachments['error'][$x]]; 
					return false;
				}
					
				//get file info
				$file_name = $attachments['name'][$x];
				$file_size = $attachments['size'][$x];
				$file_type = $attachments['type'][$x];
				
				//read file 
				$handle = fopen($attachments['tmp_name'][$x], "r");
				$content = fread($handle, $file_size);
				fclose($handle);
				$encoded_content = chunk_split(base64_encode($content)); //split into smaller chunks (RFC 2045)
				
				$body .= "--$uid\r\n";
				$body .="Content-Type: $file_type; name=".$file_name."\r\n";
				$body .="Content-Disposition: attachment; filename=".$file_name."\r\n";
				$body .="Content-Transfer-Encoding: base64\r\n";
				$body .="X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n"; 
				$body .= $encoded_content; 
			}
		}
		
		if (mail($mailto, $subject, $message, $header)) {
            return true; // Or do something here
        } else {
          return false;
        }
	}
	
	function getOrderPlacedMessage($name,$message,$orderid,$orderstatus){
		//construct a message body to be sent to recipient
		$message_body =  "Dear ".$name.",\n\n";
		$message_body .=  "Greetings of the day !\n\n";
		$message_body .=  "Thanks for choosing Glass Co. as your destination to the services required.\n\n";
		$message_body .=  "We have recorded and updated  your order to ".$orderstatus." with below remarks:\n";
		$message_body .=  "------------------------------\n";
		$message_body .=  $message."\n";
		$message_body .=  "------------------------------\n";
		$message_body .=  "You can track status for the order through <a href ='".$this->getBaseUrl()."order.php?orderkey=".$this->getEncodeSting($orderid)."'>this link </a>.\n\n";
		$message_body .=  "Please refer your friends and family to give us opertunity to serve.\n\n";
		$message_body .=  "Cheers\n";
		$message_body .=  "Glass Co.\n";	
		return $message_body;
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
	
	function isValidName($name){
		if(empty($name)){//check emtpy
			$this->msg .= "Sorry, Name is Empty.";
			return false;
		} 
		if(!filter_var($name, FILTER_SANITIZE_STRING)) {//string validation
			$this->msg .= "Sorry, Not Valid Name.";
			return false;
		} 
		return true;
	}
	
	function isValidEmail($email){
		if(empty($email)) {//check emtpy
			$this->msg .= "Sorry, Email is Empty.";
			return false;
		} 
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {//Email validation
			$this->msg .= "Sorry, Not Valid Email.";
			return false;
		} 
		return true;
	}	
	
	function isValidImage($file){
		// Check if image file is a actual image or fake image
		$check = getimagesize($file["tmp_name"]);
		if($check == false){
			$this->msg .= "Sorry, Not Image File.";
			return false;
		}
		return true;
	}
	
	function isValidFile($file, $fileType){	
		//check emtpy	
		/*if(isset($file)) {
			$this->msg .= "Sorry, Empty File.";
			return false;
		}*/
				
		// Check file size
		if ($file["size"] > 500000) {
			$this->msg .= "Sorry, your file is too large.";
			return false;
		}	
			
		$fileExt = strtolower(pathinfo(basename($file["name"]),PATHINFO_EXTENSION));
		
		// Allow certain file formats
		if(!in_array($fileExt, $fileType)){
			$this->msg .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
			return false;
		}
		return true;
	}
	
	function isFileExits($fileName){
		// Check if file already exists
		if (file_exists($fileName)) {
			$this->msg .= "Sorry, file already exists.";
			return false;
		}
		return true;
	}
	
	function validatedata(){
		$username = "";
		$email    = "";
		if (empty($username)) { array_push($errors, "Username is required"); }
		if (empty($email)) { array_push($errors, "Email is required"); }
		if (empty($message)) { array_push($errors, "Message is required"); }
		
			//php validation, exit outputting json string
		if(true){	
		}
	}
	
	/*********** Session Functions ***********/
	
	function isValidSession() {
		
		session_start();// Starting Session
		// Storing Session
		$user_check=$_SESSION['username'];
		
		
		
		if($user_check!=null){
			$sql = "select username,name,status,roleid from tbluser where username='$user_check'";
			$result = $this->dbSelect($sql);
			if($result!=NULL){
				
				return $result;
			}
		}
		
		$this->userLogout();
		header('Location: index.php'); // Redirecting To Home Page
			
	}
	
	/*********** Utility Functions ***********/
	
	function getStatusName($code){
		if($code==0)
			return "Message";
		else if($code==1)
			return "Requested";
		else if($code==2)
			return "Accepted";
		else if($code==3)
			return "InProcess";
		else if($code==4)
			return "Completed";
		else if($code==5)
			return "Rejected";
		else if($code==6)
			return "Archived";
		else  
			return "Unknown";
	}
	
	function getStatusClass($code){
		if($code==0)
			return "message";
		else if($code==1)
			return "info";
		else if($code==2)
			return "warning";
		else if($code==3)
			return "inprogress";
		else if($code==4)
			return "success";
		else if($code==5)
			return "danger";
		else if($code==6)
			return "archived";
		else  
			return "other";
	}
	
	function getDecodeSting($str) {
		$str=base64_decode($str);
		$str = substr($str,7);
		return $str;
	}
	
	
	
	function getEncodeSting($str) {
		$str=base64_encode("GlassCo".$str);
		return $str;
	}
	
	function getUniqueName(){
		$name=dechex(time());
		$name="GC".strtoupper($name);
		return $name;
	}
	
	function getDateFormat($date) {
		$date = strtotime($date);
		return date('D',$date).','.date('M',$date).' '.date('j',$date).','.date('Y',$date).' '.date('g:i A',$date);
	}
	
	function getFileExt($fileType){		
		$fileExt = array();		
		if($fileType == "image")
			$fileExt = array("jpg","png","jpeg");
		return $fileExt;
	}
	
	function getFileUpload($file,$fileType){
		$attachments = $_FILES['fileToUpload'];
		$target_dir = "orders/";
		$filename=$this->getUniqueName().'_'.basename($file["name"]);
		$target_file = $target_dir . $filename;
		
		$uploadOk = $this->isValidFile($file,$this->getFileExt($fileType)) && $this->isFileExits($target_file);
		
		// Check if $uploadOk is set to 0 by an error
		if (!$uploadOk) {
			$this->msg .= "Sorry, Not Valid File.";
			return null;
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($file["tmp_name"], $target_file)) {
				$this->msg = "The file ". basename( $file["name"]). " has been uploaded.";
				return $filename;
			}
			return null;
		}
	}
	
	/********** Controller Functions ***********/
	
	function getAction(){
		$flag = false;
		//var_dump($this->data);
		//die;
		if(isset($this->data['action'])){
			if($this->data['action']=="user_login"){
				$flag = $this->userLogin();
			} else if($this->data['action']=="user_logout"){
				$flag = $this->userLogout();
			} else if($this->data['action']=="place_order"){
				$flag = $this->placeOrder();
			} else if($this->data['action']=="update_order"){
				$flag = $this->updateOrder();
			} else if($this->data['action']=="add_status"){
				$flag = $this->addOrderStatus();
			} else if($this->data['action']=="add_message"){
				$flag = $this->addOrderMessage();
			} else if($this->data['action']=="order_list"){
				$flag = $this->getOrderList();
			}		
			echo json_encode(array(
					'status' => $flag?'success':'error',
					'message'=> $this->msg
				));
		}
	}
	
	/********** Model Functions **********/
	
	function getOrderList($status=0) {	
		if(isset($this->data['status']))
			$status = $this->data['status'];
		$sql = "SELECT * FROM tblorders";
		if($status>0)
			$sql.=" where status=".$status;
		//echo $sql;die;
		return $this->dbSelect($sql);
	}
	
	function getOrderCount($status=0) {	
		$sql = "SELECT * FROM tblorders";
		if($status>0)
			$sql.=" where status=".$status;
		//echo $sql;die;
		return $this->dbSelectCount($sql);
	}
	
	function getOrderStatus($orderkey){
		$sql = "SELECT * FROM tblorderstatus where orderkey='".$orderkey."' ";
		$result = $this->dbSelect($sql);
		if($result!=null){
		$orderstatus = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		return $orderstatus;
		}
		return null;
	}
	
	function getOrderDetails($orderkey='') {		
		$sql = "SELECT * FROM tblorders ";	
//die($sql);		
		if(strlen($orderkey)>0)
			$sql.=" where orderkey='$orderkey'";
		$result = $this->dbSelect($sql);
		$orders = mysqli_fetch_all ($result, MYSQLI_ASSOC);
		for($i = 0, $j = count($orders); $i<$j ; $i++){
			$orders[$i]['orderstatus'] = $this->getOrderStatus($orders[$i]['orderkey']);			
		}
		return $orders;
	}
	
	function getDistinctPhone() {	
		$sql = "SELECT DISTINCT(phone) FROM tblorders";
		return $this->dbSelectCount($sql);
	}
	
	function userLogin(){
		if ($this->isValidString($this->data["username"],-1) && $this->isValidString($this->data["password"],-1)) {
			$username=$_POST['username'];
			$password=$_POST['password'];
			// Establishing Connection with Server by passing server_name, user_id and password as a parameter
			// To protect MySQL injection for Security purpose
			$username = stripslashes($username);
			$password = stripslashes($password);
			//$username = mysqli_real_escape_string($this->getConnection(),$username);
			//$password = mysqli_real_escape_string($this->getConnection(),$password);
			//$password = md5($password);//encrypt the password before saving in the database
			
			// SQL query to fetch information of registerd users and finds user match.
			$query = "select * from tbluser where password='$password' AND username='$username'";
			if ($this->dbSelectCount($query) == 1) {
				session_start();// Starting Session
				$_SESSION['username'] = $username; // Initializing Session
				$_SESSION['success'] = "You are now logged in";	
				$this->msg = "Login Successfully";
				return true;
			} else {
				$this->msg .= "Username or Password is invalid ";
			}				
		} else {
			$this->msg .= "Username or Password is  not entered valid";	
		}
		return false;
	}
	
	function userLogout(){
		session_start();
		// remove all session variables
		session_unset();
		// destroy the session
		session_destroy();
		
		$_SESSION['username'] = null;
		$_SESSION['success'] = "You are now logged out";	
		
		$this->msg .= "You are now logged out";
		return true;
		//header("location:".$this->data['path']); // Redirecting To Other Page				
	}
	
	function updateOrder(){
		if(!$this->isValidName($this->data["orderkey"]))
			$this->msg .= "Sorry, there was an error in validating your name.";
		if(!$this->isValidNumber($this->data["orderstatus"],2,0))
			$this->msg .= "Sorry, there was an error in validating your status.";
		if(!$this->isValidString($this->data["message"],-1))
			$this->msg .= "Sorry, there was an error in validating your message.";
		
		if($this->isValidName($this->data["orderkey"]) 
		&& $this->isValidNumber($this->data["orderstatus"],2,0)
		&& $this->isValidString($this->data["message"],-1)){
			$orderkey  = $this->data["orderkey"]; 
			$orderstatus = $this->data["orderstatus"]; 
			$message 		 = $this->data["message"]; //capture message
			$query = "update tblorders set status = $orderstatus where orderkey='$orderkey'";
			if($this->dbUpdate($query)>0){
				$order_data=$this->getOrderDetails($orderkey)[0];
				$recipient_name=$order_data['name'];
				$recipient_email=$order_data['email'];
				if($this->addOrderStatus($recipient_name, $recipient_email, $orderkey, $orderstatus, $message)){
					$this->msg = "Information Saved Successfully,Owner will contact soon";
					return true;
				} else {
					$this->msg .= "Sorry, Error in updating status.";
				}
			} else {
				$this->msg .= "Sorry, there was an error in saving your data.";
			}			
		}
		return false;
	}
	
	function placeOrder(){
		if(!$this->isValidName($this->data["name"]))
			$this->msg .= "Sorry, there was an error in validating your name.";
		if(!$this->isValidEmail($this->data["email"]))
			$this->msg .= "Sorry, there was an error in validating your email.";
		if(!$this->isValidNumber($this->data["phone"],12,0))
			$this->msg .= "Sorry, there was an error in validating your phone.";
		if(!$this->isValidString($this->data["message"],-1))
			$this->msg .= "Sorry, there was an error in validating your message.";
		
		if($this->isValidName($this->data["name"]) 
		&& $this->isValidEmail($this->data["email"])
		&& $this->isValidNumber($this->data["phone"],12,0)
		&& $this->isValidString($this->data["message"],-1)){
			$orderkey = $this->getUniqueName();
			$recipient_name  = $this->data["name"]; //capture sender name
			$recipient_email = $this->data["email"]; //capture sender email
			$recipient_phone = $this->data["phone"];
			$message 		 = $this->data["message"]; //capture message
			$orderstatus	= 1;
			$query = "INSERT INTO tblorders (orderkey, name, email, phone, status) 
					  VALUES('$orderkey','$recipient_name', '$recipient_email','$recipient_phone', $orderstatus)";
			if($this->dbInsert($query)>0){
				if($this->addOrderStatus($recipient_name, $recipient_email, $orderkey, $orderstatus, $message)){
					$this->msg = "Information Saved Successfully,Owner will contact soon";
					return true;
				} else {
					$this->msg .= "Sorry, Error in updating status.";
				}
			} else {
				$this->msg .= "Sorry, there was an error in saving your data.";
			}			
		}
		return false;		
	}
	
	function addOrderMessage(){
		if(!$this->isValidName($this->data["orderkey"]))
			$this->msg .= "Sorry, there was an error in validating your name.";
		if(!$this->isValidNumber($this->data["orderstatus"],2,0))
			$this->msg .= "Sorry, there was an error in validating your status.";
		if(!$this->isValidString($this->data["message"],-1))
			$this->msg .= "Sorry, there was an error in validating your message.";
		
		if($this->isValidName($this->data["orderkey"]) 
		&& $this->isValidNumber($this->data["orderstatus"],2,0)
		&& $this->isValidString($this->data["message"],-1)){
			$orderkey  = $this->data["orderkey"]; 
			$orderstatus = $this->data["orderstatus"]; 
			$message 		 = $this->data["message"]; //capture message
			$order_data=$this->getOrderDetails($orderkey)[0];
			$recipient_name=$order_data['name'];
			$recipient_email=$order_data['email'];
			if($this->addOrderStatus($recipient_name, $recipient_email, $orderkey, $orderstatus, $message)){
				$this->msg = "Information Saved Successfully,Owner will contact soon";
				return true;
			} else {
				$this->msg .= "Sorry, Error in updating status.";
			}		
		}
		return false;
	}
	
	function addOrderStatus($recipient_name, $recipient_email, $orderkey, $orderstatus, $message) {
		$filename = null;
		if(isset($_FILES)){
			$filename = $this->getFileUpload($_FILES["fileToUpload"],"image");
			if(is_null($filename)){
				$this->msg .= "Sorry, there was an error in validating your file.";
				//return false;
				$filename=null;
			}
		}	
		$query = "INSERT INTO tblorderstatus (orderkey, orderstatus, comment, attachment) VALUES('$orderkey',$orderstatus, '$message', '$filename')";
		if($this->dbInsert($query)>0){
			$this->msg = "Status updated";	
			
			$message = $this->getOrderPlacedMessage($recipient_name,$message,$orderkey);
			$sentMail=true;
			//$sentMail = $this->sendEmail($recipient_email, "Order Placed::Status-".$this->getStatusName($orderstatus), $message, count($_FILES["fileToUpload"]), $filename, $_FILES["fileToUpload"]);
			if($sentMail) {//output success or failure messages
				$this->msg = "Thank you for your order !";
				return true;
			}else{
				$this->msg .= "Could not send mail! Please check your PHP mail configuration.";  
				return false;
			}		
		} 
		$this->msg .= "Sorry, there was an error in saving your data.";	
		return false;		
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


	
 }
 
//phpinfo();
//$testdata=array('action'=>'place_order','name'=>'abcd','email'=>'abc$','phone'=>'8988888888','message'=>'jhsdhjjsdagghda');

if($_REQUEST || $_POST || $_GET){
	$objServer=new Server();
	$objServer->data=isset($_REQUEST)?$_REQUEST:isset($_POST)?$_POST:$_GET;
	$objServer->getAction(); 
}
?>