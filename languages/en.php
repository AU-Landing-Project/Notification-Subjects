<?php

$english = array(
    'notification_subjects' => "Notification Subjects",
    'notification_subjects:action' => "Settings",
    'notification_subjects:default:subject' => "Default Subject",
    'notification_subjects:event:create' => "created a",
    'notification_subjects:event:delete' => "deleted the",
    'notification_subjects:event:update' => "updated the",
    'notification_subjects:group' => "in the '%s' group",
    'notification_subjects:option:allow' => "Use descriptive subject",
    'notification_subjects:option:default' => "Use default subject",
    'notification_subjects:option:deny' => "Disable notifications",
    'notification_subjects:subtype' => "Subtype",
    'notification_subjects:untitled' => "Untitled",
	
	'notification_subjects:custom:template' => "Custom Template",
	'notification_subjects:language_string' => "Template Language String",
	'ns:template_default' => '{{name}} {{action}} {{subtype}} {{group}}: {{title}}',
    
    // subtypes
    'notification_subjects:subtype:album' => "Image Album",
    'notification_subjects:subtype:blog' => "Blog post",
    'notification_subjects:subtype:bookmarks' => "Bookmark",
    'notification_subjects:subtype:event_calendar' => "Event",
    'notification_subjects:subtype:file' => "File",
    'notification_subjects:subtype:folder' => "Folder",
    'notification_subjects:subtype:groupforumtopic' => "Discussion",
    'notification_subjects:subtype:image' => "Image",
    'notification_subjects:subtype:messages' => "Message",
    'notification_subjects:subtype:page' => "Page",
    'notification_subjects:subtype:page_top' => "Page",
    'notification_subjects:subtype:poll' => "Poll",
    'notification_subjects:subtype:thewire' => "Wire Post",
	
	'notification_subjects:disclaimer' => 'Note that not all items listed here may send out notifications',
    
	'notification_subjects:settings:help' => "Instructions:
<br><br>
To rearrange the elements of the subject there are 2 methods.<br>
1. Create the custom language string.  This language string is ideal as it can be different for each language.
<br>
2. Enter the template in the custom template text input.  This is easy, but will be the same for all languages.
<br><br>
Template:
<br><br>
The default template is '{{name}} {{action}} {{subtype}} {{group}}: {{title}}'
<br>
The tokens surrounded by the braces will be replaced with the appropriate content.<br>
{{name}} - the name of the user performing the action<br>
{{action}} - the action that was performed (created/updated/etc)<br>
{{subtype}} - the type of content that was created/updated/etc<br>
{{group}} - \"in the 'group name' group\" - this is only used if the content is contained in a group<br>
{{title}} - the title or name of the content, eg. the title of a blog post
<br><br>
Custom actions can be translated using language strings in the format of 'notification_subjects:event:{{event}}' - where {{event}} = 'create', 'update', 'delete'<br>
Subtypes can be translated with language strings in the format of 'notification_subjects:subject:{{subject}}' - where {{subject}} = 'blog', 'bookmark', 'page', etc.<br>
Group names will be passed into the language string 'notification_subjects:group'",
);
					
add_translation("en",$english);
