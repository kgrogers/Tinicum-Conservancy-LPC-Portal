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
$grid4 = new jqGridRender($conn);
// $grid4->debug=true;
$grid4->showError = true;
// Write the SQL Query
$grid4->SelectCommand = "
    select p.ParcelID,
           l.LandOwner,
           w.Watershed,
           p.DeededTo,
           p.ParcelNum,
           p.Acres,
           p.ContiguousParcels,
           p.GasLease,
           p.DisqualifyingUses,
           u.LandUse,
           p.ParcelRoadNum,
           p.ParcelRoad,
           p.ParcelCity,
           p.ParcelState,
           p.ParcelZip
    from tblParcels p,
         tblLandOwners l,
         tblWatersheds w,
         tblLandUses u
    where p.LandOwnerID = l.LandOwnerID and
          p.WatershedID = w.WatershedID and
          p.LandUse = u.LandUseID";

// Set output format to json
$grid4->dataType = 'json';
$grid4->table='tblParcels';
$grid4->setPrimaryKeyID('ParcelID');
// Let the grid create the model
$grid4->setColModel();


// Set some grid options
$grid4->setGridOptions(array(
    "rowNum"=>23,
    "rowList"=>array(30,40,50),
    "sortname"=>"LandOwner",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblParcels"
));
// Set the url from where we obtain the data
$grid4->setUrl('tblParcels.php');

// Change some property of the field(s)
$grid4->setColProperty("ParcelID", array("editable"=>false,"hidden"=>false));
// $grid4->setColProperty("FirstName", array("label"=>"First Name", "required"=>true));
// $grid4->setColProperty("LastName", array("label"=>"Last Name", "required"=>true));
// $grid4->setColProperty("Phone", array("required"=>true));
// $grid4->setColProperty("eMail", array("required"=>true));
// $grid4->setColProperty("LPC", array("required"=>true));
// $grid4->setColProperty("Notes", array("label"=>"Notes","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea", "editoptions"=>array("rows"=>5, "cols"=>80)));
// $grid4->setColProperty("MemberInactive", array("required"=>true));

// Enable filter toolbar searching
$grid4->toolbarfilter = true;
// Enable operation search
$grid4->setFilterOptions(array("searchOperators"=>true));

$grid4->setSelect("WatershedID", "SELECT ws.WatershedID, ws.Watershed FROM tblWatersheds ws ORDER BY 2", false, true, false);
// $grid4->setSelect("LandOwnerID", "SELECT DISTINCT LandOwnerID, LandOwner FROM tblLandOwners as LO ORDER BY 2", false, true, false, array(""=>"Select land owner..."));
// $sql = "
    // SELECT distinct LpcMemberID,
           // case
               // when LastName = 'UNASSIGNED' then LastName
               // else concat(FirstName,' ',LastName)
           // end CB
    // FROM tblLpcMembers
    // ORDER BY 2";
// $grid4->setSelect("ContactedBy", $sql, false, true, false, array(""=>""));
$grid4->navigator = true;
$grid4->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>true,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid4->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid4->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid4->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>800,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid4->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#viewhdgrid4').html('ParcelID '+$('#v_ParcelID span').html());
}
TB;
$grid4->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid4->renderGrid('#grid4','#grid4-toppager',true, null, null, true, false);
