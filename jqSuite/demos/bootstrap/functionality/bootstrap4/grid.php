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
$grid->SelectCommand = 'SELECT OrderID, Freight, OrderDate, ShipCity  FROM orders';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"OrderID"
));
// Change some property of the field(s)
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y"),
    "search"=>false
    )
);
// Enable navigator
$grid->navigator = true;
// Enable excel export
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>true,"view"=>true));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#pager",
    array("caption"=>"Pdf", "title"=>"Export to Pdf", "onClickButton"=>"js: function(){
		jQuery('#grid').jqGrid('excelExport',{tag:'pdf', url:'grid.php'});}"
	)
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
