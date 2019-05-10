<?php
class kmuhSoapClient extends SoapClient {
	function __construct($wsdl, $options) {
		parent::__construct($wsdl, $options);
	}
	function __doRequest($request, $location, $action, $version, $one_way = 0) {
		return parent::__doRequest($request, $location, $action, $version, $one_way);
	}
}
$id = $s = "";
if(isset($_POST["id"]) && isset($_POST["day"])){
	$id = trim($_POST["id"]);
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
			echo "WEB API ERROR!";
		}else{
			$s = trim($_POST["day"]);

			$thedate = date('Y/m/d', strtotime("+".($s)." days"));
			$check_operation_name = $check_operation_status = $check_operation_date = $check_operation_time = $check_operation_place= array();
			$check_name = $check_status = $check_date = $check_time = $check_place = array();
			$len_check_data = "";
			$check_data = array();


			$len_check_data = count($data->CheckPatientInfo->CheckPatientInfoData);

			for($x = 0 ; $x < $len_check_data ; $x++){
				$check_data[$x] = $data->CheckPatientInfo->CheckPatientInfoData[$x];

				$check_operation_date[$x] = json_decode(json_encode($check_data[$x]->CheckDate),true);
				$check_operation_date[$x] = $check_operation_date[$x][0];
				$check_operation_name[$x] = json_decode(json_encode($check_data[$x]->CheckName),true);
				$check_operation_name[$x]  = $check_operation_name[$x][0];
				$check_operation_status[$x] = json_decode(json_encode($check_data[$x]->PatientStatus),true);
				$check_operation_status[$x] = $check_operation_status[$x][0];
				$check_operation_time[$x] = json_decode(json_encode($check_data[$x]->CheckTime),true);
				$check_operation_time[$x] = $check_operation_time[$x][0];
				if(!empty( json_decode(json_encode($check_data[$x]->Place),true) )){
					$check_operation_place[$x] = json_decode(json_encode($check_data[$x]->Place),true);
					$check_operation_place[$x] = $check_operation_place[$x][0];
				}else{
					$check_operation_place[$x] = null;
				}
			}
			for($x = 0 ; $x < $len_check_data ; $x++){
				if($thedate===$check_operation_date[$x]){
					array_push($check_name,$check_operation_name[$x]);
					array_push($check_status,$check_operation_status[$x]);
					array_push($check_date,$check_operation_date[$x]);
					array_push($check_place,$check_operation_place[$x]);
					array_push($check_time,$check_operation_time[$x]);
				}
			}
			for($x = 0 ; $x < count($check_date); $x++){
				echo "
				<tr>
				<td>
				".$check_date[$x]."
				</td>
				<td>
				".$check_time[$x]."
				</td>
				<td>
				".$check_name[$x]."
				</td>
				<td>
				".$check_place[$x]."
				</td>
				<td>
				".$check_status[$x]."
				</td>
				</tr>";
			}
			
		}
	}
	catch(Exception $e){
		echo $e;
	}

}else{
	echo "<script>history.back();</script>";
}

?>