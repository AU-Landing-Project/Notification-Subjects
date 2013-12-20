<?php

// get registered objects
//$objects = elgg_get_config('register_objects');
$objects = get_registered_entity_types('object');
$registered_objects = elgg_get_config('register_objects');

echo elgg_view('output/longtext', array(
	'value' => elgg_echo('notification_subjects:disclaimer')
));

//foreach($objects as $object_type => $subtype_array){
  echo "<label>" . elgg_echo($object_type) . "</label><br>";
  echo '<table class="notification_subjects_settings"><tr class="notification_subjects_headings"><td>';
  echo elgg_echo('notification_subjects:subtype');
  echo "</td><td>";
  echo elgg_echo('notification_subjects:default:subject');
  echo "</td><td>";
  echo elgg_echo('notification_subjects:action');
  echo "</td></tr>";
  
  $zebra = "odd";
  foreach($objects as $subtype){
    $param = "object_" . $subtype;
    $options = array(
        'name' => "params[{$param}]",
        'value' => $vars['entity']->$param ? $vars['entity']->$param : 'default',
        'options_values' => array(
            'default' => elgg_echo('notification_subjects:option:default'),
            'deny' => elgg_echo('notification_subjects:option:deny'),
            'allow' => elgg_echo('notification_subjects:option:allow'),
        ),
    );
    
    echo '<tr class="' . $zebra . '"><td>';
    echo elgg_echo($subtype);
    echo "</td><td>";
    echo $registered_objects['object'][$subtype];
    echo "</td><td>";
    echo elgg_view('input/dropdown', $options);
    echo "</td></tr>";
    
    if($zebra == "odd"){
      $zebra = "even";
    }
    else{
      $zebra = "odd";
    }
  }
  echo "</table><br><br>";
//}