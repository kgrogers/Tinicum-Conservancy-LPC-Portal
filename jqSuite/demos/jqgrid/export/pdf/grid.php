<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// LOAD lang file
require_once(ABSPATH.'/php/tcpdf/config/lang/eng.php');
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT OrderID, OrderDate, CustomerID, ShipName, Freight FROM orders';
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
    "sortname"=>"OrderID",
	"caption"=>"PDF export"
));
// Change some property of the field(s)
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y"),
    "search"=>false
    )
);
$grid->setColProperty("ShipName", array("width"=>"250"));
$grid->setColProperty("Freight", array("label"=>"Test", "align"=>"right"));
if($grid->oper == "pdf") {
	$grid->setPdfOptions(array(
		// reprint table header 
		"shrink_cell"=>false,
		"reprint_grid_header" => true
		));
}

// Enable navigator
$grid->navigator = true;
// Enable excel export
$grid->setNavOptions('navigator', array("pdf"=>true,"add"=>false,"edit"=>false,"del"=>false,"view"=>false, "excel"=>false));
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

