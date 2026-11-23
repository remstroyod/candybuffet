<div class="request-box__col">
    <div class="form-group">
        <div class="request-box__data">
            <?php get_template_part('templates/product', 'tooltip-taste') ?>
            <div class="request-box__data-label">Смак</div>
            <div class="request-box__data-control">
                <select name="cf_taste" class="select2-simple">
                    <?php foreach (get_all_product_tastes() as $item) { ?>
                        <option value="<?= $item ?>"><?= $item ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <input name="cf_people_count" id="field-request-quantity" type="text" aria-required class="form-control form-control-number"><label for="field-request-quantity" class="control-label">Кількість людей</label></div>
</div>
<div class="request-box__col">
    <div class="request-box__header">
        <div class="request-box__title">Бажаєте свій дизайн?</div>
        <div class="request-box__text">Якщо ви бажаєте замовити торт або цукерки за власним дизайном, тоді можете завантажити своє фото з відповідним дизайном та описом. Якшо ви бажаєте щоб ми вас здивували, тоді можете залишити це поле порожнім.</div>
    </div>
    <div class="form-group">
        <textarea name="cf_design" rows="3" placeholder="Яким ви бачите ваш дизайн..." class="form-control"></textarea>
    </div>
    <div class="request-box__attach">
        <div class="drop-box">
            <div class="drop-box__wrap">
                <label class="drop-box__button">
                    <input type="file" name="field-upload" maxlength="200" class="drop-box__input" />
                    <span class="drop-box__icon"></span>
                    <span class="drop-box__text">Прикріпити</span>
                    <span class="drop-box__name"></span>
                </label>
                <span class="drop-box__delete"></span>
            </div>
        </div>
    </div>
</div>