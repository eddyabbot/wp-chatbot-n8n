<?php
/**
 * Gestisce il salvataggio e la lettura delle impostazioni del plugin
 * tramite la WordPress Options API.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPCN_Settings {

	/**
	 * Legge un'opzione dal database.
	 */
	public static function get( $key, $default = '' ) {
		return get_option( 'wpcn_' . $key, $default );
	}

	/**
	 * Salva tutte le impostazioni. Se un campo non supera la validazione
	 * il valore in DB viene lasciato invariato (continue 2).
	 */
	public static function save( array $data ) {
		$fields = array(
			// Generale
			'enabled'                 => 'bool',
			'webhook_url'             => 'url',
			'mode'                    => 'mode',
			'load_previous_session'   => 'bool',
			'show_welcome_screen'     => 'bool',
			'enable_streaming'        => 'bool',
			'allow_file_uploads'      => 'bool',
			'allowed_mime_types'      => 'text',
			'position'                => 'position',
			// Testi
			'title'                   => 'text',
			'subtitle'                => 'text',
			'footer_text'             => 'text',
			'get_started'             => 'text',
			'input_placeholder'       => 'text',
			'initial_messages'        => 'textarea',
			// Colori
			'primary_color'           => 'color',
			'secondary_color'         => 'color',
			'header_background'       => 'color',
			'header_color'            => 'color',
			'bot_msg_background'      => 'color',
			'bot_msg_color'           => 'color',
			'user_msg_background'     => 'color',
			'user_msg_color'          => 'color',
			'toggle_background'       => 'color_nullable',
			'toggle_color'            => 'color',
			'body_background'         => 'color',
			// Dimensioni
			'window_width'            => 'css_value',
			'window_height'           => 'css_value',
			'toggle_size'             => 'css_value',
			'border_radius'           => 'css_value',
		);

		foreach ( $fields as $key => $type ) {
			$raw = isset( $data[ $key ] ) ? $data[ $key ] : '';

			switch ( $type ) {
				case 'bool':
					$value = isset( $data[ $key ] ) ? '1' : '0';
					break;

				case 'url':
					$value = esc_url_raw( $raw );
					break;

				case 'text':
					$value = sanitize_text_field( $raw );
					break;

				case 'textarea':
					$value = sanitize_textarea_field( $raw );
					break;

				case 'color':
					if ( ! preg_match( '/^#[0-9A-Fa-f]{6}$/', $raw ) ) {
						continue 2; // lascia invariato il valore in DB
					}
					$value = $raw;
					break;

				case 'color_nullable':
					// Può essere vuoto (usa default) oppure un hex valido
					if ( '' !== trim( $raw ) && ! preg_match( '/^#[0-9A-Fa-f]{6}$/', $raw ) ) {
						continue 2;
					}
					$value = trim( $raw );
					break;

				case 'mode':
					$value = in_array( $raw, array( 'window', 'fullscreen' ), true ) ? $raw : 'window';
					break;

				case 'position':
					$value = in_array( $raw, array( 'bottom-right', 'bottom-left' ), true ) ? $raw : 'bottom-right';
					break;

				case 'css_value':
					// Accetta: 400px, 600px, 0.25rem, 1em, ecc.
					$sanitized = sanitize_text_field( trim( $raw ) );
					if ( ! preg_match( '/^[\d.]+\s*(px|rem|em|%|vh|vw)?$/', $sanitized ) ) {
						continue 2;
					}
					$value = $sanitized;
					break;

				default:
					$value = sanitize_text_field( $raw );
			}

			update_option( 'wpcn_' . $key, $value );
		}
	}

	/**
	 * Restituisce tutte le impostazioni con i valori di default n8n.
	 */
	public static function all() {
		return array(
			// Generale
			'enabled'                 => self::get( 'enabled', '0' ),
			'webhook_url'             => self::get( 'webhook_url', '' ),
			'mode'                    => self::get( 'mode', 'window' ),
			'load_previous_session'   => self::get( 'load_previous_session', '1' ),
			'show_welcome_screen'     => self::get( 'show_welcome_screen', '0' ),
			'enable_streaming'        => self::get( 'enable_streaming', '0' ),
			'allow_file_uploads'      => self::get( 'allow_file_uploads', '0' ),
			'allowed_mime_types'      => self::get( 'allowed_mime_types', '' ),
			'position'                => self::get( 'position', 'bottom-right' ),
			// Testi
			'title'                   => self::get( 'title', 'Hi there! 👋' ),
			'subtitle'                => self::get( 'subtitle', "Start a chat. We're here to help you 24/7." ),
			'footer_text'             => self::get( 'footer_text', '' ),
			'get_started'             => self::get( 'get_started', 'New Conversation' ),
			'input_placeholder'       => self::get( 'input_placeholder', 'Type your question..' ),
			'initial_messages'        => self::get( 'initial_messages', "Hi there! 👋\nMy name is Nathan. How can I assist you today?" ),
			// Colori
			'primary_color'           => self::get( 'primary_color', '#e74266' ),
			'secondary_color'         => self::get( 'secondary_color', '#20b69e' ),
			'header_background'       => self::get( 'header_background', '#101330' ),
			'header_color'            => self::get( 'header_color', '#f2f4f8' ),
			'bot_msg_background'      => self::get( 'bot_msg_background', '#ffffff' ),
			'bot_msg_color'           => self::get( 'bot_msg_color', '#101330' ),
			'user_msg_background'     => self::get( 'user_msg_background', '#20b69e' ),
			'user_msg_color'          => self::get( 'user_msg_color', '#ffffff' ),
			'toggle_background'       => self::get( 'toggle_background', '' ),
			'toggle_color'            => self::get( 'toggle_color', '#ffffff' ),
			'body_background'         => self::get( 'body_background', '#f2f4f8' ),
			// Dimensioni
			'window_width'            => self::get( 'window_width', '400px' ),
			'window_height'           => self::get( 'window_height', '600px' ),
			'toggle_size'             => self::get( 'toggle_size', '64px' ),
			'border_radius'           => self::get( 'border_radius', '0.25rem' ),
		);
	}
}
