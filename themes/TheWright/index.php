<header>

	<section class="rounded-0" style="<?= Website::$_HEADER_IMAGES->count()>0 ? "background-image:url(storage/images/header_images/".Website::$_HEADER_IMAGES->first()->image.");" : ""; ?> background-size:cover;text-align:center;padding:125px 0px 125px 0px;margin:0px;">
		<div class="d-flex justify-content-center">
			<?php Website::require_theme_file("messages.php"); ?>
		</div>
		<div class="d-flex justify-content-center m-5 text-center">
		<div class="p-4 text-white" style="padding:10px 20px 10px 20px;width:450px;background-color:rgba(0,0,0,0.8);color:white;margin:auto;">
			<h2><?= Settings::get('site_name') ?></h2>
			<hr class="text-white border border-white w-50">
			<h6><?= Website::$_SETTINGS->slogan; ?></h6>
		</div>
		</div>
	</section>
</header>

<?php Website::require_theme_file("sitelinks.php"); ?>

<section class="container">
	<div class="row pt-3">
		<div class="col-md-8 pt-4">
			<?php Website::handle_routing(); ?>
		</div>
		<div class="col-md-4">
			<?php Website::require_theme_file("sidebar.php"); ?>
		</div>
	</div>
</section>