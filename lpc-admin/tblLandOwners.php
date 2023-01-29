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
$grid2 = new jqGridRender($conn);
// $grid2->debug=true;
$grid2->showError = true;
// Write the SQL Query
$grid2->SelectCommand = "
    select lo.LandownerID,
           lo.LandOwner,
           lo.LandOwnerNotes,
           ls.Status,
           concat('(',lt.LPC,') ',lm.FirstName, ' ', lm.LastName) CurrentlyAssignedTo,
           lo.LandOwnerAddress1,
           lo.LandOwnerAddress2,
           lo.LandOwnerCity,
           lo.LandOwnerState,
           lo.LandOwnerZip,
           lo.HowToContact,
           lo.MailingSalutation,
           lo.AddressedTo
    from tblLandOwners lo,
         tblLpcMembers lm,
         tblLandOwnerStatus ls,
         tblLpcType lt
    where lo.CurrentlyAssignedTo = lm.LpcMemberID and
          lo.Status = ls.StatusID and
          lm.LPC = lt.LpcID";

// Set output format to json
$grid2->dataType = 'json';
$grid2->table='tblLandOwners';
$grid2->setPrimaryKeyID('LandOwnerID');
// Let the grid create the model
$grid2->setColModel();


// Set some grid options
$grid2->setGridOptions(array(
    "rowNum"=>23,
    "rowList"=>array(30,40,50),
    "sortname"=>"Landowner",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblLandOwners"
));
// Set the url from where we obtain the data
$grid2->setUrl('tblLandOwners.php');

// Change some property of the field(s)
$grid2->setColProperty("LandownerID", array("editable"=>false,"hidden"=>false));
$grid2->setColProperty("LandOwner", array("required"=>true));
$grid2->setColProperty("LandOwnerNotes", array("label"=>"Landowner Notes","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea", "editoptions"=>array("rows"=>5, "cols"=>80),"editrules"=>array("required"=>false)));
$grid2->setColProperty("Status", array("required"=>true));
$grid2->setColProperty("CurrentlyAssignedTo", array("required"=>true,"editable"=>true));

// Enable filter toolbar searching
$grid2->toolbarfilter = true;
// Enable operation search
$grid2->setFilterOptions(array("searchOperators"=>true));

/* TODO
    1. Check Add form before submit and make sure LandOwner field isn't a duplicate.
*/
$grid2->setSelect("Status", "SELECT StatusID, Status as CM FROM tblLandOwnerStatus ORDER BY 2", false, true, false, array(""=>""));
// $sql = "
    // SELECT distinct LpcMemberID,
           // case
               // when LastName = 'UNASSIGNED' then LastName
               // else concat(FirstName,' ',LastName)
           // end CB
    // FROM tblLpcMembers
    // where MemberInactive = 0
    // ORDER BY 2";
    $sql = "
        select o.LpcMemberID,
               concat('(',t.LPC,') ',o.FirstName,' ',o.LastName) LpcMember
        from tblLpcMembers o,
             tblLpcType t
        where o.MemberInactive = 0 and
              o.LPC = t.LpcID
        ORDER BY o.LastName";
$grid2->setSelect("CurrentlyAssignedTo", $sql, false, true, false, array(""=>""));
$grid2->navigator = true;
$grid2->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>true,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid2->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid2->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid2->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid2->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#v_LandOwnerNotes').css({'height':'80px','white-space':'break-spaces'});
    jQuery('#viewhdgrid2').html('LandownerID '+$('#v_LandownerID span').html());
}
TB;
$grid2->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid2->renderGrid('#grid2','#grid2-toppager',true, null, null, true, false);
