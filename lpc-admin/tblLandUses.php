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
$grid6 = new jqGridRender($conn);
// $grid6->debug=true;
$grid6->showError = true;
// Write the SQL Query
$grid6->SelectCommand = "
    select LandUseID,
           LandUse
    from tblLandUses";

// Set output format to json
$grid6->dataType = 'json';
$grid6->table='tblLandUses';
$grid6->setPrimaryKeyID('LandUseID');
// Let the grid create the model
$grid6->setColModel();


// Set some grid options
$grid6->setGridOptions(array(
    "rowNum"=>23,
    "rowList"=>array(30,40,50),
    "sortname"=>"LandUseID",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblLandUses"
));
// Set the url from where we obtain the data
$grid6->setUrl('tblLandUses.php');

// Change some property of the field(s)
$grid6->setColProperty("LandUseID", array("width"=>"20","editable"=>false,"hidden"=>false));

// Enable filter toolbar searching
$grid6->toolbarfilter = true;

// Enable operation search
$grid6->setFilterOptions(array("searchOperators"=>true));

$grid6->navigator = true;
$grid6->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>false,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid6->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid6->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid6->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid6->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#viewhdgrid6').html('LandUseID '+$('#v_LandUseID span').html());
}
TB;
$grid6->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid6->renderGrid('#grid6','#grid6-toppager',true, null, null, true, false);
