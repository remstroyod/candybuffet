<?php
/**
 * Телефон з кодом країни для кошика
 */

defined( 'ABSPATH' ) || exit;

final class CB_Phone {

	public const VERSION = '1.0.0';
	public const UA      = '380';
	public const OTHER   = 'other';

	/** Fields normalized on checkout: phone field => country code field. */
	private const CHECKOUT_FIELDS = [ 'billing' => [ 'phone', 'phone_cc' ] ];

	/**
	 * Dial code => [ISO, name]. Ukraine first, then the countries customers most often order from.
	 * Додати країну: add_filter( 'cb_phone_countries', fn( $l ) => $l + [ '995' => [ 'GE', 'Грузія' ] ] );
	 */
	private const COUNTRIES = [
		'380' => [ 'UA', 'Україна' ],
		'48'  => [ 'PL', 'Польща' ],
		'49'  => [ 'DE', 'Німеччина' ],
		'420' => [ 'CZ', 'Чехія' ],
		'44'  => [ 'GB', 'Велика Британія' ],
		'1'   => [ 'US', 'США / Канада' ],
		'39'  => [ 'IT', 'Італія' ],
		'34'  => [ 'ES', 'Іспанія' ],
		'31'  => [ 'NL', 'Нідерланди' ],
		'33'  => [ 'FR', 'Франція' ],
		'43'  => [ 'AT', 'Австрія' ],
		'41'  => [ 'CH', 'Швейцарія' ],
		'32'  => [ 'BE', 'Бельгія' ],
		'353' => [ 'IE', 'Ірландія' ],
		'351' => [ 'PT', 'Португалія' ],
		'46'  => [ 'SE', 'Швеція' ],
		'47'  => [ 'NO', 'Норвегія' ],
		'45'  => [ 'DK', 'Данія' ],
		'358' => [ 'FI', 'Фінляндія' ],
		'370' => [ 'LT', 'Литва' ],
		'371' => [ 'LV', 'Латвія' ],
		'372' => [ 'EE', 'Естонія' ],
		'421' => [ 'SK', 'Словаччина' ],
		'36'  => [ 'HU', 'Угорщина' ],
		'40'  => [ 'RO', 'Румунія' ],
		'373' => [ 'MD', 'Молдова' ],
		'359' => [ 'BG', 'Болгарія' ],
		'30'  => [ 'GR', 'Греція' ],
		'357' => [ 'CY', 'Кіпр' ],
		'385' => [ 'HR', 'Хорватія' ],
		'90'  => [ 'TR', 'Туреччина' ],
		'995' => [ 'GE', 'Грузія' ],
		'972' => [ 'IL', 'Ізраїль' ],
		'971' => [ 'AE', 'ОАЕ' ],
	];

	public static function init(): void {
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_assets' ], 5 );
		add_action( 'wp_ajax_simplebasket_process_checkout', [ __CLASS__, 'normalize_checkout' ], 0 );
		add_action( 'wp_ajax_nopriv_simplebasket_process_checkout', [ __CLASS__, 'normalize_checkout' ], 0 );
	}

	/* ---------------- data ---------------- */

	/** @return array<string,array{0:string,1:string}> */
	public static function countries(): array {
		$list = function_exists( 'apply_filters' ) ? (array) apply_filters( 'cb_phone_countries', self::COUNTRIES ) : self::COUNTRIES;
		$out  = [];
		foreach ( $list as $code => $c ) {
			$out[ (string) $code ] = $c;
		}

		return [ self::UA => $out[ self::UA ] ?? self::COUNTRIES[ self::UA ] ] + $out;
	}

	/** «UA» → 🇺🇦 */
	public static function flag( string $iso ): string {
		$iso = strtoupper( $iso );
		if ( ! preg_match( '/^[A-Z]{2}$/', $iso ) ) {
			return '';
		}

		return mb_chr( 0x1F1E6 + ord( $iso[0] ) - 65, 'UTF-8' ) . mb_chr( 0x1F1E6 + ord( $iso[1] ) - 65, 'UTF-8' );
	}

	/**
	 * @param string $cc  dial code digits, 'other' or '' (= Ukraine, for old forms)
	 * @param string $raw what the customer typed
	 * @return array{full:string,valid:bool,ua:bool} full = digits with the country code, no «+»
	 */
	public static function normalize( string $cc, string $raw ): array {
		$raw = trim( $raw );
		$d   = preg_replace( '/\D+/', '', $raw );
		$cc  = $cc === self::OTHER ? self::OTHER : preg_replace( '/\D+/', '', $cc );
		if ( $cc === '' ) {
			$cc = self::UA;
		}

		if ( $raw !== '' && ( $raw[0] === '+' || strpos( $d, '00' ) === 0 ) ) {
			// a full international number wins over the selected code
			$full = strpos( $d, '00' ) === 0 && $raw[0] !== '+' ? substr( $d, 2 ) : $d;
		} elseif ( $cc === self::OTHER ) {
			$full = $d;
		} else {
			if ( $cc === self::UA ) {
				if ( strlen( $d ) === 12 && strpos( $d, self::UA ) === 0 ) {
					$d = substr( $d, 3 );            // 380671234567
				} elseif ( strlen( $d ) === 11 && strpos( $d, '80' ) === 0 ) {
					$d = substr( $d, 2 );            // 80671234567
				}
			}
			if ( $cc !== '39' && strlen( $d ) > 1 && $d[0] === '0' ) {
				$d = substr( $d, 1 );                // national trunk «0» is dropped (not in Italy)
			}
			$full = $cc . $d;
		}

		$ua = strpos( $full, self::UA ) === 0;

		return [ 'full' => $full, 'valid' => self::is_valid( $full ), 'ua' => $ua ];
	}

	/** Digits with the country code: Ukraine — exactly 12 digits, others — 8…15 (E.164). */
	public static function is_valid( string $full ): bool {
		$full = preg_replace( '/\D+/', '', $full );

		return strpos( $full, self::UA ) === 0 ? (bool) preg_match( '/^380\d{9}$/', $full ) : (bool) preg_match( '/^[1-9]\d{7,14}$/', $full );
	}

	/** «+380 67 123 45 67», «+48 601234567». */
	public static function format( string $full ): string {
		$d = preg_replace( '/\D+/', '', $full );
		if ( preg_match( '/^380(\d{2})(\d{3})(\d{2})(\d{2})$/', $d, $m ) ) {
			return '+380 ' . $m[1] . ' ' . $m[2] . ' ' . $m[3] . ' ' . $m[4];
		}
		$codes = array_keys( self::countries() );
		usort( $codes, static fn( $a, $b ) => strlen( (string) $b ) - strlen( (string) $a ) );
		foreach ( $codes as $code ) {
			if ( strpos( $d, (string) $code ) === 0 ) {
				return '+' . $code . ' ' . substr( $d, strlen( (string) $code ) );
			}
		}

		return $d !== '' ? '+' . $d : '';
	}

	/* ---------------- checkout ---------------- */

	/**
	 * Before Simple Basket and the cart plugin read the form: billing[phone_cc] + billing[phone] → billing[phone] = «+<full>».
	 * An invalid number stops the checkout with the same error format the cart uses.
	 */
	public static function normalize_checkout(): void {
		foreach ( self::CHECKOUT_FIELDS as $group => [ $phoneKey, $ccKey ] ) {
			if ( ! isset( $_POST[ $group ][ $ccKey ] ) || ! isset( $_POST[ $group ][ $phoneKey ] ) ) {
				continue; // an old form without the country selector — nothing to do
			}
			$n = self::normalize( (string) wp_unslash( $_POST[ $group ][ $ccKey ] ), (string) wp_unslash( $_POST[ $group ][ $phoneKey ] ) );
			if ( ! $n['valid'] ) {
				wp_send_json_error( [ 'errors' => [
					$group . '[' . $phoneKey . ']' => $n['ua'] ? 'Вкажіть телефон у форматі +380 XX XXX XX XX' : 'Перевірте номер телефону: код країни і номер повністю',
				] ] );
			}
			$_POST[ $group ][ $phoneKey ] = '+' . $n['full'];
			unset( $_POST[ $group ][ $ccKey ] );
		}
	}

	/* ---------------- field ---------------- */

	public static function register_assets(): void {
		$dir  = get_template_directory() . '/assets/phone/';
		$base = get_template_directory_uri() . '/assets/phone/';
		wp_register_script( 'cb-phone', $base . 'phone.js', [], (string) @filemtime( $dir . 'phone.js' ), true );
		wp_register_style( 'cb-phone', $base . 'phone.css', [], (string) @filemtime( $dir . 'phone.css' ) );
	}

	/**
	 * Phone field with the country selector (markup of the theme's floating-label .form-group).
	 *
	 * @param array{name?:string,id?:string,label?:string,required?:bool,default_cc?:string} $args
	 */
	public static function field( array $args = [] ): string {
		$a = array_merge( [
			'name'       => 'billing[phone]',
			'id'         => 'field-phone',
			'label'      => 'Телефон',
			'required'   => true,
			'default_cc' => self::UA,
		], $args );
		$ccName = preg_match( '/^(.+)\[([^\]]+)\]$/', $a['name'], $m ) ? $m[1] . '[' . $m[2] . '_cc]' : $a['name'] . '_cc';

		wp_enqueue_script( 'cb-phone' );
		wp_enqueue_style( 'cb-phone' );

		$countries = self::countries();
		$def       = isset( $countries[ $a['default_cc'] ] ) ? $a['default_cc'] : self::UA;
		ob_start();
		?>
		<div class="form-group cb-phone" data-cb-phone>
			<div class="cb-phone__row">
				<div class="cb-phone__cc">
					<span class="cb-phone__cc-view" data-cb-phone-view aria-hidden="true"><?= esc_html( self::flag( $countries[ $def ][0] ) . ' +' . $def ) ?></span>
					<select name="<?= esc_attr( $ccName ) ?>" data-cb-phone-cc aria-label="Код країни: <?= esc_attr( $a['label'] ) ?>" autocomplete="off">
						<?php foreach ( $countries as $code => $c ) : $flag = self::flag( $c[0] ); ?>
							<option value="<?= esc_attr( $code ) ?>" data-flag="<?= esc_attr( $flag ) ?>"<?= (string) $code === $def ? ' selected' : '' ?>><?= esc_html( trim( $flag . ' ' . $c[1] . ' +' . $code ) ) ?></option>
						<?php endforeach; ?>
						<option value="<?= esc_attr( self::OTHER ) ?>" data-flag="🌐">🌐 Інша країна — номер з кодом</option>
					</select>
				</div>
				<input name="<?= esc_attr( $a['name'] ) ?>" id="<?= esc_attr( $a['id'] ) ?>" type="tel" inputmode="tel" autocomplete="tel-national"
				       class="form-control" data-cb-phone-input placeholder="67 123 45 67"<?= $a['required'] ? ' aria-required="true"' : '' ?>>
			</div>
			<label for="<?= esc_attr( $a['id'] ) ?>" class="control-label"><?= esc_html( $a['label'] ) ?></label>
		</div>
		<?php

		return (string) ob_get_clean();
	}
}

CB_Phone::init();
