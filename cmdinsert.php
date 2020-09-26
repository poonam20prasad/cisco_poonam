<?php
include ("server.php");
$objServer = new Server();

echo "How many records you want to enter?  ";
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) <1){
    echo "ABORTING!\n";
    exit;
}else {
echo "\n";



	for($i=1;$i<=$line;$i++)
	{
		$Sapid = $objServer->generateSapid(18);
		$Hostname=$objServer->generatehostname(14);
		$Loopback = $objServer->generateip(15);
		$MacAddress= $objServer->generatemacaddress(17);
		$Status=1;
		$Type="CSS";
		
		$insertrecord=$objServer->addRouterdata($Sapid,$Hostname,$Loopback,$MacAddress,$Type,$Status);
		echo $i."Record inserted\n";
	}
}
?>