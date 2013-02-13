<?php

/*
Here the admin can configure his consumer_key and consumer_secret
*/

echo form_open($form_action, '', '');
$this->table->set_template($cp_table_template);
$this->table->set_heading('TGL Twitter Configuration', '');

//consumer keys
$consumer_key    = isset($settings['consumer_key']) ? $settings['consumer_key'] : '';
$consumer_secret = isset($settings['consumer_secret']) ? $settings['consumer_secret'] : '';

$consumer_key_input_data    = array('name' => 'consumer_key', 'value' => $consumer_key, 'maxlength' => '100', 'style' => 'width:50%');
$consumer_secret_input_data = array('name' => 'consumer_secret', 'value' => $consumer_secret, 'maxlength' => '100', 'style' => 'width:50%');

$this->table->add_row(
	"<strong>" . lang('consumer_key') . "</strong>",
	form_input($consumer_key_input_data)
);
$this->table->add_row(
	"<strong>" . lang('consumer_secret') . "</strong>",
	form_input($consumer_secret_input_data)
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