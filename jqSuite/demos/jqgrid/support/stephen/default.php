<?php 
ini_set("display_errors",1);
require_once '../../../../php/demo/tabs.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>jqGrid PHP Demo</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/trirand/ui.jqgrid.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="../../../../css/ui.multiselect.css" />
    <style type="text">
        html, body {
        margin: 0;			/* Remove body margin/padding */
    	padding: 0;
        overflow: hidden;	/* Remove scroll bars on browser window */
        font-size: 75%;
        }
		#tags {z-index: 900}
	</style>
    <script src="../../../../js/jquery.min.js" type="text/javascript"></script>
    <script src="../../../../js/trirand/i18n/grid.locale-en.js" type="text/javascript"></script>
	 	<script src="../../../../js/trirand/src/jquery.jqGrid.js" type="text/javascript"></script> <script type="text/javascript">   	  
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;
	$.jgrid.defaults.width = "700";
	</script>
     
    <script src="../../../../js/jquery-ui.min.js" type="text/javascript"></script>
    </head>
  <body>
      <div class="row">
  <div class="column"></div>
  <div>
          <b>M2Biotechnology</b>
          <?php include ("grid.php");?>
      </div>
  <div class="column"></div>

      <div>
          <b>Ingredients for the Selected Protocol</b>
          <?php include ("detail.php");?>
    
      <div>
          <b>Culture Notes for the Selected Protocol</b>
          <?php include ("detail2.php");?>
      </div>
      <br/>
      <?php tabs(array("grid.php","detail.php", "detail2.php","subgrid.php"));?>
   </body>
</html>