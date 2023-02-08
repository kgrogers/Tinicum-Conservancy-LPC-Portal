<?php
    @session_start();
    $memid = $_SESSION['LpcMemberID'];
    $LandOwnerID = $_SESSION['LandOwnerID'];
    $mLPC = (int)$_SESSION['mLPC'];

    require('mc_table1.php');
    require('sqlPDO.php');

    $db = opendb('MySQL','tinicum');
    // Get Member's contact information
    $sql = "
        select m.FirstName,
               m.LastName,
               t.LPC
        from tblLpcMembers m,
             tblLpcType t
        where m.LpcMemberID = :memberID and
              m.LPC = t.LpcID";
    $memberInfo = $db->prepare($sql);
    
    // Get Landowner Data
    $sql = "
        select l.LandOwnerID,
            concat(m.FirstName,' ',m.LastName,' (',p.LPC,')') CurrentlyAssignedTo,
            l.LandOwner,
            l.LandOwnerNotes,
            s.Status,
            l.HowToContact,
            l.LandOwnerAddress1,
            l.LandOwnerAddress2,
            l.LandOwnerCity,
            l.LandOwnerState,
            l.LandOwnerZip
        from tblLandOwners l,
            tblLandOwnerStatus s,
            tblLpcMembers m,
            tblLpcType p
        where l.LandOwnerID = :LandOwnerID and
            l.Status = s.StatusID and
            l.CurrentlyAssignedTo = m.LpcMemberID and
            m.LPC = p.LpcID";
    $landownerInfo = $db->prepare($sql);
    
    // Get parcel information for specific landowner
    // Note: ContiguousParcels,GasLease and DisqualifyingUses are booleen: 0 = false and 1 = true
    $sql = "
        select p.ParcelNum,
               p.Acres,
               p.DeededTo,
               p.ParcelRoadNum,
               p.ParcelRoad,
               p.ParcelCity,
               p.ParcelState,
               p.ParcelZip,
               w.Watershed,
               p.ContiguousParcels,
               p.GasLease,
               p.DisqualifyingUses
        from tblParcels p,
             tblWatersheds w
        where p.LandownerID = :loid and
              p.WatershedID = w.WatershedID and
              p.LPC = :mLPC
        order by p.Acres desc";
    $parcels = $db->prepare($sql);
    
    // Get contact notes for specific landowner
    $sql = "
        select n.ContactDate,
               l.FirstName,
               l.LastName,
               t.LPC,
               n.ContactNote,
               n.NextStep,
               m.ContactMode
        from tblContactNotes n,
             tblContactModes m,
             tblLpcMembers l,
             tblLpcType t
        where n.LandOwnerID = :loid and
              n.ContactMode = m.ContactModeID and
              l.LpcMemberID = n.ContactedBy and
              l.LPC = t.LpcID
        order by ContactDate desc";
    $cn = $db->prepare($sql);

    // Build report for specific member's landowners
    $memberInfo->execute(array(":memberID"=>$memid));
    $member = $memberInfo->fetch(PDO::FETCH_ASSOC);
    
    $landownerInfo->execute(array(":LandOwnerID"=>$LandOwnerID));
    $lo = $landownerInfo->fetchall(PDO::FETCH_ASSOC);
    // if (count($lo) == 0) {
        // print("<h2><b>You do not have any Landowners assigned to you</b></h2>");
        // exit;
    // }
    
    $pdf = new PDF_MC_Table('P','mm','Letter');
    $pdf->AliasNbPages();
    $pdf->SetMargins(5,5,5);

    $title = $lo[0]['LandOwner'];
    $header = array();
    if ($lo[0]['LandOwnerAddress2']!="") {
        $header[] = array(
            $lo[0]['Status'],
            $member['FirstName']." ".$member['LastName']." (".$member['LPC'].")",
            $lo[0]['LandOwnerAddress1']." ".$lo[0]['LandOwnerAddress2']." ".chr(149)." ".$lo[0]['LandOwnerCity'].', '.$lo[0]['LandOwnerState']." ".$lo[0]['LandOwnerZip']
        );
    } else {
        $header[] = array(
            $lo[0]['Status'],
            $member['FirstName']." ".$member['LastName']." (".$member['LPC'].")",
            $lo[0]['LandOwnerAddress1'].' '.chr(149).' '.$lo[0]['LandOwnerCity'].', '.$lo[0]['LandOwnerState']." ".$lo[0]['LandOwnerZip']
        );
    }
    $header[] = $lo[0]['HowToContact'];
    $header[] = $lo[0]['LandOwnerNotes'];
    
    // $row['LandOwnerID'] = 16;
    $parcels->execute(array(":loid"=>$lo[0]['LandOwnerID'],"mLPC"=>$mLPC));
    $parcelInfo = $parcels->fetchall(PDO::FETCH_ASSOC);
    
    $parcelData = array();
    foreach ($parcelInfo as $prow) {
        $parcelData[] = array(
            'ParcelNum'         => $prow['ParcelNum'],
            'Acres'             => $prow['Acres'],
            'DeededTo'          => $prow['DeededTo'],
            'ParcelLoc'         => $prow['ParcelRoadNum']." ".$prow['ParcelRoad']." ".chr(149)." ".$prow['ParcelCity']." ".$prow['ParcelState'].", ".$prow['ParcelZip'],
            'Watershed'         => $prow['Watershed'],
            'ContiguousParcels' => $prow['ContiguousParcels'],
            'GasLease'          => $prow['GasLease'],
            'DisqualifyingUses' => $prow['DisqualifyingUses']
        );
    };
    
    // Get contact notes
    $cn->execute(array(":loid"=>$lo[0]['LandOwnerID']));
    $cnInfo = $cn->fetchall(PDO::FETCH_ASSOC);
    $contactNotes = array();
    foreach ($cnInfo as $row) {
        $contactNotes[] = array(
            "ContactDate"=>$row['ContactDate'],
            "ContactedBy" => $row['FirstName']." ".$row['LastName']." (".$row['LPC'].")",
            "ContactNote"=>$row['ContactNote'],
            "NextStep"=>$row['NextStep'],
            "ContactMode"=>$row['ContactMode']
        );
    };
    // Header variables
    $h_status = $header[0][0];
    $h_assignedto = $lo[0]['CurrentlyAssignedTo'];
    $h_mailingaddress = $header[0][2];
    $h_howtocontact = $header[1];
    $h_parcelnotes = $header[2];
    
    $pdf->AddPage('L','letter',0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetWidths(array(48,170,50));
    if (count($contactNotes) == 0) {
        $pdf->SetFont('Arial','B', 20);
        $pdf->SetTextColor(255,192,203);
        $pdf->Cell(240,20,'No contact notes for this parcel',0,0,'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0,0,0);
        // continue;
    }
    for($i=0;$i<count($contactNotes);$i++) {
        $pdf->Row(array(
            $contactNotes[$i]['ContactDate']."\n".$contactNotes[$i]['ContactedBy']."\n".$contactNotes[$i]['ContactMode'],
            $contactNotes[$i]['ContactNote'],
            $contactNotes[$i]['NextStep']
        ));
    }

    $pdf->Output();
?>
