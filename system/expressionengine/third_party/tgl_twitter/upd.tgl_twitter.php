<?php
class Tgl_twitter_upd
{
	public $version = '0.2';

	public function __construct()
	{
		$this->EE      =& get_instance();
		$this->site_id = $this->EE->config->item('site_id');
	}

	public function install()
	{
		$this->EE->db->insert('modules', array(
		                                      'module_name'        => 'Tgl_twitter',
		                                      'module_version'     => $this->version,
		                                      'has_cp_backend'     => 'y',
		                                      'has_publish_fields' => 'n'
		                                 ));

		$this->EE->load->dbforge();

		//create tgl_twitter module settings table
		$fields = array(
			'id'                 => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'site_id'            => array('type' => 'int', 'constraint' => '8', 'unsigned' => TRUE, 'null' => FALSE, 'default' => '1'),
			'consumer_key'       => array('type' => 'varchar', 'constraint' => '100', 'null' => TRUE),
			'consumer_secret'    => array('type' => 'varchar', 'constraint' => '100', 'null' => TRUE),
			'oauth_token'        => array('type' => 'varchar', 'constraint' => '100', 'null' => TRUE),
			'oauth_token_secret' => array('type' => 'varchar', 'constraint' => '100', 'null' => TRUE)
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->create_table('tgl_twitter_settings');

		return TRUE;
	}

	public function update($current = '')
	{
		if ($current == $this->version)
		{
			return FALSE;
		}
		return TRUE;
	}

	public function uninstall()
	{
		$this->EE->load->dbforge();

		$this->EE->db->query("DELETE FROM exp_modules WHERE module_name = 'Tgl_Twitter'");

		$this->EE->dbforge->drop_table('tgl_twitter_settings');

		return TRUE;
	}
}

/* End of File: upd.module.php */