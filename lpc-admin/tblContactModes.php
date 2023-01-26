<?php
require_once '/var/www/html/jqSuite/jq-config.php';
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
$grid7 = new jqGridRender($conn);
// $grid7->debug=true;
$grid7->showError = true;
// Write the SQL Query
$grid7->SelectCommand = "
    select ContactModeID,
           ContactMode
    from tblContactModes";

// Set output format to json
$grid7->dataType = 'json';
$grid7->table='tblContactModes';
$grid7->setPrimaryKeyID('ContactModeID');
// Let the grid create the model
$grid7->setColModel();


// Set some grid options
$grid7->setGridOptions(array(
    "rowNum"=>23,
    "rowList"=>array(30,40,50),
    "sortname"=>"ContactMode",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblContactModes"
));
// Set the url from where we obtain the data
$grid7->setUrl('tblContactModes.php');

// Change some property of the field(s)
$grid7->setColProperty("ContactModeID", array("editable"=>false,"hidden"=>true));
// $grid7->setColProperty("LandOwnerNotes", array("label"=>"Landowner Notes","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea", "editoptions"=>array("rows"=>5, "cols"=>80),"editrules"=>array("required"=>true)));
// $grid7->setColProperty("Status", array("required"=>true));
// $grid7->setColProperty("CurrentlyAssignedTo", array("required"=>true));

// Enable filter toolbar searching
$grid7->toolbarfilter = true;
// Enable operation search
$grid7->setFilterOptions(array("searchOperators"=>true));

// $grid7->setSelect("ContactMode", "SELECT DISTINCT ContactModeID, ContactMode as CM FROM tblContactModes ORDER BY 2", false, true, false, array(""=>"Select Mode..."));
// $grid7->setSelect("LandOwnerID", "SELECT DISTINCT LandOwnerID, LandOwner FROM tblLandOwners as LO ORDER BY 2", false, true, false, array(""=>"Select land owner..."));
// $sql = "
    // SELECT distinct LpcMemberID,
           // case
               // when LastName = 'UNASSIGNED' then LastName
               // else concat(FirstName,' ',LastName)
           // end CB
    // FROM tblLpcMembers
    // ORDER BY 2";
// $grid7->setSelect("ContactedBy", $sql, false, true, false, array(""=>""));
$grid7->navigator = true;
$grid7->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>true,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid7->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid7->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid7->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid7->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#viewhdgrid7').html('ContactModeID '+$('#v_ContactModeID span').html());
}
TB;
$grid7->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid7->renderGrid('#grid7','#grid7-toppager',true, null, null, true, false);
