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
$grid->SelectCommand = 'SELECT CustomerID, CompanyName, ContactName, Phone, City FROM customers';
// Set the table to where you update the data
$grid->table = 'customers';
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
    "sortname"=>"CustomerID"
));
$grid->setColProperty('CustomerID', array("label"=>"ID", "width"=>50));
$selectorder = <<<ORDER
function(rowid, selected)
{
    if(rowid != null) {
        jQuery("#detail").jqGrid('setGridParam',{postData:{CustomerID:rowid}});
        jQuery("#detail").trigger("reloadGrid");
    }
}
ORDER;
$grid->setGridEvent('onSelectRow', $selectorder);

// Clear the data on paging and sorting
$clearorder = <<<CLORDER
function(rowid, selected)
{
    jQuery("#detail").jqGrid('setGridParam',{postData:{CustomerID:null}});
    jQuery("#detail").trigger("reloadGrid");
}
CLORDER;
$grid->setGridEvent('onSortCol', $clearorder);
$grid->setGridEvent('onPaging', $clearorder);
// Enable navigator
$grid->navigator = true;
// Enable only editing
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
$conn = null;
?>
