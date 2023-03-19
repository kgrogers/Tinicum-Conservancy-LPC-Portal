<?php
    @session_start();
    require_once '/var/www/llpc.tinicumconservancy.org/public_html/jqSuite/jq-config.php';
    require_once '/var/www/llpc.tinicumconservancy.org/public_html/jqSuite/php/PHPSuito/jqPivotGrid.php';
    require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
    
    // Connection to the server
    $db = new PDO(DB_DSN."tinicum",DB_USER,DB_PASSWORD);
    // Tell the db that we use utf-8
    $db->query("SET NAMES utf8");

    $pivot = new jqPivotGrid($db);
    $pivot->SelectCommand = "
        SELECT concat(lm.LastName,', ',lm.FirstName) as ContactedBy,
            lt.LpcDescription,
            lo.LandOwner,
            cn.ContactDate,
            case cn.ContactMode
                when 1 then 'Phone'
                when 2 then 'Other'
                when 3 then 'eMail'
                when 4 then 'Face to Face'
                when 5 then 'Mail'
                when 6 then 'Background'
                when 7 then 'no contact info'
                when 8 then 'N/A'
            end as ContactMode,
            count(*) as total
        FROM tblContactNotes cn,
            tblLpcMembers lm,
            tblLandOwners lo,
            tblLpcType lt
        where cn.ContactedBy = lm.LpcMemberID and
            cn.LandOwnerID = lo.LandOwnerID and
            lm.LPC = lt.LpcID
        GROUP BY cn.ContactedBy,
                cn.LandOwnerID,
                cn.ContactDate,
                cn.ContactMode";
    
    $pivot->setData('leaderboard.php');
    $pivot->setGridOptions(array(
        "rowNum"           => 1000,
        "width"            => "auto",
        "height"           => "auto",
        "sortname"         => "ContactDate",
        "sortorder"        => "desc",
        "rowList"          => array(20,40,60,80,1000),
        "altRows"          => true,
        "caption"      => "Leader Board",
        "groupingView" => array("groupCollapse" => true)
    ));

	$pivot->setxDimension(array(
        array(
            "dataName"  => "LpcDescription",
            "width"     => 240),
		array(
			"dataName" => "ContactedBy",
			"width"    => 80),
		array(
			"dataName"  => "LandOwner",
			"width"     => 115),
        array(
            "dataName" => "ContactDate",
            "width"    => 115)
	));
	$pivot->setyDimension(array(
		array(
			"dataName" => "ContactMode")
	));
	$pivot->setaggregates(array(
		array(
			"member"      => "total",
			"aggregator"  => "sum",
			"width"       => 80,
			"label"       => "Total",
			"formatter"   => "integer",
			"align"       => "right",
			"summaryType" => "sum")			
	));
	$pivot->setPivotOptions(array(
		"rowTotals" => true,
        "colTotals" => true
	));
    $pivot->navigator = true;
    $pivot->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"view"=>false,"search"=>false,"reload"=>false));
    $buttonoptions = array(
        "#pager12",array(
            "caption"=>"Csv",
            "title"=>"Local Export to CSV",
            "onClickButton"=>"js: function(){
                jQuery('#grid12').jqGrid('exportToCsv');
            }"
        )
    );
    $pivot->callGridMethod("#grid12", "navButtonAdd", $buttonoptions,1000);

    $buttonoptions = array(
        "#pager12",array(
            "caption"=>"Excel",
            "title"=>"Local Export to Escel",
            "onClickButton"=>"js: function(){
                jQuery('#grid12').jqGrid('exportToExcel');
            }"
        )
    );
    $pivot->callGridMethod("#grid12", "navButtonAdd", $buttonoptions,500);
    $buttonoptions = array(
        "#pager12", array(
            "caption"=>"Pdf",
            "title"=>"Local Export to PDF",
            "onClickButton"=>"js: function(){
                jQuery('#grid12').jqGrid('exportToPdf');
            }"
        )
    );
    
$expandRow = <<<EXPAND
    setTimeout(function() {
        groupRows = $('#grid12').find('tr.jqgroup');
        for (x=0; x< groupRows.length; x++) {
            if (groupRows[x].id.indexOf('ghead_0_') >= 0) {
                $('#grid12').jqGrid('groupingToggle', groupRows[x].id);
                $('#'+groupRows[x].id+' td').first().css('font-weight','bold').css('font-size','large');
            }
        }
    }, 500)
EXPAND;
    $pivot->callGridMethod("#grid12", "navButtonAdd", $buttonoptions,500);
    $pivot->setGridEvent('gridComplete',$expandRow);

	$pivot->renderPivot("#grid12","#pager12", true, null, true, true);
?>