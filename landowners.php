<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        return false;
    }
    $permission = $_POST['permission'];
    $oMemId = -1;
    switch ($permission) {
        case "root":
            $memberId = 0;
            $oMemId = (int)$_POST['memberID'];
            break;
        case "lpchead":
            $mLPC = (int)$_SESSION['mLPC'];
            $oMemId = (int)$_POST['memberID'];
            $memberID = (int)$_POST['memberID'];
            break;
        case "user":
            $memberID = (int)$_POST['memberID'];
            $oMemId = (int)$_POST['memberID'];
            break;
    }

    include('sqlPDO.php');
    $db = opendb('MySQL','tinicum');
    
    if ($memberID == 0){
        $sql = "
            select LandOwnerID,
                   LandOwner,
                   CurrentlyAssignedTo,
                   Status
            from tblLandOwners
            order by LandOwner";
        $stmt = $db->query($sql);
    } elseif ($permission == 'lpchead') {
            $sql = "
                select distinct lo.LandOwnerID,
                       lo.LandOwner,
                       CurrentlyAssignedTo,
                       lo.Status
                from tblLandOwners lo,
                     tblParcels p
                where lo.LandOwnerID = p.LandOwnerID and
                      p.LPC = :mLPC
                order by LandOwner";
            $stmt = $db->prepare($sql);
            $stmt->execute(array(":mLPC"=>$mLPC));
    } else {
        $sql = "
            select LandOwnerID,
            CurrentlyAssignedTo,
                   LandOwner,
                   Status
            from tblLandOwners
            where CurrentlyAssignedTo = :memid
            order by LandOwner";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(":memid"=>$memberID));
    }
    $rows = $stmt->fetchall(PDO::FETCH_ASSOC);
    $html = "";
    foreach ($rows as $row) {
        if ($row['Status'] != 4 && $row['CurrentlyAssignedTo'] != $oMemId) {
            $html .= "<li class='nav-item'><a class='nav-link' href='#' loid=".$row['LandOwnerID'].">".$row['LandOwner']."</a></li>";
        }
        if ($row['Status'] != 4 && $row['CurrentlyAssignedTo'] == $oMemId) {
            $html .= "<li class='nav-item'><a class='nav-link text-primary' href='#' loid=".$row['LandOwnerID'].">".$row['LandOwner']."</a></li>";
        }
        if ($row['Status'] == 4) {
            $html .= "<li class='nav-item'><a class='nav-link text-danger' href='#' loid=".$row['LandOwnerID'].">".$row['LandOwner']."</a></li>";
        }
    }
    print(json_encode($html));
?>
