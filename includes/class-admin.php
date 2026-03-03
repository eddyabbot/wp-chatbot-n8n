<?php
/**
 * Pagina impostazioni nel pannello WordPress Admin.
 * Organizzata in 4 tab: Generale, Testi, Colori, Dimensioni.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPCN_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function add_menu() {
		add_options_page(
			__( 'ChatBot N8n', 'wp-chatbot-n8n' ),
			__( 'ChatBot N8n', 'wp-chatbot-n8n' ),
			'manage_options',
			'wp-chatbot-n8n',
			array( $this, 'render_page' )
		);
	}

	public function enqueue_assets( $hook ) {
		if ( 'settings_page_wp-chatbot-n8n' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'wpcn-admin', WPCN_URL . 'assets/css/admin.css', array(), WPCN_VERSION );
		wp_enqueue_script( 'wpcn-admin', WPCN_URL . 'assets/js/admin.js', array( 'wp-color-picker' ), WPCN_VERSION, true );
	}

	public function save_settings() {
		if ( ! isset( $_POST['wpcn_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpcn_nonce'] ) ), 'wpcn_save_settings' ) ) {
			wp_die( esc_html__( 'Verifica di sicurezza fallita.', 'wp-chatbot-n8n' ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		WPCN_Settings::save( $_POST );

		wp_safe_redirect(
			add_query_arg( 'updated', '1', admin_url( 'options-general.php?page=wp-chatbot-n8n' ) )
		);
		exit;
	}

	public function render_page() {
		$s = WPCN_Settings::all();
		?>
		<div class="wrap wpcn-wrap">
			<h1><?php esc_html_e( 'ChatBot N8n', 'wp-chatbot-n8n' ); ?></h1>

			<?php if ( isset( $_GET['updated'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Impostazioni salvate.', 'wp-chatbot-n8n' ); ?></p>
				</div>
			<?php endif; ?>

			<nav class="nav-tab-wrapper wpcn-tabs">
				<a href="#" class="nav-tab" data-tab="general"><?php esc_html_e( 'Generale', 'wp-chatbot-n8n' ); ?></a>
				<a href="#" class="nav-tab" data-tab="texts"><?php esc_html_e( 'Testi', 'wp-chatbot-n8n' ); ?></a>
				<a href="#" class="nav-tab" data-tab="colors"><?php esc_html_e( 'Colori', 'wp-chatbot-n8n' ); ?></a>
				<a href="#" class="nav-tab" data-tab="dimensions"><?php esc_html_e( 'Dimensioni', 'wp-chatbot-n8n' ); ?></a>
			</nav>

			<form method="post" action="">
				<?php wp_nonce_field( 'wpcn_save_settings', 'wpcn_nonce' ); ?>

				<!-- ===================== TAB: GENERALE ===================== -->
				<div id="wpcn-panel-general" class="wpcn-tab-panel">
					<table class="form-table" role="presentation">

						<tr>
							<th scope="row"><label for="wpcn-enabled"><?php esc_html_e( 'Abilita chatbot', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<label class="wpcn-toggle">
									<input type="checkbox" id="wpcn-enabled" name="enabled" value="1" <?php checked( $s['enabled'], '1' ); ?> />
									<span class="wpcn-toggle-slider"></span>
								</label>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-webhook-url"><?php esc_html_e( 'URL Webhook N8n', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="url" id="wpcn-webhook-url" name="webhook_url"
									value="<?php echo esc_attr( $s['webhook_url'] ); ?>"
									class="regular-text" placeholder="https://your-n8n.com/webhook/xxx" />
								<p class="description"><?php esc_html_e( 'URL di produzione del workflow n8n con nodo "Chat Trigger".', 'wp-chatbot-n8n' ); ?></p>
							</td>
						</tr>

					</table>
				</div><!-- /panel-general -->

				<!-- ===================== TAB: TESTI ===================== -->
				<div id="wpcn-panel-texts" class="wpcn-tab-panel">
					<table class="form-table" role="presentation">

						<tr>
							<th scope="row"><label for="wpcn-title"><?php esc_html_e( 'Titolo header', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-title" name="title"
									value="<?php echo esc_attr( $s['title'] ); ?>"
									class="regular-text" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-subtitle"><?php esc_html_e( 'Sottotitolo header', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-subtitle" name="subtitle"
									value="<?php echo esc_attr( $s['subtitle'] ); ?>"
									class="regular-text" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-footer-text"><?php esc_html_e( 'Testo footer', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-footer-text" name="footer_text"
									value="<?php echo esc_attr( $s['footer_text'] ); ?>"
									class="regular-text" />
								<p class="description"><?php esc_html_e( 'Lascia vuoto per non mostrare il footer.', 'wp-chatbot-n8n' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-get-started"><?php esc_html_e( 'Testo pulsante "Inizia"', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-get-started" name="get_started"
									value="<?php echo esc_attr( $s['get_started'] ); ?>"
									class="regular-text" />
								<p class="description"><?php esc_html_e( 'Visibile nella schermata di benvenuto.', 'wp-chatbot-n8n' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-input-placeholder"><?php esc_html_e( 'Placeholder input', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-input-placeholder" name="input_placeholder"
									value="<?php echo esc_attr( $s['input_placeholder'] ); ?>"
									class="regular-text" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-initial-messages"><?php esc_html_e( 'Messaggi iniziali', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<textarea id="wpcn-initial-messages" name="initial_messages"
									rows="4" class="large-text"><?php echo esc_textarea( $s['initial_messages'] ); ?></textarea>
								<p class="description"><?php esc_html_e( 'Un messaggio per riga. Vengono mostrati in sequenza all\'apertura della chat.', 'wp-chatbot-n8n' ); ?></p>
							</td>
						</tr>

					</table>
				</div><!-- /panel-texts -->

				<!-- ===================== TAB: COLORI ===================== -->
				<div id="wpcn-panel-colors" class="wpcn-tab-panel">
					<table class="form-table" role="presentation">

						<tr class="wpcn-section-header"><th colspan="2"><h3><?php esc_html_e( 'Colori base', 'wp-chatbot-n8n' ); ?></h3></th></tr>

						<tr>
							<th scope="row"><label for="wpcn-primary-color"><?php esc_html_e( 'Colore primario', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-primary-color" name="primary_color"
									value="<?php echo esc_attr( $s['primary_color'] ); ?>"
									class="wpcn-color-picker" />
								<p class="description"><?php esc_html_e( 'Usato per header, pulsante toggle e accenti.', 'wp-chatbot-n8n' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-secondary-color"><?php esc_html_e( 'Colore secondario', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-secondary-color" name="secondary_color"
									value="<?php echo esc_attr( $s['secondary_color'] ); ?>"
									class="wpcn-color-picker" />
								<p class="description"><?php esc_html_e( 'Usato per i messaggi dell\'utente e i pulsanti di azione.', 'wp-chatbot-n8n' ); ?></p>
							</td>
						</tr>

						<tr class="wpcn-section-header"><th colspan="2"><h3><?php esc_html_e( 'Header', 'wp-chatbot-n8n' ); ?></h3></th></tr>

						<tr>
							<th scope="row"><label for="wpcn-header-background"><?php esc_html_e( 'Sfondo header', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-header-background" name="header_background"
									value="<?php echo esc_attr( $s['header_background'] ); ?>"
									class="wpcn-color-picker" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-header-color"><?php esc_html_e( 'Testo header', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-header-color" name="header_color"
									value="<?php echo esc_attr( $s['header_color'] ); ?>"
									class="wpcn-color-picker" />
							</td>
						</tr>

						<tr class="wpcn-section-header"><th colspan="2"><h3><?php esc_html_e( 'Messaggi', 'wp-chatbot-n8n' ); ?></h3></th></tr>

						<tr>
							<th scope="row"><label for="wpcn-bot-msg-background"><?php esc_html_e( 'Sfondo messaggi bot', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-bot-msg-background" name="bot_msg_background"
									value="<?php echo esc_attr( $s['bot_msg_background'] ); ?>"
									class="wpcn-color-picker" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-bot-msg-color"><?php esc_html_e( 'Testo messaggi bot', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-bot-msg-color" name="bot_msg_color"
									value="<?php echo esc_attr( $s['bot_msg_color'] ); ?>"
									class="wpcn-color-picker" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-user-msg-background"><?php esc_html_e( 'Sfondo messaggi utente', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-user-msg-background" name="user_msg_background"
									value="<?php echo esc_attr( $s['user_msg_background'] ); ?>"
									class="wpcn-color-picker" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-user-msg-color"><?php esc_html_e( 'Testo messaggi utente', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-user-msg-color" name="user_msg_color"
									value="<?php echo esc_attr( $s['user_msg_color'] ); ?>"
									class="wpcn-color-picker" />
							</td>
						</tr>

						<tr class="wpcn-section-header"><th colspan="2"><h3><?php esc_html_e( 'Pulsante toggle', 'wp-chatbot-n8n' ); ?></h3></th></tr>

						<tr>
							<th scope="row"><label for="wpcn-toggle-background"><?php esc_html_e( 'Sfondo pulsante toggle', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-toggle-background" name="toggle_background"
									value="<?php echo esc_attr( $s['toggle_background'] ); ?>"
									class="wpcn-color-picker" />
								<p class="description"><?php esc_html_e( 'Lascia vuoto per usare il colore primario.', 'wp-chatbot-n8n' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-toggle-color"><?php esc_html_e( 'Colore icona toggle', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-toggle-color" name="toggle_color"
									value="<?php echo esc_attr( $s['toggle_color'] ); ?>"
									class="wpcn-color-picker" />
							</td>
						</tr>

						<tr class="wpcn-section-header"><th colspan="2"><h3><?php esc_html_e( 'Finestra chat', 'wp-chatbot-n8n' ); ?></h3></th></tr>

						<tr>
							<th scope="row"><label for="wpcn-body-background"><?php esc_html_e( 'Sfondo area messaggi', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-body-background" name="body_background"
									value="<?php echo esc_attr( $s['body_background'] ); ?>"
									class="wpcn-color-picker" />
							</td>
						</tr>

					</table>
				</div><!-- /panel-colors -->

				<!-- ===================== TAB: DIMENSIONI ===================== -->
				<div id="wpcn-panel-dimensions" class="wpcn-tab-panel">
					<table class="form-table" role="presentation">

						<tr>
							<th scope="row"><label for="wpcn-window-width"><?php esc_html_e( 'Larghezza finestra', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-window-width" name="window_width"
									value="<?php echo esc_attr( $s['window_width'] ); ?>"
									class="small-text" placeholder="400px" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-window-height"><?php esc_html_e( 'Altezza finestra', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-window-height" name="window_height"
									value="<?php echo esc_attr( $s['window_height'] ); ?>"
									class="small-text" placeholder="600px" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-toggle-size"><?php esc_html_e( 'Dimensione pulsante toggle', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-toggle-size" name="toggle_size"
									value="<?php echo esc_attr( $s['toggle_size'] ); ?>"
									class="small-text" placeholder="64px" />
							</td>
						</tr>

						<tr>
							<th scope="row"><label for="wpcn-border-radius"><?php esc_html_e( 'Border radius', 'wp-chatbot-n8n' ); ?></label></th>
							<td>
								<input type="text" id="wpcn-border-radius" name="border_radius"
									value="<?php echo esc_attr( $s['border_radius'] ); ?>"
									class="small-text" placeholder="0.25rem" />
								<p class="description"><?php esc_html_e( 'Applicato a finestra, messaggi e pulsanti. Es: 0 per angoli netti, 1rem per arrotondati.', 'wp-chatbot-n8n' ); ?></p>
							</td>
						</tr>

					</table>
				</div><!-- /panel-dimensions -->

				<?php submit_button( __( 'Salva impostazioni', 'wp-chatbot-n8n' ) ); ?>
			</form>
		</div><!-- /wpcn-wrap -->
		<?php
	}
}
