<?php
$no = $name = $ward = $birthday = "";
$labFullName = $labValue = $labUnit = "";
$labName4Query = $specKind4Query = $labExecDatetime = "";
$dateFormat = "";
$startOfMonth = $endOfMonth = "";
$labRefValue = array();
class kmuhSoapClient extends SoapClient {
    function __construct($wsdl, $options) {
        parent::__construct($wsdl, $options);
    }
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
if(isset($_GET["X"]) && isset($_GET["Y"]) && isset($_GET["No"]) && isset($_GET["Ward"]) && isset($_GET["Name"]) && isset($_GET["LabFullName"]) && isset($_GET["Value"]) && isset($_GET["LabUnit"]) && isset($_GET["LabName4Query"]) && isset($_GET["SpecKind4Query"]) && isset($_GET["LabExecDatetime"])){

    $no = trim($_GET["No"]);
    $name = trim($_GET["Name"]);
    $ward = trim($_GET["Ward"]);

    $labFullName = trim($_GET["LabFullName"]);
    $labValue = trim($_GET["Value"]);
    $labUnit = trim($_GET["LabUnit"]);

    $labName4Query = trim($_GET["LabName4Query"]);
    $specKind4Query = trim($_GET["SpecKind4Query"]);
    $labExecDatetime = trim($_GET["LabExecDatetime"]);

    $d = $labExecDatetime;
    date_default_timezone_set('UTC');
    $dateFormat = date('Y/m/d A H:i:s', strtotime($d));
    if(strpos($dateFormat,"AM") !== false){
        $dateFormat = str_replace("AM", "上午", $dateFormat);
    }
    if(strpos($dateFormat,"PM") !== false){
        $dateFormat = str_replace("PM","下午", $dateFormat);
    }
    $startOfMonth = date('Y-m-01', strtotime($labExecDatetime));
    $endOfMonth = date('Y-m-t', strtotime($labExecDatetime));



    $patientid = array("pChartNo"=>$no);
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
        $res = $client ->__getLastResponse();
        $wcfservice->GetAllLabReportInfoResult;
        $xml = $wcfservice;
        $xml = ( json_decode(json_encode($wcfservice), True));
        $data = simplexml_load_string($xml["GetAllLabReportInfoResult"]);
        if($xml["GetAllLabReportInfoResult"] === "<LabReportInfo />"){
            echo "<script>alert('web api error!')</script>";
        }else{
            if(!empty($data)){

                $len = count($data->LabReport->LabExecDate);
                for($x = 0 ; $x < $len ; $x++){
                    $dates[$x] = json_decode(json_encode($data->LabReport->LabExecDate[$x]["LabDate"]), True);

                    if($dates[$x][0] === date('Y/m/d', strtotime($labExecDatetime))){
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

                        $labRefValues[$x][$y] = json_decode(json_encode($output[$x]->LabDataInfo[$y]->LabRefValue),true);
                       
                        $labRefValues[$x][$y] = $labRefValues[$x][$y][0];

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
    <!-- fullcalendar-->
    <script src="../../../js/jquery-3.1.0.min.js"></script>
    
    <link href="../../../css/d3_timeseries.css" rel="stylesheet" />
    <style>
    /* set the CSS */
        /*.line {
            fill: none;
            stroke: steelblue;
            stroke-width: 2px;
            }*/

            div.tooltip {
                position: absolute;
                text-align: center;
                width: 100px;
                height: 28px;
                padding: 2px;
                font: 12px sans-serif;
                background: lightsteelblue;
                border: 0px;
                border-radius: 8px;
                pointer-events: none;
            }

            .tooltips {
                position: absolute;
                text-align: center;
            /*width:80px;
            height: 28px;*/
            padding: 2px;
            font: 12px sans-serif;
            background: lightsteelblue;
            border: 0px;
            border-radius: 8px;
            pointer-events: none;
        }

        .area {
            fill: none;
            stroke: steelblue;
            clip-path: url(#clip);
        }

        .zoom {
            fill: none;
            pointer-events: all;
            -webkit-overflow-scrolling: touch;
        }
    </style>
    <link href="../../../Content/Loading.css" rel="stylesheet" />
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
                    </div>

                </div>
            </div>
        </div>

        <!-- /. NAV SIDE  -->
        <div id="operaInfo-wrapper">
            <div class="row operaInfo-content">
                <div class="col-md-12">

                    <div class="form-group form-Health-chart" style="margin-bottom: 0px;">
                        <label style="color:#0066CC"><?php echo $labFullName;?></label>&nbsp;
                        <label>起始日:<input id="sdate" type="Date" value="<?php echo $startOfMonth;?>"></label>
                        <label>截止日:<input id="edate" type="Date" value="<?php echo $endOfMonth;?>"></label>
                        <button type="button" class="btn btn-secondary" id="Search">查詢</button>
                        
                    </div>
                    <div class="form-group form-Health-chart">
                        <label>檢驗報告 <span>(<span style="color: #0000FF;">藍色：低於參考值、</span><span style="color: #FF0000;">紅色：高於參考值、</span><span style="color: #000000;">黑色：正常值</span>)</span></label>
                    </div>
                    <div class="form-group">

                        <div id="chart">

                        </div>
                        <div class="form-group form-Health-chart">
                            <button type="button" class="btn btn-secondary" onclick="lastclick()">上一筆</button>
                            <button type="button" class="btn btn-secondary" onclick="nextclick()">下一筆</button>
                            <label id="labeltext"><?php echo $labExecDatetime;?> </label>
                            <label id="labelvalue">檢驗值:<?php echo $labValue;?></label>
                            <label><?php echo $labUnit;?></label>
                        </div>
                        <input style="display:none;" id="LabExecDatetime" value="<?php echo $dateFormat;?>" />
                        <input style="display:none;" id="tempvalue" />
                        <div class="form-group form-Health-chart">
                            <label>
                                參考值:
                                <?php print_r($labRefValues[$_GET["X"]][$_GET["Y"]]);?>
                            </label>
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
    <script src="../../../js/d3v4.js"></script>
    <script src="../../../js/bootstrap.min.js"></script>
    <script src="../../../js/custom.js"></script>
    <script src="../../../js/moment.js"></script>
    <script src="../../../js/d3_timeseries.js"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
    </script>

    <script>

        Date.prototype.addDays = function (days) {
            var dat = new Date(this.valueOf());
            dat.setDate(dat.getDate() + days);
            return dat;
        }
        var LabExecDatetime = $("#LabExecDatetime").val();
        var sdate = $("#sdate").val();
        var edate = $("#edate").val();
    //時間範圍測試
    var theDate1 = new Date(sdate.split("-")[0], sdate.split("-")[1]-1, sdate.split("-")[2]);
    var theDate2 = new Date(edate.split("-")[0], edate.split("-")[1]-1, edate.split("-")[2]);
    var dates = [theDate1, theDate2];

    var tempid = null;
    var PatientChartNo = urlParams.get("No");
    var LabName4Query = urlParams.get("LabName4Query");
    var SpecKind4Query = urlParams.get("SpecKind4Query");
    var widths = $(window).width();
    widths = (widths >= 900 ? widths-100 : widths);

    var x, y, s;

// set the dimensions and margins of the graph
var margin = {top: 40, right: 20, bottom: 100, left: 30},
margin2 = { top: 430, right: 20, bottom: 30, left: 40 },
height = 500 - margin.top - margin.bottom,
height2 = 500 - margin2.top - margin2.bottom,
width = widths - margin.left - margin.right;

// parse the date / time
var parseTime = d3.timeParse("%Y-%m-%d %H:%M");
var formatTime = d3.timeFormat("%e %B");

// set the ranges
var x = d3.scaleTime().rangeRound([0, width]),
x2 = d3.scaleTime().rangeRound([0, width]),
y = d3.scaleLinear().range([height, 0]),
y2 = d3.scaleLinear().range([height2, 0]);

//var x = d3.scaleTime().rangeRound([0, widths]);
//var y = d3.scaleLinear().rangeRound([height, 0]);

var xAxis = d3.axisBottom(x)
.tickFormat(d3.timeFormat("%Y-%m-%d %H:%M")),
xAxis2 = d3.axisBottom(x)
.tickFormat(d3.timeFormat("%Y-%m-%d")),
yAxis = d3.axisLeft(y);

var area = d3.area()
.curve(d3.curveMonotoneX)
.x(function (d) { return x(d.date); })
.y0(height)
.y1(function (d) {
    return y(parseFloat(d.values) * 1.1);
});

var valueline = d3.line()
.x(function(d) { return x(d.date); })
.y(function (d) { return y(parseFloat(d.values)); });

var div = d3.select("body").append("div")
.attr("class", "tooltip")
.style("opacity", .1);

$.get("ExamineJson/ExamineJson.php", { PatientChartNo: PatientChartNo, LabName4Query: LabName4Query, SpecKind4Query: SpecKind4Query, LabExecDatetime: LabExecDatetime, sdate: sdate, edate: edate }, function (data) {
    data = JSON.parse(data);
    var i = 1;
    $.map(data, function (d, k) {
        if (d.bools == true) {
            $("#labeltext").html(d.date);
            $("#labelvalue").html("檢驗值:" + d.values);
            tempid = d.id;
        }
        d.date = parseTime(d.date);
        if (i == data.length) {
            $.map(dates, function (d) {
                d.date = parseTime(d.date);
            });
            var zoom = d3.zoom()
            .scaleExtent([1, Infinity])
            .translateExtent([[0, 0], [width, height]])
            .extent([[0, 0], [width, height]])
            .on("zoom", zoomed);

            var svg = d3.select("#chart").append("svg")
            .attr("width", widths + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .attr("class", "zoom")
            .call(zoom);

            //x.domain(d3.extent(data, function (d) { return d.date; }));
            x.domain(d3.extent(dates, function (d) {
                return d;
            }));

            y.domain([0, d3.max(data, function (d) {
                return parseFloat(d.values) * 1.1;
            })]);

            x2.domain(x.domain());
            y2.domain(y.domain());

            svg.append("defs").append("clipPath")
            .attr("id", "clip")
            .append("rect")
            .attr("width", width)
            .attr("height", height)

            var focus = svg.append("g")
            .attr("class", "focus")
            .attr("transform", "translate(" + (margin.left+10) + "," + margin.top + ")");

            focus.append("path")
            .datum(data)
            .attr("class", "area")
            .attr("d", valueline);

            focus.selectAll("dot")
            .data(data)
            .enter().append("circle")
            .attr("class", "circles")
            .attr("r", function (d) {
                if (d.bools == true) {
                    return 10
                } else {
                    return 5
                }
            })
            .attr("cx", function (d) { return x(d.date); })
            .attr("cy", function (d) {
             return y(parseFloat(d.values));
         })
            .attr("id", function (d) { return "circle_" + d.id })
            .style("fill", function (d) { return d.color })
            .attr("value", function (d) { return d.AllValue })
            .on("mouseover", function (d) {
              var temp = $(d3.event.target);

              d3.select("#circle_" + tempid)
              .transition()
              .duration(500)
              .attr("r", 5)

              tempid = (temp.attr("id")).split('_')[1];
              var val = $("#circle_" + tempid).attr('value').split(';');

              $("#labeltext").html(val[0]);
              $("#labelvalue").html("檢驗值:" + val[1]);

              d3.select("#circle_" + tempid)
              .transition()
              .duration(500)
              .attr("r", 9)
                      //.style("fill", "#005AB5");
                  })

            focus.append("g")
            .attr("class", "axis axis--x")
            .attr("transform", "translate(0," + height + ")")
            .style("font-size","16px")
            .call(xAxis2)
            .selectAll(".axis--x .tick text")
            .style("text-anchor", "end")
            .attr("dx", "-.8em")
            .attr("dy", ".15em")
            .attr("transform", "rotate(-65)");

            focus.append("g")
            .attr("class", "axis axis--y")
            .call(yAxis);

            focus.append('g')
            .classed('labels-group', true)
            .selectAll('foreignObject')
            .data(data)
            .enter()
            .append('foreignObject')
            .attr("class", "tooltips Alltop")
            　.attr("width", "50")
            .attr("height", "20")
            .attr('x', function (d) {
              return x(d.date)-25;
          })
            .attr('y', function (d) {
              return y(parseFloat(d.values))-25;
          })
            .html(function (d) {
              return '<div class"style-me"><p>' + +d.values + '</p></div>'
          });

            //svg.append("rect")
            //    .attr("class", "zoom")
            //    .attr("width", width)
            //    .attr("height", height)
            //    .attr("transform", "translate(" + margin.left + "," + margin.top + ")")

            function zoomed() {
                if (d3.event.sourceEvent && d3.event.sourceEvent.type === "brush") return; // ignore zoom-by-brush
                var t = d3.event.transform;
                var Date_C = new Date(t.rescaleX(x2).domain()[1] - t.rescaleX(x2).domain()[0]);
                x.domain(t.rescaleX(x2).domain());
                if (Math.floor(Date_C.getTime() / 3600000) >= 168) {
                    focus.select(".axis--x")
                    .call(xAxis2)
                    .selectAll(".axis--x .tick text")
                    .style("text-anchor", "end")
                    .attr("dx", "-.8em")
                    .attr("dy", ".15em")
                    .attr("transform", "rotate(-65)");
                } else {
                    focus.select(".axis--x")
                    .call(xAxis)
                    .selectAll(".axis--x .tick text")
                    .style("text-anchor", "end")
                    .attr("dx", "-.8em")
                    .attr("dy", ".15em")
                    .attr("transform", "rotate(-65)");
                }
                //console.log("兩個時間差距為" + Math.floor(Date_C.getTime() / 3600000) + "小時 " + Date_C.getUTCMinutes() + "分 " + Date_C.getUTCSeconds() + "秒");

                focus.select(".area").attr("d", valueline);
                focus.selectAll(".circles")
                .attr("cx", function (d) {return x( d.date);})
                .attr("cy", function (d) {
                   return y(parseFloat(d.values));
               });

                focus.selectAll("foreignObject")
                .attr('x', function (d) { return x(d.date) - 25; })
                .attr('y', function (d) {
                   return y(parseFloat(d.values)) - 25;
               });

            }
            $(".LoadingIndex").fadeOut();
        }
        i++;
    });
})

function getFormattedDate(date) {
    var year = date.getFullYear();

    var month = (1 + date.getMonth()).toString();
    month = month.length > 1 ? month : '0' + month;

    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;

    return year + '/' + month + '/' + day;
}

$("#Search").on('click', function () {
    $(".LoadingIndex").fadeIn();
    var sdate = $("#sdate").val();
    var edate = $("#edate").val();
    var theDate1 = new Date(sdate.split("-")[0], sdate.split("-")[1] - 1, sdate.split("-")[2]);
    var theDate2 = new Date(edate.split("-")[0], edate.split("-")[1] - 1, edate.split("-")[2]);
    dates = [theDate1, theDate2];

    $("#chart").html(" ");
    $("#labelvalue").html(" ");
    $.get("ExamineJson/ExamineJson.php", { PatientChartNo: PatientChartNo, LabName4Query: LabName4Query, SpecKind4Query: SpecKind4Query, LabExecDatetime: LabExecDatetime , sdate: sdate, edate: edate }, function (data) {
        var i = 1;
        data = JSON.parse(data);
        $.map(data, function (d, k) {
            if (d.bools == true) {
                $("#labeltext").html(d.date);
                $("#labelvalue").html("檢驗值:" + d.values);
                tempid = d.id; 
            }
            d.date = parseTime(d.date);
            if (i == data.length) {
                $.map(dates, function (d) {
                    d.date = parseTime(d.date);
                });
                var zoom = d3.zoom()
                .scaleExtent([1, Infinity])
                .translateExtent([[0, 0], [width, height]])
                .extent([[0, 0], [width, height]])
                .on("zoom", zoomed);

                var svg = d3.select("#chart").append("svg")
                .attr("width", widths + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom + 100)
                .attr("class", "zoom")
                .call(zoom);

                //x.domain(d3.extent(data, function (d) { return d.date; }));
                x.domain(d3.extent(dates, function (d) { return d; }));

                y.domain([0, d3.max(data, function (d) {
                    return parseFloat(d.values) * 1.1;
                })]);

                x2.domain(x.domain());
                y2.domain(y.domain());

                svg.append("defs").append("clipPath")
                .attr("id", "clip")
                .append("rect")
                .attr("width", width)
                .attr("height", height)

                var focus = svg.append("g")
                .attr("class", "focus")
                .attr("transform", "translate(" + (margin.left+10) + "," + margin.top + ")");

                focus.append("path")
                .datum(data)
                .attr("class", "area")
                .attr("d", valueline);

                focus.selectAll("dot")
                .data(data)
                .enter().append("circle")
                .attr("class", "circles")
                .attr("r", function (d) {
                 if (d.bools == true) {
                     return 10
                 } else {
                     return 5
                 }
             })
                .attr("cx", function (d) { return x(d.date); })
                .attr("cy", function (d) {
                 return y(parseFloat(d.values) * 1.1);
             })
                .attr("id", function (d) { return "circle_" + d.id })
                .style("fill", function (d) { return d.color })
                .attr("value", function (d) { return d.AllValue })
                .on("mouseover", function (d) {
                 var temp = $(d3.event.target);

                 d3.select("#circle_" + tempid)
                 .transition()
                 .duration(500)
                 .attr("r", 5)

                 tempid = (temp.attr("id")).split('_')[1];
                 var val = $("#circle_" + tempid).attr('value').split(';');

                 $("#labeltext").html(val[0]);
                 $("#labelvalue").html("檢驗值:" + val[1]);

                 d3.select("#circle_" + tempid)
                 .transition()
                 .duration(500)
                 .attr("r", 9)
                       //.style("fill", "#005AB5");
                   })

                focus.append("g")
                .attr("class", "axis axis--x")
                .attr("transform", "translate(0," + height + ")")
                .style("font-size", "16px")
                .call(xAxis2)
                .selectAll(".axis--x .tick text")
                .style("text-anchor", "end")
                .attr("dx", "-.8em")
                .attr("dy", ".15em")
                .attr("transform", "rotate(-65)");

                focus.append("g")
                .attr("class", "axis axis--y")
                .call(yAxis);

                focus.append('g')
                .classed('labels-group', true)
                .selectAll('foreignObject')
                .data(data)
                .enter()
                .append('foreignObject')
                .attr("class", "tooltips Alltop")
                .attr("width", "50")
                .attr("height", "20")
                .attr('x', function (d) {
                   return x(d.date) - 25;
               })
                .attr('y', function (d) {
                   return y(parseFloat(d.values) * 1.1) - 25;
               })
                .html(function (d) {
                   return '<div class"style-me"><p>' + +d.values + '</p></div>'
               });

                //svg.append("rect")
                //    .attr("class", "zoom")
                //    .attr("width", width)
                //    .attr("height", height)
                //    .attr("transform", "translate(" + margin.left + "," + margin.top + ")")

                function zoomed() {
                    if (d3.event.sourceEvent && d3.event.sourceEvent.type === "brush") return; // ignore zoom-by-brush
                    var t = d3.event.transform;
                    var Date_C = new Date(t.rescaleX(x2).domain()[1] - t.rescaleX(x2).domain()[0]);
                    x.domain(t.rescaleX(x2).domain());
                    if (Math.floor(Date_C.getTime() / 3600000) >= 168) {
                        focus.select(".axis--x")
                        .call(xAxis2)
                        .selectAll(".axis--x .tick text")
                        .style("text-anchor", "end")
                        .attr("dx", "-.8em")
                        .attr("dy", ".15em")
                        .attr("transform", "rotate(-65)");
                    } else {
                        focus.select(".axis--x")
                        .call(xAxis)
                        .selectAll(".axis--x .tick text")
                        .style("text-anchor", "end")
                        .attr("dx", "-.8em")
                        .attr("dy", ".15em")
                        .attr("transform", "rotate(-65)");
                    }
                    //console.log("兩個時間差距為" + Math.floor(Date_C.getTime() / 3600000) + "小時 " + Date_C.getUTCMinutes() + "分 " + Date_C.getUTCSeconds() + "秒");

                    focus.select(".area").attr("d", valueline);
                    focus.selectAll(".circles")
                    .attr("cx", function (d) { return x(d.date); })
                    .attr("cy", function (d) {
                       return y(parseFloat(d.values) * 1.1);
                   });

                    focus.selectAll("foreignObject")
                    .attr('x', function (d) { return x(d.date) - 25; })
                    .attr('y', function (d) {
                       return y(parseFloat(d.values) * 1.1) - 25;
                   });

                }
                $(".LoadingIndex").fadeOut();
            }
            i++;
        });
});
});

function lastclick() {
    var myself = $("#circle_" + (tempid)).attr('value');
    var val = $("#circle_" + (tempid - 1)).attr('value');
    if (myself != undefined && val != undefined) {
        if (myself != undefined) {
            var temp = myself.split(';');

            d3.select("#circle_" + tempid)
            .transition()
            .duration(500)
            .attr("r", 5)
            .style("fill", temp[2]);
        }
        if (val != undefined) {
            var temp = val.split(';');
            $("#labeltext").html(temp[0]);
            $("#labelvalue").html("檢驗值:" + temp[1]);

            tempid = tempid - 1;

            d3.select("#circle_" + tempid)
            .transition()
            .duration(500)
            .attr("r", 9)
            //.style("fill", "#005AB5");
        }
    }
}

function nextclick() {
    tempid = parseInt(tempid);
    var myself = $("#circle_" + (tempid)).attr('value');
    var val = $("#circle_" + (tempid + 1)).attr('value');
    if (myself != undefined && val != undefined) {
        if (myself != undefined) {
            var temp = myself.split(';');

            d3.select("#circle_" + tempid)
            .transition()
            .duration(500)
            .attr("r", 5)
            .style("fill", temp[2]);
        }

        if (val != undefined) {

            tempid = tempid + 1;

            var temp = val.split(';');
            $("#labeltext").html(temp[0]);
            $("#labelvalue").html("檢驗值:" + temp[1]);

            d3.select("#circle_" + tempid)
            .transition()
            .duration(500)
            .attr("r", 9)
            //.style("fill", "#005AB5");
        }

    }
}

//$(function () {
//    $('html').keydown(function (e) {
//        if (e.keyCode == 37) { // left
//            lastclick();
//        }
//        else if (e.keyCode == 39) { // right
//            nextclick();
//        }
//    });
//});

</script>


</body>
</html>