<?php
ini_set("display_errors",1);

require_once '../jq-config.php';
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqTreeGrid.php";


// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Create the jqTreeGrid instance
$tree = new jqTreeGrid($conn);

$tree->SelectCommand = "SELECT * FROM nested_category";

// set the table and primary key
$tree->table = 'nested_category';
$tree->setPrimaryKeyId('category_id');
// set tree model and table configuration
$tree->setTreeModel('nested');
$tree->setTableConfig(array('id'=>'category_id', 'left'=>'lft', 'right'=>'rgt'));


// autoloading is disabled
$tree->autoLoadNodes = true;
// collapse all nodes (default)
$tree->expandAll = false;
// show any error (if any ) from server
$tree->showError = true;

$tree->setColModel();

$tree->setUrl('treegrid.php');
$tree->dataType = 'json';
// Some nice setting
$tree->setColProperty('name',array("label"=>"Name", "width"=>170));
$tree->setColProperty('price',array("label"=>"Price", "width"=>90, "align"=>"right"));
$tree->setColProperty('qty_onhand',array("label"=>"Qty", "width"=>90, "align"=>"right"));
$tree->setColProperty('color',array("label"=>"Color", "width"=>100));

// hide the not needed fields
$tree->setColProperty('category_id',array("hidden"=>true,"index"=>"accounts.account_id", "width"=>50));
$tree->setColProperty('lft',array("hidden"=>true));
$tree->setColProperty('rgt',array("hidden"=>true));
$tree->setColProperty('level',array("hidden"=>true));
$tree->setColProperty('uiicon',array("hidden"=>true));

// and finaly set the expand column and height to auto
$tree->setGridOptions(array(
	"ExpandColumn"=>"name",
	"height"=>"auto",
	"rowNum" => 1,
	//"rowList"=>[1,2,3],
	"treeGrid_bigData"=>true,
	"sortname"=>"account_id"
	));

$tree->renderTree('#tree', '#pager', true,null, null, true, true);
?>
