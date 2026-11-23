<?php

use function Roots\Sage\Assets\asset_path;

?>
<div class="card__hint-drop">
    <div class="card__hint-drop-title">Як розрахувати вагу</div>
    <div class="card__hint-drop-text">
        <div class="weight-info">
            <div class="weight-info__row">
                <div class="weight-info__col">
                    <div class="weight-info__title">Гості</div>
                    <div class="weight-info__image">
                        <img src="<?= asset_path('images/peoples-icon.svg') ?>" alt=""
                             class="weight-info__image-i"/>
                    </div>
                    <div class="weight-info__list">
                        <div class="weight-info__list-item">10-15</div>
                        <div class="weight-info__list-item">15-20</div>
                        <div class="weight-info__list-item">20-30</div>
                        <div class="weight-info__list-item">30-40</div>
                        <div class="weight-info__list-item">40-50</div>
                        <div class="weight-info__list-item">50-70</div>
                        <div class="weight-info__list-item">70-90</div>
                        <div class="weight-info__list-item">100+</div>
                    </div>
                </div>
                <div class="weight-info__col">
                    <div class="weight-info__title">Вага, кг</div>
                    <div class="weight-info__image">
                        <img src="<?= asset_path('images/scale-icon.svg') ?>" alt=""
                             class="weight-info__image-i"/>
                    </div>
                    <div class="weight-info__list">
                        <div class="weight-info__list-item">2-3</div>
                        <div class="weight-info__list-item">3-4</div>
                        <div class="weight-info__list-item">4-6</div>
                        <div class="weight-info__list-item">6-8</div>
                        <div class="weight-info__list-item">8-9</div>
                        <div class="weight-info__list-item">9-11</div>
                        <div class="weight-info__list-item">11-14</div>
                        <div class="weight-info__list-item">15+</div>
                    </div>
                </div>
            </div>
            <div class="weight-info__data">
                <div class="weight-info__data-item">
                    <div class="weight-info__data-label">До 50 гостей</div>
                    <div class="weight-info__data-value">200 г на людину</div>
                </div>
                <div class="weight-info__data-item">
                    <div class="weight-info__data-label">Більше 50 гостей</div>
                    <div class="weight-info__data-value">150 г на людину</div>
                </div>
            </div>
        </div>
    </div>
</div>