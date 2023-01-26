<?php
date_default_timezone_set('US/Eastern');
require('fpdf/fpdf.php');

class PDF_MC_Table extends FPDF
{
	protected $widths;
	protected $aligns;
    
    function Header() {
        global $skipHeader;
        if ($skipHeader == true) {
            return;
        }
        global $title;
        global $h_status;
        global $h_assignedto;
        global $h_mailingaddress;
        global $h_howtocontact;
        global $h_parcelnotes;
        global $parcelData;
        
        $this->SetFillColor(202,242,120);
        $this->Cell(20,20,'',0,0,'L',1);
        $this->Image('/var/www/llpc.tinicumconservancy.org/public_html/images/tinicum-logo-encircled-TRANSPARENT.png',10,8,15);
        $this->SetFont('Arial','B',20);
        $this->Cell(248,20,$title,0,1,'L',1);

        $this->SetLineWidth(.6);
        $this->SetFillColor(244,252,228);
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(74,99,0);
        $this->Line(5,24,273,24);
        $this->Line(5,33.5,273,33.5);
        $this->Cell(47,8,'Status',0,0,'L',1);
        $this->Cell(90,8,'Currently Assigned To',0,0,'L',1);
        $this->Cell(131,8,'Mailing Address',0,1,'L',1);
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(0,0,0);
        
        $this->Cell(47,7,$h_status,0,0,'L');
        $this->Cell(90,7,$h_assignedto,0,0,'L');
        $this->Cell(110,7,$h_mailingaddress,0,1,'L');
        $this->Cell(7,7,'',0,0);
        $this->Cell(40,7,'How To Contact:',0,0,'L');
        $this->Cell(40,7,$h_howtocontact,0,1,'L');
        $this->Cell(7,7,'',0,0);
        $this->Cell(40,7,'Notes:      ',0,0,'R');
        $this->MultiCell(220,6,$h_parcelnotes,0,'L');
        $this->Ln(2);
        
        $this->SetTextColor(74,99,0);
        $this->Cell(32,7,'Parcel#','B',0,'L',1);
        $this->Cell(20,7,'Acres','B',0,'L',1);
        $this->Cell(36,7,'Deeded To','B',0,'L',1);
        $this->Cell(50,7,'Parcel Location','B',0,'L',1);
        $this->Cell(40,7,'Watershed','B',0,'L',1);
        $this->Cell(34,7,'Contig. Parcels?','B',0,'L',1);
        $this->Cell(26,7,'Gas Lease?','B',0,'L',1);
        $this->Cell(30,7,'Disqual. Uses?','B',1,'L',1);
        
        $this->SetTextColor(0,0,0);
            $y = $this->GetY();
        foreach ($parcelData as $prow) {
            $this->Cell(32,7,$prow['ParcelNum'],0,0,'L');
            $this->Cell(20,7,$prow['Acres'],0,0,'L');
            $x = $this->GetX();
            $this->SetXY($x,$y+.5);
            $this->MultiCell(36,5,$prow['DeededTo'],0,'L');
            $y1 = $this->GetY();
            $this->SetXY($x+36,$y+.5);
            $x = $this->GetX();
            $this->MultiCell(50,5,$prow['ParcelLoc'],0,'L');
            $y2 = $this->GetY();
            $this->SetXY($x+50,$y);
            $this->Cell(40,7,$prow['Watershed'],0,0,'L');
            $prow['ContiguousParcels'] == 0 ? $this->Image('/var/www/llpc.tinicumconservancy.org/public_html/images/unchecked.png',198,$y+3,4) : $this->Image('/var/www/html/images/checked.png',198,$y+3,4);
            $prow['GasLease'] == 0 ? $this->Image('/var/www/llpc.tinicumconservancy.org/public_html/images/unchecked.png',230,$y+3,4) : $this->Image('/var/www/html/images/checked.png',230,$y+3,4);
            $prow['GasLease'] == 0 ? $this->Image('/var/www/llpc.tinicumconservancy.org/public_html/images/unchecked.png',256,$y+3,4) : $this->Image('/var/www/html/images/checked.png',256,$y+3,4);
            // $this->Ln(12);
            $y1 > $y2 ? $y = $y1 : $y = $y2;
            $this->SetXY(5,$y);
        }
        $this->Ln(8);
        
        $this->Cell(48,7,'Contact','B',0,'L',1);
        $this->Cell(170,7,'Contact Note','B',0,'L',1);
        $this->Cell(52,7,'Next Step','B',1,'L',1);
        
        $x = $this->GetX();
        $y = $this->GetY();
        $this->SetFont('Arial', '', 10);
        $this->SetXY(250,7);
        $this->Cell(20,5,date("Y-m-d"),0,2,'R');
        $this->Cell(20,5,date("h:i A"),0,1,'R');
        $this->SetXY($x,$y);
        $this->SetFont('Arial', '', 12);
    }

    function Footer() {
        $this->SetY(-10);
        $this->SetFont('Arial','I',8);
        //$this->Cell(70,10,'',1);
        $this->MultiCell(150,4,'The information contained in these documents is confidential, privileged and only for the information of the intended recipient and may not be used, published or redistributed without the prior written consent of Tinicum Conservancy.',0,'C');
        $this->SetXY(-60,-10);
        $this->SetFont('Arial','',8);
        $this->Cell(60,10,'Page '.$this->PageNo().' of {nb}',0,0,'R');
    }
    
	function SetWidths($w)
	{
		// Set the array of column widths
		$this->widths = $w;
	}

	function SetAligns($a)
	{
		// Set the array of column alignments
		$this->aligns = $a;
	}

	function Row($data)
	{
		// Calculate the height of the row
		$nb = 0;
		for($i=0;$i<count($data);$i++)
			$nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h = 5*$nb;
		// Issue a page break first if needed
		$this->CheckPageBreak($h);
		// Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w = $this->widths[$i];
			$a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			// Save the current position
			$x = $this->GetX();
			$y = $this->GetY();
			// Draw the border
			//$this->Rect($x,$y,$w,$h);
            $this->Line($x,$y,$x+$w,$y);
			// Print the text
			$this->MultiCell($w,5,$data[$i],0,$a);
			// Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		// Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h)
	{
        global $skipHeader;
		// If the height h would cause an overflow, add a new page immediately
		if ($this->GetY()+$h>$this->PageBreakTrigger) {
            $skipHeader = true;
			$this->AddPage($this->CurOrientation);
            $skipHeader = false;
        }
	}

	function NbLines($w, $txt)
	{
		// Compute the number of lines a MultiCell of width w will take
		if(!isset($this->CurrentFont))
			$this->Error('No font has been set');
		$cw = $this->CurrentFont['cw'];
		if($w==0)
			$w = $this->w-$this->rMargin-$this->x;
		$wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
		$s = str_replace("\r",'',(string)$txt);
		$nb = strlen($s);
		if($nb>0 && $s[$nb-1]=="\n")
			$nb--;
		$sep = -1;
		$i = 0;
		$j = 0;
		$l = 0;
		$nl = 1;
		while($i<$nb)
		{
			$c = $s[$i];
			if($c=="\n")
			{
				$i++;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep = $i;
			$l += $cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i = $sep+1;
				$sep = -1;
				$j = $i;
				$l = 0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}
}
?>
