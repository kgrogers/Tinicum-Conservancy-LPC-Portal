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
$grid->SelectCommand = 'SELECT OrderID, OrderDate, CustomerID, Freight, ShipName, ShipCity, ShipCountry FROM orders';
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model from SQL query
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set alternate background using altRows property
$grid->setGridOptions(array(
    "rowNum"=>10,
    "sortname"=>"OrderID",
    "rowList"=>array(10,20,50),
    "height"=>'auto'
));
// Change some property of the field(s)
$grid->setColProperty("OrderID", array("label"=>"ID", "width"=>60));
$grid->setColProperty("OrderDate", array(
    "formatter"=>"date",
    "formatoptions"=>array("srcformat"=>"Y-m-d H:i:s","newformat"=>"m/d/Y")
    )
);
// Enjoy
// Set grouping header using setJSCode method
$grouping = <<< GROUP
jQuery("#grid").jqGrid('setGroupHeaders',{
	"useColSpanStyle":false,
	"groupHeaders" : [
		{ "startColumnName":'OrderID', "numberOfColumns":2, "titleText":'Order Info' },
		{ "startColumnName":'ShipName', "numberOfColumns":3, "titleText":'Shipping Details' }
	]
});
GROUP;
$grid->setJSCode($grouping);
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

