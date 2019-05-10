<?php
class kmuhSoapClient extends SoapClient {
	function __construct($wsdl, $options) {
		parent::__construct($wsdl, $options);
	}
	function __doRequest($request, $location, $action, $version, $one_way = 0) {
		return parent::__doRequest($request, $location, $action, $version, $one_way);
	}
}
$no = $sdate = $edate = "";//arguements received from tpr.php

$id = $checkmonth = $date = $time = $dates = $values = $AllValue = $color = $bools = $unit = $unittext = $stringdate = array();
$minvalue = $maxvalue = "";
$data_type = $chinese_desc = $min = $max = $len_data = "";
$data_type_bp = $chinese_desc_bp = $min_bp = $max_bp = $len_data_bp = array(); //for bp

date_default_timezone_set('UTC');

$output_data1 = array();
$output_data2 = array();
$output = array();



if(!(isset($_GET["Types"]))){
	if(isset($_GET["PatientChartNo"]) && isset($_GET["sdate"]) && isset($_GET["edate"])){

		$no = trim($_GET["PatientChartNo"]);
		date_default_timezone_set('UTC');
		$sdate = date('c', strtotime($_GET["sdate"]));
		date_default_timezone_set('UTC');
		$edate = date('c', strtotime($_GET["edate"]));
		$patient = array(
			"pChartNo"=>$no,
			"pTPRType"=>"T",
			"pBeginDate"=>$sdate,
			"pEndDate"=>$edate
		);
		try{
			$options = array(
				'content-type' => 'text/xml',
				'encoding ' => "UTF-8",
				'soap_version' => SOAP_1_1, 
				'trace' => true,
				'exceptions' => true,
				'cache_wsdl ' => WSDL_CACHE_NONE,
			);
			$wsdl = "http://172.18.2.90/WEB/eIntelligentWard/Service.svc?wsdl";
			$client = new kmuhSoapClient($wsdl,$options);
			$wcfservice = $client->GetTPRData($patient);
			$xml = $wcfservice;
			$xml = ( json_decode(json_encode($wcfservice), True));
			$data = simplexml_load_string($xml["GetTPRDataResult"]);
			if(empty($data)){
				echo "<script>history.back();</script>";
			}
			else{

				$len_data = count($data->TPRData->TPRType->Data);
			$edate= date('Y-m-d', strtotime(date("Y-m-d")));//date("Y-m-d");

			$data_type = json_decode(json_encode($data->TPRData->TPRType["Type"]),true);
			$chinese_desc = json_decode(json_encode($data->TPRData->TPRType["ChineseDesc"]),true);
			$min = json_decode(json_encode($data->TPRData->TPRType["MinYAxis"]),true);
			$max = json_decode(json_encode($data->TPRData->TPRType["MaxYAxis"]),true);

			$data_type = $data_type[0];
			$chinese_desc = $chinese_desc[0];
			$min = $min[0];
			$max = $max[0];
			for($x = 0 ; $x < $len_data ; $x++){
				$stringdate[$x] = json_decode(json_encode($data->TPRData->TPRType->Data[$x]->TPRCreatedate),true);
				$stringdate[$x] = $stringdate[$x][0];

				date_default_timezone_set('UTC');
				$id[$x] = ($x+1);
				$checkmonth[$x] = date('m', strtotime($stringdate[$x]));
				$date[$x] = date('Y-m-d H:i', strtotime($stringdate[$x]));
				$time[$x] = date('H:i', strtotime($stringdate[$x]));
				$temp_date = date("m/d/Y",strtotime($date[$x]));
				$dates[$x] = '/Date('.(strtotime($temp_date) * 1000).')/';
				$values[$x] = json_decode(json_encode($data->TPRData->TPRType->Data[$x]->TPRValue),true);
				$values[$x] = $values[$x][0];
				$unit[$x] = json_decode(json_encode($data->TPRData->TPRType->Data[$x]->TPRUnit),true);
				$unit[$x] = $unit[$x][0];
				$unittext[$x] = $chinese_desc; 
				$color[$x] = json_decode(json_encode($data->TPRData->TPRType->Data[$x]->TPRFontColor),true);
				$color[$x] = $color[$x][0];
				$AllValue[$x] = $stringdate[$x].";".$color[$x].";".$unittext[$x].";".$unit[$x];
				date_default_timezone_set('UTC');
				if($edate === date('Y-m-d', strtotime($stringdate[$x]))){
					$bools[$x] = true;
				}else{
					$bools[$x] = false;
				}
				$output_data1[$x] = array(
					"id"=>$id[$x],
					"checkmonth"=>$checkmonth[$x],
					"date"=>$date[$x],
					"time"=>$time[$x],
					"dates"=>$dates[$x],
					"values"=>$values[$x],
					"AllValue"=>$AllValue[$x],
					"color"=>$color[$x],
					"bools"=>$bools[$x],
					"unit"=>$unit[$x],
					"unittext"=>$unittext[$x],
					"stringdate"=>$stringdate[$x]
				);
			}
		}
	}catch(Exception $e){
		echo $e;
	}
}
}
if(isset($_GET["PatientChartNo"]) && isset($_GET["Types"]) && isset($_GET["sdate"]) && isset($_GET["edate"])){
	if(trim($_GET["Types"]) === "BP"){
		
		$no = trim($_GET["PatientChartNo"]);
		date_default_timezone_set('UTC');
		$sdate = date('c', strtotime($_GET["sdate"]));
		date_default_timezone_set('UTC');
		$edate = date('c', strtotime($_GET["edate"]));
		$patient = array(
			"pChartNo"=>$no,
			"pTPRType"=>"BP",
			"pBeginDate"=>$sdate,
			"pEndDate"=>$edate
		);
		try{
			$options = array(
				'content-type' => 'text/xml',
				'encoding ' => "UTF-8",
				'soap_version' => SOAP_1_1, 
				'trace' => true,
				'exceptions' => true,
				'cache_wsdl ' => WSDL_CACHE_NONE,
			);
			$wsdl = "http://172.18.2.90/WEB/eIntelligentWard/Service.svc?wsdl";
			$client = new kmuhSoapClient($wsdl,$options);
			$wcfservice = $client->GetTPRData($patient);
			$xml = $wcfservice;
			$xml = ( json_decode(json_encode($wcfservice), True));
			$data = simplexml_load_string($xml["GetTPRDataResult"]);
			if(empty($data)){
				echo "<script>history.back();</script>";
			}
			else{
				$edate= date('Y-m-d', strtotime(date("Y-m-d")));
				$len_TPRType = count($data->TPRData->TPRType);
				$len_data = array();
				for($x = 0; $x < $len_TPRType; $x++){
					$data_type_bp[$x] = json_decode(json_encode($data->TPRData->TPRType[$x]["Type"]),true);
					$data_type_bp[$x] = $data_type_bp[$x][0];
					$chinese_desc_bp[$x] = json_decode(json_encode($data->TPRData->TPRType[$x]["ChineseDesc"]),true);
					$chinese_desc_bp[$x] = $chinese_desc_bp[$x][0];
					$min_bp[$x] = json_decode(json_encode($data->TPRData->TPRType[$x]["MinYAxis"]),true);
					$min_bp[$x] = $min_bp[$x][0];
					$max_bp[$x] = json_decode(json_encode($data->TPRData->TPRType[$x]["MaxYAxis"]),true);
					$max_bp[$x] = $max_bp[$x][0];
					$len_data[$x] = count($data->TPRData->TPRType[$x]->Data);
				}
				$max = $min = 0;
				foreach( $max_bp as $vals){
					if($vals > $max){
						$max = $vals;
					}
				}
				foreach( $min_bp as $vals){
					if($vals < $min){
						$min = $vals;
					}
				}
				$output_data = array();
				$output = array();
				for($y = 0; $y < $len_TPRType; $y++){
					for($x = 0 ; $x < $len_data[$y] ; $x++){
						$stringdate[$y][$x] = json_decode(json_encode($data->TPRData->TPRType[$y]->Data[$x]->TPRCreatedate),true);
						$stringdate[$y][$x] = $stringdate[$y][$x][0];
						date_default_timezone_set('UTC');
						$id[$y][$x] = ($x+1);
						$checkmonth[$y][$x] = date('m', strtotime($stringdate[$y][$x]));
						$date[$y][$x] = date('Y-m-d H:i', strtotime($stringdate[$y][$x]));
						$time[$y][$x] = date('H:i', strtotime($stringdate[$y][$x]));
						$temp_date = date("m/d/Y",strtotime($date[$y][$x]));
						$dates[$y][$x] = '/Date('.(strtotime($temp_date) * 1000).')/';
						$values[$y][$x] = json_decode(json_encode($data->TPRData->TPRType[$y]->Data[$x]->TPRValue),true);
						$values[$y][$x] = $values[$y][$x][0];
						$unit[$y][$x] = json_decode(json_encode($data->TPRData->TPRType[$y]->Data[$x]->TPRUnit),true);
						$unit[$y][$x] = $unit[$y][$x][0];
						$unittext[$y][$x] = $chinese_desc_bp[$y]; 
						$color[$y][$x] = json_decode(json_encode($data->TPRData->TPRType[$y]->Data[$x]->TPRFontColor),true);
						$color[$y][$x] = $color[$y][$x][0];
						$AllValue[$y][$x] = $stringdate[$y][$x].";".$color[$y][$x].";".$unittext[$y][$x].";".$unit[$y][$x];
						date_default_timezone_set('UTC');
						if($edate === date('Y-m-d', strtotime($stringdate[$y][$x]))){
							$bools[$y][$x] = true;
						}else{
							$bools[$y][$x] = false;
						}
						$output_data[$y][$x] = array(
							"id"=>$id[$y][$x],
							"checkmonth"=>$checkmonth[$y][$x],
							"date"=>$date[$y][$x],
							"time"=>$time[$y][$x],
							"dates"=>$dates[$y][$x],
							"values"=>$values[$y][$x],
							"AllValue"=>$AllValue[$y][$x],
							"color"=>$color[$y][$x],
							"bools"=>$bools[$y][$x],
							"unit"=>$unit[$y][$x],
							"unittext"=>$unittext[$y][$x],
							"stringdate"=>$stringdate[$y][$x]
						);
					}
				}
				$output_data1 = $output_data[0];
				$output_data2 = $output_data[1];
			}
		}catch(Exception $e){
			echo $e;
		}
	}
	else{

		$no = trim($_GET["PatientChartNo"]);
		date_default_timezone_set('UTC');
		$sdate = date('c', strtotime($_GET["sdate"]));
		date_default_timezone_set('UTC');
		$edate = date('c', strtotime($_GET["edate"]));
		$type = trim($_GET["Types"]);
		$patient = array(
			"pChartNo"=>$no,
			"pTPRType"=>$type,
			"pBeginDate"=>$sdate,
			"pEndDate"=>$edate
		);
		try{
			$options = array(
				'content-type' => 'text/xml',
				'encoding ' => "UTF-8",
				'soap_version' => SOAP_1_1, 
				'trace' => true,
				'exceptions' => true,
				'cache_wsdl ' => WSDL_CACHE_NONE,
			);
			$wsdl = "http://172.18.2.90/WEB/eIntelligentWard/Service.svc?wsdl";
			$client = new kmuhSoapClient($wsdl,$options);
			$wcfservice = $client->GetTPRData($patient);
			$xml = $wcfservice;
			$xml = ( json_decode(json_encode($wcfservice), True));
			$data = simplexml_load_string($xml["GetTPRDataResult"]);
			if(empty($data)){
				echo "<script>history.back();</script>";
			}
			else{

				$len_data = count($data->TPRData->TPRType->Data);

				$data_type = json_decode(json_encode($data->TPRData->TPRType["Type"]),true);
				$chinese_desc = json_decode(json_encode($data->TPRData->TPRType["ChineseDesc"]),true);
				$min = json_decode(json_encode($data->TPRData->TPRType["MinYAxis"]),true);
				$max = json_decode(json_encode($data->TPRData->TPRType["MaxYAxis"]),true);

				$data_type = $data_type[0];
				$chinese_desc = $chinese_desc[0];
				$min = $min[0];
				$max = $max[0];
				$edate= date('Y-m-d', strtotime(date("Y-m-d")));
				for($x = 0 ; $x < $len_data ; $x++){
					$stringdate[$x] = json_decode(json_encode($data->TPRData->TPRType->Data[$x]->TPRCreatedate),true);
					$stringdate[$x] = $stringdate[$x][0];

					date_default_timezone_set('UTC');
					$id[$x] = ($x+1);
					$checkmonth[$x] = date('m', strtotime($stringdate[$x]));
					$date[$x] = date('Y-m-d H:i', strtotime($stringdate[$x]));
					$time[$x] = date('H:i', strtotime($stringdate[$x]));
					$temp_date = date("m/d/Y",strtotime($date[$x]));
					$dates[$x] = '/Date('.(strtotime($temp_date) * 1000).')/';
					$values[$x] = json_decode(json_encode($data->TPRData->TPRType->Data[$x]->TPRValue),true);
					$values[$x] = $values[$x][0];
					$unit[$x] = json_decode(json_encode($data->TPRData->TPRType->Data[$x]->TPRUnit),true);
					$unit[$x] = $unit[$x][0];
					$unittext[$x] = $chinese_desc; 
					$color[$x] = json_decode(json_encode($data->TPRData->TPRType->Data[$x]->TPRFontColor),true);
					$color[$x] = $color[$x][0];
					$AllValue[$x] = $stringdate[$x].";".$color[$x].";".$unittext[$x].";".$unit[$x];
					date_default_timezone_set('UTC');
					if($edate === date('Y-m-d', strtotime($stringdate[$x]))){
						$bools[$x] = true;
					}else{
						$bools[$x] = false;
					}
					$output_data1[$x] = array(
						"id"=>$id[$x],
						"checkmonth"=>$checkmonth[$x],
						"date"=>$date[$x],
						"time"=>$time[$x],
						"dates"=>$dates[$x],
						"values"=>$values[$x],
						"AllValue"=>$AllValue[$x],
						"color"=>$color[$x],
						"bools"=>$bools[$x],
						"unit"=>$unit[$x],
						"unittext"=>$unittext[$x],
						"stringdate"=>$stringdate[$x]
					);
				}
			}
		}catch(Exception $e){
			echo $e;
		}
	}
}
$output = array(
	"data"=>array_reverse($output_data1),
	"data2"=>array_reverse($output_data2),
	"minvalue"=>$min,
	"maxvalue"=>$max
);
echo json_encode($output);
?>