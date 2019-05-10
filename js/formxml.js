function CurrDate(){
        var d = new Date();      
        var year = d.getFullYear(); 
        var month = d.getMonth() + 1;
        var day = d.getDate();
        var Hours = d.getHours();
        var minutes = d.getMinutes();
        var seconds = d.getSeconds();
        var week  = d.getDay();
        var day_list = ['日', '一', '二', '三', '四', '五', '六'];
        
        if (year < 10) year = "0" + year;               
        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;
        if (Hours < 10) Hours = "0" + Hours;
        if (minutes < 10) minutes = "0" + minutes;
        if (seconds < 10) seconds = "0" + seconds;

        var currdate = year + "/" + month + "/" + day;
        var currms = Hours + ":" + minutes + ":" + seconds + " 星期" + day_list[week];

        $("#datetimepicker1").html(currms);
        $("#datetimepicker2").html(currdate);

        $("#datetimepicker3").html(currms);
        $("#datetimepicker4").html(currdate);

    }

function alert_box(path){
    
    $('#alert-box').show(); 
    window.setTimeout(function () { 
        $('#alert-box').fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
            location.href = path + ".html";

            });
        },1500);  
}


/*====================================
      GetXml
======================================*/

function getxml(evt, TtempId) {
    $.ajax({
        type: "GET",
        //url: "http://localhost:52458/file/PatientInfo.xml",
        //url: "ImPortTestXml?ent=" + evt,
        //url: "/file/"+evt+".xml",
        //dataType: "xml",
        url: evt + "Read" + "Xml?TempId=" + TtempId,
        dataType: "JSON",
        timeout: 1000,
        error: function (xml) {
            //alert('讀取xml錯誤' + xml);
        },
        success: function (xml) {
            switch (evt) {
                case "BedInfo":
                    pt_index(xml.data);//病人動態
                    break;
                case "PatientInfo":
                    pt_info(xml.data);//病人資訊
                    break;
                case "OPTCheckPatientInfo":
                    pt_surgical_examination(xml.data);//手術/檢查
                    break;
                case "TeamCarePatientInfo":
                    kmuh_team_care(xml.data);//團隊照護
                    break;
                case "ContactTelInfo":
                    kmuh_contact(xml.data);//聯絡電話
                    break;
                default:
                    break;
            }

        },
    });
}
function getforptxml(evt, Station) {
    $.ajax({
        type: "GET",
        //url: "http://localhost:52458/file/PatientInfo.xml",
        //url: "ImPortTestXml?ent=" + evt,
        //url: "/file/"+evt+".xml",
        //dataType: "xml",
        url: evt + "Read" + "Xmls?Station=" + Station,
        dataType: "JSON",
        timeout: 1000,
        error: function (xml) {
            //alert('讀取xml錯誤' + xml);
        },
        success: function (xml) {
            switch (evt) {
                case "BedInfo":
                    pt_index(xml.data);//病人動態
                    break;
            }
        },
    });
}


/*==============================================
    病人動態  
    =============================================*/

function pt_index(xml) {
    var array = [];
    var ptstr = '', ptstr2 = '', ptstr3 = '';
    $(xml).find("BedSummaryData").each(function (i) {
        var total = $(xml).find("BedSummaryData").length;
        var Station = $(this).children("Station").text();  //護理站
        var InPatientCount = $(this).children("InPatientCount").text();  //住院人數
        var OptCount = $(this).children("OptCount").text();  //手術人數
        var CheckCount = $(this).children("CheckCount").text();  //檢查人數
        var OnBedCount = $(this).children("OnBedCount").text();  //推床人數
        var ChairCount = $(this).children("ChairCount").text();  //輪椅人數
        var OnCriticalCount = $(this).children("OnCriticalCount").text();  //病危人數
        getInPatientCount = '<h1>' + InPatientCount + '</h1>'; //住院人數
        getOptCount = '<h1>' + OptCount + '</h1>'; //手術人數
        getCheckCount = '<h1>' + CheckCount + '</h1>'; //檢查人數
        getOnBedCount = '<h1 class="getOnBedCount" onclick="BedOn()">' + OnBedCount + '</h1>'; //推床人數
        getChairCount = '<h1 class="getChairCount" onclick="ChairOn()">' + ChairCount + '</h1>'; //輪椅人數
        getOnCriticalCount = '<h1>' + OnCriticalCount + '</h1>'; //病危人數

    });
    $("#getInPatientCount").html(getInPatientCount);
    $("#getOptCount").html(getOptCount);
    $("#getCheckCount").html(getCheckCount);
    $("#getOnBedCount").html(getOnBedCount); 
    $("#getChairCount").html(getChairCount); 
    $("#getOnCriticalCount").html(getOnCriticalCount);

    $(xml).find("BedDetailData").each(function (i) {
        var isSexStrdel = ' ';
        var total = $(xml).find("BedDetailData").length;
        var WardCode = $(this).children("WardCode").text();  //房間號
        var BedId = $(this).children("BedId").text();  //床號
        var SexStr = $(this).children("SexStr").text();  //性別
        var DnrStr = $(this).children("DnrStr").text();  //是否DNR
        var PatientNameSecurity = $(this).children("PatientNameSecurity").text();  //姓名
        var PatientBirthDay = $(this).children("PatientBirthDay").text();  //病人生日
        var PatientChartNo = $(this).children("PatientChartNo").text();  //病歷號
        var DrvsName = $(this).children("DrvsName").text();  //主治醫師名字
        var DrrName = $(this).children("DrrName").text();  //住院醫師名字
        var NurseName = $(this).children("NurseName").text();  //主護名字
        var NspName = $(this).children("NspName").text();  //專科護理師名字
        var DiagDesc = $(this).children("DiagDesc").text();  //診斷
        var OptDesc1 = $(this).children("OptDesc1").text();  //手術實際術式一
        var OptDesc2 = $(this).children("OptDesc2").text();  //手術實際術式二
        var OptDesc3 = $(this).children("OptDesc3").text();  //手術實際術式三
        var IsQuery = $(this).children("IsQuery").text();  //是否可被查詢
        var IsChair = $(this).children("IsChair").text();  //輪椅
        var IsOnBed = $(this).children("IsOnBed").text();  //推床
        AnimationMap(WardCode, BedId, SexStr, DnrStr, IsChair, IsOnBed,array);

        if (SexStr == '男') {
            panel_style = "panel-primary";
            isSexStrdel = isSexStrdel + '<span><i class="fa fa-mars pt-showbox4"></i><span>';
        } else if (SexStr == '女') {
            panel_style = "panel-danger";
            isSexStrdel = isSexStrdel + '<span><i class="fa fa-venus pt-showbox4"></i><span>';
        }


        if (DnrStr == 'Y') {
            DnrStr = '<span><i style=" color: red;" class="fa fa-circle"></i><span>';
        } else {
            DnrStr = '<span><i style=" color: #d1d3d1;" class="fa fa-circle"></i><span>';
        }

        //單獨顯示待辦事項
        if (IsQuery == 'Y') {
            notShow = 'style="display:none;"';
            Focus = 'tab-pane fade in active';
        } else {
            notShow = " ";
            Focus = 'tab-pane fade';
        }


        ptstr3 = ptstr3 + '<div data-Test="' + WardCode + BedId + '"' + 'class="'+'modal fade' +'"id="' + (WardCode + BedId).trim() + '"name=' + PatientChartNo + ' role="dialog">';
        ptstr3 = ptstr3 + '<div class="modal-dialog">';
        ptstr3 = ptstr3 + '<div class="modal-content">';
        ptstr3 = ptstr3 + '<div class="modal-header pt-showbox-header">';
        ptstr3 = ptstr3 + '<button type="button" class="close" data-dismiss="modal">&times;</button>';
        ptstr3 = ptstr3 + '<p class="modal-title" ' + notShow + ' >';
        ptstr3 = ptstr3 + '<span class="pt-showbox1">' + WardCode + '-' + BedId + '</span>';
        ptstr3 = ptstr3 + '<span class="pt-showbox2">' + PatientNameSecurity + '</span>';
        ptstr3 = ptstr3 + '<span class="pt-showbox3">' + PatientBirthDay + '</span>';
        //ptstr3 = ptstr3 + '<span><i class="fa fa-user pt-showbox4"></i><span>';
        ptstr3 = ptstr3 + isSexStrdel;
        ptstr3 = ptstr3 + '</p>';
        ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '<div class="modal-body pt-showbox5">';
        ptstr3 = ptstr3 + '<ul class="nav nav-pills">';
        ptstr3 = ptstr3 + '<li ' + notShow + '><a data-toggle="tab" href="#' + PatientChartNo + '_1">基本資料</a></li>';
        //ptstr3 = ptstr3 + '<li ' + notShow + '><a data-toggle="pill" href="#' + PatientChartNo + '_2">過敏資料</a></li>';
        //ptstr3 = ptstr3 + '<li ' + notShow + '><a data-toggle="pill" href="#' + PatientChartNo + '_3">醫囑資料</a></li>';
        ptstr3 = ptstr3 + '<li><a data-toggle="pill" href="#' + PatientChartNo + '_4">待辦事項</a></li>';
        ptstr3 = ptstr3 + '</ul>';
        ptstr3 = ptstr3 + '<div class="tab-content"><br>';
        ptstr3 = ptstr3 + '<div ' + notShow + ' id="' + PatientChartNo + '_1" class="tab-pane fade in active">';
        ptstr3 = ptstr3 + '<p class="text-left">病歷號 : ' + PatientChartNo + '</p>';
        ptstr3 = ptstr3 + '<p class="text-left">主治醫師 : ' + DrvsName + '</p>';
        ptstr3 = ptstr3 + '<p class="text-left">住院醫師 : ' + DrrName + '</p>';
        ptstr3 = ptstr3 + '<p class="text-left">專科護理師 : ' + NspName + '</p>';
        ptstr3 = ptstr3 + '<p class="text-left">護理師 : ' + NurseName + '</p>';
        ptstr3 = ptstr3 + '<p class="text-left">診斷 : ' + DiagDesc + '</p>';
        ptstr3 = ptstr3 + '<p class="text-left">術式 : </p>';
        ptstr3 = ptstr3 + '<p class="text-left">手術1 : ' + OptDesc1 + '</p>';
        ptstr3 = ptstr3 + '<p class="text-left">手術2 : ' + OptDesc2 + '</p>';
        ptstr3 = ptstr3 + '<p class="text-left">手術3 : ' + OptDesc3 + '</p>';
        ptstr3 = ptstr3 + '<p class="text-left">狀態 : ' + DnrStr + ' DNR<br></p>';
        ptstr3 = ptstr3 + '</div>';
        //ptstr3 = ptstr3 + '<div ' + notShow + ' id="' + PatientChartNo + '_2" class="tab-pane fade"><br>';
        //ptstr3 = ptstr3 + '</div>';
        //ptstr3 = ptstr3 + '<div ' + notShow + ' id="' + PatientChartNo + '_3" class="tab-pane fade"><br>';
        //ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '<div id="' + PatientChartNo + '_4" class="' + Focus + '"><br>';
        ptstr3 = ptstr3 + '<div class="Index1">';
        ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '<div class="Index2" style="display: inline-block;width:100%;">';
        ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '<div class="modal-footer">';
        ptstr3 = ptstr3 + '<button type="button" class="btn btn-default pt-showbox6" data-dismiss="modal">關閉</button>';
        ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '</div>';
        ptstr3 = ptstr3 + '</div>';

    });
    $("#pt-list-box_detail").html(ptstr3);

}

/*==============================================
    病人資訊  
    =============================================*/

function pt_info(xml) {
    var ptstr = '', ptstr2 = '';

    $(xml).find("PatientInfoData").each(function (i) {
        var total = $(xml).find("PatientInfoData").length;
        var Station = $(this).children("Station").text();  //護理站

        var WardCode = $(this).children("WardCode").text();  //房間號
        var BedId = $(this).children("BedId").text();  //床號
        var PatientNameSecurity = $(this).children("PatientNameSecurity").text();  //姓名
        var PatientBirthDay = $(this).children("PatientBirthDay").text();  //病人生日
        var DrvsName = $(this).children("DrvsName").text();  //主治醫師名字
        var DrrName = $(this).children("DrrName").text();  //住院醫師名字
        var NurseName = $(this).children("NurseName").text();  //主護名字
        var deptname = $(this).children("deptname").text();  //科別
        var NspName = $(this).children("NspName").text();  //專科護理師名字
        var SexStr = $(this).children("SexStr").text();  //性別

        var PatientChartNo = $(this).children("PatientChartNo").text();  //病歷號
        var DnrStr = $(this).children("DnrStr").text();  //是否DNR
        var DiagDesc = $(this).children("DiagDesc").text();  //診斷
        var OptDesc1 = $(this).children("OptDesc1").text();  //手術實際術式一
        var OptDesc2 = $(this).children("OptDesc2").text();  //手術實際術式二
        var OptDesc3 = $(this).children("OptDesc3").text();  //手術實際術式三

        var IsBedLock = $(this).children("IsBedLock").text();  //是否鎖床
        var IsBedEmpty = $(this).children("IsBedEmpty").text();  //是否空床
        var IsBedHold = $(this).children("IsBedHold").text();  //是否預佔
        var IsPreOutHosp = $(this).children("IsPreOutHosp").text();  //是否預計出院
        var IsQuery = $(this).children("IsQuery").text();  //是否可被查詢

        var IsChair = $(this).children("IsChair").text();  //輪椅
        var IsOnBed = $(this).children("IsOnBed").text();  //推床
        var iscoptstr = '';
        var isSexStr = '';
        var isDnrStr = '';
        var isSexStrdel = '';
        //判斷是否鎖床,是否空床,是否預佔
        if (IsBedLock == 'Y' || IsBedHold == 'Y' || PatientChartNo == 0) {
            isptstr = ' ';
            iscoptstr = ' ';
            panel_style = "panel-default";

            if (IsBedLock == 'Y') {
                isptstr = isptstr + '<img class="img-rounded pt-box-content-img1" src="../img/IsBedLock.png">';
            } else if (IsBedHold == 'Y') {
                isptstr = isptstr + '<img class="img-rounded pt-box-content-img1" src="../img/IsBedHold.png">';
            } else if (IsQuery == 'Y') {
                isptstr = isptstr + '<img class="img-rounded pt-box-content-img1" src="../img/IsQuery.png">';
            }

        //判斷是否是否可被查詢
        } else if (IsQuery == 'Y') {
            isptstr = ' ';
            iscoptstr = ' ';
            //PatientChartNo = " ";
            isptstr = isptstr + '<img class="img-rounded pt-box-content-img1" src="../img/IsQuery.png">';

            if (SexStr == '男') {
                panel_style = "panel-primary";
            } else if (SexStr == '女') {
                panel_style = "panel-danger";
            }

            if (IsChair == 'Y') {
                iscoptstr = iscoptstr + '<img class="img-rounded pt-box-content-img2" src="../img/IsChair.png">';
            }
            if (IsOnBed == 'Y') {
                iscoptstr = iscoptstr + '<img class="img-rounded pt-box-content-img2" src="../img/IsOnBed.png">';
            }
            if (IsPreOutHosp == 'Y') {
                iscoptstr = iscoptstr + '<img class="img-rounded pt-box-content-img2" src="../img/IsPreOutHosp.png">';
            }

            //一般使用者
        } else if (IsBedLock == 'N' || IsBedEmpty == 'N' || IsBedHold == 'N' || IsQuery == 'N') {
            isptstr = ' '; iscoptstr = ' '; isSexStr = ' '; isDnrStr = ' '; isSexStrdel = ' ';

            if (SexStr == '男') {
                panel_style = "panel-primary";
                isSexStr = isSexStr + '<span><i class="fa fa-mars pt-box-sex"></i><span>';
                isSexStrdel = isSexStrdel + '<span><i class="fa fa-mars pt-showbox4"></i><span>';
            } else if (SexStr == '女') {
                panel_style = "panel-danger";
                isSexStr = isSexStr + '<span><i class="fa fa-venus pt-box-sex"></i><span>';
                isSexStrdel = isSexStrdel + '<span><i class="fa fa-venus pt-showbox4"></i><span>';
            }

            if (DnrStr == 'Y') {
                isDnrStr = isDnrStr + '<span><i class="fa fa-circle pt-box-dnr"></i><span>';
            }
            if (IsChair == 'Y') {
                iscoptstr = iscoptstr + '<img class="img-rounded pt-box-content-img2" src="../img/IsChair.png">';
            }
            if (IsOnBed == 'Y') {
                iscoptstr = iscoptstr + '<img class="img-rounded pt-box-content-img2" src="../img/IsOnBed.png">';
            }
            if (IsPreOutHosp == 'Y') {
                iscoptstr = iscoptstr + '<img class="img-rounded pt-box-content-img2" src="../img/IsPreOutHosp.png">';
            }

            isptstr = isptstr + '<p class="text-left">科別 : ' + deptname + '</p>';
            isptstr = isptstr + '<p class="text-left">姓名 : ' + PatientNameSecurity + '</p>';
            isptstr = isptstr + '<p class="text-left">生日 : ' + PatientBirthDay + '</p>';
            isptstr = isptstr + '<p class="text-left">主治 : ' + DrvsName + '</p>';
            isptstr = isptstr + '<p class="text-left">住院 : ' + DrrName + '</p>';
            isptstr = isptstr + '<p class="text-left">專師 : ' + NspName + '</p>';
            isptstr = isptstr + '<p class="text-left">主護 : ' + NurseName + '</p>';
        }


        if (DnrStr == 'Y') {
            DnrStr = '<span><i style=" color: red;" class="fa fa-circle"></i><span>';
        } else {
            DnrStr = '<span><i style=" color: #d1d3d1;" class="fa fa-circle"></i><span>';
        }

        //單獨顯示待辦事項
        if (IsQuery == 'Y') {
            notShow = 'style="display:none;"';
            Focus = 'tab-pane fade in active';
        } else {
            notShow = " ";
            Focus = 'tab-pane fade';
        }


        ptstr = ptstr + '<div class="col-md-3">';
        ptstr = ptstr + '<a class="pt-box" data-toggle="modal" data-target="#' + PatientChartNo + '">';
        ptstr = ptstr + '<div class="panel' + ' ' + panel_style + '">';
        ptstr = ptstr + '<div class="panel-heading">';
        ptstr = ptstr + isSexStr;
        ptstr = ptstr + '<span class="pt-box-id">' + WardCode + '-' + BedId + '</span>';
        ptstr = ptstr + isDnrStr;
        ptstr = ptstr + '</div>';
        ptstr = ptstr + '<div class="panel-body">';
        ptstr = ptstr + '<div class="col-md-2">';
        ptstr = ptstr + iscoptstr;
        ptstr = ptstr + '</div>';
        ptstr = ptstr + '<div class="col-md-8 pt-box-content">';
        ptstr = ptstr + isptstr;
        ptstr = ptstr + '</div>';
        ptstr = ptstr + '</div>';
        ptstr = ptstr + '</div>';
        ptstr = ptstr + '</div>';
        ptstr = ptstr + '</a>';

        ptstr2 = ptstr2 + '<div class="modal fade" id="' + PatientChartNo + '" role="dialog"name=' + PatientChartNo + '>';
        ptstr2 = ptstr2 + '<div class="modal-dialog">';
        ptstr2 = ptstr2 + '<div class="modal-content">';
        ptstr2 = ptstr2 + '<div class="modal-header pt-showbox-header">';
        ptstr2 = ptstr2 + '<button type="button" class="close" data-dismiss="modal">&times;</button>';
        ptstr2 = ptstr2 + '<p class="modal-title" ' + notShow + ' >';
        ptstr2 = ptstr2 + '<span class="pt-showbox1">' + WardCode + '-' + BedId + '</span>';
        ptstr2 = ptstr2 + '<span class="pt-showbox2">' + PatientNameSecurity + '</span>';
        ptstr2 = ptstr2 + '<span class="pt-showbox3">' + PatientBirthDay + '</span>';
        //ptstr2 = ptstr2 + '<span><i class="fa fa-user pt-showbox4"></i><span>';
        ptstr2 = ptstr2 + isSexStrdel;
        ptstr2 = ptstr2 + '</p>';
        ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '<div class="modal-body pt-showbox5">';
        ptstr2 = ptstr2 + '<ul class="nav nav-pills">';
        ptstr2 = ptstr2 + '<li ' + notShow + '><a data-toggle="tab" href="#' + PatientChartNo + '_1">基本資料</a></li>';
        //ptstr2 = ptstr2 + '<li ' + notShow + '><a data-toggle="pill" href="#' + PatientChartNo + '_2">過敏資料</a></li>';
        //ptstr2 = ptstr2 + '<li ' + notShow + '><a data-toggle="pill" href="#' + PatientChartNo + '_3">醫囑資料</a></li>';
        ptstr2 = ptstr2 + '<li ><a data-toggle="pill" href="#' + PatientChartNo + '_4">待辦事項</a></li>';
        ptstr2 = ptstr2 + '</ul>';
        ptstr2 = ptstr2 + '<div class="tab-content"><br>';
        ptstr2 = ptstr2 + '<div ' + notShow + ' id="' + PatientChartNo + '_1" class="tab-pane fade in active">';
        ptstr2 = ptstr2 + '<p class="text-left">病歷號 : ' + PatientChartNo + '</p>';
        ptstr2 = ptstr2 + '<p class="text-left">主治醫師 : ' + DrvsName + '</p>';
        ptstr2 = ptstr2 + '<p class="text-left">住院醫師 : ' + DrrName + '</p>';
        ptstr2 = ptstr2 + '<p class="text-left">專科護理師 : ' + NspName + '</p>';
        ptstr2 = ptstr2 + '<p class="text-left">護理師 : ' + NurseName + '</p>';
        ptstr2 = ptstr2 + '<p class="text-left">診斷 : ' + DiagDesc + '</p>';
        ptstr2 = ptstr2 + '<p class="text-left">術式 : </p>';
        ptstr2 = ptstr2 + '<p class="text-left">手術1 : ' + OptDesc1 + '</p>';
        ptstr2 = ptstr2 + '<p class="text-left">手術2 : ' + OptDesc2 + '</p>';
        ptstr2 = ptstr2 + '<p class="text-left">手術3 : ' + OptDesc3 + '</p>';
        ptstr2 = ptstr2 + '<p class="text-left">狀態 : ' + DnrStr + ' DNR<br></p>';
        ptstr2 = ptstr2 + '</div>';
        //ptstr2 = ptstr2 + '<div ' + notShow + ' id="' + PatientChartNo + '_2" class="tab-pane fade"><br>';
        //ptstr2 = ptstr2 + '</div>';
        //ptstr2 = ptstr2 + '<div ' + notShow + ' id="' + PatientChartNo + '_3" class="tab-pane fade"><br>';
        //ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '<div id="' + PatientChartNo + '_4" class="' + Focus + '"><br>';
        ptstr2 = ptstr2 + '<div class="Index1">';
        ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '<div class="Index2" style="display: inline-block;width:100%;">';
        ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '<div class="modal-footer">';
        ptstr2 = ptstr2 + '<button type="button" class="btn btn-default pt-showbox6" data-dismiss="modal">關閉</button>';
        ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '</div>';
        ptstr2 = ptstr2 + '</div>';

    });
    $("#pt-list-box").html(ptstr);
    $("#pt-list-box_detail").html(ptstr2);

}

/*==============================================
    手術/檢查  
    =============================================*/

function pt_surgical_examination(xml) {
    var ptstr1 = '', ptstr2 = '', ptstr1_1 = '', ptstr2_1 = '';
    var date = new Date($.now());
    var dateString = (date.getFullYear() + '/'
        + ('0' + (date.getMonth() + 1)).slice(-2)
        + '/' + ('0' + (date.getDate())).slice(-2));

    //手術資訊
    $(xml).find("OPTPatientInfoData").each(function (i) {
        var total = $(xml).find("OPTPatientInfoData").length;
        var SeqNo = $(this).children("SeqNo").text(); // 序號
        var WardBed = $(this).children("WardBed").text();  //房床號
        var OptDate = $(this).children("OptDate").text();  //手術日期
        var PatientNameSecurity = $(this).children("PatientNameSecurity").text();  //病人姓名
        var PatientBirthDay = $(this).children("PatientBirthDay").text();  //病人生日
        var PatientChartNo = $(this).children("PatientChartNo").text();  //病歷號
        var OptName = $(this).children("OptName").text();  //術式
        var OptDoctorName = $(this).children("OptDoctorName").text();  //手術醫師姓名/主治醫師
        var OptAneDesc = $(this).children("OptAneDesc").text();  //麻醉方式
        var OptStatusDesc = $(this).children("OptStatusDesc").text();  //狀態

        var DrvsName = $(this).children("DrvsName").text();  //術者
        var DrvsGSM = $(this).children("DrvsGSM").text();  //主治醫師電話
        var OptDoctorGSM = $(this).children("OptDoctorGSM").text();  //術者電話

        if (OptDate != dateString) {
            ptstr1 = ptstr1 + '<tr data-toggle="modal" data-target="#' + PatientChartNo + OptDoctorName + '">';
            ptstr1 = ptstr1 + '<th>' + SeqNo + '</th>';
            ptstr1 = ptstr1 + '<th>' + OptDate + '</th>';
            ptstr1 = ptstr1 + '<th>' + WardBed + '</th>';
            ptstr1 = ptstr1 + '<th>' + PatientNameSecurity + '</th>';
            ptstr1 = ptstr1 + '<th>' + PatientBirthDay + '</th>';
            ptstr1 = ptstr1 + '<th>' + PatientChartNo + '</th>';
            ptstr1 = ptstr1 + '<th>' + OptName + '</th>';
            ptstr1 = ptstr1 + '<th>' + DrvsName + '</th>';
            ptstr1 = ptstr1 + '<th>' + OptDoctorName + '</th>';
            ptstr1 = ptstr1 + '<th>' + OptAneDesc + '</th>';
            ptstr1 = ptstr1 + '<th>' + OptStatusDesc + '</th>';
            ptstr1 = ptstr1 + '</tr>';
        } else {
            ptstr1 = ptstr1 + '<tr data-toggle="modal" data-target="#' + PatientChartNo + OptDoctorName + '">';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + SeqNo + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + OptDate + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + WardBed + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + PatientNameSecurity + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + PatientBirthDay + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + PatientChartNo + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + OptName + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + DrvsName + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + OptDoctorName + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + OptAneDesc + '</th>';
            ptstr1 = ptstr1 + '<th class="OPTPatient">' + OptStatusDesc + '</th>';
            ptstr1 = ptstr1 + '</tr>';
        }

        //detail
        ptstr1_1 = ptstr1_1 + '<div class="modal fade" id="' + PatientChartNo + OptDoctorName + '" role="dialog">';
        ptstr1_1 = ptstr1_1 + '<div class="modal-dialog">';
        ptstr1_1 = ptstr1_1 + '<div class="modal-content">';
        ptstr1_1 = ptstr1_1 + '<div class="modal-header pt-showbox-header">';
        ptstr1_1 = ptstr1_1 + '<button type="button" class="close" data-dismiss="modal">&times;</button>';
        ptstr1_1 = ptstr1_1 + '<p class="modal-title">';
        ptstr1_1 = ptstr1_1 + '<span class="pt-showbox1">' + WardBed + '</span>';
        ptstr1_1 = ptstr1_1 + '<span class="pt-showbox2">' + PatientNameSecurity + '</span>';
        ptstr1_1 = ptstr1_1 + '<span class="pt-showbox3">' + PatientBirthDay + '</span>';
        ptstr1_1 = ptstr1_1 + '</p>';
        ptstr1_1 = ptstr1_1 + '</div>';
        ptstr1_1 = ptstr1_1 + '<div class="modal-body">';
        ptstr1_1 = ptstr1_1 + '<div class="row infoimg-center">';
        ptstr1_1 = ptstr1_1 + '<div class="col-md-12">';
        ptstr1_1 = ptstr1_1 + '<p class="text-left">主治醫師 : ' + OptDoctorName + '</p>';
        ptstr1_1 = ptstr1_1 + '<p class="text-left">主治醫師電話 : ' + DrvsGSM + '</p>';
        ptstr1_1 = ptstr1_1 + '<p class="text-left">術者 : ' + DrvsName + '</p>';
        ptstr1_1 = ptstr1_1 + '<p class="text-left">術者電話 : ' + OptDoctorGSM + '</p>';
        ptstr1_1 = ptstr1_1 + '</div>';
        ptstr1_1 = ptstr1_1 + '</div>';
        ptstr1_1 = ptstr1_1 + '</div>';
        ptstr1_1 = ptstr1_1 + '<div class="modal-footer">';
        ptstr1_1 = ptstr1_1 + '<button type="button" class="btn btn-default pt-showbox6" data-dismiss="modal">關閉</button>';
        ptstr1_1 = ptstr1_1 + '</div>';
        ptstr1_1 = ptstr1_1 + '</div>';
        ptstr1_1 = ptstr1_1 + '</div>';
        ptstr1_1 = ptstr1_1 + '</div>';

    });
    $("#OPTPatientInfoData").html(ptstr1);
    $("#pt-OPT-box_detail1").html(ptstr1_1);


    //檢查資訊
    $(xml).find("CheckPatientInfoData").each(function (i) {
        var total = $(xml).find("CheckPatientInfoData").length;
        var SeqNo = $(this).children("SeqNo").text(); // 序號
        var WardBed = $(this).children("WardBed").text();  //房床號
        var CheckDate = $(this).children("CheckDate").text();  //排檢日期
        var PatientNameSecurity = $(this).children("PatientNameSecurity").text();  //病人姓名
        var PatientBirthDay = $(this).children("PatientBirthDay").text();  //病人生日
        var PatientChartNo = $(this).children("PatientChartNo").text();  //病歷號
        var CheckName = $(this).children("CheckName").text();  //排檢內容
        var CheckPlace = $(this).children("CheckPlace").text();  //地點
        var OptAneDesc = $(this).children("OptAneDesc").text();  //主治
        var PatientStatus = $(this).children("PatientStatus").text();  //狀態

        var DrvsName = $(this).children("DrvsName").text();  //主治醫師
        var DrvsGSM = $(this).children("DrvsGSM").text();  //主治醫師電話

        if (CheckDate != dateString) {
            ptstr2 = ptstr2 + '<tr data-toggle="modal" data-target="#' + PatientChartNo + '">';
            ptstr2 = ptstr2 + '<th>' + SeqNo + '</th>';
            ptstr2 = ptstr2 + '<th>' + CheckDate + '</th>';
            ptstr2 = ptstr2 + '<th>' + WardBed + '</th>';
            ptstr2 = ptstr2 + '<th>' + PatientNameSecurity + '</th>';
            ptstr2 = ptstr2 + '<th>' + PatientBirthDay + '</th>';
            ptstr2 = ptstr2 + '<th>' + PatientChartNo + '</th>';
            ptstr2 = ptstr2 + '<th>' + CheckName + '</th>';
            ptstr2 = ptstr2 + '<th>' + CheckPlace + '</th>';
            ptstr2 = ptstr2 + '<th>' + DrvsName + '</th>';
            ptstr2 = ptstr2 + '<th>' + PatientStatus + '</th>';
            ptstr2 = ptstr2 + '</tr>';
        } else {
            ptstr2 = ptstr2 + '<tr data-toggle="modal" data-target="#' + PatientChartNo + '">';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + SeqNo + '</th>';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + CheckDate + '</th>';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + WardBed + '</th>';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + PatientNameSecurity + '</th>';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + PatientBirthDay + '</th>';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + PatientChartNo + '</th>';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + CheckName + '</th>';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + CheckPlace + '</th>';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + DrvsName + '</th>';
            ptstr2 = ptstr2 + '<th class="OPTPatient">' + PatientStatus + '</th>';
            ptstr2 = ptstr2 + '</tr>';
        }

        //detail
        ptstr2_1 = ptstr2_1 + '<div class="modal fade" id="'+ PatientChartNo +'" role="dialog">';
        ptstr2_1 = ptstr2_1 + '<div class="modal-dialog">';
        ptstr2_1 = ptstr2_1 + '<div class="modal-content">';
        ptstr2_1 = ptstr2_1 + '<div class="modal-header pt-showbox-header">';
        ptstr2_1 = ptstr2_1 + '<button type="button" class="close" data-dismiss="modal">&times;</button>';
        ptstr2_1 = ptstr2_1 + '<p class="modal-title">';
        ptstr2_1 = ptstr2_1 + '<span class="pt-showbox1">' + WardBed + '</span>';
        ptstr2_1 = ptstr2_1 + '<span class="pt-showbox2">' + PatientNameSecurity + '</span>';
        ptstr2_1 = ptstr2_1 + '<span class="pt-showbox3">' + PatientBirthDay + '</span>';
        ptstr2_1 = ptstr2_1 + '</p>';
        ptstr2_1 = ptstr2_1 + '</div>';
        ptstr2_1 = ptstr2_1 + '<div class="modal-body">';
        ptstr2_1 = ptstr2_1 + '<div class="row infoimg-center">';
        ptstr2_1 = ptstr2_1 + '<div class="col-md-12">';
        ptstr2_1 = ptstr2_1 + '<p class="text-left">主治醫師 : ' + DrvsName + '</p>';
        ptstr2_1 = ptstr2_1 + '<p class="text-left">主治醫師電話 : ' + DrvsGSM + '</p>';
        ptstr2_1 = ptstr2_1 + '</div>';
        ptstr2_1 = ptstr2_1 + '</div>';
        ptstr2_1 = ptstr2_1 + '</div>';
        ptstr2_1 = ptstr2_1 + '<div class="modal-footer">';
        ptstr2_1 = ptstr2_1 + '<button type="button" class="btn btn-default pt-showbox6" data-dismiss="modal">關閉</button>';
        ptstr2_1 = ptstr2_1 + '</div>';
        ptstr2_1 = ptstr2_1 + '</div>';
        ptstr2_1 = ptstr2_1 + '</div>';
        ptstr2_1 = ptstr2_1 + '</div>';

    });
    $("#CheckPatientInfoData").html(ptstr2);
    $("#pt-OPT-box_detail2").html(ptstr2_1);

}

/*==============================================
    團隊照護 
    =============================================*/

function kmuh_team_care(xml) {
    var ptstr1 = '', ptstr2 = '', ptstr3 = '', ptstr4 = '', ptstr5 = '';
    //主治醫師檢索
    $(xml).find("VSCarePatientInfo").children("CarePatientInfo").each(function (i) {
        var CareUserId = $(this).find("CareUserId").text();  //照護者id
        var CareUserName = $(this).find("CareUserName").text();  //照護者姓名
        var CareUserGSM = $(this).find("CareUserGSM").text();  //照護者手機
        var WardBed = $(this).find("WardBed").append(" ● ").text();  //房床號

        ptstr1 = ptstr1 + '<tr>';
        ptstr1 = ptstr1 + '<th>' + CareUserName + '</th>';
        ptstr1 = ptstr1 + '<th>' + CareUserGSM + '</th>';
        ptstr1 = ptstr1 + '<th>' + WardBed + '</th>';
        ptstr1 = ptstr1 + '</tr>';
    });
    $("#VSCarePatientInfo").html(ptstr1);

    //住院醫師檢索
    $(xml).find("RCarePatientInfo").children("CarePatientInfo").each(function (i) {
        var CareUserId = $(this).find("CareUserId").text();  //照護者id
        var CareUserName = $(this).find("CareUserName").text();  //照護者姓名
        var CareUserGSM = $(this).find("CareUserGSM").text();  //照護者手機
        var WardBed = $(this).find("WardBed").append(" ● ").text();  //房床號

        ptstr2 = ptstr2 + '<tr>';
        ptstr2 = ptstr2 + '<th>' + CareUserName + '</th>';
        ptstr2 = ptstr2 + '<th>' + CareUserGSM + '</th>';
        ptstr2 = ptstr2 + '<th>' + WardBed + '</th>';
        ptstr2 = ptstr2 + '</tr>';

    });
    $("#RCarePatientInfo").html(ptstr2);


    //專科護理師檢索
    $(xml).find("NSPCarePatientInfo").children("CarePatientInfo").each(function (i) {
        var CareUserId = $(this).find("CareUserId").text();  //照護者id
        var CareUserName = $(this).find("CareUserName").text();  //照護者姓名
        var CareUserGSM = $(this).find("CareUserGSM").text();  //照護者手機
        var WardBed = $(this).find("WardBed").append(" ● ").text();  //房床號

        ptstr3 = ptstr3 + '<tr>';
        ptstr3 = ptstr3 + '<th>' + CareUserName + '</th>';
        ptstr3 = ptstr3 + '<th>' + CareUserGSM + '</th>';
        ptstr3 = ptstr3 + '<th>' + WardBed + '</th>';
        ptstr3 = ptstr3 + '</tr>';

    });
    $("#NSPCarePatientInfo").html(ptstr3);


    //主護理師檢索
    $(xml).find("NURSECarePatientInfo").children("CarePatientInfo").each(function (i) {
        var CareUserId = $(this).find("CareUserId").text();  //照護者id
        var CareUserName = $(this).find("CareUserName").text();  //照護者姓名
        var CareUserGSM = $(this).find("CareUserGSM").text();  //照護者手機
        var WardBed = $(this).find("WardBed").append(" ● ").text();  //房床號

        ptstr4 = ptstr4 + '<tr>';
        ptstr4 = ptstr4 + '<th>' + CareUserName + '</th>';
        ptstr4 = ptstr4 + '<th>' + CareUserGSM + '</th>';
        ptstr4 = ptstr4 + '<th>' + WardBed + '</th>';
        ptstr4 = ptstr4 + '</tr>';

    });
    $("#NURSECarePatientInfo").html(ptstr4);


    //病房檢索
    $(xml).find("StationPatientInfo").children("PatientData").each(function (i) {
        var WardBed = $(this).children("WardBed").text();  //房床號
        var DrVSCode = $(this).children("DrVSCode").text();  //主治醫師id
        var DrVSName = $(this).children("DrVSName").text();  //主治醫師姓名
        var DrRCode = $(this).children("DrRCode").text();  //住院醫師id
        var DrRName = $(this).children("DrRName").text();  //住院醫師姓名
        var NSPCode = $(this).children("NSPCode").text();  //專科護理師id
        var NSPName = $(this).children("NSPName").text();  //專科護理姓名
        var NurseCode = $(this).children("NurseCode").text();  //主護理師id
        var NurseName = $(this).children("NurseName").text();  //主護理師姓名

        ptstr5 = ptstr5 + '<tr>';
        ptstr5 = ptstr5 + '<th>' + WardBed + '</th>';
        ptstr5 = ptstr5 + '<th>' + DrVSName + '</th>';
        ptstr5 = ptstr5 + '<th>' + DrRName + '</th>';
        ptstr5 = ptstr5 + '<th>' + NSPName + '</th>';
        ptstr5 = ptstr5 + '<th>' + NurseName + '</th>';
        ptstr5 = ptstr5 + '</tr>';
        
    });
    $("#StationPatientInfo").html(ptstr5);

}

/*==============================================
     聯絡電話
    =============================================*/

function kmuh_contact(xml) {
    var ptstr1 = '';
    $(xml).find("ContactInfo").each(function (i) {
        var total = $(xml).find("ContactInfo").length;
        var ContactName = $(this).find("ContactName").text();  //名稱
        var TelNo = $(this).find("TelNo").text();  //值班電話

        ptstr1 = ptstr1 + '<tr>';
        ptstr1 = ptstr1 + '<th>' + ContactName + '</th>';
        ptstr1 = ptstr1 + '<th>' + TelNo + '</th>';
        ptstr1 = ptstr1 + '</tr>';
    });

    $("#ContactTelInfo").html(ptstr1);

}

/*==============================================
     輪椅查詢啟動
    =============================================*/

function ChairOn() {
    $(".BodyAll").show();
    $(".IsChairOn").addClass("ShowZindex");
}

/*==============================================
     推床查詢啟動
    =============================================*/

function BedOn() {
    $(".BodyAll").show();
    $(".IsOnBed").addClass("ShowZindex");
}

function BodyAll() {
    $(".pt-index-map").find("div").removeClass("ShowZindex");
    $(".BodyAll").hide();
}