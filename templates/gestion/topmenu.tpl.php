<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
	<!-- BEGIN HEADER INNER -->
	<div class="page-header-inner">
		<!-- BEGIN LOGO -->
		<div class="page-logo">
			<a href="index.html">
			<img src="../templates/gestion/assets/images/logo.png" alt="logo" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler hide">
				<!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
			</div>
		</div>
		<!-- END LOGO -->
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<div class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</div>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
				<!-- BEGIN INBOX DROPDOWN -->
				<?php if ($this->userID!=0) { ?>
					<?php  if (siteEnablePrivateMessages) { ?>
					<li class="dropdown dropdown-extended dropdown-inbox" id="header_inbox_bar">
						<a href="users--pm" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
						<i class="fa fa-envelope"></i>
						<?php if ($this->userMP!=0) { ?><span class="badge badge-default"><?php echo $this->userMP;?> </span><?php } ?>
						</a>
						<ul class="dropdown-menu">
							<li>
								<a href="users--pm">
								<i class="fa fa-inbox"></i> Bandeja de entrada </a>
							</li>
							<li>
								<a href="users--pm/sent">
								<i class="fa fa-share"></i> Enviados </a>
							</li>
							
							<li>
								<a href="users--pm/new">
								<i class="fa fa-edit"></i> Redactar mensaje </a>
							</li>
						</ul>
					</li>
					<?php } ?>
				<!-- END INBOX DROPDOWN -->
				<?php if ($this->MultiBusiness) { ?>
				<li class="dropdown dropdown-language">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<span class="langname">	
					<?php if ($this->businessID==0) { ?>
						[<?php echo siteTitle; ?>]
					<?php } else { ?>
						<?php echo $this::$db->GetDataField("business",$this->businessID,"Name"); ?>
					<?php } ?>
					</span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-business">
						<li>
							<a href="security/business_change/IDBusiness/0"> [<?php echo siteTitle; ?>] </a>
						</li> 
						<?php foreach($this->Business as $actbus) { ?>
						<li>
							<a href="security/business_change/IDBusiness/<?php echo $actbus['IDBusiness']; ?>"> <?php echo $actbus['Name']; ?> </a>
						</li>
						<?php } ?>
					</ul>
				</li>
				<?php } ?>
				<?php if ($this->TotalLangsAvailables>1) { ?>
				<li class="dropdown dropdown-language">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<span class="langname">	<?php echo $this->ActualLang; ?> </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<?php foreach($this->LangsAvailables as $lang) { ?>
						<li>
							<a href="locale/setlang/setlang/<?php echo $lang['code']; ?>"> <?php echo $lang['language']; ?> </a>
						</li>
						<?php } ?>
					</ul>
				</li>
				<?php } ?>
				<!-- BEGIN USER LOGIN DROPDOWN -->
				<li class="dropdown dropdown-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
					<img alt="" class="img-circle avatar-top" src="<?php echo $this->useravatar; ?>"/>
					<span class="username"><?php echo $this->username; ?> </span>
					<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu">
						<li>
							<a href="security/edit">
							<i class="fa fa-user"></i> Mi cuenta </a>
						</li>
						<?php if (siteEnablePrivateMessages) { ?>
						<li>
							<a href="users--pm">
							<i class="fa fa-envelope"></i> Mis mensajes <span class="badge badge-danger"><?php echo $this->userMP;?> </span>
							</a>
						</li>
						<?php } ?>
						<li class="divider">
						</li>
						<li>
							<a href="<?php echo siteprotocol . sitedomain; ?>">
							<i class="fa fa-lock"></i> Volver a la web </a>
						</li>
						<li>
							<a href="security/logout">
							<i class="fa fa-key"></i> Cerrar esta sesión </a>
						</li>
						<li>
							<a href="security/logout?closeall">
							<i class="fa fa-key"></i> Cerrar todas las sesiones </a>
						</li>
					</ul>
				</li>
				<?php } ?>
				<!-- END USER LOGIN DROPDOWN -->
				<!-- END USER LOGIN DROPDOWN -->
			</ul>
		</div>
		<!-- END TOP NAVIGATION MENU -->
	</div>
	<!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">