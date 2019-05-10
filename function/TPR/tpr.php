<?php
//GetTPRData
class kmuhSoapClient extends SoapClient {
    function __construct($wsdl, $options) {
        parent::__construct($wsdl, $options);
    }
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
$name = $no = $ward = "";
$sdate = $edate = "";

if(isset($_GET["No"])){
    $pChartNo = trim($_GET["No"]);
    date_default_timezone_set('UTC');
    $sDate = date('c', strtotime("-3 days"));
    date_default_timezone_set('UTC');
    $eDate = date('c', strtotime("now"));
    $patient = array(
        "pChartNo"=>$pChartNo,
        "pTPRType"=>"T",
        "pBeginDate"=>$sDate,
        "pEndDate"=>$eDate
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
        $wcfservice = $client->GetTPRData($patient);
        $xml = $wcfservice;
        $xml = ( json_decode(json_encode($wcfservice), True));
        $data = simplexml_load_string($xml["GetTPRDataResult"]);
        if(empty($data)){
            echo "<script>history.back();</script>";
        }
        else{
            $name = $data->PatientInfo->PatientName;
            $no = $data->PatientInfo->PatientChartNo;
            $ward = $data->PatientInfo->WardBed;
            $sdate = date('Y-m-d',strtotime("-3 days"));
            $edate = date('Y-m-d', strtotime(date("Y-m-d")));

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
    <link href="../../css/font-open-sans.css" rel="stylesheet" />
    <link  href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous" rel="stylesheet" />
    <!-- fullcalendar-->
    <script src="../../js/jquery-3.1.0.min.js"></script>
    
    <link href="../../css/d3_timeseries.css" rel="stylesheet" />
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

        .area2 {
            fill: none;
            stroke: steelblue;
            clip-path: url(#clip2);
        }
        /*.circles{
            clip-path: url(#clip);
            }*/
            .zoom {
                fill: none;
                pointer-events: all;
                -webkit-overflow-scrolling: touch;
            }

            .triangle-up {
                width: 0;
                height: 0;
                border-left: 50px solid transparent;
                border-right: 50px solid transparent;
                border-bottom: 100px solid red;
            }

            .triangle-down {
                width: 0;
                height: 0;
                border-left: 50px solid transparent;
                border-right: 50px solid transparent;
                border-top: 100px solid red;
            }

            .grid line {
                stroke: lightgrey;
                stroke-opacity: 0.7;
                shape-rendering: crispEdges;
            }

            .grid path {
                stroke-width: 0;
            }

            .touchclick {
                background-color: rgba(255, 188, 120, 0.66);
            }


            .tpr-content tbody::-webkit-scrollbar {
                display: none;
            }

            .tpr-table1 th:nth-child(1), .tpr-table1 td:nth-child(1) {
                width: 40%;
            }

            .tpr-table1 th:nth-child(2), .tpr-table1 td:nth-child(2) {
                width: 30%;
            }

            .tpr-table1 th:nth-child(3), .tpr-table1 td:nth-child(3) {
                width: 30%;
            }

            .tpr-table2 th:nth-child(1), .tpr-table2 td:nth-child(1) {
                width: 25%;
            }

            .tpr-table2 th:nth-child(2), .tpr-table2 td:nth-child(2) {
                width: 25%;
            }

            .tpr-table2 th:nth-child(3), .tpr-table2 td:nth-child(3) {
                width: 25%;
            }

            .tpr-table2 th:nth-child(4), .tpr-table2 td:nth-child(4) {
                width: 25%;
            }
        </style>
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
                            <span class="top-header">病歷: <?php echo $no;?></span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- /. NAV SIDE  -->
            <div id="operaInfo-wrapper">
                <div class="row tpr-content">

                    <div class="col-md-12">

                        <div class="col-md-12 form-group tpr_header">
                            <br /> 
                            <div class="tpr_topmenu btn-group">

                                <a class="btn style1 active" onclick="Onclickpr('T',null,null)" name="T">
                                    <strong>體溫(T)</strong>
                                </a>
                                <a class="btn style2" onclick="Onclickpr('P',null,null)" name="P">
                                    <strong>脈搏(P)</strong>
                                </a>
                                <a class="btn style3" onclick="Onclickpr('R',null,null)" name="R">
                                    <strong>呼吸(R)</strong>
                                </a>
                                <a class="btn style4" onclick="Onclickprblod('BP',null,null)" name="BP">
                                    <strong>血壓(BP)</strong>
                                </a>
                                <a class="btn style5" onclick="Onclickpr('SPO2',null,null)" name="SPO2">
                                    <strong>血氧(SPO2)</strong>
                                </a>

                            </div>

                        </div>

                        <div class="col-md-12 form-group form-Health-chart">
                            <br /> 
                            <label>起始日:<input id="sdate" type="Date" value="<?php echo $sdate;?>"></label>
                            <label>截止日:<input id="edate" type="Date" value="<?php echo $edate;?>"></label>
                            <button type="button" class="btn btn-secondary" id="Search">查詢</button>
                        </div>
                        <div class="col-md-12 table-responsive tpr-table1" id="table1">

                            <table class="table table-hover table-condensed table-bordered">
                                <thead>
                                    <tr>
                                        <th width="25%" id="unittext">體溫</th>
                                        <th width="25%">數值</th>
                                        <th width="25%">單位</th>
                                    </tr>
                                </thead>
                                <tbody id="tablepart"></tbody>
                            </table>

                        </div>
                        <div class="col-md-12 table-responsive tpr-table2" id="table2" style="display:none;">

                            <table class="table table-hover table-condensed table-bordered">
                                <thead>
                                    <tr>
                                        <th width="25%">日期</th>
                                        <th width="25%">收縮壓(▲)</th>
                                        <th width="25%">舒張壓(▼)</th>
                                        <th width="25%">單位</th>
                                    </tr>
                                </thead>
                                <tbody id="BP1"></tbody>
                            </table>
                        </div>
                        <div class="col-md-12 form-group">
                            <div id="chart">
                            </div>


                            <input style="display:none;" id="tempvalue" />
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
        <script src="../../js/d3v4.js"></script>
        <script src="../../js/bootstrap.min.js"></script>
        <script src="../../js/custom.js"></script>
        <script>
            jQuery.fn.scrollTo = function(elem, speed) {
                $(this).animate({
                    scrollTop:  $(this).scrollTop() - $(this).offset().top + $(elem).offset().top
                }, speed == undefined ? 1000 : speed);
                return this;
            };
            Date.prototype.addDays = function (days) {
                var dat = new Date(this.valueOf());
                dat.setDate(dat.getDate() + days);
                return dat;
            }
            function formatDate(date) {
                var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

                if (month.length < 2) month = '0' + month;
                if (day.length < 2) day = '0' + day;

                return [year, month, day].join('-');
            }
            var nows = new Date(getdate());
            var sdate = $("#sdate").val();
            var edate = $("#edate").val();

            var PatientChartNo = '30291857';

            var tempid = null;
            var tempid2 = null;

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

// gridlines in x axis function
function make_x_gridlines() {
    return d3.axisBottom(x)
    .ticks(5)
}

// gridlines in y axis function
function make_y_gridlines() {
    return d3.axisLeft(y)
    .ticks(5)
}

$.get("pttprJSON/pttprJSON.php", { PatientChartNo: PatientChartNo, sdate: sdate, edate: edate }, function (ds) {
    var i = 1;
    var first = true;
    var first2 = true;
    ds = JSON.parse(ds);
    var tablehtml="";
    console.log(ds.data);
    if(ds.data.length != 0 ){
        $.map(ds.data, function (d, k) {

            if (d.bools == true && first == true) {
                $("#labeltext1").html(d.date);
                $("#labelvalue1").html(d.AllValue.split(";")[3]+":"+ d.values+d.AllValue.split(";")[4]);
                tempid = d.id;
                first = false;

                tablehtml = tablehtml+="<tr name='"+d.id+"' onclick='touchclick(this)' class='touchclick'>",
                tablehtml = tablehtml+="<td>"+d.stringdate+"</td>",
                //tablehtml = tablehtml+="<td>"+d.unittext+"</td>",
                tablehtml = tablehtml+="<td  style='"+d.color+"'>"+d.values+"</td>",
                tablehtml = tablehtml+="<td>"+d.unit+"</td>",
                tablehtml = tablehtml+="</tr>";

            }else{
                tablehtml = tablehtml+="<tr name='"+d.id+"' onclick='touchclick(this)'>",
                tablehtml = tablehtml+="<td>"+d.stringdate+"</td>",
                //tablehtml = tablehtml+="<td>"+d.unittext+"</td>",
                tablehtml = tablehtml+="<td  style='"+d.color+"'>"+d.values+"</td>",
                tablehtml = tablehtml+="<td>"+d.unit+"</td>",
                tablehtml = tablehtml+="</tr>";
            }
            d.date = parseTime(d.date);
            if (i == ds.data.length) {
                $("#tablepart").html(tablehtml);
                //$.map(dates, function (d) {
                //    d.date = parseTime(d.date);
                //});
                var zoom = d3.zoom()
                .scaleExtent([1, Infinity])
                .translateExtent([[0, 0], [width, height]])
                .extent([[0, 0], [width, height]])
                .on("zoom", zoomed);

                var svg = d3.select("#chart").append("svg")
                .attr("width", widths + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom+ 100)
                .attr("class", "zoom")
                .call(zoom);

                x.domain(d3.extent(ds.data, function (d) { return d.date; }));

                //x.domain(d3.extent(dates, function (d) {
                //    return d;
                //}));

                //y.domain([0, d3.max(ds.data, function (d) {
                //    return parseFloat(d.values) * 1.1;
                //})]);
                y.domain([ds.minvalue,ds.maxvalue]);

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
                .datum(ds.data)
                .attr("class", "area")
                .attr("d", valueline);

                focus.selectAll("dot")
                .data(ds.data)
                .enter().append("circle")
                .attr("class", "circles")
                .attr("r", function (d) {
                   if (d.bools == true && first2 == true) {
                       first2 = false;
                       return 10;
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
                .on("mouseover", function (d){
                   var temp = $(d3.event.target);

                   d3.select("#circle_" + tempid)
                   .transition()
                   .duration(500)
                   .attr("r", 5)

                   tempid = (temp.attr("id")).split('_')[1];
                   var target = $("#tablepart tr[name='"+tempid+"']");
                   $(target).addClass("touchclick").siblings().removeClass("touchclick");
                   $("#table1 tbody").scrollTo(target, 500);

                   var val = $("#circle_" + tempid).attr('value').split(';');

                   $("#labeltext1").html(val[0]);
                   $("#labelvalue1").html(val[3] + ":" + val[1]+val[4]);

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

                //focus.append('g')
                //     .classed('labels-group', true)
                //     .selectAll('foreignObject')
                //     .data(ds.data)
                //     .enter()
                //     .append('foreignObject')
                //     .attr("class", "tooltips Alltop")
                //     .attr("width", "50")
                //     .attr("height", "20")
                //     .attr('x', function (d) {
                //         return x(d.date)-25;
                //     })
                //     .attr('y', function (d) {
                //         return y(parseFloat(d.values))-25;
                //     })
                //     .html(function (d) {
                //         return '<div class"style-me"><p>' + +d.values + '</p></div>'
                //     });

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

                    //focus.selectAll("foreignObject")
                    //     .attr('x', function (d) { return x(d.date) - 25; })
                    //     .attr('y', function (d) {
                    //         return y(parseFloat(d.values)) - 25;
                    //     });

                }
                $(".LoadingIndex").fadeOut();
            }
            i++;
        });
}else{
    $("#chart").html("<label style='font-size:32px;color:red;'>無參數值</label>");
    $(".LoadingIndex").fadeOut();
}
})

function getFormattedDate(date) {
    var year = date.getFullYear();

    var month = (1 + date.getMonth()).toString();
    month = month.length > 1 ? month : '0' + month;

    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;

    return year + '/' + month + '/' + day;
}
function Onclickpr(value,sdate,edate) {
    $(".LoadingIndex").fadeIn();
    $("#tablepart").html("");
    var tablehtml= "";
    var nows = new Date(getdate());
    var sdate = (sdate==null?formatDate(nows.addDays(-3)):sdate);
    var edate = (edate==null?formatDate(nows.addDays(3)):edate);
    $("#chart").html(" ");
    $("#labelvalue1").html(" ");
    $("#labeltext1").html(" ");
    $("#labelvalue2").html(" ");
    $("#labeltext2").html(" ");
    $("#table1").show();
    $("#table2").hide();

    $.get("pttprJSON/pttprJSON.php", {PatientChartNo: PatientChartNo, Types:value , sdate: sdate, edate: edate }, function (ds) {
        ds = JSON.parse(ds);
        var i = 1;
        var first = true;
        var first2 = true;
        if(ds.data.length==0){
            $("#chart").html("<label style='font-size:32px;color:red;'>無參數值</label>");
            $(".LoadingIndex").fadeOut();
        }else
        $.map(ds.data, function (d, k) {

            $("#unittext").html(d.unittext);

            if (d.bools == true && first == true) {
                $("#labeltext1").html(d.date);
                $("#labelvalue1").html(d.AllValue.split(";")[3] + ":" + d.values+d.AllValue.split(";")[4]);
                tempid = d.id;
                first = false;
                tablehtml = tablehtml+="<tr name='"+d.id+"' onclick='touchclick(this)' class='touchclick'>",
                tablehtml = tablehtml+="<td>"+d.stringdate+"</td>",
                //tablehtml = tablehtml+="<td>"+d.unittext+"</td>",
                tablehtml = tablehtml+="<td  style='"+d.color+"'>"+d.values+"</td>",
                tablehtml = tablehtml+="<td>"+d.unit+"</td>",
                tablehtml = tablehtml+="</tr>";
            }else{
                tablehtml = tablehtml+="<tr name='"+d.id+"' onclick='touchclick(this)'>",
                tablehtml = tablehtml+="<td>"+d.stringdate+"</td>",
                //tablehtml = tablehtml+="<td>"+d.unittext+"</td>",
                tablehtml = tablehtml+="<td  style='"+d.color+"'>"+d.values+"</td>",
                tablehtml = tablehtml+="<td>"+d.unit+"</td>",
                tablehtml = tablehtml+="</tr>";
            }
            d.date = parseTime(d.date);
            if (i == ds.data.length) {
                $("#tablepart").html(tablehtml);
                $.map(ds.data2, function (d) {
                    d.date = parseTime(d.date);
                });
                var zoom = d3.zoom()
                .scaleExtent([1, Infinity])
                .translateExtent([[0, 0], [width, height]])
                .extent([[0, 0], [width, height]])
                .on("zoom", zoomed);

                var svg = d3.select("#chart").append("svg")
                .attr("width", widths + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom+ 100)
                .attr("class", "zoom")
                .call(zoom);

                x.domain(d3.extent(ds.data, function (d) { return d.date; }));

                //x.domain(d3.extent(dates, function (d) { return d; }));

                //y.domain([0, d3.max(ds.data, function (d) {
                //    return parseFloat(d.values) * 1.1;
                //})]);

                y.domain([ds.minvalue,ds.maxvalue]);

                x2.domain(x.domain());
                y2.domain(y.domain());

                svg.append("defs").append("clipPath")
                .attr("id", "clip")
                .append("rect")
                .attr("width", width)
                .attr("height", height)

                var focus = svg.append("g")
                .attr("class", "focus")
                .attr("transform", "translate(" + (margin.left + 10) + "," + margin.top + ")");


                focus.append("path")
                .datum(ds.data)
                .attr("class", "area")
                .attr("d", valueline);

                focus.selectAll("dot")
                .data(ds.data)
                .enter().append("circle")
                .attr("class", "circles")
                .attr("r", function (d) {
                   if (d.bools == true && first2 == true) {
                       first2 = false;
                       return 10;
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

                   d3.selectAll("#circle_" + tempid)
                   .transition()
                   .duration(500)
                   .attr("r", 5)

                   tempid = (temp.attr("id")).split('_')[1];
                   var target = $("#tablepart tr[name='"+tempid+"']");
                   $(target).addClass("touchclick").siblings().removeClass("touchclick");
                   $("#table1 tbody").scrollTo(target, 500);

                   var val = $("#circle_" + tempid).attr('value').split(';');

                   $("#labeltext1").html(val[0]);
                   $("#labelvalue1").html(val[3] + ":" + val[1]+val[4]);

                   d3.selectAll("#circle_" + tempid)
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

                //focus.append('g')
                //     .classed('labels-group', true)
                //     .selectAll('foreignObject')
                //     .data(ds.data)
                //     .enter()
                //     .append('foreignObject')
                //     .attr("class", "tooltips Alltop")
                //     .attr("width", "50")
                //     .attr("height", "20")
                //     .attr('x', function (d) {
                //         return x(d.date) - 25;
                //     })
                //     .attr('y', function (d) {
                //         return y(parseFloat(d.values)) - 25;
                //     })
                //     .html(function (d) {
                //         return '<div class"style-me"><p>' + +d.values + '</p></div>'
                //     });
                ////收紓壓
                if(ds.data2.length!=0){

                    svg.append("defs").append("clipPath")
                    .attr("id", "clip2")
                    .append("rect")
                    .attr("width", width)
                    .attr("height", height)

                    focus.append("path")
                    .datum(ds.data2)
                    .attr("class", "area2")
                    .attr("d", valueline);

                    focus.selectAll("dot")
                    .data(ds.data2)
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
                       d3.selectAll("#circle_" + tempid)
                       .transition()
                       .duration(500)
                       .attr("r", 5)

                       tempid = (temp.attr("id")).split('_')[1];
                       var target = $("#tablepart tr[name='"+tempid+"']");
                       $(target).addClass("touchclick").siblings().removeClass("touchclick");
                       $("#table1 tbody").scrollTo(target, 500);

                       var val = $("#circle_" + tempid).attr('value').split(';');

                       $("#labeltext1").html(val[0]);
                       $("#labelvalue1").html(val[3] + ":" + val[1]+val[4]);

                       d3.selectAll("#circle_" + tempid)
                       .transition()
                       .duration(500)
                       .attr("r", 9)
                           //.style("fill", "#005AB5");
                       })

                    //focus.append("g")
                    //    .attr("class", "axis axis--x")
                    //    .attr("transform", "translate(0," + height + ")")
                    //    .style("font-size", "16px")
                    //    .call(xAxis2)
                    //    .selectAll(".axis--x .tick text")
                    //    .style("text-anchor", "end")
                    //    .attr("dx", "-.8em")
                    //    .attr("dy", ".15em")
                    //    .attr("transform", "rotate(-65)");

                    //focus.append("g")
                    //     .attr("class", "axis axis--y")
                    //     .call(yAxis);

                    //focus.append('g')
                    //     .classed('labels-group', true)
                    //     .selectAll('foreignObject')
                    //     .data(ds.data2)
                    //     .enter()
                    //     .append('foreignObject')
                    //     .attr("class", "tooltips Alltop")
                    //     .attr("width", "50")
                    //     .attr("height", "20")
                    //     .attr('x', function (d) {
                    //         return x(d.date) - 25;
                    //     })
                    //     .attr('y', function (d) {
                    //         return y(parseFloat(d.values)) - 25;
                    //     })
                    //     .html(function (d) {
                    //         return '<div class"style-me"><p>' + +d.values + '</p></div>'
                    //     });
                }
                ////收紓壓
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

                    focus.select(".area").attr("d", valueline);
                    focus.select(".area2").attr("d", valueline);

                    focus.selectAll(".circles")
                    .attr("cx", function (d) { return x(d.date); })
                    .attr("cy", function (d) {
                     return y(parseFloat(d.values));
                 });


                    //focus.selectAll("foreignObject")
                    //     .attr('x', function (d) { return x(d.date) - 25; })
                    //     .attr('y', function (d) {
                    //         return y(parseFloat(d.values)) - 25;
                    //     });
                }
                $(".LoadingIndex").fadeOut();
            }
            i++;
        });
});
}
function Onclickprblod(value,sdate,edate) {
    $(".LoadingIndex").fadeIn();
    $("#tablepart").html("");
    var tablehtml= "";
    var tablehtm2= "";
    var nows = new Date(getdate());
    var sdate = (sdate==null?formatDate(nows.addDays(-3)):sdate);
    var edate = (edate==null?formatDate(nows.addDays(3)):edate);
    $("#chart").html(" ");
    $("#labelvalue1").html(" ");
    $("#labeltext1").html(" ");
    $("#table2").show();
    $("#table1").hide();

    var arc = d3.symbol().type(d3.symbolTriangle);

    $.get("pttprJSON/pttprJSON.php", {PatientChartNo: PatientChartNo, Types:value , sdate: sdate, edate: edate }, function (ds) {
        ds = JSON.parse(ds);
        var i = 1;
        var first = true;
        var first2 = true;
        for (var asd = 0; asd < ds.data.length;asd++) {
            if (ds.data[asd].bools == true && first == true) {
                tablehtml = tablehtml+="<tr name='"+ds.data[asd].id+"' onclick='touchclickBP(this)'class='touchclick'>",
                tablehtml = tablehtml+="<td>"+ds.data[asd].stringdate+"</td>",
                tablehtml = tablehtml+="<td style='"+ds.data[asd].color+"'>"+ds.data[asd].values+"</td>",
                tablehtml = tablehtml+="<td style='"+ds.data2[asd].color+"'>"+ds.data2[asd].values+"</td>",
                tablehtml = tablehtml+="<td>"+ds.data[asd].unit+"</td>",
                tablehtml = tablehtml+="</tr>";
                tempid = ds.data[asd].id;
                first = false;
            }else{
                tablehtml = tablehtml+="<tr name='"+ds.data[asd].id+"' onclick='touchclickBP(this)'>",
                tablehtml = tablehtml+="<td>"+ds.data[asd].stringdate+"</td>",
                tablehtml = tablehtml+="<td style='"+ds.data[asd].color+"'>"+ds.data[asd].values+"</td>",
                tablehtml = tablehtml+="<td style='"+ds.data2[asd].color+"'>"+ds.data2[asd].values+"</td>",
                tablehtml = tablehtml+="<td>"+ds.data[asd].unit+"</td>",
                tablehtml = tablehtml+="</tr>";
            }
        }
        if(ds.data.length==0){
            $("#chart").html("<label style='font-size:32px;color:red;'>無參數值</label>");
            $(".LoadingIndex").fadeOut();
        }else
        $.map(ds.data, function (d, k) {
                //if (d.bools == true && first == true) {
                //    tempid = d.id;
                //    first = false;
                //}
                d.date = parseTime(d.date);
                if (i == ds.data.length) {
                    $("#BP1").html(tablehtml);
                    //$("#BP2").html(tablehtm2);

                    $.map(ds.data2, function (d) {
                        d.date = parseTime(d.date);
                    });
                    var zoom = d3.zoom()
                    .scaleExtent([1, Infinity])
                    .translateExtent([[0, 0], [width, height]])
                    .extent([[0, 0], [width, height]])
                    .on("zoom", zoomed);

                    var svg = d3.select("#chart").append("svg")
                    .attr("width", widths + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom+ 100)
                    .attr("class", "zoom")
                    .call(zoom);

                    x.domain(d3.extent(ds.data, function (d) { return d.date; }));

                    y.domain([ds.minvalue,ds.maxvalue]);

                    //x.domain(d3.extent(dates, function (d) { return d; }));

                    //y.domain([0, d3.max(ds.data, function (d) {
                    //    return parseFloat(d.values) * 1.1;
                    //})]);

                    x2.domain(x.domain());
                    y2.domain(y.domain());

                    svg.append("defs").append("clipPath")
                    .attr("id", "clip")
                    .append("rect")
                    .attr("width", width)
                    .attr("height", height)

                    var focus = svg.append("g")
                    .attr("class", "focus")
                    .attr("transform", "translate(" + (margin.left + 10) + "," + margin.top + ")");


                    //focus.append("path")
                    //    .datum(ds.data)
                    //    .attr("class", "area")
                    //    .attr("d", valueline);

                    focus.selectAll("dot")
                    .data(ds.data)
                    .enter().append("path")
                    .attr("class", "circles")
                    .attr('d', arc)
                    .attr('fill',function (d) { return d.color })
                       //.attr('stroke', '#000')
                       .attr('stroke',function (d) { return d.color })
                       .attr('stroke-width',function(d){
                           if (d.bools == true && first2 == true) {
                               first2 = false;
                               return 10;
                           } else {
                               return 1
                           }
                       })
                       .attr('transform', function(d) {
                           return "translate(" + x(d.date) + ","+ y(parseFloat(d.values))+")"
                       })
                       .attr("id", function (d) { return "circle_" + d.id })
                       .attr("value", function (d) { return d.AllValue })
                       .on("mouseover", function (d) {
                           var temp = $(d3.event.target);
                           d3.selectAll("#circle_" + tempid)
                           .transition()
                           .duration(500)
                           .attr('stroke-width',1)

                           tempid = (temp.attr("id")).split('_')[1];
                           var target = $("#BP1 tr[name='"+tempid+"']");
                           $(target).addClass("touchclick").siblings().removeClass("touchclick");
                           $("#table2 tbody").scrollTo(target, 500);
                           var icount =1;
                           d3.selectAll("#circle_" + tempid)
                           .filter(function(d){
                             var val = d.AllValue.split(';');
                             $("#labeltext"+icount).html(val[0]);
                             $("#labelvalue"+icount).html(val[3] + ":" + val[1]+val[4]);
                             icount++;
                         });

                           d3.selectAll("#circle_" + tempid)
                           .transition()
                           .duration(500)
                           .attr('stroke-width',8)

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

                    //focus.append('g')
                    //     .classed('labels-group', true)
                    //     .selectAll('foreignObject')
                    //     .data(ds.data)
                    //     .enter()
                    //     .append('foreignObject')
                    //     .attr("class", "tooltips Alltop")
                    //     .attr("width", "50")
                    //     .attr("height", "20")
                    //     .attr('x', function (d) {
                    //         return x(d.date) - 25;
                    //     })
                    //     .attr('y', function (d) {
                    //         return y(parseFloat(d.values)) - 25;
                    //     })
                    //     .html(function (d) {
                    //         return '<div class"style-me"><p>' + +d.values + '</p></div>'
                    //     });
                    ////收紓壓
                    if(ds.data2.length!=0){
                        first2 = true;
                        svg.append("defs").append("clipPath")
                        .attr("id", "clip2")
                        .append("rect")
                        .attr("width", width)
                        .attr("height", height)

                        //focus.append("path")
                        //    .datum(ds.data2)
                        //    .attr("class", "area2")
                        //    .attr("d", valueline);

                        focus.selectAll("dot")
                        .data(ds.data2)
                        .enter().append("path")
                        .attr("class", "circles2")
                        .attr('d', arc)
                        .attr('fill',function (d) { return d.color })
                        .attr('stroke',function (d) { return d.color })
                        .attr('stroke-width',function(d){
                         if (d.bools == true && first2 == true) {
                             first2 = false;
                             return 10;
                         } else {
                             return 1
                         }
                     })
                        .attr('transform', function(d) {
                         return "translate(" + x(d.date) + ","+ y(parseFloat(d.values))+") rotate(180)"
                     })
                        .attr("id", function (d) { return "circle_" + d.id })
                        .attr("value", function (d) { return d.AllValue })
                        .on("mouseover", function (d) {
                          var temp = $(d3.event.target);
                          d3.selectAll("#circle_" + tempid)
                          .transition()
                          .duration(500)
                          .attr('stroke-width',1)

                          tempid = (temp.attr("id")).split('_')[1];
                          var target = $("#BP1 tr[name='"+tempid+"']");
                          $(target).addClass("touchclick").siblings().removeClass("touchclick");
                          $("#table2 tbody").scrollTo(target, 500);

                          var icount =1;
                          d3.selectAll("#circle_" + tempid)
                          .filter(function(d){
                            var val = d.AllValue.split(';');
                            $("#labeltext"+icount).html(val[0]);
                            $("#labelvalue"+icount).html(val[3] + ":" + val[1]+val[4]);
                            icount++;
                        });

                          d3.selectAll("#circle_" + tempid)
                          .transition()
                          .duration(500)
                          .attr('stroke-width',8)
                      })

                        //focus.append('g')
                        //     .classed('labels-group', true)
                        //     .selectAll('foreignObject')
                        //     .data(ds.data2)
                        //     .enter()
                        //     .append('foreignObject')
                        //     .attr("class", "tooltips Alltop")
                        //     .attr("width", "50")
                        //     .attr("height", "20")
                        //     .attr('x', function (d) {
                        //         return x(d.date) - 25;
                        //     })
                        //     .attr('y', function (d) {
                        //         return y(parseFloat(d.values)) - 25;
                        //     })
                        //     .html(function (d) {
                        //         return '<div class"style-me"><p>' + +d.values + '</p></div>'
                        //     });
                        var icount =1;
                        d3.selectAll("#circle_" + tempid)
                        .filter(function(d){
                          var val = d.AllValue.split(';');
                          $("#labeltext"+icount).html(val[0]);
                          $("#labelvalue"+icount).html(val[3] + ":" + val[1]+val[4]);
                          icount++;
                      });
                    }
                    ////收紓壓
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

                        focus.select(".area").attr("d", valueline);
                        focus.select(".area2").attr("d", valueline);

                        focus.selectAll(".circles")
                        .attr('transform', function(d) {
                          return "translate(" + x(d.date) + ","+ y(parseFloat(d.values))+")"
                      });

                        focus.selectAll(".circles2")
                        .attr('transform', function(d) {
                          return "translate(" + x(d.date) + ","+ y(parseFloat(d.values))+") rotate(180)"
                      });

                        //focus.selectAll("foreignObject")
                        //     .attr('x', function (d) { return x(d.date) - 25; })
                        //     .attr('y', function (d) {
                        //         return y(parseFloat(d.values)) - 25;
                        //     });
                    }
                    $(".LoadingIndex").fadeOut();
                }
                i++;
            });
});
}
function getdate() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    today = yyyy + '/' + mm + '/' + dd;
    return today;
}
function lastclick() {
    var myself = $("#circle_" + (tempid)).attr('value');
    var val = $("#circle_" + (tempid - 1)).attr('value');
    if (myself != undefined && val != undefined) {
        if (myself != undefined) {
            var temp = myself.split(';');

            d3.selectAll("#circle_" + tempid)
            .transition()
            .duration(500)
            .attr("r", 5)
            .style("fill", temp[2]);
        }
        if (val != undefined) {
            var count = d3.selectAll("#circle_" + tempid).size();
            if(count ==2){

                var icount =1;
                tempid = tempid - 1;
                d3.selectAll("#circle_" + tempid)
                .filter(function(d){
                  var val = d.AllValue.split(';');
                  $("#labeltext"+icount).html(val[0]);
                  $("#labelvalue"+icount).html(val[3] + ":" + val[1]+val[4]);
                  icount++;
              });

                d3.selectAll("#circle_" + tempid)
                .transition()
                .duration(500)
                .attr("r", 9);

            }else{
                var temp = val.split(';');

                $("#labeltext1").html(temp[0]);
                $("#labelvalue1").html(temp[3]+":"+ temp[1]+temp[4]);

                tempid = tempid - 1;

                d3.selectAll("#circle_" + tempid)
                .transition()
                .duration(500)
                .attr("r", 9)
                //.style("fill", "#005AB5");
            }
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

            d3.selectAll("#circle_" + tempid)
            .transition()
            .duration(500)
            .attr("r", 5)
            .style("fill", temp[2]);
        }

        if (val != undefined) {

            tempid = tempid + 1;
            var count = d3.selectAll("#circle_" + tempid).size();
            if(count==2){
                var icount =1;
                d3.selectAll("#circle_" + tempid)
                .filter(function(d){
                  var val = d.AllValue.split(';');
                  $("#labeltext"+icount).html(val[0]);
                  $("#labelvalue"+icount).html(val[3] + ":" + val[1]+val[4]);
                  icount++;
              });

                d3.selectAll("#circle_" + tempid)
                .transition()
                .duration(500)
                .attr("r", 9);

            }else{
                var temp = val.split(';');
                $("#labeltext1").html(temp[0]);
                $("#labelvalue1").html(temp[3] + ":" + temp[1]+temp[4]);

                d3.selectAll("#circle_" + tempid)
                .transition()
                .duration(500)
                .attr("r", 9)
            }
        }

    }
}
function touchclick(event) {
    var id = $(event).attr("name");
    $(event).addClass("touchclick").siblings().removeClass("touchclick");
    if(id !=tempid){

        d3.select("#circle_" + tempid)
        .transition()
        .duration(500)
        .attr("r", 5)

        tempid = id;

        d3.select("#circle_" + tempid)
        .transition()
        .duration(500)
        .attr("r", 9)

    }else{

    }

}
function touchclickBP(event) {
    var id = $(event).attr("name");
    $(event).addClass("touchclick").siblings().removeClass("touchclick");
    if(id != tempid){
        d3.selectAll("#circle_" + tempid)
        .transition()
        .duration(500)
        .attr('stroke-width',1)
        tempid = id;
        d3.selectAll("#circle_" + tempid)
        .transition()
        .duration(500)
        .attr('stroke-width',8);
    }else{

    }

}

$("#Search").on('click', function () {
    $(".LoadingIndex").fadeIn();
    var sdate = $("#sdate").val();
    var edate = $("#edate").val();
    var theDate1 = new Date(sdate.split("-")[0], sdate.split("-")[1] - 1, sdate.split("-")[2]);
    var theDate2 = new Date(edate.split("-")[0], edate.split("-")[1] - 1, edate.split("-")[2]);
    dates = [theDate1, theDate2];
    var value = $(".tpr_topmenu .active").attr("name");
    $.get("Checkpttpr/Checkpttpr.php",{sdate:sdate,edate:edate},function(drt){
        drt = JSON.parse(drt);
        if(drt === false){
            $(".LoadingIndex").fadeOut();
            alert("起止時間不可超過13天");
        }else{
            $("#chart").html(" ");
            $("#labelvalue1").html(" ");
            $("#labeltext1").html(" ");
            $("#labelvalue2").html(" ");
            $("#labeltext2").html(" ");
            $("#table1").show();
            $("#table2").hide();
            if(value == "BP"){
                Onclickprblod(value,sdate,edate);
            }else{
                Onclickpr(value,sdate,edate);
            }
        }
    });

});

$(".btn-group > .btn").click(function(){
    $(this).addClass("active").siblings().removeClass("active");
});
</script>


</body>
</html>