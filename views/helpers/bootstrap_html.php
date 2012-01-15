<?php
App::import('Helper', 'Html');

class BootstrapHtmlHelper extends HtmlHelper {

	function __construct($options = null) {
		parent::__construct($options);

		$this->tags['checkboxmultiplestart'] = '<ul class="inputs-list">';
		$this->tags['checkboxmultipleend'] = '</ul>';
		$this->tags['checkboxmultiple'] = '<li><label><input type="checkbox" name="%s[]" %s/><span>%s</span></label></li>';
	}
}
