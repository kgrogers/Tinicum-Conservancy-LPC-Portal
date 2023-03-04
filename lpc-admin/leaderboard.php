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
             tblLandOwners lo
        where cn.ContactedBy = lm.LpcMemberID and
              cn.LandOwnerID = lo.LandOwnerID
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
			"dataName" => "ContactedBy",
			"width"    => 240),
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
    
	$pivot->renderPivot("#grid12","#pager12", true, null, true, true);
?>