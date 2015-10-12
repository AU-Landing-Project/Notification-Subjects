<?php

namespace AU\NotificationSubjects;

/**
 * Prevent notifications if that's the setting
 * 
 * @param type $hook
 * @param type $type
 * @param type $return
 * @param type $params
 * @return boolean
 */
function prevent_notifications($hook, $type, $return, $params) {
	if (!elgg_instanceof($params['object'])) {
		return $return;
	}

	$setting_name = $params['object']->type . '_' . $params['object']->getSubtype();

	$setting = elgg_get_plugin_setting($setting_name, PLUGIN_ID);

	if ($setting == 'deny') {
		return false;
	}
	return $return;
}

/**
 * modify the subject if necessary
 * 
 * @param type $hook
 * @param string $type
 * @param \Elgg\Notifications\Event $return
 * @param type $params
 */
function queued_notifications($hook, $type, $return, $params) {

	if (strpos($type, 'notification:') !== 0) {
		return $return;
	}
	
	$entity = $params['event']->getObject();
	$action = $params['event']->getAction();
	
	if (!($entity instanceof \ElggObject)) {
		return $return;
	}
	
	$subtype = $entity->getSubtype();

	if (!is_registered_entity_type($entity->type, $subtype)) {
		return $return;
	}

	// get setting for this notification
	$setting = get_subject_setting($entity->type . '_' . $subtype);

	if (!$setting || $action == 'annotate') {
		// default forced for annotations at the moment
		$setting = 'default';
	}

	switch ($setting) {
		case 'allow':
			// replace the default subject with our own
			$title = get_notification_subject($action, $entity, $params['language']);
			$return->subject = $title;
			return $return;
			break;

		case 'default':
		default:
			// don't change anything
			return $return;
			break;
	}
	
	return $return;
}
