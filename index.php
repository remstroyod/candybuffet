<? get_template_part('templates/header', 'breadcrumbs') ?>

<div class="container">
    <div class="page-wrap page-wrap_wide">
        <div class="page-wrap__content">
            <div class="content-box content-box_bottom-offset">
                <div class="content-box__inner content-box__inner--reverse">
                    <div class="content-box__aside">
                        <div class="content-box__images">
                            <div class="content-box__image content-box__image_big">
                                <? the_post_thumbnail('medium') ?>
                            </div>
                            <div class="content-box__image content-box__image_small">
                            </div>
                        </div>
                    </div>
                    <div class="content-box__content">
                        <div class="content-box__header">
                            <div class="content-box__title">
                                <div class="styled-heding">
                                    <div class="styled-heding__header-inner">
                                        <div class="styled-heding__label">Blog</div>
                                        <h1 class="styled-heding__title"><? the_title() ?></h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="content-box__text">
                            <? the_content() ?>
                        </div>
                        <div class="content-box__wrap-text">
                            <div class="content-box__date"><? the_date('d.m.Y') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>