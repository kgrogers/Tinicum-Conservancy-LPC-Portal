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
$grid5 = new jqGridRender($conn);
// $grid5->debug=true;
$grid5->showError = true;
// Write the SQL Query
$grid5->SelectCommand = "
    select WatershedID,
           WatershedAbrev,
           Watershed,
           WatershedNotes
    from tblWatersheds";

// Set output format to json
$grid5->dataType = 'json';
$grid5->table='tblWatersheds';
$grid5->setPrimaryKeyID('WatershedID');
// Let the grid create the model
$grid5->setColModel();


// Set some grid options
$grid5->setGridOptions(array(
    "rowNum"=>23,
    "rowList"=>array(30,40,50),
    "sortname"=>"WaterShedAbrev",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblWatershed"
));
// Set the url from where we obtain the data
$grid5->setUrl('tblWatersheds.php');

// Change some property of the field(s)
$grid5->setColProperty("WatershedID", array("editable"=>false,"hidden"=>true));
// $grid5->setColProperty("LandOwnerNotes", array("label"=>"Landowner Notes","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea", "editoptions"=>array("rows"=>5, "cols"=>80),"editrules"=>array("required"=>true)));
// $grid5->setColProperty("Status", array("required"=>true));
// $grid5->setColProperty("CurrentlyAssignedTo", array("required"=>true));

// Enable filter toolbar searching
$grid5->toolbarfilter = true;
// Enable operation search
$grid5->setFilterOptions(array("searchOperators"=>true));

// $grid5->setSelect("ContactMode", "SELECT DISTINCT ContactModeID, ContactMode as CM FROM tblContactModes ORDER BY 2", false, true, false, array(""=>"Select Mode..."));
// $grid5->setSelect("LandOwnerID", "SELECT DISTINCT LandOwnerID, LandOwner FROM tblLandOwners as LO ORDER BY 2", false, true, false, array(""=>"Select land owner..."));
// $sql = "
    // SELECT distinct LpcMemberID,
           // case
               // when LastName = 'UNASSIGNED' then LastName
               // else concat(FirstName,' ',LastName)
           // end CB
    // FROM tblLpcMembers
    // ORDER BY 2";
// $grid5->setSelect("ContactedBy", $sql, false, true, false, array(""=>""));
$grid5->navigator = true;
$grid5->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>true,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid5->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid5->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid5->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid5->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#viewhdgrid5').html('WatershedID '+$('#v_WatershedID span').html());
}
TB;
$grid5->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid5->renderGrid('#grid5','#grid5-toppager',true, null, null, true, false);
