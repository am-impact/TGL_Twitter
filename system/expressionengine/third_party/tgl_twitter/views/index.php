<?php

/**
A View where only the user has to accept the tokens
 */

//Check if message is present, stop all other actions
if (isset($message))
{
	echo $message;
	return;
}

$this->table->set_template($cp_table_template);
$this->table->set_heading(lang('tgl_twitter_user_setup'), '');

// Setup form
echo form_open($form_action, '', '');
echo form_hidden('temporary_oauth_token', $temporary_credentials['oauth_token']);
echo form_hidden('temporary_oauth_token_secret', $temporary_credentials['oauth_token_secret']);

$this->table->add_row(
	array(
	     'colspan' => 2,
	     'data'    => '<strong>' . lang('information') . '</strong>'
	)
);

$this->table->add_row(
	"<strong>" . lang('authentication_link') . "</strong>",
	"<p><a id='generate_request_token' target='_blank' href='$authentication_url'>" . lang('get_authentication') . "</a></p>"
);

$this->table->add_row(
	"<strong>" . lang('enter_pin') . "</strong>",
	"<p><input type=\"text\" id=\"auth_verifier\" name=\"auth_verifier\" /></p>"
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