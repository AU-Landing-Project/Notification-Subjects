<?php

// get registered objects
//$objects = elgg_get_config('register_objects');
$objects = get_registered_entity_types('object');
$registered_objects = elgg_get_config('register_objects');

echo elgg_view('output/longtext', array(
	'value' => elgg_echo('notification_subjects:disclaimer')
));


  echo "<label>" . elgg_echo($object_type) . "</label><br>";
  echo '<table class="notification_subjects_settings"><tr class="notification_subjects_headings"><td>';
  echo elgg_echo('notification_subjects:subtype');
  echo "</td><td>";
  echo elgg_echo('notification_subjects:action');
  echo '</td><td>';
  echo elgg_echo('notification_subjects:language_string');
  echo "</td></tr>";
  
  $zebra = "odd";
  foreach($objects as $subtype){
	  
    $param = "object_" . $subtype;
	$param_template = $param . '_template';
    $options = array(
        'name' => "params[{$param}]",
        'value' => $vars['entity']->$param ? $vars['entity']->$param : 'default',
        'options_values' => array(
            'default' => elgg_echo('notification_subjects:option:default'),
            'deny' => elgg_echo('notification_subjects:option:deny'),
            'allow' => elgg_echo('notification_subjects:option:allow'),
        ),
    );
		
	$sample = notification_subjects_build_subject(array(
		'template_param' => $param_template,
		'name' => "John Doe",
		'action' => notification_subjects_get_action_string('create'),
		'subtype' => notification_subjects_get_subtype_string($subtype),
		'group' => elgg_echo('notification_subjects:group', array('Site Users')),
		'title' => "Test Content"
	));
    
    echo '<tr class="' . $zebra . '"><td>';
    echo elgg_echo($subtype);
    echo "</td><td>";
    echo elgg_view('input/dropdown', $options);
    echo "</td><td>";
	echo 'ns:' . $param_template;
	echo "</td></tr>";
	echo '<tr class="' . $zebra . '"><td></td><td colspan="2">';
	echo elgg_view('input/text', array('name' => "params[{$param_template}]", 'value' => $vars['entity']->$param_template, 'placeholder' => elgg_echo('ns:template_default')));
	echo elgg_view('output/longtext', array('value' => '<strong>Eg.</strong>&nbsp;' . $sample, 'class' => 'elgg-subtext'));
	echo '</td></tr>';
    
    if($zebra == "odd"){
      $zebra = "even";
    }
    else{
      $zebra = "odd";
    }
  }
  echo "</table><br><br>";

  echo elgg_echo('notification_subjects:settings:help');
?>
<br><br>