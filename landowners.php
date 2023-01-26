<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        return false;
    }
    switch ($_POST['permission']) {
        case "root":
            $memberID = 0;
            break;
        case "lpchead":
        case "comittee":
        case "user":
            $memberID = (int)$_POST['memberID'];
            break;
    }

    include('sqlPDO.php');
    $db = opendb('MySQL','tinicum');
    
    if ($memberID == 0){
        $sql = "
            select LandOwnerID,
                   LandOwner
            from tblLandOwners
            order by LandOwner";
        $stmt = $db->query($sql);
    } else {
        $sql = "
            select LandOwnerID,
                LandOwner
            from tblLandOwners
            where CurrentlyAssignedTo = :memid
            order by LandOwner";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(":memid"=>$memberID));
    }
    $rows = $stmt->fetchall(PDO::FETCH_ASSOC);
    $html = "";
    foreach ($rows as $row) {
        $html .= "<li class='nav-item'><a class='nav-link' href='#' loid=".$row['LandOwnerID'].">".$row['LandOwner']."</a></li>";
    }
    print(json_encode($html));
?>
