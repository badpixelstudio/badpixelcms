<?php 
$this->loadtemplate("header.tpl.php"); 
$this->loadtemplate("topmenu.tpl.php"); 
$this->loadtemplate("mainmenu.tpl.php"); 
$this->loadtemplate("topcontent.tpl.php"); 
?>
<!--Welcome block-->
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="portlet box blue-steel">
			<div class="portlet-title">
				<div class="caption"><?php echo stripslashes($this->Data['Title']); ?></div>
			</div>
		</div>
		<p>
			<button class="btn green gridster-add " type="submit">Añadir bloque</button> 
			<button class="btn green gridster-addseparator " type="submit">Añadir separador</button> 
			<button class="btn green gridster-seralize pull-right" type="submit">Guardar</button>
		</p>

		<div class="gridster" rel="<?php echo siteprotocol . sitedomain . sitePanelFolder . '/' . $this->module; ?>">
	        <ul>
	        <?php if ($this->ItemsCount>0) { 
	        	$row=1; 
	        	$col=0; 
	        	foreach ($this->Items as $item) {
	        		$col=$col+$item['Width'];
	        		if ($col>2) { $row++; $col=1; } ?>

	            <li id="<?php echo $item['ID']; ?>" grid="<?php echo $item['ID']; ?>" grid-style="<?php echo $item['Style']; ?>" grid-module="<?php echo $item['Module']; ?>" grid-id="<?php echo $item['ModuleID']; ?>" grid-title="<?php echo $item['Title']; ?>" grid-subtitle="<?php echo $item['Subtitle']; ?>" data-row="<?php echo $row; ?>" data-col="<?php echo $col; ?>" data-sizex="<?php echo $item['Width']; ?>" data-sizey="1">
	            	<br>
	            	<a class="gridster-del pull-right" href="#"><img src="../templates/gestion/assets/images/cross.png"></a>
	            	<?php if ($item['Module']!="separator") { ?>
		            	<select id="gridster-style-<?php echo $item['ID']; ?>" class="gridster-style">
		            		<?php echo $this->PopulateStyles($item['Style']); ?>
		            	</select>
		            	<br>
		            	<select id="gridster-module-<?php echo $item['ID']; ?>" class="gridster-module">
		            	<?php foreach($this->ItemsModules as $m=>$t) { ?>
		            		<option <?php if($item['Module']==$m) { echo "selected"; } ?>  value="<?php echo $m; ?>"><?php echo $t; ?></option>
		            	<?php } ?>
		            	</select>
		            	<br>
		            	<select id="gridster-moduleid-<?php echo $item['ID']; ?>" class="gridster-moduleid">
		            		<?php echo $this->PopulateComboItems($item['Module'],$item['ModuleID']); ?>
		            	</select>
		            	<input type="hidden" id="gridster-title-<?php echo $item['ID']; ?>" class="gridster-title" value="">
		            	<input type="hidden" id="gridster-subtitle-<?php echo $item['ID']; ?>" class="gridster-subtitle" value="">
		            <?php } else { ?>
		            	<input type="hidden" id="gridster-style-<?php echo $item['ID']; ?>" class="gridster-style" value="xl">
		            	<input type="hidden" id="gridster-module-<?php echo $item['ID']; ?>" class="gridster-module" value="separator">
		            	<input type="hidden" id="gridster-moduleid-<?php echo $item['ID']; ?>" class="gridster-moduleid" value="0">
		            	<strong>Título de sección/Separador</strong><br>
		            	<input type="text" id="gridster-title-<?php echo $item['ID']; ?>" class="gridster-title" value="<?php echo $item['Title']; ?>">
		            	<input type="text" id="gridster-subtitle-<?php echo $item['ID']; ?>" class="gridster-subtitle" value="<?php echo $item['Subtitle']; ?>">
		            <?php } ?>
	            </li>
	            <?php }
	        } ?>
	        </ul>
	    </div>
	    <br>
	    
	</div>
	<form id="form" action="<?php echo $this->TemplatePostScript; ?>" enctype="multipart/form-data" method="<?php echo $this->TemplateMethodScript; ?>" autocomplete="off">
	<input type="hidden" id="Json" name="Json" value="">
	<input type="hidden" id="Form_IDFather" name="Form_IDFather" value="<?php echo $this->id; ?>">
	</form>
</div>
<?php $this->loadtemplate("footer.tpl.php"); ?>