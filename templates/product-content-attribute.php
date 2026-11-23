<? if (isset($label, $field) && ! empty($field)): ?>
  <div class="card__data-item">
    <? if (isset($modal)): ?>
      <a href="#modal-help-<?= $modal ?>" data-toggle="modal" class="card__hint card__hint_<?= $modal ?>">
        <? if (! empty($modal_hint)): ?>
          <span class="card__hint-label"><?= $modal_hint ?></span>
        <? else: ?>
          <span class="card__hint-icon"></span>
        <? endif ?>
      </a>
    <? elseif (isset($tooltip)):
      get_template_part('templates/product', 'tooltip-'.$tooltip);
    endif ?>
    <div class="card__data-label"><?= $label ?></div>

    <? if (is_array($field)):
      $is_qty_field = $input_name === 'product_quantity';
      ?>

      <div class="card__data-control">
        <select
          class="select2-simple select2-<?= $input_name ?> <?= $is_qty_field ? 'simplebasket__weight' : 'simplebasket__attr' ?>"
          tabindex="-1" aria-hidden="true">
          <? if ($is_qty_field): ?>
            <? foreach ($field as $value): ?>
              <option value="<?= apply_filters('simplebasket__qty_normalize', $value) ?>"><?= $value ?></option>
            <? endforeach ?>
          <? else: ?>
            <? foreach ($field as $key => $value): ?>
              <option data-id="<?= $key ?>" value="<?= $value ?>"><?= $value ?></option>
            <? endforeach ?>
          <? endif; ?>
        </select>
      </div>
    <? elseif (is_string($field)): ?>
      <? if ($input_name === 'product_attr_delivery_date'): ?>
        <div class="card__date">
          <input placeholder="Обрати дату" name="<?= $input_name ?>" data-min="<?= $field ?>" type="text" readonly="readonly"
                 class="simplebasket__attr card__date-field datepicker form-control"/>
        </div>
      <? else: ?>
        <div class="card__data-value"><?= $field ?></div>
      <? endif ?>
    <? endif ?>
  </div>
<? endif; ?>
