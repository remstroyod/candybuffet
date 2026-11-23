<?php

add_action('wp_ajax_nopriv_contactForm', 'contactForm');
add_action('wp_ajax_contactForm', 'contactForm');

function contactForm() {
	if (!isset($_POST['cf_name'], $_POST['cf_phone'], $_POST['cf_email'])) {
		wp_die();
	}

	$html_msg = '<p>Имя: ' . $_POST['cf_name'] . '</p>';
	$html_msg .= '<p>Email: ' . $_POST['cf_email'] . '</p>';
	$html_msg .= '<p>Тел: ' . $_POST['cf_phone'] . '</p>';
	$html_msg .= '<p>Сообщение: ' . $_POST['cf_message'] . '</p>';

	wp_mail(get_option('admin_email'), 'Контактная форма | ' . get_bloginfo('name'), $html_msg, ['Content-Type: text/html; charset=UTF-8']);

	wp_die();
}

add_action('wp_ajax_nopriv_corporateForm', 'corporateForm');
add_action('wp_ajax_corporateForm', 'corporateForm');

function corporateForm() {
	if ( ! isset( $_POST['cf_name'], $_POST['cf_phone'], $_POST['page'] ) ) {
		wp_die();
	}

	$html_msg = '<p>Имя: ' . $_POST['cf_name'] . '</p>';
	$html_msg .= '<p>Тел: ' . $_POST['cf_phone'] . '</p>';

	wp_mail( get_option( 'admin_email' ), 'Форма заявки на корп. заказ | ' . get_bloginfo( 'name' ), $html_msg, [ 'Content-Type: text/html; charset=UTF-8' ] );
	$file = get_field('presentation_file', 103);
	if ($file) {
	  wp_send_json_success(['file' => $file['file']['url']]);
	}
}

function media_upload($file_id) {

	if (!isset($_FILES[ $file_id ]) || !$_FILES[ $file_id ]['size'] || $_FILES[ $file_id ]['error']) {
		return false;
	}

	// These files need to be included as dependencies when on the front end.
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/media.php');

	//add_filter('import_upload_size_limit', function () {});

	add_filter('upload_mimes', function ($mimes) {
		$mimes = array_filter($mimes, function ($val) {
			return strpos($val, 'video/') === false && strpos($val, 'audio/') === false && strpos($val, 'zip') === false;
		});

		return $mimes;
	});

	add_filter('upload_dir', function ($dir) {
		return [
			'path'    => WP_CONTENT_DIR . '/uploads/userfiles',
			'url'     => WP_CONTENT_URL . '/uploads/userfiles',
			'subdir'  => '',
			'baseurl' => WP_CONTENT_URL . '/uploads/userfiles',
			'basedir' => WP_CONTENT_DIR . '/uploads/userfiles',
			'error'   => false
		];
	});

	$file = wp_handle_upload($_FILES[ $file_id ], ['test_form' => false]);

	if (isset($file['error'])) {
		wp_die(json_encode(new WP_Error('upload_error', $file['error'])));
	}

	$name = $_FILES[ $file_id ]['name'];
	$ext  = strtoupper(pathinfo($name, PATHINFO_EXTENSION));
	$name = wp_basename($name, ".$ext");

	$url = $file['url'];

	$file = $file['file'];


	return ['name' => $name, 'url' => $url, 'ext' => $ext, 'size' => size_format(filesize($file))];
}

add_action('wp_ajax_nopriv_orderForm', 'orderForm');
add_action('wp_ajax_orderForm', 'orderForm');

function orderForm() {

	if (!empty($_POST)) {
		$basket = new SimpleBasketOrderForm();
		//$total = $basket->getTotal();

		if ($basket->handle()) {
			$response = ['status' => 'success'];

			/*$event = new stdClass();
			$event->eventTypeKey = 'first_buy';
			$event->keyValue = $_POST[$basket::USER_EMAIL];

			send_esputnik_event($event);*/

			if ($_POST['payment'] == 'card') {
				//$response['liqpay'] = get_liqpay_form($total);
			}

			echo json_encode($response);
		} else {
			echo json_encode(['status' => 'fail']);
		}
	}


	die;
}
