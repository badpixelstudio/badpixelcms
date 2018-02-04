<?php 
$this->loadtemplate("header.tpl.php"); 
$this->loadtemplate("topmenu.tpl.php"); 
$this->loadtemplate("mainmenu.tpl.php"); 
$this->loadtemplate("topcontent.tpl.php"); 
?>

<div class="row">
	<div class="col-md-12">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption"><?php echo _("Todos los comentarios"); ?></div>
			</div>
			
			<div class="portlet-body" id="chats">
				<div class="scroller" data-always-visible="1" data-rail-visible1="1">
					<ul class="chats">
					<?php foreach($this->Data as $elemento=>$item) { 
						$modulo=$item['TableName'];
						if ($modulo=="cats") { $modulo="cats_pages"; }
						if ($modulo=="calendar") { $modulo="calendar_events"; }
						if ($modulo=="blogs") { $modulo="blogs_entries"; }
						if ($modulo=="movies") { $modulo="movies_pictures"; }
						$permalink=$this->GetPermalink($modulo,$item['IDFather']); 
						if ($item['UserAvatar']=="") { $item['UserAvatar']="../templates/gestion/assets/images/no-avatar.png";} 
						$clase="green"; $texto="Activado";
						if ($item['Active']==0) { $clase="red"; $texto="Desactivado"; } ?>
						<li class="in">
							<img class="avatar img-responsive" alt="" src="<?php echo $item['UserAvatar']; ?>" />
							<div class="message">
								<span class="arrow"></span>
								<span class="name"><?php echo $item['Name']; ?><span class="datetime"> escribió el <?php echo EuroScreenDateTime($item['DatePublish']); ?> en la sección <?php echo $item['TableName']; ?> (<a href="<?php echo siteprotocol . sitedomain . $permalink; ?>" target="_blank"><?php echo siteprotocol . sitedomain . $permalink; ?></a>)</span></span>
								<span class="body"><?php echo stripslashes($item['Comment']); ?></span>
								<a class="btn btn-xs <?php echo $clase; ?> default comment-change-activation" href="<?php echo siteprotocol . sitedomain . sitePanelFolder; ?>/allcomments/change/table/<?php echo $item['TableName']; ?>/id/<?php echo $item['ID']; ?>"><?php echo $texto; ?></a> 
								<a class="btn btn-xs default" href="<?php echo siteprotocol . sitedomain . sitePanelFolder; ?>/allcomments/edit_from_all/table/<?php echo $item['TableName']; ?>/id/<?php echo $item['ID']; ?>">Editar</a> 
								<a class="btn btn-xs default comment-delete-confirm" href="<?php echo siteprotocol . sitedomain . sitePanelFolder; ?>/allcomments/delete_from_all/table/<?php echo $item['TableName']; ?>/id/<?php echo $item['ID']; ?>">Borrar</a>
							</div>
						</li>
					<?php } ?>
					</ul>
				</div>
			</div>
		</div>
		<!-- END TAB PORTLET-->
	</div>

</form>
</div>

<?php $this->loadtemplate("footer.tpl.php"); ?>
<script>
$(document).ready(function() {
	$(form).submit(function () {
	$('form').find('input[type=file]').wl_File();
	});
});
</script>