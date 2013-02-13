<?php if (! defined('BASEPATH'))
{
	exit('No direct script access allowed');
}

require_once PATH_THIRD . 'tgl_twitter/classes/twitteroauth.php';

class Tgl_twitter_mcp
{
	private $data = array();

	public function __construct()
	{
		$this->EE       =& get_instance();
		$this->site_id  = $this->EE->config->item('site_id');
		$this->base_url = BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=tgl_twitter';

		// load table lib for control panel
		$this->EE->load->library('table');
		$this->EE->load->helper('form');

		// Setting up menu
		$menu = array(
			'User settings' => BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=tgl_twitter',
		);

		if ($this->EE->session->userdata('group_id') == 1)
		{
			$menu['Configuration'] = BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=tgl_twitter' . AMP . 'method=configuration';
		}

		// Set page settings
		$this->EE->cp->load_package_css('tgl_twitter');
		$this->EE->cp->set_right_nav($menu);
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('tgl_twitter_module_name'));
	}

	/**
	 * Module CP index function
	 *
	 * @return void
	 * @author Bryant Hughes
	 */
	public function index()
	{
		$this->EE->load->model('tgl_twitter_model');

		$this->data['form_action'] = AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=tgl_twitter' . AMP . 'method=submit_user_settings';
		$this->data['settings']    = $this->EE->tgl_twitter_model->get_settings();

		if (empty($this->data['settings']['consumer_key']) && empty($this->data['settings']['consumer_secret']))
		{
			$this->data['message'] = $this->EE->lang->line('TGL Twitter has not been configured yet, contact an administrator to get it configured.');
		}

		$connection                          = new TwitterOAuth($this->data['settings']['consumer_key'], $this->data['settings']['consumer_secret']);
		$this->data['temporary_credentials'] = $connection->getRequestToken();
		$this->data['authentication_url']    = $connection->getAuthorizeURL($this->data['temporary_credentials']);

		return $this->EE->load->view('index', $this->data, TRUE);
	}

	/**
	 * Called after new settings have been submitted
	 *
	 * @return void
	 * @author Bryant Hughes
	 */
	public function configuration()
	{
		if ($this->EE->session->userdata('group_id') != 1)
		{
			$this->EE->functions->redirect(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=tgl_twitter');
		}

		$this->EE->load->model('tgl_twitter_model');

		$this->data['form_action'] = AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=tgl_twitter' . AMP . 'method=submit_configuration';
		$this->data['settings']    = $this->EE->tgl_twitter_model->get_settings();

		return $this->EE->load->view('configuration', $this->data, TRUE);
	}

	/**
	 * Called after a user clicks "Register"
	 *
	 * @return void
	 * @author Bryant Hughes
	 */
	public function submit_user_settings()
	{
		$this->EE->load->model('tgl_twitter_model');
		$this->data['settings']       = $this->EE->tgl_twitter_model->get_settings();
		$temporary_oauth_token        = $this->EE->input->post('temporary_oauth_token');
		$temporary_oauth_token_secret = $this->EE->input->post('temporary_oauth_token_secret');
		$success                      = FALSE;

		$connection        = new TwitterOAuth(
			$this->data['settings']['consumer_key'],
			$this->data['settings']['consumer_secret'],
			$temporary_oauth_token,
			$temporary_oauth_token_secret
		);
		$token_credentials = $connection->getAccessToken();

		if (isset($token_credentials['oauth_token']) && isset($token_credentials['oauth_token_secret']))
		{
			$success = $this->EE->tgl_twitter_model->insert_oauth_token($token_credentials['oauth_token'], $token_credentials['oauth_token_secret']);
		}

		if ($success)
		{
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('Oauth -token and -secret are now saved!'));
		}
		else
		{
			$this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('Oauth -token and -secret could not be saved!'));
		}

		$this->EE->functions->redirect(BASE . AMP . 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=tgl_twitter');
	}

	/**
	 * Used to generate the access tokens from Twitter.  This is the last step in the authentication process
	 *
	 * @param string $settings 
	 * @return boolean : depending if we were able to generate the tokens and save them to the DB or NOT
	 * @author Bryant Hughes
	 */
	private function generate_access_tokens($settings)
	{
		$this->EE->load->model('tgl_twitter_model');
		
		//Retrieve our previously generated request token & secret
		$requestToken = $settings['request_token'];
		$requestTokenSecret = $settings['request_token_secret'];
		
		$oauth = new TwitterOAuth('consumer_key', 'consumer_secret', $requestToken, $requestTokenSecret);
		
		// Generate access token by providing PIN for Twitter
		$request = $oauth->getAccessToken(NULL, $settings['pin']);
		
		if($request != FALSE)
		{
			$access_token = $request['oauth_token'];
			$access_token_secret = $request['oauth_token_secret'];

			// Save our access token/secret
			return $this->EE->tgl_twitter_model->insert_access_token($access_token, $access_token_secret);
		}
		else
		{
			return FALSE;
		}
		
	}

	/**
	 * function that kills all settings in the DB and starts us over at square one.
	 *
	 * @return void
	 * @author Bryant Hughes
	 */
	public function erase_settings()
	{
		$this->EE->load->model('tgl_twitter_model');
		$this->EE->tgl_twitter_model->delete_all_settings();
		$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('Authentication Settings Erased.'));
		$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=tgl_twitter');
	}
}

/* End of File: mcp.module.php */