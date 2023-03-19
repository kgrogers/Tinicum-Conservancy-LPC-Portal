<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        $signedIn['loggedin'] = 'no';
        echo "<h2>You are not authorized to view this page</h2>";
        exit();
    } else {
        $signedIn = $_SESSION;
    }
    include "sqlPDO.php";
    $db = opendb('MySQL','tinicum');
    
    $sql = "
        select LpcDescription
        from tblLpcType
        order by LpcID";
    $LPChtml = array();
    foreach ($db->query($sql) as $row) {
        $LPChtml[strval($row['LpcDescription'])] = $row['LpcDescription'];
    }
    
    $sql = "
        select distinct year(ContactDate) year
        from tblContactNotes
        where year(ContactDate) > 0
        order by 1";
    $Yearhtml = array();
    foreach ($db->query($sql) as $row) {
        $Yearhtml[strval($row['year'])] = $row['year'];
    }
    
    $sql = "
        select distinct month(ContactDate) monthnum,
               date_format(ContactDate,'%M') monthname               
        from tblContactNotes
        where month(ContactDate) > 0
        order by 1";
    $Monthhtml = array();
    foreach ($db->query($sql) as $row) {
        $Monthhtml[strval($row['monthnum'])] = $row['monthname'];
    }


?>
<!DOCTYPE html>
<html>
    <head>
        <title>Administrative Page</title>
        <link rel="stylesheet" type="text/css" media="screen" href="/jqSuite/css/jquery-ui.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="/jqSuite/css/trirand/ui.jqgrid.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="/jqSuite/css/ui.multiselect.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="/css/mtabs.css" />
        <style type="text">
            html, body {
                margin: 0;            /* Remove body margin/padding */
                padding: 0;
                overflow: hidden;    /* Remove scroll bars on browser window */
                font-size: 100%;
            }
            .ui-jqgrid {
                font-size: 14px;
            }
            .ui-jqgrid .ui-jqgrid-btable tbody tr.jqgrow td {
                white-space: normal !important;
                height: auto;
            }
            .mylongdata {
                height: 190px;
                overflow-y: auto;
            }

        </style>
        <script src="/jqSuite/js/jquery.min.js" type="text/javascript"></script>
        <script src="/jqSuite/js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>
        <script src="/jqSuite/js/trirand/jquery.jqGrid.min.js" type="text/javascript"></script>
        <!-- Files for the export ---------------------->
        <script type="text/javascript" language="javascript" src="/js/pivotExport/pdfmake.min.js"></script>
        <script type="text/javascript" language="javascript" src="/js/pivotExport/vfs_fonts.js"></script>
        <script type="text/javascript" language="javascript" src="/js/pivotExport/jszip.min.js"></script>
        <script type="text/javascript">         
            $.jgrid.no_legacy_api = true;
            $.jgrid.useJSON = true;
            $.jgrid.defaults.width = "700";
        </script>
        <script src="/jqSuite/js/jquery-ui.min.js" type="text/javascript"></script>
        <script>
            var LPChtml = <?php print(json_encode($LPChtml)); ?>;
            var Yearhtml = <?php print(json_encode($Yearhtml)); ?>;
            var Monthhtml = <?php print(json_encode($Monthhtml)); ?>;
            
            $(document).ready(function(){
                $(".mtabs-list li a").click(function(e){
                    e.preventDefault();
                });
                
                $(".mtabs-list li").click(function(){
                    var tabid = $(this).find("a").attr("href");
                    $(".mtabs-list li,.mtabs div.mtab").removeClass("active");   // removing active class from tab
                    $(".mtab").hide();   // hiding open tab
                    $(tabid).show();    // show tab
                    $(this).addClass("active"); //  adding active class to clicked tab
                });
                
                $.each(LPChtml, function(val, text) {
                    // console.log("val: "+val+" text: "+text);
                    $('#LPC').append($("<option></option>").attr("value",val).text(text));
                });

                $.each(Yearhtml, function(val, text) {
                    // console.log("val: "+val+" text: "+text);
                    $('#year').append($("<option></option>").attr("value",val).text(text));
                });

                $.each(Monthhtml, function(val, text) {
                    // console.log("val: "+val+" text: "+text);
                    $('#month').append($("<option></option>").attr("value",val).text(text));
                });
                
/***************************************************************************************************************************************/
                function buildCustomSearch( rule_arr, group ){
                    if(group === undefined) {
                        group = "AND";
                    }
                    var ruleGroup = "";
                    if(Array.isArray(rule_arr) && rule_arr.length) 
                    {
                        ruleGroup = "{\"groupOp\":\"" + group + "\",\"rules\":[";
                        var gi=0;
                        $.each(rule_arr,function(i,n){
                            if (gi > 0) {ruleGroup += ",";}
                            ruleGroup += "{\"field\":\"" + n.name + "\",";
                            ruleGroup += "\"op\":\"" + n.oper + "\",";
                            ruleGroup += "\"data\":\"" + n.val.replace(/\\/g,'\\\\').replace(/\"/g,'\\"') + "\"}";
                            gi++;
                        });
                        ruleGroup += "]}";
                        console.log(ruleGroup);
                    }
                    return ruleGroup;
                }
                var grid = $("#grid12");
                $("#do_search").on('click',function(){
                    console.log("Search was clicked - val is "+$("#year").val());
                    var my_fld=[]; 
                    /*
                    *opts : ['eq'=>'equal','ne'=>'not equal','lt'=>'less','le'=>'less or equal','gt'=>'greater','ge'=>'greater or equal','bw'=>'begins with','bn'=>'does not begin with','bt'=>'between','in'=>'is in','ni'=>'is not in','ew'=>'ends with','en'=>'does not end with','cn'=>'contains','nc'=>'does not contain'] 
                    */
                    if ($("#LPC").val() != '%') {
                        my_fld.push({ 
                            name: "LpcDescription", 
                            val : $("#LPC").val(), 
                            oper:"eq"
                        });
                    } else {
                        my_fld.push({ 
                            name: "LpcDescription", 
                            val : $("#LPC").val(), 
                            oper:"ne"
                        });
                    }
                    if ($('#year').val() != '%') {
                        my_fld.push({
                            name: "ContactDate",
                            val : $('#year').val(),
                            oper: "bw"
                        });
                    } else {
                        my_fld.push({
                            name: "ContactDate",
                            val : '-',
                            oper: "cn"
                        });
                    }
                    if ($('#month').val() != '%') {
                        my_fld.push({
                            name: "ContactDate",
                            val : '-'+$('#month').val().padStart(2,0)+'-',
                            oper: "cn"
                        });
                    } else {
                        my_fld.push({
                            name: "ContactDate",
                            val : '-',
                            oper: "cn"
                        });
                    }
                    // console.log(my_fld);
                    var rule = buildCustomSearch( my_fld, "AND");
                    grid.setGridParam({postData:{filters:rule}, search:true}).trigger("reloadGrid");

                    groupRows = $('#grid12').find('tr.jqgroup');
                    for (x=0; x< groupRows.length; x++) {
                        if (groupRows[x].id.indexOf('ghead_0_') >= 0) {
                            $('#grid12').jqGrid('groupingToggle', groupRows[x].id);
                            $('#'+groupRows[x].id+' td').first().css('font-weight','bold').css('font-size','large');
                        }
                    }
                });
/***************************************************************************************************************************************/
            });
        </script>
    </head>
    <body>
        <div class="mtabs">
            <ul class="mtabs-list">
                <li class="active"><a href="#ContactNotes">Contact Notes</a></li>
                <li><a href="#LandOwners">Land Owners</a></li>
                <li><a href="#Parcels">Parcels</a></li>
                <li><a href="#ContactMode">Contact Modes</a></li>
                <li><a href="#LOStatus">Land Owner Status</a></li>
                <li><a href="#Members">LPC Members</a></li>
            <!--    <li><a href="#memcreds">LPC Member Cred</a></li> -->
                <li><a href="#LpcType">LPC Type</a></li>
                <li><a href="#Watershed">Watersheds</a></li>
                <li><a href="#LandUse">Land Uses</a></li>
                <li><a href="#MemberActivity">Member Activity</a></li>
                <li><a href="#leaderboard">Leader Board</a></li>
            </ul>
            <div id="ContactNotes" class="mtab active" style="display:block;">
                <?php include("tblContactNotes.php"); ?>
            </div>
            <div id="LandOwners" class="mtab" style="display:none;">
                <?php include("tblLandOwners.php"); ?>
            </div>
            <div id="Members" class="mtab" style="display:none;">
                <?php include("tblLpcMembers.php"); ?>
            </div>
            <div id="Parcels" class="mtab" style="display:none;">
                <?php include("tblParcels.php"); ?>
            </div>
            <div id="Watershed" class="mtab" style="display:none;">
                <?php include("tblWatersheds.php"); ?>
            </div>
            <div id="LandUse" class="mtab" style="display:none;">
                <?php include("tblLandUses.php"); ?>
            </div>
            <div id="ContactMode" class="mtab" style="display:none;">
                <?php include("tblContactModes.php"); ?>
            </div>
            <div id="LOStatus" class="mtab" style="display:none;">
                <?php include("tblLandOwnerStatus.php"); ?>
            </div>
            <div id="LpcType" class="mtab" style="display:none;">
                <?php include("tblLpcType.php"); ?>
            </div>
            <div id="MemberActivity" class="mtab" style="display:none;">
                <?php include("tblMemberActivity.php"); ?>
            </div>
            <div id="leaderboard" class="mtab" style="display:none;">
                <div id="selectLPC">
                    <form action="#">
                        <label for="LPC">LPC:</label>
                        <select id="LPC" name="LPC">
                            <option value='%'>All</option>
                        </select>
                        <label for="year">Year:</label>
                        <select id="year" name="year">
                            <option value='%'>All</option>
                        </select>
                        <label for="year">Month:</label>
                        <select id="month" name="month">
                            <option value='%'>All</option>
                        </select>
                        <input id="do_search" type="button" value="Search"></input>
                    </form>
                    <br />
                </div>
                <?php include("leaderboard.php"); ?>
            </div>
        </div>
    </body>
</html>
