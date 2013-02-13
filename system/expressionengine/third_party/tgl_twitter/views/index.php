<?php

	/*
  This view displays all of the module wide settings that can be 
  edited by the admin.
	*/

//Check if message is present, stop all other actions
if (isset($message))
{
	echo $message;
	return;
}

$this->table->set_template($cp_table_template);
$this->table->set_heading(lang('tgl_twitter_user_setup'));

// Setup form
echo form_open($form_action, '', '');
echo form_hidden('temporary_oauth_token', $temporary_credentials['oauth_token']);
echo form_hidden('temporary_oauth_token_secret', $temporary_credentials['oauth_token_secret']);

$this->table->add_row(
	"<strong>" . lang('authentication_link') . "</strong>",
	"<p><a id='generate_request_token' target='_blank' href='$authentication_url'>" . lang('get_authentication') . "</a></p>"
);
$this->table->add_row(
	"<strong>" . lang('delete_authentication') . "</strong>",
	"<p><a id='generate_request_token' href='". BASE . AMP . "C=addons_modules" . AMP . "M=show_module_cp" . AMP . "module=tgl_twitter" . AMP . "method=prevoke_authentication'>" . lang('prevoke_authentication') . "</a></p>"
);

echo $this->table->generate();
echo form_submit(
	array(
	     'name'  => 'Submit',
	     'id'    => 'submit',
	     'value' => 'Update',
	     'class' => 'submit'
	)
);