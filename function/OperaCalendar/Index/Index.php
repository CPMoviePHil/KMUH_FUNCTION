<?php
class kmuhSoapClient extends SoapClient {
    function __construct($wsdl, $options) {
        parent::__construct($wsdl, $options);
    }
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
$id = "";
$name = $ward = $no = "";
$thedate = "";
$theinteval = "";
$p_data = "";

$operation_name = $operation_status = $operation_date = array();
$op_name = $op_status = $op_date = array();
$res = array();
$len_data = "";
$p_data = "";
$t_data = array();

$check_operation_name = $check_operation_status = $check_operation_date = $check_operation_time = $check_operation_place= array();
$check_name = $check_status = $check_date = $check_time = $check_place = array();
$len_check_data = "";
$check_data = array();

if(isset($_GET["Id"]) && isset($_GET["Date"])){
    $id = trim($_GET["Id"]);
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
            echo "WEB API error";
        }else{
            if(!empty($data)){
               $thedate= date('Y/m/d', strtotime($_GET["Date"]));
               $today= date('Y/m/d', strtotime(date("Y/m/d")));
               $sdate = new Datetime($_GET["Date"]);
               $edate = new Datetime($today);
               $theinteval = $sdate->diff($edate)->d;
               $temp1_op = $data->OPTPatientInfo;
               $temp2_check = $data->CheckPatientInfo;
               if(isset($data->CheckPatientInfo->CheckPatientInfoData)){
                $p_data = $temp2_check;
                $no = $p_data->CheckPatientInfoData[0]->PatientChartNo;
                $ward = $p_data->CheckPatientInfoData[0]->WardBed;
                $name = $p_data->CheckPatientInfoData[0]->PatientName;

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
            }
            if(isset($data->OPTPatientInfo->OPTPatientInfoData)){

                $p_data = $temp1_op;
                $no = $p_data->OPTPatientInfoData[0]->PatientChartNo;
                $ward = $p_data->OPTPatientInfoData[0]->WardBed;
                $name = $p_data->OPTPatientInfoData[0]->PatientName;
                
                $len_data = count($data->OPTPatientInfo->OPTPatientInfoData);

                for($x = 0 ; $x < $len_data ; $x++){
                    $t_data[$x] = $data->OPTPatientInfo->OPTPatientInfoData[$x];
                    $op_date[$x] = json_decode(json_encode($t_data[$x]->OptDate),true);
                    $op_date[$x] = $op_date[$x][0];
                    $op_name[$x] = json_decode(json_encode($t_data[$x]->OptName),true);
                    $op_name[$x] = $op_name[$x][0];
                    $op_status[$x] = json_decode(json_encode($t_data[$x]->OptStatusDesc),true);
                    $op_status[$x] = $op_status[$x][0];
                }
                for($x = 0 ; $x < $len_data ; $x++){
                    if($thedate===$op_date[$x]){
                        array_push($operation_name,$op_name[$x]);
                        array_push($operation_status,$op_status[$x]);
                        array_push($operation_date,$op_date[$x]);
                    }
                    var_dump($thedate);
                }

            }
        }
    }

}catch(Exception $e){
    echo $e;
}
}else{
    echo "<script>history.back();</script>";
}

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
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    
    <script src="../../../js/jquery-1.10.2.js"></script>
    

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
                        <input id="id" value="<?php echo $no;?>" hidden />
                    </div>

                </div>
            </div>
        </div>

        <!-- /. NAV SIDE  -->
        <div id="operaInfo-wrapper">

            <div class="row operaInfo-content">
                <div class="col-md-12">
                    <div class="col-md-12 table-responsive">
                        <h1 style="font-weight:800;">手術資訊</h1>
                        <table class="table table-striped table-bordered table-hover table-bordered results">
                            <thead class="thead-inverse">
                                <tr>
                                    <th>排刀日期</th>
                                    <th>術式</th>
                                    <th>狀態</th>
                                </tr>
                            </thead>
                            <tbody id="part1">
                                <?php for($x = 0 ; $x < count($operation_date); $x++){
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
                                }?>
                            </tbody>

                        </table>
                        <div id="search-time">
                            <input value="<?php echo $theinteval;?>" id="part1day" hidden />
                            <button type="button" class="btn btn-secondary btn-lg " onclick="lastpart1()">前一日</button>
                            <label id="part1lable" class="text-right"><?php echo $thedate;?></label>
                            <button type="button" class="btn btn-secondary btn-lg " onclick="nextpart1()">後一日</button>
                        </div>

                    </div>

                    <div class="col-md-12 table-responsive">
                        <h1 style="font-weight:800;">檢查資訊</h1>
                        <table class="table table-striped table-bordered table-hover table-bordered results">
                            <thead class="thead-inverse">
                                <tr>
                                    <th>排檢日期</th>
                                    <th>預定時間</th>
                                    <th>排檢內容</th>
                                    <th>地點</th>
                                    <!--<th>注意事項</th>-->
                                    <th>狀態</th>
                                </tr>
                            </thead>
                            <tbody id="part2">
                                <?php
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
                                ?>
                            </tbody>
                        </table>
                        <div id="search-time">
                            <input value="<?php echo $theinteval;?>" id="part2day" hidden />
                            <button type="button" class="btn btn-secondary btn-lg " onclick="lastpart2()">前一日</button>
                            <label id="part2lable" class="text-right"><?php echo $thedate;?> </label>
                            <button type="button" class="btn btn-secondary btn-lg " onclick="nextpart2()">後一日</button>
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
    <script src="../../../js/bootstrap.min.js"></script>


    <script src="../../../js/custom.js"></script>
    <script>

        function lastpart1() {
            var upday = $("#part1day").val();
            var id = $("#id").val();
            if (upday <= -7) {
                alert("已達最大上限");
            } else {
                var s = parseInt(upday) - 1;
                $("#part1day").val(parseInt(s));
                $("#part1").load("OperaInfopart1List/OperaInfopart1List.php", { id: id, day: s }, function () {
                    var datetime = new Date($("#part1lable").text()).addDays(-1);
                    var newDt = datetime.getFullYear() + "/" +
                    ((datetime.getMonth() + 1).toString().length == 1 ? "0" + (datetime.getMonth() + 1) : (datetime.getMonth() + 1))
                    + "/" + (datetime.getDate().toString().length == 1 ? "0" + datetime.getDate() : datetime.getDate());
                    $("#part1lable").text(newDt)
                });
            }
        }

        function nextpart1() {
            var upday = $("#part1day").val();
            var id = $("#id").val();

            if (upday >= 7) {
                alert("已達最大上限");
            } else {
                var s = parseInt(upday) + 1;
                $("#part1day").val(s);

                $("#part1").load("OperaInfopart1List/OperaInfopart1List.php", { id: id, day: s }, function () {
                    var datetime = new Date($("#part1lable").text()).addDays(+1);
                    var newDt = datetime.getFullYear() + "/" +
                    ((datetime.getMonth() + 1).toString().length == 1 ? "0" + (datetime.getMonth() + 1) : (datetime.getMonth() + 1))
                    + "/" + (datetime.getDate().toString().length == 1 ? "0" + datetime.getDate() : datetime.getDate());
                    $("#part1lable").text(newDt)
                });

            }

        }

        function lastpart2() {
            var upday = $("#part2day").val();
            var id = $("#id").val();
            if (upday <= -7) {
                alert("已達最大上限");
            } else {
                var s = parseInt(upday) - 1;console.log(s);
                $("#part2day").val(parseInt(s));
                $("#part2").load("CheckOperaInfopart1List/CheckOperaInfopart1List.php", { id: id, day: s }, function () {
                    var datetime = new Date($("#part2lable").text()).addDays(-1);
                    var newDt = datetime.getFullYear() + "/" +
                    ((datetime.getMonth() + 1).toString().length == 1 ? "0" + (datetime.getMonth() + 1) : (datetime.getMonth() + 1))
                    + "/" + (datetime.getDate().toString().length == 1 ? "0" + datetime.getDate() : datetime.getDate());
                    $("#part2lable").text(newDt)
                });
            }
        }

        function nextpart2() {
            var upday = $("#part2day").val();
            var id = $("#id").val();

            if (upday >= 7) {
                alert("已達最大上限");
            } else {
                var s = parseInt(upday) + 1;
                $("#part2day").val(s);
                console.log(s);
                $("#part2").load("CheckOperaInfopart1List/CheckOperaInfopart1List.php", { id: id, day: s }, function () {
                    var datetime = new Date($("#part2lable").text()).addDays(+1);
                    var newDt = datetime.getFullYear() + "/" +
                    ((datetime.getMonth() + 1).toString().length == 1 ? "0" + (datetime.getMonth() + 1) : (datetime.getMonth() + 1))
                    + "/" + (datetime.getDate().toString().length == 1 ? "0" + datetime.getDate() : datetime.getDate());
                    $("#part2lable").text(newDt)
                });

            }

        }

        Date.prototype.addDays = function (days) {
            var dat = new Date(this.valueOf());
            dat.setDate(dat.getDate() + days);
            return dat;
        }

    </script>
</body>
</html>
