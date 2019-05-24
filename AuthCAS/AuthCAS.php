<?php

class AuthCAS extends PluginAbstract
{
	/**
	* @var string Name of plugin
	*/
	public $name = 'AuthCAS';

	/**
	* @var string Description of plugin
	*/
	public $description = 'Provides authentication integration with CAS services. Adapted from a WebAuth implementation by Wes Wright.';

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
	public $version = '0.0.2';

	/**
	* Attaches plugin methods to hooks in code base
	*/
	public function load() {
		Plugin::attachEvent ( 'login.start' , array( __CLASS__ , 'verify_cas_login' ) );
	}

	/**
	* Check that a user is logged in via CAS, and create a new account for 
	* them if this is their first login.
	*
	*/
	public function verify_cas_login() {
		
		//confirm we have the server vars we need		
		$cas_user = AuthCAS::get_remote_user();

		if($cas_user) {
			$auth_service = new AuthService();
			$user = $auth_service->validateCredentials( $cas_user, 'dummy_password' );
			if (!$user) {
				AuthCAS::new_cas_user($cas_user);
			}
			else {
				$auth_service->login( $user );
			}
		}	

	}
	
	/**
	* Get remote user if it is set.
	* 
	* return bool false if no user, string user if authed in.
	*
	*/
	public function get_remote_user() {
		$cas_user = $_SERVER['REMOTE_USER'] ?? $_SERVER['REDIRECT_REMOTE_USER'] ?? false;
		return $cas_user;
	}
	
	/**
	* Create a new user with server vars from CAS auth.
	*
	* @var string user name from cas remote user vars.
	*
	*/
	public function new_cas_user($cas_user) {
		$new_user= new User();
		$new_user->username = $cas_user;
		$new_user->password = 'dummy_password';
		$new_user->released = true;
		$new_user->duration = true;
		
		$new_user->email = $new_user->username . '@uvm.edu';

		$userService = new UserService();
		$our_user = $userService->create($new_user);

		
		// Set name, website, homedirectory, title, etc.
		if( class_exists('LDAP') ){
			LDAP::set_user_meta($our_user);
		}
	
		$userService->approve($our_user,'approve');
		
		$auth_service = new AuthService();
		$auth_service->login( $our_user );
	}
}

