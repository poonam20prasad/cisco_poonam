<?php

class ApiController extends Controller
{

        Const APPLICATION_ID = 'Poonam';
        private $format = 'json';

        private function _sendResponse($status = 200, $body = '', $content_type = 'application/json')
        {

            // set the status
            $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
            header($status_header);
            // and the content type
            header('Content-type: ' . $content_type);
            // pages with body are easy
            if($body != '')
            {
                // send the body
				header("Access-Control-Allow-Origin: *");
                echo $body;
            }
            // we need to create the body if none is passed
            else
            {
                
                $message = '';
                
                switch($status)
                {
                    case 401:
                        $message = 'You must be authorized to view this page.';
                        break;
                    case 404:
                        $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                        break;
                    case 500:
                        $message = 'The server encountered an error processing your request.';
                        break;
                    case 501:
                        $message = 'The requested method is not implemented.';
                        break;
                }

                // servers don't always have a signature turned on
                // (this is an apache directive "ServerSignature On")
                $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

                // this should be templated in a real-world solution
                $body = '
                    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
                    <html>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                        <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
                    </head>
                    <body>

                        <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
                        <p>' . $message . '</p>
                        <hr />
                        <address>' . $signature . '</address>
                    </body>
                    </html>';
                echo $body;
            }
            //Yii::app()->end();
        }

        private function _getStatusCodeMessage($status)
        {

            $codes = Array(
                200 => 'OK',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
            );
            return (isset($codes[$status])) ? $codes[$status] : '';
        }

        private function _checkAuth()
        {
           $purifier = new CHtmlPurifier();
           $_POST = $purifier->purify($_POST);
           if(isset($_POST['username']) && isset($_POST['password']))
           {
                $username = $_POST['username'];
                $password = $_POST['password'];

                $check = Tblapiusers::model()->authenticate($username, $password);

                if($check!=FALSE)
                {
                    return $check;
                }
                else
                {
                    return FALSE;
                }
           }

           if(isset($_POST['Key']))
           {
               $key = $_POST['Key'];
               $check = Tblapiusers::model()->authenticatekey($key);
               if($check!=FALSE)
               {
                    return $check;
               }
               else
               {
                    return FALSE;
               }
           }
           else
           {
               return FALSE;
           }

        }

        public function filters()
        {
            return array();
        }


        public function actionIndex()
	{
		$this->render('index');
	}

    public function actionRouterlistIpRange()
    {
		//die('hii');
       $this->layout = '';
       $a = $this->_checkAuth();
       $listservice = NULL;
       if ($a==FALSE)
       {
           $a = 0;
           $result = array('status'=>0,'Message'=>'Sorry! You are not authorized to perform this action re-check username and password');
       }

       else
       {
            if(isset($_POST['min']) && isset($_POST['max']) )
            {
                $minip = $_POST['min'];
				$maxip = $_POST['max'];
				
                $records = Tblrouter::model()->findAll(array('condition'=>'Status=:Status','params'=>array(':Status'=>1)));
				//var_dump($records);die;
				$range=array();
				$range[0]=$minip;
				$range[1]=$maxip;
				
				//$recordip=$records['Loopback'];
				//var_dump($records);die;
				$result=array();
				foreach($records as $rec)
				{
					$recordip=$rec['Loopback'];
					//echo $recordip;
					if(($this->ip_in_range($recordip, $range))){
						//echo 'hiiiiiii';
						array_push($result,$recordip);
					}
				}
            }
            else
            {
                $result = array('status'=>0,'Message'=>'Request Parameters missing');
            }

       }


       $resultjson = json_encode($result);
       $this->_sendResponse(200,$resultjson);

    }
	public static function ip_in_range($ip, $range)
	{
		//var_dump($range);
		if (is_array($range)) {
		  foreach ($range as $r) {
			  return self::ip_in_range($ip, $r);
		  }
		} else {
		  if ($ip === $range) { 
			 return TRUE;
		  }
		} 
    
	}
	
	public function actionDeleteRecord()
    {
		//die('hii');
       $this->layout = '';
       $a = $this->_checkAuth();
       $listservice = NULL;
       if ($a==FALSE)
       {
           $a = 0;
           $result = array('status'=>0,'Message'=>'Sorry! You are not authorized to perform this action re-check username and password');
       }

       else
       {
            if(isset($_POST['ip'])) 
            {
                $ip = $_POST['ip'];
				//var_dump($ip);die;
				$checkip = Tblrouter::model()->find(array('condition'=>'Loopback=:Loopback','params'=>array(':Loopback'=>$ip)));
				//var_dump($checkip);die;
				if($checkip!=NULL){
					$result = Tblrouter::model()->deleterouter($ip);
				}
				else
				{
					$result = array('status'=>0,'Message'=>'IP does not exist');
				}
                
            }
            else
            {
                $result = array('status'=>0,'Message'=>'Request Parameters missing');
            }

       }


       $resultjson = json_encode($result);
       $this->_sendResponse(200,$resultjson);

    }
	
	public function actionUpdateRecord()
    {
		//die('hii');
       $this->layout = '';
       $a = $this->_checkAuth();
       $listservice = NULL;
       if ($a==FALSE)
       {
           $a = 0;
           $result = array('status'=>0,'Message'=>'Sorry! You are not authorized to perform this action re-check username and password');
       }

       else
       {
            if(isset($_POST['ip'])) 
            {
				
                $ip = $_POST['ip'];
				//var_dump($ip);die;
				$checkip = Tblrouter::model()->find(array('condition'=>'Loopback=:Loopback','params'=>array(':Loopback'=>$ip)));
				//var_dump($checkip);die;
				if($checkip!=NULL){
					$connection = Yii::app()->db;
					$query = "UPDATE tblrouter SET";
		
					if(isset($_POST["sapid"]))
						$query .= " Sapid = '".$_POST["sapid"]."',";
					if(isset($_POST["hostname"]))
						$query .= " Hostname = '".$_POST["hostname"]."',";
					if(isset($_POST["ipaddress"]))
						$query .= " Loopback = '".$_POST["ipaddress"]."',";
					if(isset($_POST["macaddress"]))
						$query .= " MacAddress = '".$_POST["macaddress"]."',";
					if(isset($_POST["type"]))
						$query .= " Type = '".$_POST["type"]."',";
					if(isset($_POST["status"]))
						$query .= " Status = ".$_POST["status"];
					else
						$query .= " Status = 1";
					$query .= " WHERE  Loopback= '".$ip."'";
					//die($query);
					$command=$connection->createCommand($query);
					$command->execute();
					$result = array('status'=>1,'Message'=>'Record updated succesfully');
				}
				else
				{
					$result = array('status'=>0,'Message'=>'IP does not exist');
				}
                
            }
            else
            {
                $result = array('status'=>0,'Message'=>'Request Parameters missing');
            }

       }


       $resultjson = json_encode($result);
       $this->_sendResponse(200,$resultjson);

    }
	
	public function actiongetRouterbyType()
    {
		//die('hii');
       $this->layout = '';
       $a = $this->_checkAuth();
       $listservice = NULL;
       if ($a==FALSE)
       {
           $a = 0;
           $result = array('status'=>0,'Message'=>'Sorry! You are not authorized to perform this action re-check username and password');
       }

       else
       {
            if(isset($_POST['type'])) 
            {
				
                $type = $_POST['type'];			
				$query = "SELECT * FROM `tblrouter` WHERE `Type` LIKE '$type' and Status=1 ";
                $data = Yii::app()->db->createCommand($query)->queryAll();
				if($data!=NULL)
				{
				   foreach ($data as $value)
				   {
					   $routerdata[$value['Sapid']] = $value['Sapid'];
					   $routerdata[$value['Hostname']] = $value['Hostname'];
					   $routerdata[$value['Loopback']] = $value['Loopback'];
					   $routerdata[$value['MacAddress']] = $value['MacAddress'];
				   }

				   $result = array('status'=>1,'routerdata'=>$routerdata);
				}
				
				else
				{
					$result = array('status'=>0,'Message'=>'No records Found');
				}
                
            }
            else
            {
                $result = array('status'=>0,'Message'=>'Request Parameters missing');
            }

       }


       $resultjson = json_encode($result);
       $this->_sendResponse(200,$resultjson);

    }
	
	public function actionCreateRouter()
    {
		//die('hii');
       $this->layout = '';
       $a = $this->_checkAuth();
       $listservice = NULL;
       if ($a==FALSE)
       {
           $a = 0;
           $result = array('status'=>0,'Message'=>'Sorry! You are not authorized to perform this action re-check username and password');
       }

       else
       {
			$sapid = Tblrouter::model()->generateSapid(18);
			//die($sapid);
			$hostname=Tblrouter::model()->generatehostname(14);
			$ip = Tblrouter::model()->generateip(15);
			$macaddress = Tblrouter::model()->generatemacaddress(17);
			$status=1;
			$type="CSS";
			$connection = Yii::app()->db;
			$query = "insert into tblrouter ( Sapid,Hostname,Loopback,MacAddress,Type,Status ) VALUES ('".$sapid."','".$hostname."','".$ip."','".$macaddress."','".$type."','".$status."')";
			//die($query);
			$command=$connection->createCommand($query);
			$command->execute();
			$result = array('status'=>1,'Message'=>'Record inserted succesfully');
			 
            

       }


       $resultjson = json_encode($result);
       $this->_sendResponse(200,$resultjson);

    }
	
	function generateRandomString($length = 18) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}



}
