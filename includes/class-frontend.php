<?php
/**
 * Inietta il widget n8n nel footer del sito frontend.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPCN_Frontend {

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'inject_widget' ) );
	}

	public function inject_widget() {
		$s = WPCN_Settings::all();

		if ( '1' !== $s['enabled'] ) return;
		if ( empty( $s['webhook_url'] ) ) return;

		// --- Messaggi iniziali: textarea → array (uno per riga) ---
		$messages = array_values(
			array_filter(
				array_map( 'trim', explode( "\n", $s['initial_messages'] ) )
			)
		);
		if ( empty( $messages ) ) {
			$messages = array( 'Hi there! 👋' );
		}

		// --- Config JS ---
		$config = array(
			'webhookUrl'       => $s['webhook_url'],
			'mode'             => 'window',
			'chatInputKey'     => 'chatInput',
			'chatSessionKey'   => 'sessionId',
			'initialMessages'  => $messages,
			'defaultLanguage'  => 'en',
			'i18n'             => array(
				'en' => array(
					'title'            => $s['title'],
					'subtitle'         => $s['subtitle'],
					'footer'           => $s['footer_text'],
					'getStarted'       => $s['get_started'],
					'inputPlaceholder' => $s['input_placeholder'],
				),
			),
		);

		// --- Shades automatici dal colore primario e secondario ---
		$primary           = $s['primary_color'];
		$primary_shade_50  = self::darken( $primary, 5 );
		$primary_shade_100 = self::darken( $primary, 10 );
		$secondary         = $s['secondary_color'];
		$secondary_shade   = self::darken( $secondary, 5 );

		// Toggle background: se vuoto usa il primario
		$toggle_bg   = ! empty( $s['toggle_background'] ) ? $s['toggle_background'] : $primary;
		$config_json = wp_json_encode( $config );
		?>

		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" />

		<style>
		:root {
			/* Colori primari */
			--chat--color--primary:            <?php echo esc_attr( $primary ); ?>;
			--chat--color--primary-shade-50:   <?php echo esc_attr( $primary_shade_50 ); ?>;
			--chat--color--primary--shade-100: <?php echo esc_attr( $primary_shade_100 ); ?>;

			/* Colori secondari */
			--chat--color--secondary:          <?php echo esc_attr( $secondary ); ?>;
			--chat--color-secondary-shade-50:  <?php echo esc_attr( $secondary_shade ); ?>;

			/* Header */
			--chat--header--background:        <?php echo esc_attr( $s['header_background'] ); ?>;
			--chat--header--color:             <?php echo esc_attr( $s['header_color'] ); ?>;

			/* Messaggi bot */
			--chat--message--bot--background:  <?php echo esc_attr( $s['bot_msg_background'] ); ?>;
			--chat--message--bot--color:       <?php echo esc_attr( $s['bot_msg_color'] ); ?>;

			/* Messaggi utente */
			--chat--message--user--background: <?php echo esc_attr( $s['user_msg_background'] ); ?>;
			--chat--message--user--color:      <?php echo esc_attr( $s['user_msg_color'] ); ?>;

			/* Toggle button */
			--chat--toggle--background:        <?php echo esc_attr( $toggle_bg ); ?>;
			--chat--toggle--hover--background: <?php echo esc_attr( $primary_shade_50 ); ?>;
			--chat--toggle--active--background:<?php echo esc_attr( $primary_shade_100 ); ?>;
			--chat--toggle--color:             <?php echo esc_attr( $s['toggle_color'] ); ?>;
			--chat--toggle--size:              <?php echo esc_attr( $s['toggle_size'] ); ?>;

			/* Body / footer */
			--chat--body--background:          <?php echo esc_attr( $s['body_background'] ); ?>;
			--chat--footer--background:        <?php echo esc_attr( $s['body_background'] ); ?>;

			/* Dimensioni finestra */
			--chat--window--width:             <?php echo esc_attr( $s['window_width'] ); ?>;
			--chat--window--height:            <?php echo esc_attr( $s['window_height'] ); ?>;

			/* Border radius globale */
			--chat--border-radius:             <?php echo esc_attr( $s['border_radius'] ); ?>;
		}
		</style>

		<!--
			esm.sh viene usato al posto di jsDelivr perché @n8n/chat dipende da Vue
			con bare imports ('import vue') che il browser non risolve nativamente.
			esm.sh bundla tutte le dipendenze automaticamente.
		-->
		<script type="module">
			import { createChat } from 'https://esm.sh/@n8n/chat';
			createChat( <?php echo $config_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> );
		</script>

		<?php
	}

	/**
	 * Scurisce un colore hex di una percentuale data.
	 *
	 * @param  string $hex     Es. '#e74266'
	 * @param  int    $percent Percentuale di scurimento (0–100)
	 * @return string          Hex risultante
	 */
	private static function darken( $hex, $percent ) {
		$hex = ltrim( $hex, '#' );
		if ( strlen( $hex ) !== 6 ) return '#' . $hex;

		$r = max( 0, (int) ( hexdec( substr( $hex, 0, 2 ) ) * ( 1 - $percent / 100 ) ) );
		$g = max( 0, (int) ( hexdec( substr( $hex, 2, 2 ) ) * ( 1 - $percent / 100 ) ) );
		$b = max( 0, (int) ( hexdec( substr( $hex, 4, 2 ) ) * ( 1 - $percent / 100 ) ) );

		return sprintf( '#%02x%02x%02x', $r, $g, $b );
	}
}
