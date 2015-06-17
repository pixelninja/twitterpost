<?php

require_once(TOOLKIT . '/class.sectionmanager.php');
require_once(TOOLKIT . '/class.entrymanager.php');
require_once(TOOLKIT . '/class.authormanager.php');

class Extension_TwitterPost extends Extension {

    public function about() {
		return array(
			'name'         => 'Twitter Post',
			'version'      => '1.0.0',
			'release-date' => '2015-05-17',
			'author'       => array(
				'name'    => 'Phillip Gray',
				'email'   => 'pixel.ninjad@gmail.com'
			),
			'description' => 'Add a `post to twitter button` to set section edit pages.'
		);
	}

	public function uninstall() {
		return Symphony::Configuration()->remove('twitter-post');
	}

	public function install() {

    }

	public function getSubscribedDelegates() {
		return array(
			array(
				'page' 		=> '/backend/',
				'delegate'	=> 'InitaliseAdminPageHead',
				'callback'	=> 'initialiseAdminPageHead'
			),
			array(
				'page'		=> '/system/preferences/',
				'delegate' 	=> 'AddCustomPreferenceFieldsets',
				'callback' 	=> 'appendToPreferences'
			),
			array(
				'page' => '/system/preferences/',
				'delegate' => 'Save',
				'callback' => 'savePreferences'
			),
			array(
				'page' => '/system/preferences/success/',
				'delegate' => 'Save',
				'callback' => 'savePreferences'
			)
		);
	}

	public function savePreferences($context) {
        Symphony::Configuration()->remove('twitter-post');

        $selection = $context['settings']['twitter-post'];

        $context['settings']['twitter-post'] = array();

        if(is_array($selection)) {
            foreach($selection as $key => $item) {
                $context['settings']['twitter-post']['section'] .= $item;
                if ($key < (count($selection) - 1)) $context['settings']['twitter-post']['section'] .= ',';
            }
        }
	}

	public function initialiseAdminPageHead($context) {
        $page = Administration::instance()->Page;
        $sections = Symphony::Configuration()->get('twitter-post');
        $sections_array = explode(',', $sections['section']);

        if(in_array($page->_context['section_handle'], $sections_array) && $page->_context['page'] === 'edit') {
            $page->addStylesheetToHead(URL . '/extensions/twitterpost/assets/twitterpost.css', 'screen', 9001);
            $page->addScriptToHead('https://platform.twitter.com/widgets.js', 667);
            $page->addScriptToHead(URL . '/extensions/twitterpost/assets/twitterpost.js', 667);
        }
	}

	public function appendToPreferences($context) {
		$fieldset = new XMLElement('fieldset');
		$fieldset->setAttribute('class', 'settings');
		$fieldset->appendChild(new XMLElement('legend', __('Twitter Post Sections')));

		$div = new XMLElement('div');
		$div->setAttribute('class','group');

		$label = Widget::Label(__('Add twitter button to these sections:'));

        $sections = Symphony::Database()->fetch("
			SELECT * FROM `sym_sections`
		");
        $options= array();

        $existing_values = explode(',', Symphony::Configuration()->get('section', 'twitter-post'));
        
        foreach ($sections as $section) {
            $options[] = array(
                $section['handle'],
                (in_array($section['handle'], $existing_values) ? true : false),
                $section['name']
            );
        }

        $label->appendChild(Widget::Select('settings[twitter-post][]', $options, array('multiple' => 'multiple')));
        $div->appendChild($label);        

        $fieldset->appendChild($div);

		$context['wrapper']->appendChild($fieldset);
	}
}
