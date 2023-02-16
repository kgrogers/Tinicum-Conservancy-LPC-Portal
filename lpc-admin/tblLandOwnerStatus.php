<?php
require_once '/var/www/llpc.tinicumconservancy.org/public_html/jqSuite/jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// include the datepicker
// require_once ABSPATH."php/jqCalendar.php";

// Connection to the server
$conn = new PDO(DB_DSN."tinicum",DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Create the jqGrid instance
$grid8 = new jqGridRender($conn);
// $grid8->debug=true;
$grid8->showError = true;
// Write the SQL Query
$grid8->SelectCommand = "
    select StatusID,
           Status
    from tblLandOwnerStatus";

// Set output format to json
$grid8->dataType = 'json';
$grid8->table='tblLandOwnerStatus';
$grid8->setPrimaryKeyID('StatusID');
// Let the grid create the model
$grid8->setColModel();


// Set some grid options
$grid8->setGridOptions(array(
    "rowNum"=>23,
    "rowList"=>array(30,40,50),
    "sortname"=>"StatusID",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblLandOwnerStatus"
));
// Set the url from where we obtain the data
$grid8->setUrl('tblLandOwnerStatus.php');

// Change some property of the field(s)
$grid8->setColProperty("StatusID", array("width"=>"20","editable"=>false,"hidden"=>false));
// $grid8->setColProperty("LandOwnerNotes", array("label"=>"Landowner Notes","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea", "editoptions"=>array("rows"=>5, "cols"=>80),"editrules"=>array("required"=>true)));
// $grid8->setColProperty("Status", array("required"=>true));
// $grid8->setColProperty("CurrentlyAssignedTo", array("required"=>true));

// Enable filter toolbar searching
$grid8->toolbarfilter = true;
// Enable operation search
$grid8->setFilterOptions(array("searchOperators"=>true));

// $grid8->setSelect("ContactMode", "SELECT DISTINCT ContactModeID, ContactMode as CM FROM tblContactModes ORDER BY 2", false, true, false, array(""=>"Select Mode..."));
// $grid8->setSelect("LandOwnerID", "SELECT DISTINCT LandOwnerID, LandOwner FROM tblLandOwners as LO ORDER BY 2", false, true, false, array(""=>"Select land owner..."));
// $sql = "
    // SELECT distinct LpcMemberID,
           // case
               // when LastName = 'UNASSIGNED' then LastName
               // else concat(FirstName,' ',LastName)
           // end CB
    // FROM tblLpcMembers
    // ORDER BY 2";
// $grid8->setSelect("ContactedBy", $sql, false, true, false, array(""=>""));
$grid8->navigator = true;
$grid8->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>true,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid8->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid8->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid8->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid8->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#viewhdgrid8').html('StatusID '+$('#v_StatusID span').html());
}
TB;
$grid8->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid8->renderGrid('#grid8','#grid8-toppager',true, null, null, true, false);
