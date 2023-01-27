<?php
require_once '/var/www/llpc.tinicumconservancy.org/public_html/jqSuite/jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// include the datepicker
// require_once ABSPATH."php/jqCalendar.php";

// Connection to the server
$conn = new PDO(DB_DSN."tinicum",DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Create the jqGrid instance
$grid10 = new jqGridRender($conn);
// $grid10->debug=true;
$grid10->showError = true;
// Write the SQL Query
$grid10->SelectCommand = "
    select c.LpcMemberID,
           concat(m.Firstname, ' ', m.LastName) LpcMember,
           c.username,
           c.password,
           c.permission
    from tblMemberCreds c,
         tblLpcMembers m
    where c.LpcMemberID = m.LpcMemberID
    order by m.LastName";

// Set output format to json
$grid10->dataType = 'json';
$grid10->table='tblMemberCreds';
$grid10->setPrimaryKeyID('LpcMemberID');
// Let the grid create the model
$grid10->setColModel();


// Set some grid options
$grid10->setGridOptions(array(
    "rowNum"=>23,
    "rowList"=>array(30,40,50),
    // "sortname"=>"LpcDescription",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblMemberCreds"
));
// Set the url from where we obtain the data
$grid10->setUrl('tblMemberCreds.php');

// Change some property of the field(s)
$grid10->setColProperty("LpcMemberID", array("editable"=>false,"hidden"=>true));
// $grid10->setColProperty("LandOwnerNotes", array("label"=>"Landowner Notes","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea", "editoptions"=>array("rows"=>5, "cols"=>80),"editrules"=>array("required"=>true)));
// $grid10->setColProperty("Status", array("required"=>true));
// $grid10->setColProperty("CurrentlyAssignedTo", array("required"=>true));

// Enable filter toolbar searching
$grid10->toolbarfilter = true;
// Enable operation search
$grid10->setFilterOptions(array("searchOperators"=>true));

// $grid10->setSelect("ContactMode", "SELECT DISTINCT ContactModeID, ContactMode as CM FROM tblContactModes ORDER BY 2", false, true, false, array(""=>"Select Mode..."));
// $grid10->setSelect("LandOwnerID", "SELECT DISTINCT LandOwnerID, LandOwner FROM tblLandOwners as LO ORDER BY 2", false, true, false, array(""=>"Select land owner..."));
// $sql = "
    // SELECT distinct LpcMemberID,
           // case
               // when LastName = 'UNASSIGNED' then LastName
               // else concat(FirstName,' ',LastName)
           // end CB
    // FROM tblLpcMembers
    // ORDER BY 2";
// $grid10->setSelect("ContactedBy", $sql, false, true, false, array(""=>""));
$grid10->navigator = true;
$grid10->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>true,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid10->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid10->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid10->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid10->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#viewhdgrid10').html('LpcMemberID '+$('#v_LpcMemberID span').html());
}
TB;
$grid10->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid10->renderGrid('#grid10','#grid10-toppager',true, null, null, true, false);
