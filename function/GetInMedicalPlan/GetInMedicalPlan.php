<?php
class kmuhSoapClient extends SoapClient {
    function __construct($wsdl, $options) {
        parent::__construct($wsdl, $options);
    }
    function __doRequest($request, $location, $action, $version, $one_way = 0) {
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
}
$name = $no = $ward = "";
$notedate = $notecontent = array();
$len_note = array();

if(isset($_GET["No"])){
    $pChartNo = trim($_GET["No"]);
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
        $wcfservice = $client->GetInMedicalPlan($patientid);
        $xml = $wcfservice;
        $xml = ( json_decode(json_encode($wcfservice), True));
        $data = simplexml_load_string($xml["GetInMedicalPlanResult"]);
        if(empty($data)){
            echo "<script>history.back();</script>";
        }
        else{
            $name = $data->PatientInfo->PatientName;
            $no = $data->PatientInfo->PatientChartNo;
            $ward = $data->PatientInfo->WardBed;

            $len_note = count($data->NoteInfo->Note);
            for($x = 0; $x < $len_note; $x++){
                $notedate[$x] = $data->NoteInfo->Note[$x]->NoteDate;
                $notecontent[$x] = $data->NoteInfo->Note[$x]->NoteContent;
            }
        }
    }catch(Exception $e){
        echo $e;
    }

}else{
    echo "<script>history.back();</script>";
}




?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>住院診療計劃</title>
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
    <script src="../../js/formxml.js"></script>
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
                            <span class="top-header">姓名: <?php echo $name;?></span>
                            <span class="top-header">床號: <?php echo $ward;?></span>
                            <span class="top-header">病歷: <?php echo $no;?></span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- /. NAV SIDE  -->
            <div id="operaInfo-wrapper">
                <div class="container pt-MedicalPlan-content">
                    <div class="text-center mp-title">
                        <br /> 
                        <label>住院診療計劃</label>
                    </div>

                    <div class="form-group">
                        <select id="SelectedChange">
                            <?php
                            for($x = 0; $x < count($notedate); $x++){
                                $output = "";
                                if($x === 0){
                                    $output = '<option value="'.$notedate[$x].'" selected>'.$notedate[$x].'</option>';
                                }else{
                                    $output = '<option value="'.$notedate[$x].'">'.$notedate[$x].'</option>';
                                }
                                echo $output;
                            }
                            ?>
                        </select>
                    </div>

                    <div class="panel">
                        <?php
                        
                            echo 
                            '<a class="list-group-item text-center" data-toggle="collapse" href="#pef1">
                            <strong>'.$notedate[(count($notedate)-1)].'</strong>
                            <span class="fa fa-chevron-down pt-list-item-right"></span>
                            </a>';
                            echo '
                            <div id="pef1" class="panel-collapse collapse in" name="'.$notedate[(count($notedate)-1)].'">
                            <div class="list-group">
                            <div class="well well-lg">
                            <div class="media-body">
                            <div class="form-group">
                            <textarea class="form-control" rows="23" disabled="">    '.$notecontent[(count($notedate)-1)].'
                            </textarea>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>';
                        
                        ?>
                    </div><!-- /. end of panel  -->

                </div>
            </div>
            <!-- /. PAGE WRAPPER  -->
        </div>

        <div class="opera-footer">
            <div class="row">
                <div class="col-lg-12">
                    <!--&copy;  2017 兆葳科技有限公司 | Design by: <a href="http://www.103cloud.com" style="color:#fff;" target="_blank">www.103cloud.com</a>-->
                </div>
            </div>
        </div>

        <!-- /. WRAPPER  -->
        <script src="../../js/bootstrap.min.js"></script>
        <script src="../../js/custom.js"></script>


        <script>
            $("#SelectedChange").change(function(){
                var val = $("#SelectedChange option:selected").val();
                $(".panel .panel-collapse").collapse("hide");
                $(".panel .panel-collapse[name*='"+val+"']").collapse("show");
            });
        </script>
    </body>
    </html>