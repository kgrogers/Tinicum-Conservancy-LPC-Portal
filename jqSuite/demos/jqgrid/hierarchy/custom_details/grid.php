<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
jqGridDB::query($conn,"SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT EmployeeID, LastName, FirstName, Title FROM employees';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "height"=>250,
    "rowList"=>array(10,20,30),
    "sortname"=>"EmployeeID"
));
// Set the url from where we get the data
$grid->setSubGridGrid('details.php');
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

