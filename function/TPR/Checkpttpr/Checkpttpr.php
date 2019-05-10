<?php
$sdate = $edate = "";
$today= new DateTime();
$today->format("Y-m-d");
if(isset($_GET["sdate"]) && (isset($_GET["edate"]))){
	$sdate = new Datetime($_GET["sdate"]);
	$edate = new Datetime($_GET["edate"]);
	$sdate->format("Y-m-d");
	$edate->format("Y-m-d");

	$interval = $sdate->diff($today);
	$interval2 = $today->diff($edate);
	if((($interval->y)===0)&&(($interval2->y)===0)){
		if((($interval->m)===0)&&(($interval2->m)===0)){
			if((($interval->d) <=13 )&&(($interval->d) <=13 )){
				echo $interval->y;
			}else{
				echo "false";
			}
		}else{
			echo "false";
		}
	}else{
		echo "false";
	}
}

?>