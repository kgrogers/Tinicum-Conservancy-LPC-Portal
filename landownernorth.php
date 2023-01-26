<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        return false;
    }
    
    $lndonrID = $_POST['loid'];
    $_SESSION['LandOwnerID'] = $lndonrID;

    include "sqlPDO.php";
    $db = opendb('MySQL','tinicum');
    $sql = "
        select l.LandOwner 'Land Owner',
               concat(m.FirstName, ' ', m.LastName) 'Assigned To',
               s.Status,
               l.HowToContact 'How To Contact',
               l.LandOwnerNotes 'Land Owner Notes',
               l.MailingSalutation ma_MailingSalutation,
               l.LandOwnerAddress1 ma_LandOwnerAddress1,
               l.LandOwnerAddress2 ma_LandOwnerAddress2,
               l.LandOwnerCity ma_LandOwnerCity,
               l.LandOwnerState ma_LandOwnerState,
               l.LandOwnerZip ma_LandOwnerZip,
               l.AddressedTo ma_AddressedTo
        from tblLandOwners l,
             tblLpcMembers m,
             tblLandOwnerStatus s
        where l.CurrentlyAssignedTo = m.LpcMemberID and
              l.Status = s.StatusID and
              l.LandOwnerID = :loid";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(':loid' => $lndonrID));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(substr($row['Assigned To'],0,10) == "UNASSIGNED") {
        $row['assignedto'] = 'UNASSIGNED';
    }
    
    print(json_encode($row));

 ?>
    