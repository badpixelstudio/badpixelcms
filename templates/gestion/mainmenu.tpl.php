	<div class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapse">
			<ul class="page-sidebar-menu" data-auto-scroll="false" data-auto-speed="200" id="nav">
				<li class="sidebar-toggler-wrapper">
					<div class="sidebar-toggler">
					</div>
				</li>
                <li>&nbsp;</li>
				<li class="start">
					<a href="#"><i class="fa fa-home"></i><span class="title">Inicio</span></a>
				</li>
				<?php if ($this->businessID!=0) { ?>
					<li>
						<a href="javascript:;"><i class="fa fa-archive"></i><span class="title">Mi empresa</span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('_data')) { ?>
								<li><a href="mybusiness">Datos de la Empresa</a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('business--timetable')) { ?>
	                        	<li><a href="mybusiness--timetable"><?php echo _($this->GetModuleName('business--timetable'));?></a></li>
	                        <?php } ?>
	                        <?php if ($this->ModuleInstalledAndEnabled('business--holidays')) { ?>
	                        	<li><a href="mybusiness--holidays"><?php echo _($this->GetModuleName('business--holidays'));?></a></li>
	                        <?php } ?>
	                	</ul>
					<li>    
				<?php } ?>
				<?php require_once(sitepath . "include/core/mainmenu.class.php");
				$MainMenu= new MainMenu(null);
				$MainMenu->GenerateMainMenu();
				if ($MainMenu->ItemsCount>0) {
				foreach($MainMenu->Items as $item) { ?>
				<li>
					<a href="javascript:;">
					<i class="fa <?php echo $item['Icon']; ?>"></i><span class="title"><?php echo _($item['Title']); ?></span><span class="arrow "></span>
					<?php if ($item['Block']=="system") { ?><span class="badge badge-danger warning-updates"> <?php echo $this->ItsUpdated; ?> </span><?php } ?>
					</a>
					<ul class="sub-menu">
					<?php foreach($item['Items'] as $link=>$text) { ?>
						<li>
							<a href="<?php echo $link; ?>"><?php echo _($text);?>
							<?php if($link=="modules") { ?><span class="badge badge-danger warning-updates" id="ItsUpdated"> <?php echo $this->ItsUpdated; ?> </span></a><?php } ?>
							</a>
						</li>
					<?php } ?>
					</ul>
				</li>
				<?php }
				} ?>
			</ul>
		</div>
	</div>