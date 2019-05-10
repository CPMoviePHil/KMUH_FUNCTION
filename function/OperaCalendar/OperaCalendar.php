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
$today= date('Y-m-d', strtotime(date("Y-m-d")));
$p_data = "";
if(isset($_GET["id"])){
    $id = trim($_GET["id"]);
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
            echo "<script>history.back();</script>";
        }else{
            if(!empty($data)){
               $temp1_op = $data->OPTPatientInfo;
               $temp2_check = $data->CheckPatientInfo;
               if(empty($temp1_op)){
                $p_data = $temp2_check;
                $no = $p_data->CheckPatientInfoData[0]->PatientChartNo;
                $ward = $p_data->CheckPatientInfoData[0]->WardBed;
                $name = $p_data->CheckPatientInfoData[0]->PatientName; 
               }else{
                $p_data = $temp1_op;
                $no = $p_data->OPTPatientInfoData[0]->PatientChartNo;
                $ward = $p_data->OPTPatientInfoData[0]->WardBed;
                $name = $p_data->OPTPatientInfoData[0]->PatientName;
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
    
    <script src="../../js/jquery-1.8.3.js"></script>
    
    <!-- fullcalendar-->
    <script src='../../css/assets/js/fullcalendar/moment.min.js'></script>
    <script src='../../css/assets/js/fullcalendar/fullcalendar.min.js'></script>
    <script src='../../css/assets/js/fullcalendar/locale-all.js'></script>
    
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
        var urlId = $(window.location.href.split("/")).last()[0].replace("OperaCalendar.php?id=","");
        var ary = [];
        $(document).ready(function () {
            var initialLocaleCode = 'zh-tw';
            var OPTcolor = '#0000FF'; //手術
            var Checkcolor = '#33FF33'; //檢查
            //var ToDolistcolor = '#FF2D2D'; //檢驗報告
            var chain = $.Deferred().resolve();
            chain = chain.pipe(function () {
                return $.get("GETBISAllPatient/GETBISAllPatient.php",{ No:urlId}, function(data){
                    data = JSON.parse(data);
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
                            var day = "_"+date.getFullYear()+"-"+
                            ((date.getMonth() + 1).toString().length == 1?"0"+(date.getMonth() + 1):(date.getMonth() + 1))+"-"+
                            (date.getDate().toString().length == 1 ? "0" + date.getDate() : date.getDate()) + "_";
                            var check = $("td[name='" + day + "']").length;
                            if(check!=0)
                                divclick(day);

                        },
                        events: data.data,
                        eventRender: function (event, element) {
                            if (event.title != null) {
                                var i = 1;
                                var day = event.start._i;
                                element.attr("name", day);
                                element.addClass("_" + day + "_");
                            } else {
                                var day = event.start._i;
                                element.attr("name", "_" + day + "_");
                            }
                        },
                    });
                })
            });
            if ($(Window).width() < 767) {
                $('#operacalendar').fullCalendar('option', 'height', "auto");
            };
        });

        function divclick(val) {
            var day = val.replace("_", '').replace("_", '');
            window.location.href = 'Index/Index.php?Id=' + urlId + '&type=3&Date=' + day;
        };
    </script>

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
            <div class="row operaCalendar-content">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="operaCalendar-info">
                            <span>手術</span>
                            <div class="operaCalendar-info-1"></div>
                        </div>
                        <div class="operaCalendar-info">
                            <span>檢查</span>
                            <div class="operaCalendar-info-2"></div>
                        </div>
                        
                    </div>    

                    <div class="col-md-6">
                        <div id='operacalendar'></div>
                    </div>
                    

                    <div class="col-md-6 ">
                        <h2>愛的叮嚀</h2>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover table-bordered results">
                                <thead class="thead-inverse">
                                    <tr>
                                        <th width="60%">叮嚀</th>
                                        <th width="20%">時間(起)</th>
                                        <th width="20%">時間(止)</th>
                                    </tr>
                                </thead>
                                <tbody id="part2">

                                </tbody>
                            </table>
                        </div>
                        <div id="search-time">
                            <input value="0" id="part2day" hidden />
                            <button type="button" class="btn btn-default btn-lg " onclick="lastpart2()">前一日</button>
                            <label id="part2lable" class="text-right"><?php echo $today;?></label>
                            <button type="button" class="btn btn-default btn-lg "onclick="nextpart2()">後一日</button>
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

    <script>

        function lastpart2()
        {
            var upday = $("#part2day").val();
            var id = $("#id").val();
            if (upday <= -7) {
                alert("已達最大上限");
            } else {
                var s = parseInt(upday) - 1;
                $("#part2day").val(parseInt(s));
                $("#part2").load("operaCalendarPart2List/operaCalendarPart2List.php", { id: id, day: s }, function () {
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
            }
            else {
                var s = parseInt(upday) + 1;
                $("#part2day").val(s);
                $("#part2").load("operaCalendarPart2List/operaCalendarPart2List.php", { id: id, day: s }, function () {
                    var datetime = new Date($("#part2lable").text()).addDays(+1);
                    var newDt = datetime.getFullYear() + "/" +
                    ((datetime.getMonth() + 1).toString().length == 1 ? "0" + (datetime.getMonth() + 1) : (datetime.getMonth() + 1))
                    + "/" + (datetime.getDate().toString().length == 1 ? "0" + datetime.getDate() : datetime.getDate());
                    $("#part2lable").text(newDt)
                });
            }
        };

        Date.prototype.addDays = function (days) {
            var dat = new Date(this.valueOf());
            dat.setDate(dat.getDate() + days);
            return dat;
        }
        function myFunction(event) {
            var val = $(event).attr('id');
            var r = confirm("是否刪除此資料？");
            if (r == true) {
                $.post("operaCalendarPart2Delete/operaCalendarPart2Delete.php", new { Id: val }, function () {

                })
            } else {

            }
        }
    </script>

</body>
</html>
