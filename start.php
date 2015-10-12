<?php

namespace AU\NotificationSubjects;

const PLUGIN_ID = 'notification_subjects';

require_once __DIR__ . '/lib/hooks.php';
require_once __DIR__ . '/lib/functions.php';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

function init() {
  elgg_extend_view('css/admin', 'css/notification_subjects');
  
  // see if we need to prevent the notification
  elgg_register_plugin_hook_handler('enqueue', 'notification', __NAMESPACE__ . '\\prevent_notifications', 1000);
  
  elgg_register_plugin_hook_handler('prepare', 'all', __NAMESPACE__ . '\\queued_notifications', 1000);
}
