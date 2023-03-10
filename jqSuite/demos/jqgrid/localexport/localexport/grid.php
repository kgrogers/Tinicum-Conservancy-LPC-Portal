<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT OrderID, RequiredDate, ShipName, ShipCity, Freight FROM orders WHERE OrderID <=10400';
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
	"rowNum"=>10,
	"rowList"=>array(10,20,130),
	"sortname"=>"OrderID",
	"rowTotal"=>-1,
	"loadonce"=>true
));
// Enable footerdata an tell the grid to obtain it from the request
$grid->setGridOptions(array("footerrow"=>true,"userDataOnFooter"=>true));
// Change some property of the field(s)
$grid->setColProperty("RequiredDate", array("formatter"=>"date","formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")));
// At end call footerData to put total  label
$grid->callGridMethod('#grid', 'footerData', array("set",array("ShipCity"=>"Total:")));
// Set which parameter to be sumarized
$summaryrows = array("Freight"=>array("Freight"=>"SUM"));

// Enable navigator
$grid->navigator = true;
// Enable excel export
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "search"=>false, "refresh"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#pager",
    array("caption"=>"Csv", "title"=>"Local Export to CSV", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('exportToCsv');}"
	)
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);
$buttonoptions = array("#pager",
    array("caption"=>"Excel", "title"=>"Local Export to Escel", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('exportToExcel');}"
	)
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);
$buttonoptions = array("#pager",
    array("caption"=>"Pdf", "title"=>"Local Export to PDF", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('exportToPdf');}"
	)
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);


$grid->renderGrid('#grid','#pager',true,$summaryrows , null, true,true);

