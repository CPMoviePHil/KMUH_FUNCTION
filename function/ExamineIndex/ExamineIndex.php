<?php
class kmuhSoapClient extends SoapClient {
    function __construct($wsdl, $options) {
        parent::__construct($wsdl, $options);
    }
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
$webapialert = false;
$no = $name = $birthday = $ward = "";
if(isset($_GET["id"])){

    $id = trim($_GET["id"]);
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
            $webapialert = true;
        }else{
            if(!empty($data)){
                $no = $data->PatientInfo->PatientChartNo;
                $name = $data->PatientInfo->PatientName;
                $birthday = $data->PatientInfo->PatientBirthday;
                $ward = $data->PatientInfo->WardBed;
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
    <link href="../../css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="../../css/font-awesome.css" rel="stylesheet" />
    <!-- CUSTOM STYLES-->
    <link href="../../css/main.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <!-- fullcalendar-->
    <link href='../../css/assets/css/fullcalendar/fullcalendar.css' rel='stylesheet' />
    <link href='../../css/assets/css/fullcalendar/fullcalendar.print.min.css' rel='stylesheet' media='print' />
    <script src="../../js/jquery-3.3.1.min.js"></script>
    <!-- fullcalendar-->
    <script src='../../css/assets/js/fullcalendar/moment.min.js'></script>
    <script src='../../css/assets/js/fullcalendar/fullcalendar.min.js'></script>
    <script src='../../css/assets/js/fullcalendar/locale-all.js'></script>

    <style>
    .fc-time{
        display:none;
    }
</style>

<script>
    function default_date(){
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1;
        var yyyy = today.getFullYear();
        if(dd<10) {
            dd = '0'+dd;
        }
        if(mm<10) {
            mm = '0'+mm;
        }
        return yyyy + '-' + mm + '-' + dd;
    }
    //console.log(default_date());
    var urlId = $(window.location.href.split("/")).last()[0].replace("ExamineIndex.php?id=", "").replace("ExamineIndex.php?Id=", "");
    var ary = [];
    $(document).ready(function () {
        var initialLocaleCode = 'zh-tw';
            var ToDolistcolor = '#FF2D2D'; //檢驗報告
            var chain = $.Deferred().resolve();
            chain = chain.pipe(function () {
                $('#operacalendar').fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                        //right: 'month,listYear'
                    },
                    defaultDate: default_date(),
                    locale: initialLocaleCode,
                    aspectRatio: 1.35,
                    //contentHeight: 'auto',
                    businessHours: true, // display business hours
                    buttonIcons: false, // show the prev/next text
                    weekNumbers: false,
                    navLinks: false, // can click day/week names to navigate views
                    editable: false,
                    eventLimit: false, // allow "more" link when too many events
                    selectable: true,
                    selectHelper: true,
                    select: function (start, end) {
                        var date = new Date(start._d);
                        var day = date.getFullYear() + "-" +
                        ((date.getMonth() + 1).toString().length == 1 ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1)) + "-" +
                        (date.getDate().toString().length == 1 ? "0" + date.getDate() : date.getDate());
                        divclick(day);
                    },
                    events: {
                        url: 'GETBISMonthExamine/GETBISMonthExamine.php',
                        type: 'POST',
                        data: {
                            No: urlId
                        },
                        success: function (doc, d) {
                            $(".LoadingIndex").fadeOut();
                        },
                        error: function () {
                            alert('資料錯誤');
                        }
                    },
                });
            })

            if ($(Window).width() < 767) {
                $('#operacalendar').fullCalendar('option', 'height', "auto");
            };
        });
    function divclick(val) {
        $.get('CheckExaminehave.php?No=' + urlId + '&Date=' + val, function (d) {
            if (d == true) {
                window.location.href = 'ExamineInfo/ExamineInfo.php?No=' + urlId + '&Date=' + val;
            } else {

            }
        });
    };
</script>

<link href="../../Content/Loading.css" rel="stylesheet" />

</head>

<body>

    <div class="LoadingIndex">
        <div class="sk-fading-circle">
            <div class="sk-circle1 sk-circle"></div>
            <div class="sk-circle2 sk-circle"></div>
            <div class="sk-circle3 sk-circle"></div>
            <div class="sk-circle4 sk-circle"></div>
            <div class="sk-circle5 sk-circle"></div>
            <div class="sk-circle6 sk-circle"></div>
            <div class="sk-circle7 sk-circle"></div>
            <div class="sk-circle8 sk-circle"></div>
            <div class="sk-circle9 sk-circle"></div>
            <div class="sk-circle10 sk-circle"></div>
            <div class="sk-circle11 sk-circle"></div>
            <div class="sk-circle12 sk-circle"></div>
        </div>
        <div class="sk-fading-text"><h3>讀取中<br />請稍後</h3></div>
    </div>

    <div id="wrapper">
        <div class="navbar navbar-inverse navbar-fixed-top operaInfo-navbar">
            <div class="adjust-nav">
                <div class="navbar-header">
                    <div class="operaInfo-top col-md-12">
                        <span class="top-header">姓名: <?php echo $name;?></span>
                        <span class="top-header">床號: <?php echo $ward;?></span>
                        <span class="top-header">病歷號:<?php echo $no;?></span>
                        <input id="id" hidden />
                    </div>

                </div>
            </div>
        </div>

        <!-- /. NAV SIDE  -->
        <div id="operaInfo-wrapper">
            <div class="row operaCalendar-content">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="operaCalendar-info">
                            <span>已完成檢驗報告</span>
                            <div class="operaCalendar-info-3"></div>
                        </div>
                        <br />
                        <div class="operaCalendar-info">
                            <span>以下僅提供常規檢驗項目之報告，特殊檢驗項目結果須由醫師為你說明。</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div id='operacalendar'></div>
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

    <?php
    if($webapialert){
        echo
        '<script type="text/javascript">
        var message = function () {
            alert("WEB API 錯誤");
        };
        setTimeout(message, 500);
        </script>';
    }
    ?>
    <!-- /. WRAPPER  -->
    <script src="../../js/bootstrap.min.js"></script>
    <script src="../../js/custom.js"></script>

</body>
</html>
