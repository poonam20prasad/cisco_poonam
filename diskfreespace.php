<?php
// On Windows:
$df_c = disk_free_space("C:");
echo "C;/ Directory Free Space: ".$df_c." bytes<br>";
$df_d = disk_free_space("D:");
echo "D:/ Directory Free Space: ".$df_d." bytes<br>";
/*Script using php to diskfreespace */

// $df contains the number of bytes available on "/"
$df = disk_free_space("/");
echo "Current Directory Free Space: ".$df." bytes<br>";

	$bytes = disk_free_space(".");
    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
    $base = 1024;
    $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
    echo "Current Directory Free Space:".sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />'; 
?>
