<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        return false;
    }

    $LpcMemberID = $_POST['LpcMemberID'];
    include('sqlPDO.php');
    $db = opendb('MySQL','tinicum');
    
    $sql = 'select ContactModeID,ContactMode from tblContactModes order by ContactMode';
    
    $contactModes = '<option selected value="">Choose contact mode</option>';
    foreach ($db->query($sql, PDO::FETCH_ASSOC) as $row) {
        $contactModes .= '<option value="'.$row['ContactModeID'].'">'.$row['ContactMode'].'</option>';
    }
    
    $sql = "
        select LpcMemberID,
               FirstName,
               LastName
        from tblLpcMembers
        where MemberInactive = 0
        order by LastName
    ";
    $members = '';
    foreach ($db->query($sql, PDO::FETCH_ASSOC) as $row) {
        if ($row['LpcMemberID'] == $LpcMemberID) {
            $members .= '<option selected value="'.$row['LpcMemberID'].'">'.$row['FirstName'].' '.$row['LastName'].'</option>';
        } else {
            $members .= '<option value="'.$row['LpcMemberID'].'">'.$row['FirstName'].' '.$row['LastName'].'</option>';
        }
    }
    
    print(json_encode(array(
        "contactModes"=>$contactModes,
        "members"=>$members
    )));
?>
