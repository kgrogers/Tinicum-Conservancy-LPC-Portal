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
$grid1 = new jqGridRender($conn);
// $grid1->debug=true;
$grid1->showError = true;
// Write the SQL Query
$grid1->SelectCommand = "
    select c.ContactNoteID,
           lo.LandOwner LandOwnerID,
           c.ContactDate,
           case
               when m.LastName = 'UNASSIGNED' then 'UNASSIGNED'
               else concat(m.FirstName, ' ', m.LastName)
           end ContactedBy,
           cm.ContactMode,
           c.ContactNote,
           c.NextStep
    from tblContactNotes c,
         tblLpcMembers m,
         tblContactModes cm,
         tblLandOwners lo
    where c.ContactedBy = m.LpcMemberID and
          c.LandOwnerId = lo.LandOwnerID and
          c.ContactMode = cm.ContactModeID
    order by lo.Landowner asc,
             c.ContactDate desc";

// Set output format to json
$grid1->dataType = 'json';
$grid1->table='tblContactNotes';
$grid1->setPrimaryKeyID('ContactNoteID');
// Let the grid create the model
$grid1->setColModel();


// Set some grid options
$grid1->setGridOptions(array(
    "rowNum"=>23,
    "rowList"=>array(30,40,50),
    // "sortname"=>"Landowner,ContactDate",
    // "sortorder"=>"asc",
    "hoverrows"=>true,
    "altRows"=>true,
    "height"=>"600",
    "autowidth"=>true,
    "toppager"=>true,
    "caption"=>"tblContactNotes"
));
// Set the url from where we obtain the data
$grid1->setUrl('tblContactNotes.php');

// Change some property of the field(s)
$grid1->setColProperty("ContactDate", array("label"=>"Contact Date","editrules"=>array("required"=>true),"formatter"=>"date","searchoptions"=>array("sopt"=>array("eq","ne","le","lt","ge","gt"))));
$grid1->setColProperty("ContactNoteID", array("editable"=>false,"hidden"=>false));
$grid1->setColProperty("LandOwnerID", array("label"=>"LandOwner","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"editrules"=>array("required"=>true)));
$grid1->setColProperty("ContactNote", array("label"=>"Contact Note","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea", "editoptions"=>array("rows"=>5, "cols"=>80),"editrules"=>array("required"=>true)));
$grid1->setColProperty("ContactMode", array("lable"=>"Contact Mode","editrules"=>array("required"=>true)));
$grid1->setColProperty("ContactedBy", array("label"=>"Contacted By","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"editrules"=>array("required"=>true)));
$grid1->setColProperty("NextStep", array("width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea","editoptions"=>array("rows"=>5, "cols"=>80)));
/* 
$myformat = <<<WR
function MyFormatter (cellValue, options, rowdata){
    var cellHtml = '<div class="mylongdata">' + cellValue + '</div>';
    return cellHtml;
}
WR;

$grid1->setJSCode($myformat); 
$grid1->setColProperty("ContactNote", array('formatter'=>'js:MyFormatter'));
 */

// Set the datepicker
$grid1->setDatepicker( "ContactDate" );

// Enable filter toolbar searching
$grid1->toolbarfilter = true;
// Enable operation search
$grid1->setFilterOptions(array("searchOperators"=>true));

$grid1->setSelect("ContactMode", "SELECT DISTINCT ContactModeID, ContactMode as CM FROM tblContactModes ORDER BY 2", false, true, false, array(""=>"Select Mode..."));
$grid1->setSelect("LandOwnerID", "SELECT DISTINCT LandOwnerID, LandOwner FROM tblLandOwners as LO ORDER BY 2", false, true, false, array(""=>"Select land owner..."));
$sql = "
    SELECT distinct LpcMemberID,
           case
               when LastName = 'UNASSIGNED' then LastName
               else concat(FirstName,' ',LastName)
           end CB
    FROM tblLpcMembers
    ORDER BY 2";
$grid1->setSelect("ContactedBy", $sql, false, true, false, array(""=>""));
$grid1->navigator = true;
$grid1->setNavOptions('navigator', array("excel"=>false,"add"=>true,"edit"=>true,"del"=>true,"view"=>true, "search"=>true, "cloneToTop"=>true));
$grid1->setNavOptions('edit',array("height"=>"auto","dataheight"=>"auto","width"=>700,"closeAfterEdit"=>true));
$grid1->setNavOptions('add',array("height"=>"auto","dataheight"=>"auto","width"=>"auto","closeAfterAdd"=>true));
$grid1->setNavOptions('view',array("top"=>30,"left"=>30,"height"=>"auto","dataheight"=>"auto","width"=>600,"labelswidth"=>"20%"));

//Trigger toolbar with custom button
$search = <<<SEARCH
jQuery("#searchtoolbar").click(function(){
    jQuery('#grid')[0].triggerToolbar();
    return false;
});
SEARCH;
$grid1->setJSCode($search);

$txtbx = <<<TB
function(formid) {
    jQuery('#v_ContactNote').css({'height':'80px','white-space':'break-spaces'});
    jQuery('#v_NextStep').css({'height':'80px','white-space':'break-spaces'});
    jQuery('#viewhdgrid1').html('ContactNoteID '+$('#v_ContactNoteID span').html());
}
TB;
$grid1->setNavEvent('view','beforeShowForm',$txtbx);
// Enjoy
$grid1->renderGrid('#grid1','#grid1-toppager',true, null, null, true, false);
