<?php

function notification_subjects_init(){
  elgg_extend_view('css/admin', 'notification_subjects/css');
  // hook into the object notifications, make sure we do it last
  // the subject is registered in the global $CONFIG
  // we'll change it in $CONFIG after any other hooks do their thing
  elgg_register_plugin_hook_handler('object:notifications', 'all', 'notification_subjects_modify_subject', 1000);
}


function notification_subjects_modify_subject($hook, $type, $returnvalue, $params){
  // full memorization not needed, just remember last call
  static $last_guid;
  static $last_event;
  static $last_return;

  $event = $params['event'];
  $object = $params['object']; /* @var ElggObject $object */

  if ($last_event !== $event || $last_guid !== $object->guid) {
    $returnvalue = notification_subjects_get_hook_return($event, $object, $returnvalue);
    $last_return = $returnvalue;
    $last_event = $event;
    $last_guid = $object->guid;
  }
  
  return $last_return;
}

function notification_subjects_get_hook_return($event, ElggObject $object, $current_value) {
  $object_type = $object->getType() ? $object->getType() : '__BLANK__';
  $object_subtype = $object->getSubtype() ? $object->getSubtype() : '__BLANK__';
  
  $objects = elgg_get_config('register_objects');
  
  // get setting for this notification
  $setting = elgg_get_plugin_setting($object_type . '_' . $object_subtype, 'notification_subjects');
  if(!$setting || $event == 'annotate'){
    // default forced for annotations at the moment
    $setting = 'default';
  }

  switch ($setting) {
    case 'deny':
      // pretend we've handled it, don't send notification
      return TRUE;
      break;
    
    case 'allow':
      // replace the default subject with our own
      $title = notification_subjects_build_title($event, $object);
      $objects[$object_type][$object_subtype] = $title;
      elgg_set_config('register_objects', $objects);
      return $current_value;
      break;
    
    case 'default':
    default:
      // don't change anything
      return $current_value;
      break;
    
  }
}


//
// constructs a title like:
// Matt Beckett created a blog post in the group 'My Group' : My latest blog post
// 
// default template: '{{name}} {{action}} a {{subtype}}{{group}}: {{title}}'
function notification_subjects_build_title($event, ElggObject $object){
  // owners name
  $name = $object->getOwnerEntity()->name;
  
  $action = notification_subjects_get_action_string($event);

  $subtype = notification_subjects_get_subtype_string($object->getSubtype());
  
  
  // find out if it's a group or personal item
  $container = $object->getContainerEntity();
  $group = '';
  if(elgg_instanceof($container, 'group')){
    $group = elgg_echo('notification_subjects:group', array($container->name));
  }
  
  // add in the title of the object
  // limit the length to ~50 chars
  if (!empty($object->title)) {
    $title = $object->title;
  }
  elseif (!empty($object->name)) {
    $title = $object->name;
  }
  elseif (!empty($object->description)) {
    $title = $object->description;
  }
  else {
    $title = elgg_echo('notification_subjects:untitled');
  }
  
  $title = elgg_get_excerpt($title, 25);
  
  return notification_subjects_build_subject(array(
	  'template_param' => 'object_' . $object->getSubtype() . '_template',
	  'name' => $name,
	  'action' => $action,
	  'subtype' => $subtype,
	  'group' => $group,
	  'title' => $title
  ));
}

function notification_subjects_build_subject($options = array()) {
	$template_param = $options['template_param'];
	if (empty($template_param)) {
		return '';
	}
  
  $template = elgg_get_plugin_setting($template_param, 'notification_subjects');
  if (empty($template)) {
	  $template = elgg_echo('ns:' . $template_param);
	  if ($template == ('ns:' . $template_param)) {
		  // language string didn't exist...
		  $template = elgg_echo('ns:template_default');
	  }
  }
  
  $notification_subject = str_replace('{{name}}', $options['name'], $template);
  $notification_subject = str_replace('{{action}}', $options['action'], $notification_subject);
  $notification_subject = str_replace('{{subtype}}', $options['subtype'], $notification_subject);
  $notification_subject = str_replace('{{group}}', $options['group'], $notification_subject);
  $notification_subject = str_replace('{{title}}', $options['title'], $notification_subject);
  
  return $notification_subject;
}



function notification_subjects_get_subtype_string($subtype) {
	// put in the subtype
  $return = elgg_echo('notification_subjects:subtype:' . $subtype);
  if ($return == "notification_subjects:subtype:" . $subtype){
    // we didn't supply a language string
    // so lets see if the core/plugin did, otherwise just use the straight subtype
    $return = elgg_echo($subtype);
  }
  
  return $return;
}


function notification_subjects_get_action_string($action) {

  if (elgg_echo('notification_subjects:event:' . $action) == "notification_subjects:event:{$action}") {
	  // default to create if unknown
      $return = elgg_echo('notification_subjects:event:create');
  }
  else {
    $return = elgg_echo('notification_subjects:event:' . $action);
  }
  
  return $return;
}


elgg_register_event_handler('init', 'system', 'notification_subjects_init');
 