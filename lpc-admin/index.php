<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        $signedIn['loggedin'] = 'no';
        echo "<h2>You are not authorized to view this page</h2>";
        exit();
    } else {
        $signedIn = $_SESSION;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Contact Notes</title>
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
        <script type="text/javascript">         
            $.jgrid.no_legacy_api = true;
            $.jgrid.useJSON = true;
            $.jgrid.defaults.width = "700";
        </script>
        <script src="/jqSuite/js/jquery-ui.min.js" type="text/javascript"></script>
        <script>
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
        <!--    <div id="memcreds" class="mtab" style="display:none;">
                <?php include("tblMemberCreds.php"); ?>
            </div> -->
        </div>
    </body>
</html>
