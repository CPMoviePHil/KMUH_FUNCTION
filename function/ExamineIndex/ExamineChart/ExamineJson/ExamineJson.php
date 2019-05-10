<?php
class kmuhSoapClient extends SoapClient {
	function __construct($wsdl, $options) {
		parent::__construct($wsdl, $options);
	}
	function __doRequest($request, $location, $action, $version, $one_way = 0) {
		return parent::__doRequest($request, $location, $action, $version, $one_way);
	}
}

$no = $labName4Query = $specKind4Query = $labExecDatetime = $sdate = $edate = "";//ExamineChart.php post的參數
$dates = array();//檢查日期
$id = $checkmonth = $date = $time = $dates = $values = $AllValue = $color = $bools = $unit = $unittext = $stringdate = array();
//output的變數，用JSON包覆
//bools是用來核對是否是同一天
//date是檢查的日期，time是檢查的時間，dates就是date不過要用微秒包裝
//unittext和stringdate都是null


if((isset($_GET["PatientChartNo"])) && (isset($_GET["LabName4Query"])) && (isset($_GET["SpecKind4Query"])) && (isset($_GET["LabExecDatetime"])) && (isset($_GET["sdate"])) && (isset($_GET["edate"])) )
{
	$id = trim($_GET["PatientChartNo"]);
$patienId = array("pChartNo"=>$id);
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
	$wcfservice = $client->GetAllLabReportInfo($patienId);
	$res = $client ->__getLastResponse();
	$wcfservice->GetAllLabReportInfoResult;
	$xml = $wcfservice;
	$xml = ( json_decode(json_encode($wcfservice), True));
	$data = simplexml_load_string($xml["GetAllLabReportInfoResult"]);

	if($xml["GetAllLabReportInfoResult"] === "<LabReportInfo />"){
		echo "<script>alert('web api error!')</script>";
	}else{
		if(!empty($data)){
			date_default_timezone_set('UTC');
			$sdate = date('Y/m/d', strtotime($_GET["sdate"]));
			date_default_timezone_set('UTC');
			$edate = date('Y/m/d', strtotime($_GET["edate"]));

	//-------get the length and data from the xml;
	//get the times of doing examination;
			$len_labExecDate = count($data->LabReport->LabExecDate);
			$output = array();
			$new_dates = array();
			$i = 0;
			for($x = 0 ; $x < $len_labExecDate ; $x++){
				$dates[$x] = json_decode(json_encode($data->LabReport->LabExecDate[$x]["LabDate"]),true);
				$dates[$x] = $dates[$x][0];
				if(($dates[$x]<$edate)&&($dates[$x]>$sdate)){
					$output[$i] = $data->LabReport->LabExecDate[$x];
					$new_dates[$i] = $dates[$x];
					$i++;

				}
			}

			$lens_LabType = array();
			for($x = 0 ; $x < count($output) ; $x++){
				$lens_LabType[$x] = count($output[$x]->LabType);
			}
	//length of labs
			$len_parse_data = array();
			for($x = 0; $x < count($output) ; $x++){
				for($y = 0 ; $y <$lens_LabType[$x]; $y++){
					$len_parse_data[$x][$y] = count($output[$x]->LabType[$y]->LabDataInfo);
				}
			}

	//get all the data, then compare each one of them in below section to see there is same element;
			$parse_data = array();
			for($x = 0; $x < count($output) ; $x++){
				for($y = 0 ; $y <$lens_LabType[$x]; $y++){
					for($z = 0; $z < $len_parse_data[$x][$y];$z++){
						$parse_data[$x][$y][$z] = $output[$x]->LabType[$y]->LabDataInfo[$z];
					}
				}
			}


	//--------parsing the xml simple object;

			$output_data = array();
			$index = 0;
			$month = array();
			for($x = 0 ; $x < count($new_dates); $x++){
				$month[$x] = date("m",strtotime($new_dates[$x]));
			}
			$labExecDatetime = $_GET["LabExecDatetime"];
			$labExecDatetime = date('Y/m/d H:i', strtotime($labExecDatetime));

			$labName4Query = $_GET["LabName4Query"];
			$specKind4Query = $_GET["SpecKind4Query"];

			for($x = 0; $x < count($output) ; $x++){
				for($y = 0 ; $y <$lens_LabType[$x]; $y++){
					for($z = 0; $z < $len_parse_data[$x][$y];$z++){
						if(((string)($parse_data[$x][$y][$z]->LabName4Query) === $labName4Query) && ((string)($parse_data[$x][$y][$z]->SpecKind4Query)===$specKind4Query))
						{
							$time = date("H:i",strtotime($parse_data[$x][$y][$z]->LabExecDatetime));
							$temp_date = date("m/d/Y",strtotime($labExecDatetime));
							$str = '/Date('.(strtotime($temp_date) * 1000).')/';
					//echo $parse_data[$x][$y][$z]->LabExecDatetime;
							if((string)($parse_data[$x][$y][$z]->LabExecDatetime)===$labExecDatetime){

								$t = json_decode(json_encode($parse_data[$x][$y][$z]->LabExecDatetime),true);
								$d = date('Y-m-d H:i', strtotime($t[0]));
								$v = json_decode(json_encode($parse_data[$x][$y][$z]->LabValue),true);
								$c = json_decode(json_encode($parse_data[$x][$y][$z]->LabFontColor),true);
								$output_data[$index] = array(
									"id"=>(string)($index+1),
									"checkmonth"=>$month[$x],
									"date"=>$d,
									"time"=>$time,
									"dates"=>$str,
									"values"=>$v[0],
									"AllValue"=>(string)$t[0].";".(string)$parse_data[$x][$y][$z]->LabValue.";".(string)$parse_data[$x][$y][$z]->LabFontColor,
									"color"=>$c[0],
									"bools"=>true,
									"unit"=>null,
									"unittext"=>null,
									"stringdate"=>null
								);
							}
							else{
								$t = json_decode(json_encode($parse_data[$x][$y][$z]->LabExecDatetime),true);
								$d = date('Y-m-d H:i', strtotime($t[0]));
								$v = json_decode(json_encode($parse_data[$x][$y][$z]->LabValue),true);
								$c = json_decode(json_encode($parse_data[$x][$y][$z]->LabFontColor),true);
								$output_data[$index] = array(
									"id"=>(string)($index+1),
									"checkmonth"=>$month[$x],
									"date"=>$d,
									"time"=>$time,
									"dates"=>$str,
									"values"=>$v[0],
									"AllValue"=>$parse_data[$x][$y][$z]->LabExecDatetime.";".$parse_data[$x][$y][$z]->LabValue.";".$parse_data[$x][$y][$z]->LabFontColor,
									"color"=>$c[0],
									"bools"=>false,
									"unit"=>null,
									"unittext"=>null,
									"stringdate"=>null
								);
							}
							$index++;
						}
					}
				}
			}
			echo (json_encode($output_data));
		}
	}

}catch(Exception $e){
	echo $e;
}
}
?>