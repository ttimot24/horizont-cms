<?php

if (isset(Website::$_SLUGS[1])) {
	$blogpost = \App\Model\Blogpost::findBySlug(Website::$_SLUGS[1]);
	echo "<div class='container well card card-body p-4 pt-5'>";
	echo "<h1>" . $blogpost->title . "</h1>";
	echo "<div class='row'><h6 class='w-100 text-right mr-4'><a href='admin/blogpost/edit/" . $blogpost->id . "'>Edit post</a> | <a>" . $blogpost->author->username . "</a> | " . $blogpost->created_at . "</h6></div>";

	echo "<div class='p-2'>";
	if ($blogpost->isDraft()) {
		echo Website::$message->note("This is a draft post.");
	}
	echo "</div>";

	if ($blogpost->hasImage()) {
		echo "<center><img class='img w-100' src='" . $blogpost->getImage() . "'></center>";
	}

	echo "<h3 class='mt-3'>" . $blogpost->summary . "</h3>";

	echo "<p>" . $blogpost->text . "</p>";

	echo "</div>";
	return;
}


?>

<div class="col-md-12">

	<h1 class="page-header"><?= Website::$_REQUESTED_PAGE->name ?></h1><br>


	<?php $all_blogposts = \App\Model\Blogpost::orderBy('id', 'desc')->paginate(\Settings::get('blogposts_on_page')) ?>

	<section>
		<?php foreach ($all_blogposts as $blogpost) : ?>
			<?php if ($blogpost->isDraft()) {
				continue;
			} ?>

			<?php if ($blogpost->isFeatured()) : ?>
				<div class="well card card-body col-md-12">
					<div class="col">
						<img class="img img-rounded col-md-12 w-100" src="<?= $blogpost->getImage() ?>" style="object-fit:cover;margin-bottom:15px;">
						<h2><a href="<?= Website::$_REQUESTED_PAGE->getSlug() . '/' . $blogpost->getSlug() ?>"><?= $blogpost->title ?></a></h2>
						<p style="padding-left:5px;">Written by <a href="#"><?= $blogpost->author->username ?></a> on <a href="#"><?= $blogpost->created_at->diffForHumans() ?></a> in 
						
						<?php foreach($blogpost->categories as $category): ?>
						<a href="#"><?= $category->name ?></a></p>
						<?php endforeach ?>
						<p><b><?= $blogpost->getExcerpt() ?></b></p>
					</div>
				</div>
			<?php else : ?>
				<div class="well card card-body col-md-12">

					<div class="row">
						<div class="col-md-5">
							<img class="img img-rounded w-100" src="<?= $blogpost->getImage() ?>">
						</div>
						<div class="col-md-7 pt-2">
							<h2><a href="<?= Website::$_REQUESTED_PAGE->getSlug() . '/' . $blogpost->getSlug() ?>"><?= $blogpost->title ?></a></h2>
							<p class="pl-1" style="padding-left:5px;">Written by <a href="#"><?= $blogpost->author->username ?></a> on <a href="#"><?= $blogpost->created_at->diffForHumans() ?></a> in 
						
							<?php foreach($blogpost->categories as $category): ?>
							<a href="#"><?= $category->name ?></a></p>
							<?php endforeach ?>
						</p>
							<p><b><?= $blogpost->getExcerpt() ?></b></p>
						</div>
					</div>

				</div>
			<?php endif; ?>
			<br>
		<?php endforeach; ?>
	</section>

	<div class="d-flex justify-content-center m-5 text-center">
		<?= $all_blogposts->links() ?>
	</div>
	<br>

</div>