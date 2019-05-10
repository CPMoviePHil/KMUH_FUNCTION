<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
     // The request is using the POST method
}
class kmuhSoapClient extends SoapClient {
    function __construct($wsdl, $options) {
        parent::__construct($wsdl, $options);
    }
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
$no = $name = $totalfee = $nhifee = $selffee = $displaytext = $ward = "";
$items = array();
$items_fee = array();
$patientid = array();

if(isset($_GET["no"])){
    $pChartNo = trim($_GET["no"]);
    $patientid = array("pChartNo"=>$pChartNo);
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
        $wcfservice = $client->GetTotalFee($patientid);
        $xml = $wcfservice;
        $xml = ( json_decode(json_encode($wcfservice), True));
        $data = simplexml_load_string($xml["GetTotalFeeResult"]);
        if(empty($data)){
            echo "<script>history.back();</script>";
        }
        else{
            $no = $data->PatientInfo['PatientChartNo'];
            $name = $data->PatientInfo['PatientName'];
            if(isset($data->TotalFee['Fee'])){
                $totalfee = number_format((string)$data->TotalFee['Fee']);
            }else{
                $totalfee = 0;
            }
            if(isset($data->NhiFee['Fee'])){
                $nhifee = number_format((string)$data->NhiFee['Fee']);
            }else{
                $nhifee = 0;
            }
            if(isset($data->SelfFee['Fee'])){
                $selffee = number_format((string)$data->SelfFee['Fee']);
            }else{
                $selffee = 0;
            }

            $len = (count( $data->SelfFee->SelfDetail ) );
            for($x = 0; $x < $len ;$x++){
                $items[$x] = $data->SelfFee->SelfDetail[$x]['ItemName'];
                $items_fee[$x] = number_format((string)$data->SelfFee->SelfDetail[$x]['ItemFee']);
            }
            $displaytext = $data->DisplayText;
        }
    }catch(Exception $e){
        echo $e;
    }
}else{
    echo "<script>history.back();</script>";
}
if(isset($_GET["no"])){
    $pChartNo = trim($_GET["no"]);
    $patientid = array("pChartNo"=>$pChartNo);
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
        $wcfservice = $client->GetAllLabReportInfo($patientid);
        $wcfservice->GetAllLabReportInfoResult;
        $xml = $wcfservice;
        $xml = ( json_decode(json_encode($wcfservice), True));
        $data = simplexml_load_string($xml["GetAllLabReportInfoResult"]);
        if($xml["GetAllLabReportInfoResult"] === "<LabReportInfo />"){
            $webapialert = true;
        }else{
            if(!empty($data)){
                $ward = $data->PatientInfo->WardBed;
            }
        }

    }catch(Exception $e){
        echo $e;
    }
}else{
    echo "<script>history.back();</script>";
}
//http://172.18.2.90/WEB/eIntelligentWard/Service.svc?singleWsdl
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>帳務查詢</title>
    <!-- BOOTSTRAP STYLES-->
    <link href="../../css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="../../css/font-awesome.css" rel="stylesheet" />
    <link  href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous" rel="stylesheet" />
    <!-- CUSTOM STYLES-->
    <link href="../../css/main.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href="../../css/font-open-sans.css" rel="stylesheet" />
    <script src="../../js/jquery-1.10.2.js"></script>

</head>

<body>

    <div id="wrapper">

        <div class="navbar navbar-inverse navbar-fixed-top operaInfo-navbar">
            <div class="adjust-nav">
                <div class="navbar-header">
                    <!--<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>-->
                    <div class="operaInfo-top col-md-12">

                        <span class="top-header">姓名:<?php echo $name;?></span>
                        <span class="top-header">床號:<?php echo $ward;?></span>
                        <span class="top-header">病歷號:<?php echo $no;?></span>
                        
                    </div>

                </div>
            </div>
        </div>

        <!-- /. NAV SIDE  -->
        <div id="operaInfo-wrapper">
            <div class="row pt-cost-content">
                <div class="col-lg-12">
                    <br /> 
                </div>
                <div class="col-md-12">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label><span class="cost-title">醫療費用金額</span>: <span class="cost-price"><?php echo $totalfee;?></span></label>
                        </div>
                        <div class="form-group">
                            <label><span class="cost-title">健保自付額</span>: <span class="cost-price"><?php echo $nhifee;?></span></label>
                        </div>
                        <div class="form-group">
                            <label><span class="cost-title">自費總金額</span>: <span class="cost-price"><?php echo $selffee;?></span></label>
                        </div>
                        <div class="form-group">
                            <label class="cost-note"><?php echo $displaytext;?></label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <img src="../../img/e-h-play-qrcode.png" class="img-fluid">
                        </div>
                        <div class="form-group">
                            <label class="cost-note">『不必到櫃檯排隊，遠端操作刷卡繳費，讓您不用帶現金!』</label>
                        </div>   
                    </div>    

                    <div class="col-md-12">
                        <div class="panel-group pefitem">

                            <div class="panel">
                                <a class="list-group-item text-center " data-toggle="collapse" href="#pef1">
                                    <strong>自費項目明細</strong>
                                    <span class="fa fa-chevron-down pt-list-item-right"></span>
                                </a>

                                <div id="pef1" class="panel">
                                    <div class="list-group">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover table-bordered results">
                                                <thead class="thead-inverse">
                                                    <tr>
                                                        <th width="70%">項目</th>
                                                        <th width="30%"><div class="text-center">金額(新台幣)</div></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    for($x = 0; $x < count($items) ; $x++){
                                                        echo '<tr><td><div>'.$items[$x].'</td><td><div class="ptcsot-td-cost-div ">'.$items_fee[$x].'</div></td></tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
    <div class="opera-footer">
        <div class="row">
            <div class="col-lg-12">
                &copy;  2017 邦城科技股份有限公司 | Design by: <a href="http://www.project-up.com" style="color:#fff;" target="_blank">www.project-up.com</a>
            </div>
        </div>
    </div>



    <!-- /. WRAPPER  -->
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/custom.js"></script>

</body>
</html>
