<div class="container">
	<div class="page-wrap">
		<div class="page-wrap__content">
			<div class="page-wrap__header">
				<? get_template_part('templates/part', 'styled-heding') ?>
			</div>
			<section class="articles-carousel-disabled">
				<div class="articles-carousel__list">
					<? while(have_posts()): the_post(); ?>
						<div class="articles-carousel__list-item">
							<section class="post-card">
								<div class="post-card__image">
                                    <a href="<? the_permalink() ?>">
                                        <? the_post_thumbnail('news-archive', ['class' => 'post-card__image-i']) ?>
                                    </a>
								</div>
								<h3 class="post-card__title"><? the_title() ?></h3>
								<div class="post-card__text"><? the_excerpt() ?></div>
								<a href="<? the_permalink() ?>" class="post-card__more">читати</a>
							</section>
						</div>
					<? endwhile ?>
				</div>
			</section>
		</div>
	</div>
</div>
