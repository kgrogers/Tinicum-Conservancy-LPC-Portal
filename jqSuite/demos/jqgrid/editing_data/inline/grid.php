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
$grid->SelectCommand = 'SELECT EmployeeID, FirstName, LastName, BirthDate FROM employees';
// Set the table to where you add the data
$grid->table = 'employees';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
$grid->addCol(array(
    "name"=>"actions",
    "formatter"=>"actions",
    "editable"=>false,
    "sortable"=>false,
    "resizable"=>false,
    "fixed"=>true,
    "width"=>60,
    "formatoptions"=>array("keys"=>true)
    ), "first");
$grid->setColProperty('EmployeeID', array("editable"=>false));
$grid->setColProperty('BirthDate', 
        array("formatter"=>"date","formatoptions"=>array("reformatAfterEdit"=>true, "srcformat"=>"Y-m-d H:i:s", "newformat"=>"Y-m-d")));
// Set some grid options
$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"EmployeeID"
));
// Date formatting and settings - editing
$grid->setDbDate('Y-m-d');
$grid->setDbTime('Y-m-d H:i:s');

//User date see formatter data. Birthdate is a defined as datetime
$grid->setUserDate('Y-m-d');
// the same as formatter
$grid->setUserTime('Y-m-d');
// serching date
$grid->datearray = array('EmployeeID');
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

