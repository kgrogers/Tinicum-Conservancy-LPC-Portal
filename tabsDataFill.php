<?php
    @session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != 'yes') {
        return false;
    }
    
    $mLPC = (int)$_SESSION['mLPC'];
    $lndonrID = $_POST['loid'];
    $checked = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>';
    // $unchecked = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-slash-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M11.354 4.646a.5.5 0 0 0-.708 0l-6 6a.5.5 0 0 0 .708.708l6-6a.5.5 0 0 0 0-.708z"/></svg>';
    $unchecked = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';

    include "sqlPDO.php";
    $db = opendb('MySQL','tinicum');
    
    $contactnoteshtml = '';
    $sql = "
        select c.ContactNoteID,
               c.ContactDate,
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
        $contactnoteshtml .= '<tr >
                                <td id="'.$row['ContactNoteID'].'">'.$row['ContactDate'].'</td>
                                <td>'.$row['ContactedBy'].'</td>
                                <td>'.$row['ContactMode'].'</td>
                                <td>'.$row['ContactNote'].'</td>
                                <td>'.$row['NextStep'].'</td>
                              </tr>';
    }
    if (isset($_POST['notesOnly'])) {
        print json_encode(array("contactnoteshtml"=>$contactnoteshtml));
        exit();
    }
    $parcelshtml = '';
    if ($_SESSION['permission'] == 'root' or $_SESSION['permission'] == 'user') {
        $sql = "
            select p.ParcelNum,
                p.Acres,
                p.DeededTo,
                p.ParcelRoadNum,
                p.ParcelRoad,
                p.ParcelCity,
                p.ParcelState,
                p.ParcelZip,
                l.LandUse,
                w.Watershed,
                p.ContiguousParcels,
                p.GasLease,
                p.DisqualifyingUses,
                pt.LpcDescription,
                lo.Status
            from tblParcels p,
                tblWatersheds w,
                tblLpcType pt,
                tblLandUses l,
                tblLandOwners lo
            where p.LandOwnerID = :loid and
                p.WatershedID = w.WatershedID and
                p.LPC = pt.LpcID and
                p.LandUse = l.LandUseID and
                p.LandOwnerID = lo.LandOwnerID";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(':loid' => $lndonrID));
    } else {
        $sql = "
            select p.ParcelNum,
                p.Acres,
                p.DeededTo,
                p.ParcelRoadNum,
                p.ParcelRoad,
                p.ParcelCity,
                p.ParcelState,
                p.ParcelZip,
                p.LandUse,
                w.Watershed,
                p.ContiguousParcels,
                p.GasLease,
                p.DisqualifyingUses,
                pt.LpcDescription,
                lo.Status
            from tblParcels p,
                tblWatersheds w,
                tblLpcType pt,
                tblLandOwners lo
            where p.LandOwnerID = :loid and
                p.LPC = :mLPC and
                p.WatershedID = w.WatershedID and
                p.LPC = pt.LpcID and
                p.LandOwnerID = lo.LandOwnerID";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(':loid' => $lndonrID, ':mLPC' => $mLPC));
    }
        $sql = "select count(*) from tblParcels where ParcelNum = :parcelnum";
        $pnum = $db->prepare($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $pnum->execute(array(":parcelnum"=>$row['ParcelNum']));
            $numrows = $pnum->fetchColumn();
            $row['ContiguousParcels'] == 1 ? $row['ContiguousParcels'] = $checked : $row['ContiguousParcels'] = $unchecked;
            $row['GasLease'] == 1 ? $row['GasLease'] = $checked : $row['GasLease'] = $unchecked;
            $row['DisqualifyingUses'] == 1 ? $row['DisqualifyingUses'] = $checked : $row['DisqualifyingUses'] = $unchecked;
            if ($row['Status'] != 4) {
                $parcelshtml .= "<tr><td>".$row['ParcelNum']."</td><td>".$row['Acres']."</td><td>".$row['DeededTo']."</td><td>".$row['ParcelRoadNum']."</td><td>".$row['ParcelRoad']."</td><td>".$row['ParcelCity']."</td><td>".$row['ParcelState']."</td><td>".$row['ParcelZip']."</td><td>".$row['LandUse']."</td><td>".$row["Watershed"]."</td><td>".$row['ContiguousParcels']."</td><td>".$row['GasLease']."</td><td>".$row['DisqualifyingUses']."</td><td>".$row['LpcDescription']."</td></tr>";
            } else {
                if ($numrows != 1) {
                    $parcelshtml .= "<tr class='bg-danger bg-opacity-25'><td>".$row['ParcelNum']."</td><td>".$row['Acres']."</td><td>".$row['DeededTo']."</td><td>".$row['ParcelRoadNum']."</td><td>".$row['ParcelRoad']."</td><td>".$row['ParcelCity']."</td><td>".$row['ParcelState']."</td><td>".$row['ParcelZip']."</td><td>".$row['LandUse']."</td><td>".$row["Watershed"]."</td><td>".$row['ContiguousParcels']."</td><td>".$row['GasLease']."</td><td>".$row['DisqualifyingUses']."</td><td>".$row['LpcDescription']."</td></tr>";
                } else {
                    $parcelshtml .= "<tr><td>".$row['ParcelNum']."</td><td>".$row['Acres']."</td><td>".$row['DeededTo']."</td><td>".$row['ParcelRoadNum']."</td><td>".$row['ParcelRoad']."</td><td>".$row['ParcelCity']."</td><td>".$row['ParcelState']."</td><td>".$row['ParcelZip']."</td><td>".$row['LandUse']."</td><td>".$row["Watershed"]."</td><td>".$row['ContiguousParcels']."</td><td>".$row['GasLease']."</td><td>".$row['DisqualifyingUses']."</td><td>".$row['LpcDescription']."</td></tr>";
                }
            }
        }

        $mailaddrhtml = '';
        $sql = "
            select AddressedTo,
                   LandOwnerAddress1,
                   LandOwnerAddress2,
                   LandOwnerCity,
                   LandOwnerState,
                   LandOwnerZip,
                   concat(LandOwnerAddress1,' ',LandOwnerAddress2,' ',LandOwnerCity,', ',LandOwnerState,' ',LandOwnerZip) fulladdress
            from tblLandOwners
            where LandOwnerID = :loid";
        $stmt = $db->prepare($sql);
        $stmt->execute(array(':loid' => $lndonrID));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $mailaddrhtml .= "<tr><td>".$row['AddressedTo']."</td><td>".$row['LandOwnerAddress1']."</td><td>".$row['LandOwnerAddress2']."</td><td>".$row['LandOwnerCity']."</td><td>".$row['LandOwnerState']."</td><td>".$row['LandOwnerZip']."</td><td>".$row['fulladdress']."</td></tr>";
        }
    
    print(json_encode(array(
        "contactnoteshtml"=>$contactnoteshtml,
        "parcelshtml"=>$parcelshtml,
        "mailaddrhtml"=>$mailaddrhtml
    )));
?>
