<?php  if (! defined('BASEPATH'))
{
	exit('No direct script access allowed');
}

/**
 * Model that handles DB communication for the TGL Twitter Module
 *
 * @author Bryant Hughes
 * @version 0.1
 * @copyright The Good Lab - http://thegoodlab.com , 18 August, 2011
 **/

class Tgl_twitter_model extends CI_Model
{

	public $site_id;

	var $_ee;
	var $cache;

	function __construct()
	{
		parent::__construct();

		$this->_ee     =& get_instance();
		$this->site_id = $this->_ee->config->item('site_id');

		//prep-cache
		if (! isset($this->_ee->session->cache['tgl_twitter']))
		{
			$this->_ee->session->cache['tgl_twitter'] = array();
		}
		$this->cache =& $this->_ee->session->cache['tgl_twitter'];

	}

	/**
	 * Returns all channel field settings
	 *
	 * @return array : settings for the module
	 * @author Bryant Hughes
	 */
	function get_settings()
	{
		$query = $this->db->query("SELECT * FROM `exp_tgl_twitter_settings` WHERE `site_id` = " . $this->site_id . " LIMIT 1");

		if ($query->num_rows() == 0)
		{
			return FALSE;
		}

		$settings = $query->result_array();

		return $settings[0];
	}

	/**
	 * Deletes all old settings, then loops through the post and creates new settings based on the values
	 * that are submitted.
	 *
	 * @return boolean - if the operation was successful
	 * @author Bryant Hughes
	 */
	function insert_new_settings()
	{
		// Removing the submit post value
		unset($_POST['Submit']);

		if (empty($_POST['consumer_key']) && empty($_POST['consumer_secret']))
		{
			return FALSE;
		}

		//remove all settings before we re-add them
		$this->delete_all_settings();

		$sql = sprintf("INSERT INTO `exp_tgl_twitter_settings` (`id`, `site_id`, `consumer_key`, `consumer_secret`, `oauth_token`, `oauth_token_secret`)
			VALUES
				('%d', '%d', '%s', '%s', NULL, NULL)",
			1, //INSERT ID
			$this->site_id, //CURRENT SITE ID
			$this->db->escape_str($_POST['consumer_key']),
			$this->db->escape_str($_POST['consumer_secret'])
		);

		return $this->db->query($sql);
	}

	/**
	 * deletes any old request tokens and then re-inserts the provided tokens
	 *
	 * @param string $request_token 
	 * @param string $request_token_secret 
	 * @return boolean - if the operation was successful
	 * @author Bryant Hughes
	 */
	function insert_oauth_token($oauth_token, $oauth_token_secret)
	{
		return $this->db->update(
			'exp_tgl_twitter_settings',
			array(
			     'oauth_token'        => $oauth_token,
			     'oauth_token_secret' => $oauth_token_secret
			),
			'`site_id` = ' . $this->site_id);
	}

	/**
	 * deletes any old access tokens and then re-inserts the provided tokens
	 *
	 * @param string $access_token 
	 * @param string $access_token_secret 
	 * @return void
	 * @author Bryant Hughes
	 */
	function delete_oauth_tokens()
	{
		return $this->db->update(
			'exp_tgl_twitter_settings',
			array(
			     'oauth_token'        => NULL,
			     'oauth_token_secret' => NULL
			),
			'`site_id` = ' . $this->site_id);
	}

	/**
	 * deletes all settings for the module
	 *
	 * @return void
	 * @author Bryant Hughes
	 */
	function delete_all_settings()
	{
		// clense current settings out of DB : we add the WHERE site_id = $site_id, because the only setting we want to save is the module_id
		// setting, which is set to site_id 0 -- because its not site specific
		return $this->db->query("DELETE FROM `exp_tgl_twitter_settings` WHERE `site_id` = " . $this->site_id);
	}
}
	
	
	
	
	
