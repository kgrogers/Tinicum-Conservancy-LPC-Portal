<?php
require_once '/var/www/llpc.tinicumconservancy.org/public_html/jqSuite/jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";

$date = new DateTime('now', new DateTimeZone('US/Eastern'));
if ($date->format('I') == 0) {
    $tzoffset = "\-05:00";
} else {
    $tzoffset = "\-04:00";
}

// Connection to the server
$conn = new PDO(DB_DSN."tinicum",DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Create the jqGrid instance
$grid11 = new jqGridRender($conn);
// $grid11->debug=true;
$grid11->showError = true;
// Write the SQL Query
$grid11->SelectCommand = "
    select a.idx,
           concat(m.FirstName,' ',m.LastName) Name,
           activity,
           convert_tz(occurredAt,'+00:00','".$tzoffset."') 'occurredAt (EST)',
           result
    from tblMemberActivity a,
         tblLpcMembers m
    where a.LpcMemberID = m.LpcMemberID";
// Set output format to json
$grid11->dataType = 'json';
$grid11->table='tblMemberActivity';
$grid11->setPrimaryKeyID('idx');
$grid11->serialKey = true;

// Let the grid create the model
$grid11->setColModel();

// Set some grid options
$grid11->setGridOptions(array(
    "rowNum"=>40,
    "rowList"=>array(30,40,50),
    "sortname"=>"idx",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"Member Activity"
));
// Set the url from where we obtain the data
$grid11->setUrl('tblMemberActivity.php');

// Change some property of the field(s)

// Enable filter toolbar searching
$grid11->toolbarfilter = true;

// Enable operation search
$grid11->setFilterOptions(array("searchOperators"=>true));


$grid11->navigator = true;
$grid11->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid11->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

// Enjoy
$grid11->renderGrid('#grid11','#grid11-toppager',true, null, null, true, false);
?>

