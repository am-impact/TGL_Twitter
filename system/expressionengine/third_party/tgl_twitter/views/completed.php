<?php

/**
A View where only the user has to accept the tokens
 */

//Check if message is present, stop all other actions
$this->table->set_template($cp_table_template);
$this->table->set_heading(lang('tgl_twitter_user_setup'), '');

// Setup form
$this->table->add_row(
    array(
        'colspan' => 2,
        'data'  => '<strong>' . $message  .'</strong>')

);

$this->table->add_row(
    "<strong>" . lang('delete_authentication') . "</strong>",
    "<p><a id='generate_request_token' href='". BASE . AMP . "C=addons_modules" . AMP . "M=show_module_cp" . AMP . "module=tgl_twitter" . AMP . "method=revoke_authentication'>" . lang('revoke_authentication') . "</a></p>"
);

echo $this->table->generate();
