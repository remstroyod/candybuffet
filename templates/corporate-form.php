<div class="request-box__col">
    <div class="form-group">
        <input name="cf_company" id="field-request-company-name" type="text" aria-required
               class="form-control">
        <label for="field-request-company-name" class="control-label">Назва компанії</label>
    </div>
    <div class="form-group">
        <? get_template_part('templates/product', 'tooltip-weight') ?>
        <input name="cf_people_count" id="field-request-quantity" type="text" aria-required
               class="form-control form-control-number">
        <label for="field-request-quantity" class="control-label">Кількість людей</label>
    </div>
</div>
<div class="request-box__col">
    <div class="request-box__header">
        <div class="request-box__title">Розкажіть про свою компанію</div>
        <div class="request-box__text">Розкажіть нам більше про себе, щоб ми змогли точно
            підібрати дизайн відповідно до Ваших вподобань. Якшо Ви бажаєте, щоб ми Вас
            здивували, тоді залишайте це поле порожнім.
        </div>
    </div>
    <div class="form-group">
        <textarea name="cf_conception" rows="3" placeholder="Яким ви бачите ваш дизайн..." class="form-control"></textarea>
    </div>
    <div class="request-box__attach">
        <div class="drop-box">
            <div class="drop-box__wrap">
                <label class="drop-box__button">
                    <input type="file" name="field-upload" maxlength="200"
                           class="drop-box__input"/>
                    <span class="drop-box__icon"></span>
                    <span class="drop-box__text">Прикріпити</span>
                    <span class="drop-box__name"></span>
                </label>
                <span class="drop-box__delete"></span>
            </div>
        </div>
    </div>
</div>