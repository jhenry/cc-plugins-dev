<?php

class EncoderPlus extends PluginAbstract
{
	/**
	* @var string Name of plugin
	*/
	public $name = 'EncoderPlus';

	/**
	* @var string Description of plugin
	*/
	public $description = 'Adds support for HD (720p) encoding, and queued encoding.  Based on work by Wes Wright.';

	/**
	* @var string Name of plugin author
	*/
	public $author = 'Justin Henry';

	/**
	* @var string URL to plugin's website
	*/
	public $url = 'https://uvm.edu/~jhenry/';

	/**
	* @var string Current version of plugin
	*/
	public $version = '0.0.1';
	
	
	/**
        * Performs install operations for plugin. Called when user clicks install
        * plugin in admin panel.
        *
        * 
        */
        public function install(){
		
        }

	/**
	* The plugin's gateway into codebase. Place plugin hook attachments here.
	*/	
	public function load(){
		// Attach filter for upload event
	}


	/**
	 * Outputs the settings page HTML and handles form posts on the plugin's
	 * settings page.
	 */
	public function settings(){
		$data = array();
		$errors = array();
		$message = null;
		
		// Retrieve settings from database
		$data['encoderplus_enable_queue'] = Settings::get('encoderplus_enable_queue');
		$data['encoderplus_enable_720p'] = Settings::get('encoderplus_enable_720p');

		// Handle form if submitted
		if (isset($_POST['submitted'])) {
			// Validate form nonce token and submission speed
			$is_valid_form = Wowza::_validate_form_nonce();
			
			if( $is_valid_form ){
				// Validate wowza base upload dir
				if( !empty($_POST['wowza_upload_dir']) ) {
					$data['wowza_upload_dir'] = trim($_POST['wowza_upload_dir']);
				} else {
					$errors['wowza_upload_dir'] = 'Invalid Wowza upload directory. ';
				}
				// Validate wowza rtmp host
				if( !empty($_POST['wowza_rtmp_host']) ) {
					$data['wowza_rtmp_host'] = trim($_POST['wowza_rtmp_host']);
				} else {
					$errors['wowza_rtmp_host'] = 'Invalid Wowza upload directory. ';
				}
			}
			else {
				$errors['session'] = 'Expired or invalid session';
			}
			
			// Error check and update data
			Wowza::_handle_settings_form($data, $errors);

	}
			// Generate new form nonce
			$formNonce = md5(uniqid(rand(), true));
			$_SESSION['formNonce'] = $formNonce;
			$_SESSION['formTime'] = time();

			// Display form
			include(dirname(__FILE__) . '/settings_form.php');
}

	/**
	 * Check for form errors and save settings
	 * 
	 */
	private function _handle_settings_form($data, $errors){
		if (empty($errors)) {
			foreach ($data as $key => $value) {
				Settings::set($key, $value);
			}
			$message = 'Settings have been updated.';
			$message_type = 'alert-success';
		} else {
			$message = 'The following errors were found. Please correct them and try again.';
			$message .= '<br /><br /> - ' . implode('<br /> - ', $errors);
			$message_type = 'alert-danger';
		}
	}

	/**
	 * Validate settings form nonce token and submission speed
	 * 
	 */
	private function _validate_form_nonce(){
			if (
				!empty($_POST['nonce'])
				&& !empty($_SESSION['formNonce'])
				&& !empty($_SESSION['formTime'])
				&& $_POST['nonce'] == $_SESSION['formNonce']
				&& time() - $_SESSION['formTime'] >= 2
			   ) {
				return true;
			 
			} 
			else {
				return false;
			}
		
	}
}

