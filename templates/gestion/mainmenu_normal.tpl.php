
	<!-- BEGIN SIDEBAR -->
	<div class="page-sidebar-wrapper">
		<!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
		<!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<ul class="page-sidebar-menu" data-auto-scroll="false" data-auto-speed="200" id="nav">
				<li class="sidebar-toggler-wrapper">
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
				</li>
                <li>&nbsp;</li>
				<!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
				<li class="start">
					<a href="#"><i class="fa fa-home"></i><span class="title">Inicio</span></a>
				</li>
				<?php if ((($this->ModuleInstalledAndEnabled('config')) and ($this->businessID==0)) or ($this->ModuleInstalledAndEnabled('permalinks')) or 
						  ($this->ModuleInstalledAndEnabled('apps')) or ($this->ModuleInstalledAndEnabled('locale')) or ($this->ModuleInstalledAndEnabled('backup')) or 
						  ($this->ModuleInstalledAndEnabled('levels')) or ($this->ModuleInstalledAndEnabled('users')) or ($this->ModuleInstalledAndEnabled('invitations'))) {?>
	                <li>
						<a href="javascript:;">
							<i class="fa fa-cogs"></i><span class="title">Sistema</span><span class="arrow "></span>
							<?php if ($this->ModuleInstalledAndEnabled('modules')) { ?><span class="badge badge-danger warning-updates"> 
							<?php echo $this->ItsUpdated; ?> </span><?php } ?>
						</a>
						<ul class="sub-menu">
							<?php if (($this->ModuleInstalledAndEnabled('config')) and ($this->businessID==0)) { ?>
								<li><a href="config"><?php echo _($this->GetModuleName('config'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('modules')) { ?>
									<li><a href="modules"><?php echo _('Gestionar Módulos');?><span class="badge badge-danger warning-updates" id="ItsUpdated"> <?php echo $this->ItsUpdated; ?> </span></a></li>
								<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('levels')) { ?>
								<li><a href="levels"><?php echo _($this->GetModuleName('levels'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('users')) { ?>
		                        <li><a href="users"><?php echo _($this->GetModuleName('users'));?></a></li>
		                    <?php } ?>
		                    <?php if ($this->ModuleInstalledAndEnabled('invitations')) { ?> 
		                        <li><a href="invitations"><?php echo _($this->GetModuleName('invitations'));?></a></li>
		                    <?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('permalinks')) { ?>
	                        	<li><a href="permalinks"><?php echo _($this->GetModuleName('permalinks'));?></a></li>
	                        <?php } ?>
	                        <?php if ($this->ModuleInstalledAndEnabled('apps')) { ?>
	                        	<li><a href="apps"><?php echo _($this->GetModuleName('apps'));?></a></li>
	                        <?php } ?>
	                        <?php if ($this->ModuleInstalledAndEnabled('core--locale')) { ?>
	                        	<li><a href="core--locale"><?php echo _($this->GetModuleName('core--locale'));?></a></li>
	                        <?php } ?>
	                        <?php if ($this->ModuleInstalledAndEnabled('backup')) { ?>
	                        	<li><a href="backup"><?php echo _($this->GetModuleName('backup'));?></a></li>
	                        <?php } ?>
	                	</ul>
					<li>
				<?php } ?>
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
				<?php } else { 
				if (($this->ModuleInstalledAndEnabled('business')) or ($this->ModuleInstalledAndEnabled('business--sets')) or ($this->ModuleInstalledAndEnabled('business--crm'))) { ?>
					<li>
						<a href="javascript:;"><i class="fa fa-archive"></i><span class="title"><?php echo _($this->GetModuleName('business'));?></span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('business')) { ?>
	                        	<li><a href="business"><?php echo _($this->GetModuleName('business'));?></a></li>
	                        <?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('business--sets')) { ?>
	                        	<li><a href="business--sets"><?php echo _($this->GetModuleName('business--sets'));?></a></li>
	                        <?php } ?>
	                        <?php if ($this->ModuleInstalledAndEnabled('business--crm')) { ?>
	                        	<li><a href="business--crm"><?php echo _($this->GetModuleName('business--crm'));?></a></li>
	                        <?php } ?>
	                    </ul>
	                </li>
	            <?php }
	            } ?>

				<?php if (($this->ModuleInstalledAndEnabled('menu')) or ($this->ModuleInstalledAndEnabled('sticker')) or 
						 ($this->ModuleInstalledAndEnabled('banners')) or ($this->ModuleInstalledAndEnabled('sponsors')) or 
						 ($this->ModuleInstalledAndEnabled('slider')) or ($this->ModuleInstalledAndEnabled('htmleditor')) or
						 ($this->ModuleInstalledAndEnabled('blocks')) or ($this->ModuleInstalledAndEnabled('blocks'))) { ?>
	                <li>
						<a href="javascript:;"><i class="fa fa-dashboard"></i><span class="title">Apariencia</span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('menu')) { ?>
								<li><a href="menu"><?php echo _($this->GetModuleName('menu'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('slider')) { ?>
								<li><a href="slider"><?php echo _($this->GetModuleName('slider'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('blocks')) { ?>
								<li><a href="blocks"><?php echo _($this->GetModuleName('blocks'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('composser')) { ?>
								<li><a href="composser"><?php echo _($this->GetModuleName('composser'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('sticker')) { ?>
								<li><a href="sticker"><?php echo _($this->GetModuleName('sticker'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('banners')) { ?>
								<li><a href="banners"><?php echo _($this->GetModuleName('banners'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('sponsors')) { ?>
								<li><a href="sponsors"><?php echo _($this->GetModuleName('sponsors'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('htmleditor')) { ?>
								<li><a href="htmleditor"><?php echo _($this->GetModuleName('htmleditor'));?></a></li>
							<?php } ?>
	                	</ul>
					<li>                               
                <?php } ?>
                <?php if (($this->ModuleInstalledAndEnabled('contents')) or ($this->ModuleInstalledAndEnabled('catpages')) or ($this->ModuleInstalledAndEnabled('singlegallery')) or ($this->ModuleInstalledAndEnabled('gallery')) or 
                		  ($this->ModuleInstalledAndEnabled('files')) or ($this->ModuleInstalledAndEnabled('links')) or ($this->ModuleInstalledAndEnabled('polls')) or ($this->ModuleInstalledAndEnabled('services')) or
                		  ($this->ModuleInstalledAndEnabled('singlevideos'))  or ($this->ModuleInstalledAndEnabled('portfolio') or ($this->ModuleInstalledAndEnabled('pharmacies')) or ($this->ModuleInstalledAndEnabled('otherservices')) or
                		  ($this->ModuleInstalledAndEnabled('singlepages')) or ($this->ModuleInstalledAndEnabled('videoacta')) or ($this->ModuleInstalledAndEnabled('multigallery')) or
                		  ($this->ModuleInstalledAndEnabled('routes')) or ($this->ModuleInstalledAndEnabled('experiencies')) or ($this->ModuleInstalledAndEnabled('singleblog')))) { ?> 
	                <li>
						<a href="javascript:;"><i class="fa fa-archive"></i><span class="title">Contenidos</span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('contents')) { ?>
								<li><a href="contents"><?php echo _($this->GetModuleName('contents'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('catpages')) { ?>
								<li><a href="catpages"><?php echo _($this->GetModuleName('catpages'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('services')) { ?>
								<li><a href="services"><?php echo _($this->GetModuleName('services'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('otherservices')) { ?>
								<li><a href="otherservices"><?php echo _($this->GetModuleName('otherservices'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('singlegallery')) { ?>
								<li><a href="singlegallery"><?php echo _($this->GetModuleName('singlegallery'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('gallery')) { ?>
								<li><a href="gallery"><?php echo _($this->GetModuleName('gallery'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('multigallery')) { ?>
								<li><a href="multigallery"><?php echo _($this->GetModuleName('multigallery'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('files')) { ?>
								<li><a href="files"><?php echo _($this->GetModuleName('files'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('links')) { ?>
								<li><a href="links"><?php echo _($this->GetModuleName('links'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('polls')) { ?>
								<li><a href="polls"><?php echo _($this->GetModuleName('polls'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('singlepages')) { ?>
								<li><a href="singlepages"><?php echo _($this->GetModuleName('singlepages'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('singlevideos')) { ?>
								<li><a href="singlevideos"><?php echo _($this->GetModuleName('singlevideos'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('portfolio')) { ?>
								<li><a href="portfolio"><?php echo _($this->GetModuleName('portfolio'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('pharmacies')) { ?>
								<li><a href="pharmacies"><?php echo _($this->GetModuleName('pharmacies'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('faq')) { ?>
								<li><a href="faq"><?php echo _($this->GetModuleName('faq'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('routes')) { ?>
								<li><a href="routes"><?php echo _($this->GetModuleName('routes'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('experiencies')) { ?>
								<li><a href="experiencies"><?php echo _($this->GetModuleName('experiencies'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('singleblog')) { ?>
								<li><a href="singleblog"><?php echo _($this->GetModuleName('singleblog'));?></a></li>
							<?php } ?>
	                	</ul>
					<li> 
				<?php } ?>  
				<?php if ($this->ModuleInstalledAndEnabled('calendar')) { ?>  
	                <li>
						<a href="calendar"><i class="fa fa-calendar"></i><span class="title"><?php echo _($this->GetModuleName('calendar'));?></span></a>
					<li>
				<?php } ?>
				<?php if (($this->ModuleInstalledAndEnabled('geography')) or ($this->ModuleInstalledAndEnabled('tags')) or ($this->ModuleInstalledAndEnabled('surveys')) or 
						  ($this->ModuleInstalledAndEnabled('majordomo')) or ($this->ModuleInstalledAndEnabled('comments')) or ($this->ModuleInstalledAndEnabled('socialmedia')) or 
						  ($this->ModuleInstalledAndEnabled('import')) or ($this->ModuleInstalledAndEnabled('qr')) or 
						  ($this->ModuleInstalledAndEnabled('coupons')) or ($this->ModuleInstalledAndEnabled('forms')) or ($this->ModuleInstalledAndEnabled('minimizer'))
						  or ($this->ModuleInstalledAndEnabled('slideshow'))) { ?>
	                <li>
						<a href="javascript:;"><i class="fa fa-wrench"></i><span class="title">Herramientas</span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('geography')) { ?>
								<li><a href="geography"><?php echo _($this->GetModuleName('geography'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('tags')) { ?>
								<li><a href="tags"><?php echo _($this->GetModuleName('tags'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('surveys')) { ?>
								<li><a href="surveys"><?php echo _($this->GetModuleName('surveys'));?></a></li>
								<li><a href="setsurveys"><?php echo _($this->GetModuleName('setsurveys'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('majordomo')) { ?>
								<li><a href="majordomo"><?php echo _($this->GetModuleName('majordomo'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('comments')) { ?>
								<li><a href="comments"><?php echo _($this->GetModuleName('comments'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('import')) { ?>
								<li><a href="import"><?php echo _($this->GetModuleName('import'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('core--socialmedia')) { ?>
								<li><a href="core--socialmedia"><?php echo _($this->GetModuleName('core--socialmedia'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('qr')) { ?>
								<li><a href="qr"><?php echo _($this->GetModuleName('qr'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('coupons')) { ?>
								<li><a href="coupons"><?php echo _($this->GetModuleName('coupons'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('forms')) { ?>
								<li><a href="forms"><?php echo _($this->GetModuleName('forms'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('core--minimizer')) { ?>
								<li><a href="core--minimizer"><?php echo _($this->GetModuleName('core--minimizer'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('slideshow')) { ?>
								<li><a href="slideshow"><?php echo _($this->GetModuleName('slideshow'));?></a></li>
							<?php } ?>
	                	</ul>
					<li>                                         
                <?php } ?>
                <?php if ($this->ModuleInstalledAndEnabled('music')) { ?>        
	                <li>
						<a href="javascript:;"><i class="fa fa-music"></i><span class="title"><?php echo _($this->GetModuleName('music'));?></span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<li><a href="music"><?php echo _($this->GetModuleName('music'));?></a></li>
							<li><a href="music--types"><?php echo _($this->GetModuleName('music--types'));?></a></li>
							<li><a href="music--authors"><?php echo _($this->GetModuleName('music--authors'));?></a></li>
							<li><a href="music--styles"><?php echo _($this->GetModuleName('music--styles'));?></a></li>
							<li><a href="music--licenses"><?php echo _($this->GetModuleName('music--licenses'));?></a></li>
						</ul>
					<li>
				<?php } ?>
                <?php if ($this->ModuleInstalledAndEnabled('catalog')) { ?>   
	                <li>
						<a href="javascript:;"><i class="fa fa-gift"></i><span class="title"><?php echo _("Catálogo de productos");?></span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('catalog')) { ?>
							<li><a href="catalog"><?php echo _($this->GetModuleName('catalog'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('catalog--sets')) { ?>
							<li><a href="catalog--sets"><?php echo _("Atributos"); ?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('catalog--families')) { ?>
							<li><a href="catalog--families"><?php echo _($this->GetModuleName('catalog--families'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('catalog--manufacturers')) { ?>
							<li><a href="catalog--manufacturers"><?php echo _($this->GetModuleName('catalog--manufacturers'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('catalog--taxations')) { ?>
	                        <li><a href="catalog--taxations"><?php echo _($this->GetModuleName('catalog--taxations'));?></a></li>
	                        <?php } ?>
	                        <?php if ($this->ModuleInstalledAndEnabled('catalog--shippings')) { ?>
	                        <li><a href="catalog--shippings"><?php echo _($this->GetModuleName('catalog--shippings'));?></a></li>
	                        <?php } ?>
	                        <?php if ($this->ModuleInstalledAndEnabled('catalog--payments')) { ?>
	                        <li><a href="catalog--payments"><?php echo _($this->GetModuleName('catalog--payments'));?></a></li>
	                        <?php } ?>
	                        <?php if ($this->ModuleInstalledAndEnabled('catalog--orders')) { ?>
	                        <li><a href="catalog--orders"><?php echo _($this->GetModuleName('catalog--orders'));?></a></li>
	                        <?php } ?>
	                        <?php if ($this->ModuleInstalledAndEnabled('catalog--invoices')) { ?>
	                        <li><a href="catalog--invoices"><?php echo _($this->GetModuleName('catalog--invoices'));?></a></li>
	                        <?php } ?>
	                	</ul>
					<li>        
				<?php } ?>  
				<?php if ($this->ModuleInstalledAndEnabled('blogs')) { ?>        
	                <li>
						<a href="blogs"><i class="fa fa-edit"></i><span class="title"><?php echo _($this->GetModuleName('blogs'));?></span></a>
					<li>
				<?php } ?>
				<?php if (($this->ModuleInstalledAndEnabled('movies')) or ($this->ModuleInstalledAndEnabled('promomovies'))) { ?>
	                <li>
						<a href="javascript:;"><i class="fa fa-film"></i><span class="title">Cine</span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('movies--theaters')) { ?>
								<li><a href="movies--theaters"><?php echo _($this->GetModuleName('movies--theaters'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('movies--pictures')) { ?>
								<li><a href="movies--pictures"><?php echo _($this->GetModuleName('movies--pictures'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('movies')) { ?>
								<li><a href="movies"><?php echo _($this->GetModuleName('movies'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('promomovies')) { ?>
								<li><a href="promomovies"><?php echo _($this->GetModuleName('promomovies'));?></a></li>
							<?php } ?>
	                	</ul>
					<li>  
				<?php } ?>  
				<?php if (($this->ModuleInstalledAndEnabled('special_newyear')) or ($this->ModuleInstalledAndEnabled('special_dtapas')) or ($this->ModuleInstalledAndEnabled('special_reserves'))) { ?>
	                <li>
						<a href="javascript:;"><i class="fa fa-star"></i><span class="title">Especiales</span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('special_newyear')) { ?>
								<li><a href="special_newyear"><?php echo _($this->GetModuleName('special_newyear'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('special_dtapas')) { ?>
								<li><a href="special_dtapas"><?php echo _($this->GetModuleName('special_dtapas'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('special_reserves')) { ?>
								<li><a href="special_reserves"><?php echo _($this->GetModuleName('special_reserves'));?></a></li>
							<?php } ?>
	                	</ul>
					<li>   
				<?php } ?> 
				<?php if ($this->ModuleInstalledAndEnabled('registrations')) { ?>   
	                <li>
						<a href="javascript:;"><i class="fa fa-edit"></i><span class="title"><?php echo _($this->GetModuleName('registrations'));?></span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<li><a href="registrations"><?php echo _($this->GetModuleName('registrations'));?></a></li>
							<li><a href="registrations--sets">Personalizar Campos</a></li>
	                	</ul>
					<li>        
				<?php } ?>  
				<?php if ($this->ModuleInstalledAndEnabled('sweepstakes')) { ?>        
	                <li>
						<a href="sweepstakes"><i class="fa fa-bolt"></i><span class="title"><?php echo _($this->GetModuleName('sweepstakes'));?></span></a>
					<li>
				<?php } ?>
				<?php if ($this->ModuleInstalledAndEnabled('videoacta')) { ?>        
	                <li>
						<a href="videoacta"><i class="fa fa-video-camera"></i><span class="title"><?php echo _($this->GetModuleName('videoacta'));?></span></a>
					<li>
				<?php } ?>  
				<?php if ($this->ModuleInstalledAndEnabled('modulerepository')) { ?>        
	                <li>
						<a href="modulerepository"><i class="fa fa-cloud"></i><span class="title"><?php echo _($this->GetModuleName('modulerepository'));?></span></a>
					<li>
				<?php } ?> 
				<?php if (($this->ModuleInstalledAndEnabled('realestates')) or ($this->ModuleInstalledAndEnabled('realestates--sets'))) { ?>
	                <li>
						<a href="javascript:;"><i class="fa fa-map-marker"></i><span class="title"><?php echo _($this->GetModuleName('realestates'));?></span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('realestates')) { ?>
								<li><a href="realestates"><?php echo _($this->GetModuleName('realestates'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('realestates--sets')) { ?>
								<li><a href="realestates--sets"><?php echo _($this->GetModuleName('realestates--sets'));?></a></li>
							<?php } ?>
	                	</ul>
					<li>   
				<?php } ?>
				<?php if (($this->ModuleInstalledAndEnabled('tickets')) or ($this->ModuleInstalledAndEnabled('tickets--cats'))) { ?>
	                <li>
						<a href="javascript:;"><i class="fa fa-support"></i><span class="title"><?php echo _($this->GetModuleName('tickets'));?></span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('tickets')) { ?>
								<li><a href="tickets"><?php echo _($this->GetModuleName('tickets'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('tickets--cats')) { ?>
								<li><a href="tickets--cats"><?php echo _($this->GetModuleName('tickets--cats'));?></a></li>
							<?php } ?>
	                	</ul>
					<li>   
				<?php } ?> 
				<?php if (($this->ModuleInstalledAndEnabled('customers')) or ($this->ModuleInstalledAndEnabled('customers--products')) or ($this->ModuleInstalledAndEnabled('customers--projects'))) { ?>
	                <li>
						<a href="javascript:;"><i class="fa fa-users"></i><span class="title"><?php echo _($this->GetModuleName('customers'));?></span><span class="arrow "></span></a>
						<ul class="sub-menu">
							<?php if ($this->ModuleInstalledAndEnabled('customers')) { ?>
								<li><a href="customers"><?php echo _($this->GetModuleName('customers'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('customers--products')) { ?>
								<li><a href="customers--products"><?php echo _($this->GetModuleName('customers--products'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('customers--projects')) { ?>
								<li><a href="customers--projects"><?php echo _($this->GetModuleName('customers--projects'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('customers--actions')) { ?>
								<li><a href="customers--actions"><?php echo _($this->GetModuleName('customers--actions'));?></a></li>
							<?php } ?>
							<?php if ($this->ModuleInstalledAndEnabled('customers--payings')) { ?>
								<li><a href="customers--payings"><?php echo _($this->GetModuleName('customers--payings'));?></a></li>
							<?php } ?>
	                	</ul>
					<li>   
				<?php } ?> 
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
	<!-- END SIDEBAR -->