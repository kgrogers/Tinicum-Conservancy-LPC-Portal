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
$grid9 = new jqGridRender($conn);
// $grid9->debug=true;
$grid9->showError = true;
// Write the SQL Query
$grid9->SelectCommand = "
    select LpcID,
           LpcPrefix,
           LPC,
           LpcDescription
    from tblLpcType";

// Set output format to json
$grid9->dataType = 'json';
$grid9->table='tblLpcType';
$grid9->setPrimaryKeyID('LpcID');
// Let the grid create the model
$grid9->setColModel();


// Set some grid options
$grid9->setGridOptions(array(
    "rowNum"=>23,
    "rowList"=>array(30,40,50),
    "sortname"=>"LpcID",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblLpcType"
));
// Set the url from where we obtain the data
$grid9->setUrl('tblLpcType.php');

// Change some property of the field(s)
$grid9->setColProperty("LpcID", array("width"=>"40","editable"=>false,"hidden"=>false));

// Enable filter toolbar searching
$grid9->toolbarfilter = true;

// Enable operation search
$grid9->setFilterOptions(array("searchOperators"=>true));

$grid9->navigator = true;
$grid9->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>false,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid9->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid9->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid9->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid9->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#viewhdgrid9').html('LpcID '+$('#v_LpcID span').html());
}
TB;
$grid9->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid9->renderGrid('#grid9','#grid9-toppager',true, null, null, true, false);
