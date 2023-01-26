<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        return false;
    }

    // $data = $_POST['newNoteData'];
    $lndonrID = $_POST['LOID'];
    $data = array(
        'LOID' => $_POST['LOID'],
        'contactBy' => $_POST['contactBy'],
        'contactDate' => $_POST['contactDate'],
        'contactMode' => $_POST['contactMode'],
        'contactNote' => $_POST['contactNote'],
        'contactNextStep' => $_POST['contactNextStep']
    );
    
    include 'sqlPDO.php';
    $db = opendb('MySQL','tinicum');
    
    $sql = "
        insert into tblContactNotes
        (LandownerID, ContactedBy, ContactDate, ContactMode, ContactNote, NextStep)
        values (:LOID, :contactBy, :contactDate, :contactMode, :contactNote, :contactNextStep)
    ";
    $stmt = $db->prepare($sql);
    $stmt->execute($data);
    
    $contactnoteshtml = '';
    $sql = "
        select c.ContactDate,
               concat(m.FirstName, ' ', m.LastName) ContactedBy,
               cm.ContactMode,
               c.ContactNote,
               c.NextStep
        from tblContactNotes c,
            tblLpcMembers m,
            tblContactModes cm
        where c.ContactedBy = m.LpcMemberID and
              c.LandOwnerID = :loid and
              c.ContactMode = cm.ContactModeID
        order by c.ContactDate desc, c.ContactNoteID desc";
    $stmt = $db->prepare($sql);
    $stmt->execute(array(':loid' => $lndonrID));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $contactnoteshtml .= "<tr><td>".$row['ContactDate']."</td><td>".$row['ContactedBy']."</td><td>".$row['ContactMode']."</td><td>".$row['ContactNote']."</td><td>".$row['NextStep']."</td></tr>";
    }
    print($contactnoteshtml);
?>
