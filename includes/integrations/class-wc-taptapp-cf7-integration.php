<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Taptapp_CF7_Integration {

	private static $instance = null;

	private function __construct() {
		// Verifica si el plugin Contact Form 7 está activo antes de agregar acciones
		if ( $this->is_cf7_active() ) {
			add_action( 'wpcf7_editor_panels', array( $this, 'add_whatsapp_settings_panel' ) );
			add_action( 'wpcf7_save_contact_form', array( $this, 'save_whatsapp_settings' ) );
			add_action( 'wpcf7_mail_sent', array( $this, 'send_cf7_whatsapp_notification' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		}
	}

	public static function init() {
		if ( self::$instance == null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function is_cf7_active() {
		// Comprueba si el plugin Contact Form 7 está activo
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( 'contact-form-7/wp-contact-form-7.php' );
	}

	public function add_whatsapp_settings_panel( $panels ) {
		$panels['whatsapp-settings'] = array(
			'title' => __( 'WhatsApp Settings', 'wc-taptapp-notifications' ),
			'callback' => array( $this, 'render_whatsapp_settings_panel' ),
		);
		return $panels;
	}

	public function enqueue_admin_styles() {
		wp_enqueue_style( 'wc-taptapp-admin-styles', plugins_url( 'css/admin-styles.css', __FILE__ ) );
	}

	public function render_whatsapp_settings_panel( $post ) {
		$whatsapp_enabled = get_post_meta( $post->id(), '_wpcf7_whatsapp_enabled', true );
		$phone_field_name = get_post_meta( $post->id(), '_wpcf7_whatsapp_phone_field', true );
		$whatsapp_number = get_post_meta( $post->id(), '_wpcf7_whatsapp_number', true );
		$client_message_template = get_post_meta( $post->id(), '_wpcf7_client_message_template', true );
		$configured_number_message_template = get_post_meta( $post->id(), '_wpcf7_configured_number_message_template', true );

		$checked = $whatsapp_enabled ? 'checked="checked"' : '';
		?>
		<style>
			.whatsapp-settings-panel {
				background-color: #f9f9f9;
				border: 1px solid #e0e0e0;
				padding: 20px;
				border-radius: 5px;
			}
			.whatsapp-settings-panel h2 {
				margin-top: 0;
			}
			.whatsapp-settings-panel label {
				display: block;
				font-weight: bold;
				margin-bottom: 5px;
			}
			.whatsapp-settings-panel input[type="text"],
			.whatsapp-settings-panel textarea {
				width: 100%;
				padding: 8px;
				border: 1px solid #ccc;
				border-radius: 3px;
				margin-bottom: 15px;
			}
			.whatsapp-settings-panel textarea {
				resize: vertical;
			}
		</style>
		<div class="whatsapp-settings-panel">
			<h2><?php echo esc_html__( 'WhatsApp Settings', 'wc-taptapp-notifications' ); ?></h2>
			<p>
				<label>
					<input type="checkbox" name="whatsapp_enabled" value="1" <?php echo $checked; ?> />
					<?php echo esc_html__( 'Enviar notificaciones de WhatsApp', 'wc-taptapp-notifications' ); ?>
				</label>
			</p>
			<p>
				<label><?php echo esc_html__( 'Nombre del campo de teléfono:', 'wc-taptapp-notifications' ); ?></label>
				<input type="text" name="whatsapp_phone_field" value="<?php echo esc_attr( $phone_field_name ); ?>" />
			</p>
			<p>
				<label><?php echo esc_html__( 'Número de WhatsApp para enviar notificaciones:', 'wc-taptapp-notifications' ); ?></label>
				<input type="text" name="whatsapp_number" value="<?php echo esc_attr( $whatsapp_number ); ?>" />
			</p>
			<p>
				<label><?php echo esc_html__( 'Mensaje para el cliente (usa [campo] para placeholders):', 'wc-taptapp-notifications' ); ?></label>
				<textarea name="client_message_template" rows="8"><?php echo esc_textarea( $client_message_template ); ?></textarea>
			</p>
			<p>
				<label><?php echo esc_html__( 'Mensaje para el número configurado (usa [campo] para placeholders):', 'wc-taptapp-notifications' ); ?></label>
				<textarea name="configured_number_message_template" rows="8"><?php echo esc_textarea( $configured_number_message_template ); ?></textarea>
			</p>
		</div>
		<?php
	}

	public function save_whatsapp_settings( $contact_form ) {
		$whatsapp_enabled = isset( $_POST['whatsapp_enabled'] ) ? 1 : 0;
		$phone_field_name = isset( $_POST['whatsapp_phone_field'] ) ? sanitize_text_field( $_POST['whatsapp_phone_field'] ) : '';
		$whatsapp_number = isset( $_POST['whatsapp_number'] ) ? sanitize_text_field( $_POST['whatsapp_number'] ) : '';
		$client_message_template = isset( $_POST['client_message_template'] ) ? sanitize_textarea_field( $_POST['client_message_template'] ) : '';
		$configured_number_message_template = isset( $_POST['configured_number_message_template'] ) ? sanitize_textarea_field( $_POST['configured_number_message_template'] ) : '';

		update_post_meta( $contact_form->id(), '_wpcf7_whatsapp_enabled', $whatsapp_enabled );
		update_post_meta( $contact_form->id(), '_wpcf7_whatsapp_phone_field', $phone_field_name );
		update_post_meta( $contact_form->id(), '_wpcf7_whatsapp_number', $whatsapp_number );
		update_post_meta( $contact_form->id(), '_wpcf7_client_message_template', $client_message_template );
		update_post_meta( $contact_form->id(), '_wpcf7_configured_number_message_template', $configured_number_message_template );
	}

	public function send_cf7_whatsapp_notification( $contact_form ) {
		// Comprueba si las notificaciones de WhatsApp están habilitadas para este formulario
		$whatsapp_enabled = get_post_meta( $contact_form->id(), '_wpcf7_whatsapp_enabled', true );
		if ( ! $whatsapp_enabled ) {
			return;
		}

		// Obtiene los datos de envío del formulario
		$submission = WPCF7_Submission::get_instance();

		if ( $submission ) {
			$data = $submission->get_posted_data();
			$phone_field_name = get_post_meta( $contact_form->id(), '_wpcf7_whatsapp_phone_field', true );
			$whatsapp_number = get_post_meta( $contact_form->id(), '_wpcf7_whatsapp_number', true );
			$client_message_template = get_post_meta( $contact_form->id(), '_wpcf7_client_message_template', true );
			$configured_number_message_template = get_post_meta( $contact_form->id(), '_wpcf7_configured_number_message_template', true );

			// Obtiene el número de teléfono del cliente del campo del formulario especificado
			$phone = isset($data[$phone_field_name]) ? sanitize_text_field($data[$phone_field_name]) : '';
			$form_title = $contact_form->title();

			$placeholders = array_merge( $data, array( 'form_title' => $form_title ) );

			$client_message = $this->replace_placeholders( $client_message_template, $placeholders );
			$configured_number_message = $this->replace_placeholders( $configured_number_message_template, $placeholders );

			// Envía el mensaje al teléfono del cliente
			if ( ! empty( $phone ) ) {
				WC_Taptapp_WhatsApp::send_message( $phone, $client_message );
			}

			// Envía el mensaje al número de WhatsApp configurado
			if ( ! empty( $whatsapp_number ) ) {
				WC_Taptapp_WhatsApp::send_message( $whatsapp_number, $configured_number_message );
			}
		}
	}

	private function replace_placeholders( $template, $placeholders ) {
		foreach ( $placeholders as $key => $value ) {
			$template = str_replace( '[' . $key . ']', $value, $template );
		}
		return $template;
	}
}

// Inicializa la integración
WC_Taptapp_CF7_Integration::init();
