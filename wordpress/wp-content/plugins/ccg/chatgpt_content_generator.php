<?php
/**
 * Plugin Name: ChatGPT Content Generator
 * Description: A plugin to generate content using ChatGPT.
 * Version: 1.0
 * Author: Volodymyr Shved
 */

// Hook for adding admin menus
add_action( 'admin_menu', 'chatgpt_content_generator_menu' );

// Action function for the above hook
function chatgpt_content_generator_menu(): void {
	// Add a new top-level menu
	add_menu_page(
		'ChatGPT Content Generator',
		'ChatGPT',
		'manage_options',
		'chatgpt-content-generator',
		'chatgpt_content_generator_settings_page',
		'dashicons-welcome-write-blog',
		6
	);
}

// Function to display the settings page
/**
 * @throws JsonException
 */
function chatgpt_content_generator_settings_page(): void {
	if ( ! empty( $_POST['chatgpt_prompt'] ) ) {
		$prompt            = sanitize_text_field( $_POST['chatgpt_prompt'] );
		$generated_content = chatgpt_send_request( $prompt );
	}

	?>
    <div class="wrap">
        <h1>ChatGPT Content Generator Settings</h1>
        <form method="post" action="options.php">
			<?php
			settings_fields( 'chatgpt-content-generator-options' );
			do_settings_sections( 'chatgpt-content-generator' );
			submit_button();
			?>
        </form>

        <h2>Generate Content</h2>
        <form method="post">
            <label>
                <textarea name="chatgpt_prompt" rows="4" cols="50"></textarea>
            </label>
            <br>
            <input type="submit" value="Generate Content">
        </form>

		<?php if ( isset( $generated_content ) ): ?>
            <h3>Generated Content:</h3>
            <p><?php echo esc_html( $generated_content ); ?></p>
		<?php endif; ?>
    </div>
	<?php
}

// Register plugin settings
add_action( 'admin_init', 'chatgpt_content_generator_register_settings' );

function chatgpt_content_generator_register_settings(): void {
	// Register a new setting for "chatgpt-content-generator" page
	register_setting( 'chatgpt-content-generator-options', 'chatgpt_api_key' );

	// Add a new section to a settings page
	add_settings_section(
		'chatgpt_content_generator_api_settings',
		'API Settings',
		'chatgpt_content_generator_api_settings_section_callback',
		'chatgpt-content-generator'
	);

	// Add a new field to a section of a settings page
	add_settings_field(
		'chatgpt_api_key_field',
		'ChatGPT API Key',
		'chatgpt_api_key_field_callback',
		'chatgpt-content-generator',
		'chatgpt_content_generator_api_settings'
	);
}

// Callback function for the section
function chatgpt_content_generator_api_settings_section_callback(): void {
	echo '<p>Enter your OpenAI ChatGPT API Key here.</p>';
}

// Callback function for the field
function chatgpt_api_key_field_callback(): void {
	$chatgpt_api_key = get_option( 'chatgpt_api_key' );
	echo '<input type="text" id="chatgpt_api_key" name="chatgpt_api_key" value="' . esc_attr( $chatgpt_api_key ) . '"/>';
}

/**
 * Function to send a request to the ChatGPT API
 *
 * @param string $prompt The prompt to send to the ChatGPT API
 *
 * @return string The response from the ChatGPT API
 * @throws JsonException
 */
function chatgpt_send_request( string $prompt ): string {
	$api_key = get_option( 'chatgpt_api_key' );
	$api_url = 'https://api.openai.com/v1/chat/completions';

	$messages = [
		[
			"role"    => "system",
			"content" => "Generate post content using this prompt",
		],
		[
			"role"    => "user",
			"content" => $prompt,
		],
	];

	$body = json_encode( [
		"model"       => "gpt-3.5-turbo-0125",
		"messages"    => $messages,
		"temperature" => 0.8,
		"max_tokens"  => 300,
		"top_p"       => 1,
	], JSON_THROW_ON_ERROR );

	$response = wp_remote_post( $api_url, [
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'Content-Type'  => 'application/json',
		],
		'body'    => $body,
		'timeout' => 15,
	] );

	if ( is_wp_error( $response ) ) {
		return 'Error: ' . $response->get_error_message();
	}

	$body = wp_remote_retrieve_body( $response );

	$decoded = json_decode( $body, true, 512, JSON_THROW_ON_ERROR );

	return $decoded['choices'][0]['message']['content'] ?? 'Error: Unexpected response format';
}
