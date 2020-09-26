<?php
$dir    = 'C:/xampp/htdocs/cisco';
echo "<pre>";
function getDirContents($dir) {
	$files = scandir($dir);
	foreach ($files as $key => $value) {
		$path = $dir . DIRECTORY_SEPARATOR . $value;
		if (!is_dir($path)) {
			echo "Inode Value =".fileinode("$path").":: Path=".$path."\n";					
		} else if ($value != "." && $value != "..") {
			//echo "<hr/>";
			echo "----------".basename($path)."----------".$path."\n";	
			getDirContents($path);
		}
	}
}
getDirContents($dir); 
?>

