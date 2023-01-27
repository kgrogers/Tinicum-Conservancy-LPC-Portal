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
$grid3 = new jqGridRender($conn);
// $grid3->debug=true;
$grid3->showError = true;
// Write the SQL Query
$grid3->SelectCommand = "
    select m.LpcMemberID,
           m.FirstName,
           m.LastName,
           m.Phone,
           m.eMail,
           t.LpcDescription,
           m.Notes,
           case
               when MemberInactive = 0 then 'Active'
               else 'Inactive'
           end MemberInactive
    from tblLpcMembers m,
         tblLpcType t
    where m.LPC = t.LpcID";

// Set output format to json
$grid3->dataType = 'json';
$grid3->table='tblLpcmembers';
$grid3->setPrimaryKeyID('LpcMemberID');
// Let the grid create the model
$grid3->setColModel();


// Set some grid options
$grid3->setGridOptions(array(
    "rowNum"=>40,
    "rowList"=>array(30,40,50),
    "sortname"=>"LastName,FirstName",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblLpcMembers"
));
// Set the url from where we obtain the data
$grid3->setUrl('tblLpcMembers.php');

// Change some property of the field(s)
$grid3->setColProperty("LpcMemberID", array("editable"=>false,"hidden"=>true));
$grid3->setColProperty("FirstName", array("label"=>"First Name", "required"=>true));
$grid3->setColProperty("LastName", array("label"=>"Last Name", "required"=>true));
$grid3->setColProperty("Phone", array("required"=>true));
$grid3->setColProperty("eMail", array("required"=>true));
$grid3->setColProperty("LPC", array("required"=>true));
$grid3->setColProperty("Notes", array("label"=>"Notes","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea", "editoptions"=>array("rows"=>5, "cols"=>80)));
$grid3->setColProperty("MemberInactive", array("label"=>"Active Status","required"=>true));

// Enable filter toolbar searching
$grid3->toolbarfilter = true;
// Enable operation search
$grid3->setFilterOptions(array("searchOperators"=>true));

// $grid3->setSelect("ContactMode", "SELECT DISTINCT ContactModeID, ContactMode as CM FROM tblContactModes ORDER BY 2", false, true, false, array(""=>"Select Mode..."));
// $grid3->setSelect("LandOwnerID", "SELECT DISTINCT LandOwnerID, LandOwner FROM tblLandOwners as LO ORDER BY 2", false, true, false, array(""=>"Select land owner..."));
// $sql = "
    // SELECT distinct LpcMemberID,
           // case
               // when LastName = 'UNASSIGNED' then LastName
               // else concat(FirstName,' ',LastName)
           // end CB
    // FROM tblLpcMembers
    // ORDER BY 2";
// $grid3->setSelect("ContactedBy", $sql, false, true, false, array(""=>""));
$grid3->navigator = true;
$grid3->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>true,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid3->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid3->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid3->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid3->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#v_Notes').css({'height':'80px','white-space':'break-spaces'});
    jQuery('#viewhdgrid3').html('LpcMemberID '+$('#v_LpcMemberID span').html());
}
TB;
$grid3->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid3->renderGrid('#grid3','#grid3-toppager',true, null, null, true, false);
