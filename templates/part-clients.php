<section class="logos-carousel">
	<div class="logos-carousel__header">
		<div class="styled-heding styled-heding_pos-center">
			<div class="styled-heding__header-inner">
				<div class="styled-heding__label">Our clients</div>
				<h2 class="styled-heding__title">Нам довіряють</h2>
			</div>
		</div>
	</div>
	<div class="logos-carousel__list">
		<? if ($clients = get_field('наши_клиенты', get_option('page_on_front'))) foreach($clients as $i): ?>
			<div class="logos-carousel__list-item">
				<div class="logos-carousel__image"><img src="<?= $i['url'] ?>" alt="" class="logos-carousel__image-i" /></div>
			</div>
		<? endforeach; ?>
	</div>
</section>