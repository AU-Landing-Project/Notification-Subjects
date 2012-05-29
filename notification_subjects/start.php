<?php

function notification_subjects_init(){
  elgg_extend_view('css/admin', 'notification_subjects/css');
  // hook into the object notifications, make sure we do it last
  // the subject is registered in the global $CONFIG
  // we'll change it in $CONFIG after any other hooks do their thing
  elgg_register_plugin_hook_handler('object:notifications', 'all', 'notification_subjects_modify_subject', 1000);
}


function notification_subjects_modify_subject($hook, $type, $returnvalue, $params){
  $event = $params['event'];
  $object = $params['object'];
  
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
      return $returnvalue;
      break;
    
    case 'default':
    default:
      // don't change anything
      return $returnvalue;
      break;
    
  }
}


//
// constructs a title like:
// Matt Beckett created a group blog post: My latest blog post
// $owner $event {$group/null} {$subtype}: $title
function notification_subjects_build_title($event, $object){
  
  // owners name
  $title = $object->getOwnerEntity()->name;
  
  // event create/update/delete - send to past tense
  $title .= " " . elgg_echo('notification_subjects:event:' . $event);
  
  // find out if it's a group or personal item
  if($object->getContainerEntity() instanceof ElggGroup){
    $title .= " " . elgg_echo('notification_subjects:group');
  }
  
  // put in the subtype
  $subtype = elgg_echo('notification_subjects:subtype:' . $object->getSubtype());
  
  if($subtype == "notification_subjects:subtype:" . $object->getSubtype()){
    // we didn't supply a language string
    // so lets see if the core/plugin did, otherwise just use the straight subtype
    $subtype = elgg_echo($object->getSubtype());
  }
  
  $title .= " " . $subtype . ": ";
  
  // add in the title of the object
  // limit the length to ~25 chars
  if(!empty($object->title)){
    $obj_title = $object->title;
  }
  elseif(!empty($object->name)){
    $obj_title = $object->name;
  }
  elseif(!empty($object->description)){
    $obj_title = $object->description;
  }
  else{
    $obj_title = elgg_echo('notification_subjects:untitled');
  }
  
  if(strlen($obj_title) > 25){
    $obj_title = substr($obj_title, 0, 22) . "...";
  }
  
  $title .= $obj_title;
  
  return $title;
}

elgg_register_event_handler('init', 'system', 'notification_subjects_init');
 