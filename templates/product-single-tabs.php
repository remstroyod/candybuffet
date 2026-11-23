<div class="card__add">
    <ul class="card__add-nav">
	    <?
        $i = 0;
	    foreach($tabs as $tab):
            if (empty($tab['content'])) continue;
            ?>
            <li class="<?= !$i ? 'active' : '' ?> card__add-nav-item <?= !empty($tab['hide_tab']) ? 'hide' : '' ?>">
                <a href="#card-add-tab-<?= $i ?>"
                   data-toggle="tab"
                   class="card__add-nav-link card__add-nav-link-<?= $i ?>"><?= $tab['name'] ?></a>
            </li>
        <? $i++; endforeach ?>
    </ul>
    <div class="tab-content">
	    <?
        $i = 0;
	    foreach($tabs as $tab):
		    if (empty($tab['content'])) continue;
            ?>
            <div id="card-add-tab-<?= $i ?>" class="tab-pane fade in <?= !$i ? 'active' : '' ?>">
                <div class="card__add-wrap">
                    <? if(!empty($tab['image'])): ?>
                        <div class="card__add-image">
                            <?= wp_get_attachment_image($tab['image'], 'thumbnail', false, ['class' => 'card__add-image-i']) ?>
                        </div>
                    <? endif ?>

                    <div class="card__add-text"><?= $tab['content'] ?></div>
                </div>
            </div>
        <? $i++; endforeach ?>
    </div>
</div>