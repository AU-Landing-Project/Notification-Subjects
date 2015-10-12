<?php

namespace AU\NotificationSubjects;

/**
 * gets a cached plugin setting, so the db isn't hit with a new query for each recipient
 * 
 * @staticvar type $return
 * @param string $setting
 * @return array
 */
function get_subject_setting($setting) {
	static $return;
	if (!is_array($return)) {
		$return = array();
	}

	if ($return[$setting]) {
		return $return[$setting];
	}

	$return[$setting] = elgg_get_plugin_setting($setting, PLUGIN_ID);

	return $return[$setting];
}

/**
 * Constructs a title like:
 * Matt Beckett created a blog post in the group 'My Group' : My latest blog post
 *
 * default template: '{{name}} {{action}} a {{subtype}}{{group}}: {{title}}'
 * 
 * @param type $event
 * @param \ElggObject $object
 * @return type
 */
function get_notification_subject($event, \ElggObject $object, $lang = 'en') {

	static $subject;

	if (!is_array($subject)) {
		$subject = array();
	}

	// use cached value if available
	if ($subject[$lang][$event . $object->guid]) {
		return $subject[$lang][$event . $object->guid];
	}

	// owners name
	$name = $object->getOwnerEntity()->name;

	$action = get_action_string($event, $lang);

	$subtype = get_subtype_string($object->getSubtype(), $lang);


	// find out if it's a group or personal item
	// note that on cron there is no logged in user
	// and notification subjects needs enhanced access
	if (!elgg_is_logged_in()) {
		$ia = elgg_set_ignore_access(true);
	}
	$container = $object->getContainerEntity();
	$group = '';
	if (elgg_instanceof($container, 'group')) {
		$group = elgg_echo('notification_subjects:group', array(elgg_get_excerpt(html_entity_decode($container->name, ENT_QUOTES), 45)), $lang);
	}

	if (!elgg_is_logged_in()) {
		elgg_set_ignore_access($ia);
	}

	// add in the title of the object
	// limit the length to ~50 chars
	if (!empty($object->title)) {
		$title = $object->title;
	} elseif (!empty($object->name)) {
		$title = $object->name;
	} elseif (!empty($object->description)) {
		$title = $object->description;
	} else {
		$title = elgg_echo('notification_subjects:untitled', array(), $lang);
	}

	$title = elgg_get_excerpt(html_entity_decode($title, ENT_QUOTES), 45);

	$subject[$lang][$event . $object->guid] = build_subject(array(
		'template_param' => 'object_' . $object->getSubtype() . '_template',
		'name' => $name,
		'action' => $action,
		'subtype' => $subtype,
		'group' => $group,
		'title' => $title,
		'lang' => $lang
	));

	return $subject[$lang][$event . $object->guid];
}

/**
 * get an action string
 * 
 * @param type $action
 * @return type
 */
function get_action_string($action, $lang = 'en') {

	if (elgg_echo('notification_subjects:event:' . $action, array(), $lang) == "notification_subjects:event:{$action}") {
		// default to create if unknown
		$return = elgg_echo('notification_subjects:event:create', array(), $lang);
	} else {
		$return = elgg_echo('notification_subjects:event:' . $action, array(), $lang);
	}

	return $return;
}

/**
 * get a subtype string
 * 
 * @param type $subtype
 * @return type
 */
function get_subtype_string($subtype, $lang = 'en') {
	// put in the subtype
	$return = elgg_echo('notification_subjects:subtype:' . $subtype, array(), $lang);
	if ($return == "notification_subjects:subtype:" . $subtype) {
		// we didn't supply a language string
		// so lets see if the core/plugin did, otherwise just use the straight subtype
		$return = elgg_echo($subtype, array(), $lang);
	}

	return $return;
}

/**
 * Assemble a subject from an array of pieces
 * 
 * @param type $options
 * @return string
 */
function build_subject($options = array()) {
	$template_param = $options['template_param'];
	if (empty($template_param)) {
		return '';
	}
	
	if (!$options['lang']) {
		$options['lang'] = 'en';
	}

	$template = elgg_get_plugin_setting($template_param, PLUGIN_ID);
	if (empty($template)) {
		$template = elgg_echo('ns:' . $template_param, array(), $lang);
		if ($template == ('ns:' . $template_param)) {
			// language string didn't exist...
			$template = elgg_echo('ns:template_default', array(), $lang);
		}
	}

	$notification_subject = str_replace('{{name}}', $options['name'], $template);
	$notification_subject = str_replace('{{action}}', $options['action'], $notification_subject);
	$notification_subject = str_replace('{{subtype}}', $options['subtype'], $notification_subject);
	$notification_subject = str_replace('{{group}}', $options['group'], $notification_subject);
	$notification_subject = str_replace('{{title}}', $options['title'], $notification_subject);

	return ucfirst($notification_subject);
}
