<?php
class kmuhSoapClient extends SoapClient {
	function __construct($wsdl, $options) {
		parent::__construct($wsdl, $options);
	}
	function __doRequest($request, $location, $action, $version, $one_way = 0) {
		return parent::__doRequest($request, $location, $action, $version, $one_way);
	}
}
$color = $title = $start = $url = $overlap = $rendering = $end = $colortext = array();
$output1 = $output2 = array();
$no = "";
$dates = array();
$operation = $check = array();
$operation_date = $check_date = array();
$len_operation = $len_check = "";
$check_back = array();
$operation_back = array();
$output_data = array();
if(isset($_GET["No"])){
	$id = trim($_GET["No"]);
	$no = $id;
	$patientid = array(
		"pChartNo"=>$id
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
		$wcfservice = $client->GetScheduleInfoByChartNo($patientid);
		$wcfservice->GetScheduleInfoByChartNoResult;
		$xml = $wcfservice;
		$xml = ( json_decode(json_encode($wcfservice), True));
		$data = simplexml_load_string($xml["GetScheduleInfoByChartNoResult"]);
		if(empty($data)){
			echo "WEB API error!";
		}else{
			if(!empty($data)){
				
				if(!isset($data->OPTPatientInfo->OPTPatientInfoData)){

				}else{
					$len_operation = count($data->OPTPatientInfo->OPTPatientInfoData);
					for($x = 0; $x < $len_operation ; $x++){
						$operation[$x] = $data->OPTPatientInfo->OPTPatientInfoData[$x];
						$operation_date[$x] = json_decode(json_encode($operation[$x]->OptDate),true);
						$operation_date[$x] = date("Y-m-d",strtotime($operation_date[$x][0]));
					}
					$operation_date = array_values(array_unique($operation_date));

					for($x = 0 ; $x < (count($operation_date)); $x++){
						$output2[$x] = array(
							"color"=>"#0000FF",
							"title"=>"　",
							"start"=>$operation_date[$x],
							"url"=>"Index/Index.php?Id=".$no."&type=3&Date=".$operation_date[$x]."",
							"overlap"=>null,
							"rendering"=>null,
							"end"=>$operation_date[$x],
							"colortext"=>null
						);
						array_push($output_data,$output2[$x]);
						$operation_back[$x] = array(
							"color"=>"#FFC78E",
							"title"=>null,
							"start"=>$operation_date[$x],
							"url"=>null,
							"overlap"=>"false",
							"rendering"=>"background",
							"end"=>null,
							"colortext"=>null
						);
						
						array_push($output_data,$operation_back[$x]);
					}
				}
				if(!isset($data->CheckPatientInfo->CheckPatientInfoData)){

				}else{
					$len_check = count($data->CheckPatientInfo->CheckPatientInfoData);
					for($x = 0; $x < $len_check ; $x++){
						$check[$x] = $data->CheckPatientInfo->CheckPatientInfoData[$x];
						$check_date[$x] = json_decode(json_encode($check[$x]->CheckDate),true);
						$check_date[$x] = date("Y-m-d",strtotime($check_date[$x][0]));
					}
					$check_date = array_values(array_unique($check_date));
					for($x = 0 ; $x < (count($check_date)); $x++){
						$output1[$x] = array(
							"color"=>"#33FF33",
							"title"=>"　",
							"start"=>$check_date[$x],
							"url"=>"Index/Index.php?Id=".$no."&type=3&Date=".$check_date[$x]."",
							"overlap"=>null,
							"rendering"=>null,
							"end"=>$check_date[$x],
							"colortext"=>null
						);
						array_push($output_data,$output1[$x]);
						$check_back[$x] = array(
							"color"=>"#FFC78E",
							"title"=>null,
							"start"=>$check_date[$x],
							"url"=>null,
							"overlap"=>"false",
							"rendering"=>"background",
							"end"=>null,
							"colortext"=>null
						);

						array_push($output_data,$check_back[$x]);

					}
				}
				$output = array(
					"data"=>$output_data
				);
				echo json_encode($output);
			}
		}

	}catch(Exception $e){
		echo $e;
	}
}else{
	echo "<script>history.back();</script>";
}






?>