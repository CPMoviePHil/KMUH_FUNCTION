<?php
class kmuhSoapClient extends SoapClient {
    function __construct($wsdl, $options) {
        parent::__construct($wsdl, $options);
    }
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
$no = $name = $birthday = $ward = "";
$len = "";
$typeNames = array();
$labDataInfo_len = array();
$labTimes = $labFullNames = $labValues = $labUnits = $labRefValues = $labFontColors = array();
$labName4Queries = $specKind4Queries = array();
if(isset($_GET["No"]) && isset($_GET["Date"])){

    $Id = trim($_GET["No"]);
    $Date = trim($_GET["Date"]);
    date_default_timezone_set('UTC');
    $newDate = date('Y/m/d', strtotime($Date));
    $webapialert = false;
    $patienId = array(
        "pChartNo"=>$Id
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
            echo "<script>alert('web api error!')</script>";
        }else{
            if(!empty($data)){
                $no = $data->PatientInfo->PatientChartNo;
                $name = $data->PatientInfo->PatientName;
                $birthday = $data->PatientInfo->PatientBirthday;
                $ward = $data->PatientInfo->WardBed;

                $len = count($data->LabReport->LabExecDate);
                for($x = 0 ; $x < $len ; $x++){
                    $dates[$x] = json_decode(json_encode($data->LabReport->LabExecDate[$x]["LabDate"]), True);

                    if($dates[$x][0] === $Date){
                        $output_array_len = (count($data->LabReport->LabExecDate[$x]->LabType));
                        for($y = 0 ; $y < $output_array_len ; $y++){
                            $output[$y] = $data->LabReport->LabExecDate[$x]->LabType[$y];
                        }
                    }
                }
                
                for($x = 0; $x < count($output) ; $x++){
                    $typeNames[$x] = $output[$x]["LabTypeName"];
                    $typeNames[$x] = json_decode(json_encode($typeNames[$x]), True);
                    $labDataInfo_len[$x] = count($output[$x]->LabDataInfo);

                }
                for($x = 0; $x < count($output) ; $x++){
                    for($y = 0; $y < $labDataInfo_len[$x]; $y++){
                        $labTimes[$x][$y] = json_decode(json_encode($output[$x]->LabDataInfo[$y]->LabExecDatetime),true);
                        $labFullNames[$x][$y] = json_decode(json_encode($output[$x]->LabDataInfo[$y]->LabFullName),true);
                        $labValues[$x][$y] = json_decode(json_encode($output[$x]->LabDataInfo[$y]->LabValue),true);
                        $labUnits[$x][$y] = json_decode(json_encode($output[$x]->LabDataInfo[$y]->LabUnit),true);
                        $labRefValues[$x][$y] = json_decode(json_encode($output[$x]->LabDataInfo[$y]->LabRefValue),true);
                        $labFontColors[$x][$y] = json_decode(json_encode($output[$x]->LabDataInfo[$y]->LabFontColor),true);
                        $labName4Queries[$x][$y] = json_decode(json_encode($output[$x]->LabDataInfo[$y]->LabName4Query),true);
                        $specKind4Queries[$x][$y] = json_decode(json_encode($output[$x]->LabDataInfo[$y]->SpecKind4Query),true);

                        $labTimes[$x][$y] = $labTimes[$x][$y][0];
                        $labFullNames[$x][$y] = $labFullNames[$x][$y][0];
                        $labValues[$x][$y] = $labValues[$x][$y][0];
                        $labUnits[$x][$y] = $labUnits[$x][$y][0];
                        $labRefValues[$x][$y] = $labRefValues[$x][$y][0];
                        $labFontColors[$x][$y] = $labFontColors[$x][$y][0];
                        $labName4Queries[$x][$y] = $labName4Queries[$x][$y][0];
                        $specKind4Queries[$x][$y] = $specKind4Queries[$x][$y][0];

                        $labRefValues[$x][$y] = str_replace(";","<br />",$labRefValues[$x][$y]);
                        if(substr_count($labRefValues[$x][$y], "<br />") === 1){
                            $labRefValues[$x][$y] = $labRefValues[$x][$y]."<br />";
                        }
                    }
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
//http://172.18.2.90/WEB/eIntelligentWard/Service.svc?singleWsdl
?>



<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BIS</title>
    <!-- BOOTSTRAP STYLES-->
    <link href="../../../css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="../../../css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLES-->
    <link href="../../../css/main.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link  href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous" rel="stylesheet" />
    <!-- fullcalendar-->
    <script src="../../../js/jquery-1.8.3.js"></script>
</head>

<style>
.operaHealthInfo-content tbody::-webkit-scrollbar {
    display: none;
}

th:nth-child(1), td:nth-child(1) {
    width: 15%;
}

th:nth-child(2), td:nth-child(2) {
    width: 25%;
}

th:nth-child(3), td:nth-child(3) {
    width: 5%;
}

th:nth-child(4), td:nth-child(4) {
    width: 10%;
}

th:nth-child(5), td:nth-child(5) {
    width: 35%;
}

th:nth-child(6), td:nth-child(6) {
    width: 5%;
}

th:nth-child(7), td:nth-child(7) {
    width: 5%;
}
</style>

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
                        <span class="top-header">姓名: <?php echo $name;?></span>
                        <span class="top-header">床號: <?php echo $ward;?></span>
                        <span class="top-header">病歷號:<?php echo $no;?></span>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-12 form-group operaHealthInfo-form">
            <label>
                <!--檢驗報告 : <span>(<span style="color: #0000FF;">藍色：低於參考值、</span><span style="color: #FF0000;">紅色：高於參考值、</span><span style="color: #000000;">黑色：正常值</span>)</span>-->
                檢驗報告 : <span>(<span style="color: #0000FF;">藍色：低於參考值、</span><span style="color: #FF0000;">紅色：高於參考值、</span><span style="color: #000000;">黑色：正常值</span>)</span>
            </label>
            <label>
                類別
                <select id="typelist">
                    <option value="All">全部</option>
                    <?php
                    for($x = 0 ; $x < count($typeNames) ; $x++){
                        echo '<option value="'.$typeNames[$x][0].'">'.$typeNames[$x][0].'</option>';
                    }
                    ?>
                </select>
            </label>
        </div>

        <!-- /. NAV SIDE  -->
        <div id="operaInfo-wrapper">
            <div class="row operaHealthInfo-content">
                <div class="col-md-12">

                    <div class="col-md-12 table-responsive">
                        <table class="table table-striped table-hover table-condensed table-bordered">
                            <thead>
                                <tr>
                                    <th>採檢日期</th>
                                    <th>檢驗項目</th>
                                    <th>檢驗值</th>
                                    <th>單位</th>
                                    <th>參考值</th>
                                    <th>趨勢圖</th>
                                    <!--<th></th>-->
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                for($x = 0 ; $x < count($typeNames) ; $x++){
                                    echo '
                                    <tr class="'.$typeNames[$x][0].' All">
                                    <th colspan="7" class="table-title">
                                    <strong>'.$typeNames[$x][0].'</strong>
                                    </th>
                                    </tr>';
                                    for($y = 0 ; $y < $labDataInfo_len[$x] ; $y++){
                                        echo '
                                        <tr class="'.$typeNames[$x][0].' All">
                                        <td>'.$labTimes[$x][$y].'</td>
                                        <td>'.$labFullNames[$x][$y].'</td>
                                        <td style="color:'.$labFontColors[$x][$y].'">'.$labValues[$x][$y].'</td>
                                        <td>'.$labUnits[$x][$y].'</td>
                                        <td>'.$labRefValues[$x][$y].'</td>
                                        <td><a href="../ExamineChart/ExamineChart.php?X='.$x.'&Y='.$y.'&No='.$no.'&Ward='.$ward.'&Name='.$name.'&LabFullName='.$labFullNames[$x][$y].'&Value='.$labValues[$x][$y].'&LabUnit='.$labUnits[$x][$y].'&LabName4Query='.$labName4Queries[$x][$y].'&SpecKind4Query='.$specKind4Queries[$x][$y].'&LabExecDatetime='.$labTimes[$x][$y].'"> 
                                        <i class="fa fa-bar-chart">
                                        </a></td>
                                        </tr>
                                        ';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>

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
    <script src="../../../js/bootstrap.min.js"></script>
    <script src="../../../js/custom.js"></script>
    <script>
        $("#typelist")
        .change(function () {
            var val = $("#typelist option:selected").val();
            $(".All").hide();
            $("." + val).show();
        })
    </script>
</body>
</html>
