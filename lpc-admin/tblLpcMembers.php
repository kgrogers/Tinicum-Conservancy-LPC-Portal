<?php
require_once '/var/www/llpc.tinicumconservancy.org/public_html/jqSuite/jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";

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
           t.LpcDescription as LPC,
           m.Notes,
           case
               when MemberInactive = 0 then 'Active'
               else 'Inactive'
           end MemberInactive,
           m.username,
           m.password,
           m.permission
    from tblLpcMembers m,
         tblLpcType t
    where m.LPC = t.LpcID";

// Set output format to json
$grid3->dataType = 'json';
$grid3->table='tblLpcMembers';
$grid3->setPrimaryKeyID('LpcMemberID');
$grid3->serialKey = true;
// Let the grid create the model
$grid3->setColModel();

// Set some grid options
$grid3->setGridOptions(array(
    "rowNum"=>40,
    "rowList"=>array(30,40,50),
    "sortname"=>"LpcMemberID",
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
$grid3->setColProperty("LpcMemberID", array("width"=>"50","key"=>true,"editable"=>false,"hidden"=>false));
$grid3->setColProperty("FirstName", array("label"=>"First Name", "required"=>true));
$grid3->setColProperty("LastName", array("label"=>"Last Name", "required"=>true));
$grid3->setColProperty("Phone", array("required"=>true));
$grid3->setColProperty("eMail", array("required"=>true));
$grid3->setColProperty("LPC", array("label"=>"LPC Description","editrules"=>array("required"=>true)));
$grid3->setColProperty("Notes", array("label"=>"Notes","width"=>"200", "searchoptions"=>array("sopt"=>array('cn','bw','bn','nc')),"edittype"=>"textarea", "editoptions"=>array("rows"=>5, "cols"=>80)));
$grid3->setColProperty("MemberInactive", array("label"=>"Active Status","required"=>true));
$grid3->setColProperty("username",array("label"=>"User Name"));
$grid3->setColProperty("password",array("label"=>"Encrypted Password"));

$cid = jqGridUtils::GetParam('LpcMemberID');
// $cid = $conn->lastInsertId();
// $grid3->setAfterCrudAction("add","insert into tblMemberCreds (LpcMemberID) values(?)",array($cid));

// Enable filter toolbar searching
$grid3->toolbarfilter = true;
// Enable operation search
$grid3->setFilterOptions(array("searchOperators"=>true));

$grid3->setSelect("LPC", "select LpcID,LpcDescription from tblLpcType order by 2", false, true, false);
$grid3->setSelect("MemberInactive", array(0=>'Active',1=>'Inactive'), false, true, false);
$grid3->setSelect("permission", array('user'=>'user','lpchead'=>'lpchead','root'=>'root'), false, true, false);

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
$editBox = <<<EB
    function(formid) {
        jQuery('#Notes').css({'width':'545px'});
        jQuery('#password').css({'width':'470px'});
}
EB;

$grid3->setNavEvent('view','beforeShowForm',$txtbx);
$grid3->setNavEvent('edit','beforeShowForm',$editBox);
// Enjoy
$grid3->renderGrid('#grid3','#grid3-toppager',true, null, null, true, false);
?>

