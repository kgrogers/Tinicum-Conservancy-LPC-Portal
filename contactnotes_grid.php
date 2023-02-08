<?php
    require_once '/var/www/html/jqSuite/jq-config.php';
    require_once ABSPATH."php/PHPSuito/jqGrid.php";
    require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
    $conn = new PDO(DB_DSN."tinicum",DB_USER,DB_PASSWORD);
    $conn->query("SET NAMES utf8");
    
    $grid =  new jqGridRender($conn);
    $search = jqGridUtils::GetParam('_search','false');
    if($search == 'true') {
        $loid = jqGridUtils::GetParam('loid',0);
        $_GET['_search'] = 'false';
        $grid->SelectCommand = "
            select ContactDate,
                   case
                       when FirstName = 'UNASSIGNED' then FirstName
                       else concat(FirstName, ' ', LastName, ' (', left(LPC,2), ')')
                    end ContactedBy,
                    ContactMode,
                    ContactNote,
                    NextStep
            from tblContactNotes,
                 tblLpcMembers
            where tblContactNotes.ContactedBy = tblLpcMembers.LpcMemberID and
                  LandOwnerID = ".$loid;
    } else {
        $grid->SelectCommand = "
            select ContactDate,
                   case
                       when FirstName = 'UNASSIGNED' then FirstName
                       else concat(FirstName, ' ', LastName, ' (', left(LPC,2), ')')
                    end ContactedBy,
                    ContactMode,
                    ContactNote,
                    NextStep
            from tblContactNotes,
                 tblLpcMembers
            where tblContactNotes.ContactedBy = tblLpcMembers.LpcMemberID and
                  LandOwnerID = 0";
    }
    $grid->setPrimaryKeyId("ContactNoteID");
    $grid->serialKey = false;

    $grid->dataType = 'json';
    $grid->setColModel();
    $grid->setUrl('contactnotes_grid.php');
    $grid->setGridOptions(array(
        "rowNum"=>16,
        "rowList"=>array(16,36,52),
        "sortname"=>"ContactDate",
        "sortorder"=>"desc",
        "height"=>"auto",
        "width"=>"auto"
    ));
    $grid->setColProperty('ContactNote',array("width"=>281));
    $grid->navigator = true;
    $grid->setNavOptions('navigator', array("search"=>false, "excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>true,"refresh"=>false));
    $grid->setNavOptions('view', array("width"=>415,"height"=>240));
    $grid->renderGrid('#gridc','#pagerc',true, null, null, true,true);
    $conn = null;
?>
