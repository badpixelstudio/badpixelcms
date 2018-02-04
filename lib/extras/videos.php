<html>    
  <head> 
    <style>
      *{
        font-family: verdana;
        font-size: 12px;
      }
      body{
        text-align:center;
		background: #E1E1E1;
      }
	  iframe{
		margin: 80px 50px;
	  }
    </style>

  <body>
  
<?php
if(isset($_GET['id'])){
	$id=$_GET['id'];
	if(isset($_GET['table'])){ $tabla =$_GET['table']; } else { $tabla ="cats";} 
	require_once("../../include/core/database.class.php");
	$Obj = new DBase();
	$Video = $Obj->GetDataRecordFromSQL("SELECT * FROM ".$tabla."_videos WHERE ID=".$id);
	echo $Video['Embed'];
} else {
	echo "No hay ningún video vinculado al identidficador seleccionado.";
}

?>

  </body>
</html>