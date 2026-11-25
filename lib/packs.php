<?php
/**
 * Фірмові пакети в кошику (Основні налаштування → «Пакет з фото»: назва, ціна, фото, «Показувати»).
 *
 * Тема відповідає за перелік пакетів, вибір покупця (сесія) і запис пакета в замовлення.
 * Кошик (плагін cb-black-friday) лише рахує суму і показує рядок у підсумку — через хуки:
 *   apply_filters( 'cb_packs', [] )           — доступні пакети [ key, label, price, image ];
 *   apply_filters( 'cb_packs_title', '' )     — заголовок блоку;
 *   apply_filters( 'cb_pack_selected', null ) — обраний пакет або null;
 *   do_action( 'cb_pack_select', $key )       — обрати пакет ('' / 'none' / null — без пакета).
 */

use SimpleBasket\Helper;

final class CB_Packs {

	/** The session key stays the same as before, so choices made before the update are kept. */
	public const SESSION = 'cbbf_pack';

	private static ?array $cache = null;

	public static function init(): void {
		add_filter( 'cb_packs', [ __CLASS__, 'all' ] );
		add_filter( 'cb_packs_title', [ __CLASS__, 'title' ] );
		add_filter( 'cb_pack_selected', [ __CLASS__, 'selected' ] );
		add_action( 'cb_pack_select', [ __CLASS__, 'select' ] );
		// order: write the pack before the cart is emptied, forget the choice afterwards
		add_action( 'simplebasket_order/new_order', [ __CLASS__, 'save_to_order' ], 2 );
		add_action( 'simplebasket_order/new_order', static function () {
			self::select( null );
		}, 200 );
	}

	/**
	 * Packs shown to customers: rows with a name and «Показувати» on (old rows without the switch are shown).
	 *
	 * @return array<int,array{key:int,label:string,price:int,image:string}>
	 */
	public static function all(): array {
		if ( self::$cache !== null ) {
			return self::$cache;
		}
		self::$cache = [];
		if ( ! function_exists( 'get_field' ) ) {
			return self::$cache;
		}
		$group = get_field( 'paket_z_foto', 'option' );
		if ( empty( $group['pakety'] ) || ! is_array( $group['pakety'] ) ) {
			return self::$cache;
		}
		foreach ( $group['pakety'] as $i => $row ) {
			$label  = trim( (string) ( $row['name'] ?? '' ) );
			$active = $row['active'] ?? true;
			if ( $label === '' || $active === false || $active === 0 || $active === '0' ) {
				continue;
			}
			$price = (int) round( (float) preg_replace( '/[^\d.]/', '', (string) ( $row['price'] ?? '0' ) ) );
			$img   = '';
			if ( ! empty( $row['image'] ) ) {
				$img = is_array( $row['image'] )
					? ( $row['image']['sizes']['medium'] ?? $row['image']['url'] ?? '' )
					: (string) wp_get_attachment_image_url( (int) $row['image'], 'medium' );
			}
			// the key is the row number in the settings — it identifies the choice in the session
			self::$cache[] = [ 'key' => (int) $i, 'label' => $label, 'price' => max( 0, $price ), 'image' => $img ];
		}

		return self::$cache;
	}

	public static function title(): string {
		$group = function_exists( 'get_field' ) ? get_field( 'paket_z_foto', 'option' ) : null;
		$title = trim( (string) ( $group['title'] ?? '' ) );

		return $title !== '' ? $title : 'Фірмовий пакет з логотипом Candy Buffet';
	}

	public static function find( $key ): ?array {
		if ( $key === null || $key === '' || $key === 'none' || ! is_numeric( $key ) ) {
			return null;
		}
		foreach ( self::all() as $p ) {
			if ( $p['key'] === (int) $key ) {
				return $p;
			}
		}

		return null;
	}

	/** The customer's choice, if that pack is still offered (a hidden pack is dropped from the order). */
	public static function selected(): ?array {
		return class_exists( Helper::class ) ? self::find( Helper::get_session_value( self::SESSION, null ) ) : null;
	}

	/** @param int|string|null $key pack key; '', 'none' or null — no pack */
	public static function select( $key = null ): void {
		if ( ! class_exists( Helper::class ) ) {
			return;
		}
		$pack = self::find( is_array( $key ) ? null : $key );
		Helper::update_session_value( self::SESSION, $pack ? $pack['key'] : null );
	}

	/** Meta read by the order screen, the thank-you page and KeyCRM. */
	public static function save_to_order( $order ): void {
		$order_id = ( is_object( $order ) && isset( $order->ID ) ) ? (int) $order->ID : (int) $order;
		$pack     = self::selected();
		if ( $order_id <= 0 || ! $pack ) {
			return;
		}
		update_post_meta( $order_id, 'photo_pack_label', $pack['label'] );
		update_post_meta( $order_id, 'photo_pack_price', $pack['price'] );
	}
}

CB_Packs::init();
