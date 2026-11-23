<div class="request-box">
    <div class="request-box__inner">
        <div class="validation-wrap">
            <form novalidate="novalidate" enctype="multipart/form-data" class="request-box__form js__form">
                <input type="hidden" name="action" value="corporateForm">
                <div class="active request-box__section request-box__section_1">
                    <div class="request-box__section-title">1/2</div>
                    <div class="request-box__body">
                        <div class="request-box__row">
                            <div class="request-box__col">
                                <? get_template_part('templates/form', 'base') ?>
                            </div>
                            <div class="request-box__col">
                                <div class="form-group">
                                    <div class="request-box__date">
                                        <input name="cf_date" type="text" readonly="readonly"
                                               value="<?= date('d.m.Y', strtotime('+1 day')) ?>"
                                               class="datepicker form-control request-box__date-field"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input id="field-question-subj" name="cf_subj" class="form-control">
                                    <label for="field-question-subj" class="control-label">Тематика</label>
                                </div>
                                <div class="form-group">
                                    <input id="field-question-for" name="cf_for" class="form-control">
                                    <label for="field-question-for" class="control-label">Для кого</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="request-box__footer request-box__footer_align-right">
                        <div class="btn btn-bordered request-box__button request-box__button_next">Далі</div>
                    </div>
                </div>
                <div class="request-box__section request-box__section_2">
                    <div class="request-box__section-title">2/2</div>
                    <div class="request-box__body">
                        <div class="request-box__row">
                            <?= $form ?>
                        </div>
                    </div>
                    <div class="request-box__footer request-box__footer_align-wide">
                        <div class="btn btn-bordered request-box__button request-box__button_prev">Назад</div>
                        <button type="submit" class="btn btn-default request-box__button request-box__button_submit">
                            Відправити
                        </button>
                    </div>
                </div>
                <div class="request-box__success">
                    <div class="request-box__success-title">Ваша заявка на найсмачніший в світі торт відправлена!</div>
                    <div class="request-box__success-text">Для уточнення замовлення наш манеджер <br>зв’яжеться з вами в
                        найближчий час
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

