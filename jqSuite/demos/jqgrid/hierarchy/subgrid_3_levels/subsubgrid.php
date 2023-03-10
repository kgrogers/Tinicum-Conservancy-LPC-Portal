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
// Get the needed parameters passed from the main grid
$subtable = jqGridUtils::GetParam("subgrid");
$rowid = jqGridUtils::GetParam("rowid");
if(!$subtable && !$rowid) {
	die("Missed parameters");
}
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = "SELECT OrderID, ProductID, Quantity, UnitPrice FROM order_details WHERE OrderID=?";
// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel(null, array(&$rowid));
// Set the url from where we obtain the data
$grid->setUrl('subsubgrid.php');
// Set some grid options
$grid->setGridOptions(array(
    "width"=>480,
    "rowNum"=>10,
    "sortname"=>"OrderID",
    "height"=>'auto',
    "postData"=>array("subgrid"=>$subtable,"rowid"=>$rowid)));
// Change some property of the field(s)
$grid->navigator = true;
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// Enjoy
$subtable = $subtable."_t";
$pager = $subtable."_p";
$grid->renderGrid($subtable,$pager, true, null, array(&$rowid), true,true);

