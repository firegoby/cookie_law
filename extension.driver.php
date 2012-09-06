<?php
 Class extension_cookie_law extends Extension
 {
	 public function about() {
		 return array(
			 'name' => 'Cookie Law',
			 'version' => '1.0',
			 'release-date' => '2012-09-05',
			 'author' => array(
				 'name'     => '<a href="http://gielberkers.com">Giel Berkers</a>'
			 ),
			 'description' => 'Inject Javascript as soon as the user accepts the cookie law.'
		 );
	 }

	 public function getSubscribedDelegates(){
		 return array(
			 array('page'		=> '/system/preferences/',
				 'delegate'	=> 'AddCustomPreferenceFieldsets',
				 'callback'	=> 'appendPresets'),
			 array('page'		=> '/system/preferences/',
				 'delegate'	=> 'Save',
				 'callback'	=> 'savePresets')
		 );
	 }

	 public function appendPresets($context)
	 {
		 Administration::instance()->Page->addScriptToHead(URL.'/extensions/cookie_law/assets/cookie_law.js', 2000);

		 $wrapper = $context['wrapper'];

		 $fieldset = new XMLElement('fieldset', '', array('class'=>'settings'));
		 $fieldset->appendChild(new XMLElement('legend', __('Cookie Law')));

		 $label = Widget::Label(__('Enter the fields below or select a preset:'));
		 $options = array(
			 array('0', true, __('Choose a preset'))
		 );
		 $presets = glob(EXTENSIONS.'/cookie_law/presets/*.json');
		 foreach($presets as $preset)
		 {
			 $a = explode('-', basename(str_replace('.json', '', $preset)));
			 $name = ucfirst($a[0]).': '.ucfirst($a[1]);
			 $options[] = array(URL.'/extensions/cookie_law/presets/'.basename($preset), false, $name);
		 }

		 $label->appendChild(Widget::Select('cookie_preset', $options));
		 $fieldset->appendChild($label);

		 $label = Widget::Label(__('Text to show (wrap disclaimer link in curly braces)'));
		 $value = Symphony::Configuration()->get('text', 'cookie_law');
		 $label->appendChild(Widget::Input('settings[cookie_law][text]', $value));
		 $fieldset->appendChild($label);

		 $label = Widget::Label(__('Disclaimer text:'));
		 $value = Symphony::Configuration()->get('disclaimer', 'cookie_law');
		 $label->appendChild(Widget::Textarea('settings[cookie_law][disclaimer]', 15, 50, $value));
		 $fieldset->appendChild($label);

		 $label = Widget::Label(__('Accept Text'));
		 $value = Symphony::Configuration()->get('accept', 'cookie_law');
		 $label->appendChild(Widget::Input('settings[cookie_law][accept]', $value));
		 $fieldset->appendChild($label);

		 $label = Widget::Label(__('Decline Text'));
		 $value = Symphony::Configuration()->get('decline', 'cookie_law');
		 $label->appendChild(Widget::Input('settings[cookie_law][decline]', $value));
		 $fieldset->appendChild($label);

		 $label = Widget::Label(__('Javascript code to inject when the user accepts the cookie law:'));
		 $value = Symphony::Configuration()->get('javascript', 'cookie_law');
		 $label->appendChild(Widget::Textarea('settings[cookie_law][javascript]', 15, 50, $value));
		 $fieldset->appendChild($label);

		 $label = Widget::Label();
		 $value = Symphony::Configuration()->get('default_styling', 'cookie_law');
		 if(empty($value)) { $value = 'yes'; }
		 $input = Widget::Input('settings[cookie_law][default_styling]', 'yes' , 'checkbox', ($value == 'yes' ? array('checked'=>'checked') : null));
		 $label->setValue($input->generate() . ' ' . __('Include default styling'));
		 $fieldset->appendChild($label);

		 $wrapper->appendChild($fieldset);
	 }

	 public function savePresets($context)
	 {
		 $data = $context['settings']['cookie_law'];
		 if(!isset($data['default_styling'])) { $data['default_styling'] = 'no'; }
		 foreach($data as $key => $value)
		 {
			 Symphony::Configuration()->set($key, $value, 'cookie_law');
		 }
		 if(version_compare(Administration::Configuration()->get('version', 'symphony'), '2.2.5', '>'))
		 {
			 // S2.3+
		 	 Symphony::Configuration()->write();
		 } else {
			 // S2.2.5-
			 Administration::instance()->saveConfig();
		 }
	 }

 }