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
			if(isset($data->OPTPatientInfo->OPTPatientInfoData)){
				
				$s = trim($_POST["day"]);

				$thedate = date('Y/m/d', strtotime("+".($s)." days"));
				$operation_name = $operation_status = $operation_date = array();
				$op_name = $op_status = $op_date = array();
				$res = array();
				$len_data = "";
				$p_data = array();


				$len_data = count($data->OPTPatientInfo->OPTPatientInfoData);

				for($x = 0 ; $x < $len_data ; $x++){
					$p_data[$x] = $data->OPTPatientInfo->OPTPatientInfoData[$x];
					$op_date[$x] = json_decode(json_encode($p_data[$x]->OptDate),true);
					$op_date[$x] = $op_date[$x][0];
					$op_name[$x] = json_decode(json_encode($p_data[$x]->OptName),true);
					$op_name[$x] = $op_name[$x][0];
					$op_status[$x] = json_decode(json_encode($p_data[$x]->OptStatusDesc),true);
					$op_status[$x] = $op_status[$x][0];
				}
				for($x = 0 ; $x < $len_data ; $x++){
					if($thedate===$op_date[$x]){
						array_push($operation_name,$op_name[$x]);
						array_push($operation_status,$op_status[$x]);
						array_push($operation_date,$op_date[$x]);
					}
				}
				for($x = 0 ; $x < count($operation_date); $x++){
					echo "
					<tr>
					<td>
					".$operation_date[$x]."
					</td>
					<td>
					".$operation_name[$x]."
					</td>
					<td>
					".$operation_status[$x]."
					</td>
					</tr>";
				}
				
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