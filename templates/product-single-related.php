<section class="tiles-carousel tiles-carousel_simple-heading">
    <div class="container">
        <div class="tiles-carousel__header">
            <div class="styled-heding styled-heding_pos-right">
                <div class="styled-heding__header-inner">
                    <div class="styled-heding__label">Should be interesting</div>
                    <h2 class="styled-heding__title">Вас також зацікавить</h2>
                </div>
            </div>
        </div>
        <div class="tiles-carousel__list">
			<?
            global $post;

            foreach ($posts as $post): setup_postdata($post); ?>
                <div class="tiles-carousel__list-item">
					<? get_template_part('templates/product', 'list-item') ?>
                </div>
			<? endforeach;
			wp_reset_postdata(); ?>
        </div>
    </div>
</section>