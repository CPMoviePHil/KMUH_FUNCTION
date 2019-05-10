<?php

class kmuhSoapClient extends SoapClient {
	function __construct($wsdl, $options) {
		parent::__construct($wsdl, $options);
	}
	function __doRequest($request, $location, $action, $version, $one_way = 0) {
		return parent::__doRequest($request, $location, $action, $version, $one_way);
	}
}
$dates = array();
$title = $starts = $colors = $urls = $overlaps = $renderings = $ends = $colortexts = array();
$output = array();
if(isset($_POST["No"])){

	$id = trim($_POST["No"]);
    //$webapialert = false;
	$patienId = array(
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
		$wcfservice = $client->GetAllLabReportInfo($patienId);
		$res = $client ->__getLastResponse();
		$wcfservice->GetAllLabReportInfoResult;
		$xml = $wcfservice;
		$xml = ( json_decode(json_encode($wcfservice), True));
		$data = simplexml_load_string($xml["GetAllLabReportInfoResult"]);
		if($xml["GetAllLabReportInfoResult"] === "<LabReportInfo />"){
			echo "WEB API錯誤!";
		}else{
			if(!empty($data)){
				$len = count($data->LabReport->LabExecDate);
				for($x = 0; $x < $len; $x++){
					$dates[$x] = json_decode(json_encode($data->LabReport->LabExecDate[$x]["LabDate"]), True);
					$output[$x] =array(
						"title"=>"　",
						"start"=>$dates[$x][0],
						"color"=>"#FF2D2D",
						"url"=>"ExamineInfo/ExamineInfo.php?No=".$id ."&Date=".$dates[$x][0],
						"overlap"=>null,
						"rendering"=>null,
						"end"=>$dates[$x][0],
						"colortext"=>null
					);
				}
			}
		}

	}catch(Exception $e){
		echo $e;
	}
}
else{
    //echo "<script>history.back();</script>";
}
echo json_encode($output);
?>