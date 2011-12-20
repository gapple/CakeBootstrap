<?php
App::import('Helper', 'Form');

class BootstrapFormHelper extends FormHelper {

	// TODO override $this->Html with BootstrapHtml if necessary

	/**
	 * Set defaults for Bootstrap structure and pass through to Form::error().
	 */
	function error($field, $text = null, $options = array()) {
		$defaults = array(
			'wrap' => 'span',
			'class' => 'help-inline',
		);

		$options = array_merge($defaults, $options);

		return parent::error($field, $text, $options);
	}
	/**
	 * Generates a form input element complete with label and wrapper div.
	 *
	 * Bootstrap specific changes include an additional wrapper and adjusted
	 * structure
	 *
	 * CakePHP:
	 * <div class='input'>
	 *   --Before--
	 *   <label>
	 *   --Between--
	 *   <input>
	 *   --After--
	 * </div>
	 * --Error--
	 *
	 * Bootstrap:
	 * <div class="clearfix">
	 * --Before--
	 * <label>
	 * --Between--
	 * <div class="input">
	 *   <input>
	 *   --Error--
	 * </div>
	 * --After--
	 * </div>
	 *
	 * @see HtmlHelper::input().
	 */
	function input($fieldName, $options = array()) {
		$this->setEntity($fieldName);

		$options = array_merge(
			array('before' => null, 'between' => null, 'after' => null, 'format' => null),
			$this->_inputDefaults,
			$options
		);

		$modelKey = $this->model();
		$fieldKey = $this->field();
		if (!isset($this->fieldset[$modelKey])) {
			$this->_introspectModel($modelKey);
		}

		if (!isset($options['type'])) {
			$magicType = true;
			$options['type'] = 'text';
			if (isset($options['options'])) {
				$options['type'] = 'select';
			} elseif (in_array($fieldKey, array('psword', 'passwd', 'password'))) {
				$options['type'] = 'password';
			} elseif (isset($this->fieldset[$modelKey]['fields'][$fieldKey])) {
				$fieldDef = $this->fieldset[$modelKey]['fields'][$fieldKey];
				$type = $fieldDef['type'];
				$primaryKey = $this->fieldset[$modelKey]['key'];
			}

			if (isset($type)) {
				$map = array(
					'string'  => 'text',     'datetime'  => 'datetime',
					'boolean' => 'checkbox', 'timestamp' => 'datetime',
					'text'    => 'textarea', 'time'      => 'time',
					'date'    => 'date',     'float'     => 'text'
				);

				if (isset($this->map[$type])) {
					$options['type'] = $this->map[$type];
				} elseif (isset($map[$type])) {
					$options['type'] = $map[$type];
				}
				if ($fieldKey == $primaryKey) {
					$options['type'] = 'hidden';
				}
			}
			if (preg_match('/_id$/', $fieldKey) && $options['type'] !== 'hidden') {
				$options['type'] = 'select';
			}

			if ($modelKey === $fieldKey) {
				$options['type'] = 'select';
				if (!isset($options['multiple'])) {
					$options['multiple'] = 'multiple';
				}
			}
		}
		$types = array('checkbox', 'radio', 'select');

		if (
			(!isset($options['options']) && in_array($options['type'], $types)) ||
			(isset($magicType) && $options['type'] == 'text')
		) {
			$view =& ClassRegistry::getObject('view');
			$varName = Inflector::variable(
				Inflector::pluralize(preg_replace('/_id$/', '', $fieldKey))
			);
			$varOptions = $view->getVar($varName);
			if (is_array($varOptions)) {
				if ($options['type'] !== 'radio') {
					$options['type'] = 'select';
				}
				$options['options'] = $varOptions;
			}
		}

		$autoLength = (!array_key_exists('maxlength', $options) && isset($fieldDef['length']));
		if ($autoLength && $options['type'] == 'text') {
			$options['maxlength'] = $fieldDef['length'];
		}
		if ($autoLength && $fieldDef['type'] == 'float') {
			$options['maxlength'] = array_sum(explode(',', $fieldDef['length']))+1;
		}

		$divOptions = array();
		$div = $this->_extractOption('div', $options, true);
		unset($options['div']);

		if (!empty($div)) {
			$divOptions['class'] = 'input';
			$divOptions = $this->addClass($divOptions, $options['type']);
			if (is_string($div)) {
				$divOptions['class'] = $div;
			} elseif (is_array($div)) {
				$divOptions = array_merge($divOptions, $div);
			}
			if (
				isset($this->fieldset[$modelKey]) &&
				in_array($fieldKey, $this->fieldset[$modelKey]['validates'])
			) {
				$divOptions = $this->addClass($divOptions, 'required');
			}
			if (!isset($divOptions['tag'])) {
				$divOptions['tag'] = 'div';
			}
		}

		$clearOptions = array();
		$clear = $this->_extractOption('clearfix', $options, true);
		unset($options['clearfix']);
		if (!empty($clear)) {
			$clearOptions['class'] = 'clearfix';
			if (is_string($clear)) {
				$clearOptions['class'] = $this->addClass($clearOptions, $clear);
			}
			elseif (is_array($clear)) {
				$clearOptions = array_merge($clearOptions, $clear);
			}
			if (!isset($clearOptions['tag'])) {
				$clearOptions['tag'] = 'div';
			}
		}

		$label = null;
		if (isset($options['label']) && $options['type'] !== 'radio' && $options['type'] !== 'checkbox') {
			$label = $options['label'];
			unset($options['label']);
		}

		if ($options['type'] === 'radio') {
			$label = false;
			if (isset($options['options'])) {
				$radioOptions = (array)$options['options'];
				unset($options['options']);
			}
		}

		if ($options['type'] === 'checkbox') {
			$label = false;
		}

		if ($label !== false) {
			$label = $this->_inputLabel($fieldName, $label, $options);
		}

		$error = $this->_extractOption('error', $options, null);
		unset($options['error']);

		$selected = $this->_extractOption('selected', $options, null);
		unset($options['selected']);

		if (isset($options['rows']) || isset($options['cols'])) {
			$options['type'] = 'textarea';
		}

		if ($options['type'] === 'datetime' || $options['type'] === 'date' || $options['type'] === 'time' || $options['type'] === 'select') {
			$options += array('empty' => false);
		}
		if ($options['type'] === 'datetime' || $options['type'] === 'date' || $options['type'] === 'time') {
			$dateFormat = $this->_extractOption('dateFormat', $options, 'MDY');
			$timeFormat = $this->_extractOption('timeFormat', $options, 12);
			unset($options['dateFormat'], $options['timeFormat']);
		}

		$type = $options['type'];
		$out = array_merge(
			array('before' => null, 'label' => null, 'between' => null, 'input' => null, 'after' => null, 'error' => null),
			array('before' => $options['before'], 'label' => $label, 'between' => $options['between'], 'after' => $options['after'])
		);
		$format = null;
		if (is_array($options['format']) && in_array('input', $options['format'])) {
			$format = $options['format'];
		}
		unset($options['type'], $options['before'], $options['between'], $options['after'], $options['format']);

		switch ($type) {
			case 'hidden':
				$input = $this->hidden($fieldName, $options);
				$format = array('input');
				unset($divOptions);
			break;
			case 'checkbox':
				$input = $this->checkbox($fieldName, $options);
				$format = $format ? $format : array('before', 'input', 'after');
			break;
			case 'radio':
				$input = $this->radio($fieldName, $radioOptions, $options);
			break;
			case 'text':
			case 'password':
			case 'file':
				$input = $this->{$type}($fieldName, $options);
			break;
			case 'select':
				$options += array('options' => array());
				$list = $options['options'];
				unset($options['options']);
				$input = $this->select($fieldName, $list, $selected, $options);
			break;
			case 'time':
				$input = $this->dateTime($fieldName, null, $timeFormat, $selected, $options);
			break;
			case 'date':
				$input = $this->dateTime($fieldName, $dateFormat, null, $selected, $options);
			break;
			case 'datetime':
				$input = $this->dateTime($fieldName, $dateFormat, $timeFormat, $selected, $options);
			break;
			case 'textarea':
			default:
				$input = $this->textarea($fieldName, $options + array('cols' => '30', 'rows' => '6'));
			break;
		}

		if ($type != 'hidden' && $error !== false) {
			$errMsg = $this->error($fieldName, $error);
			if ($errMsg) {
				$divOptions = $this->addClass($divOptions, 'error');
				$input .= $errMsg;
			}
		}

		if (!empty($divOptions['tag'])) {
			$tag = $divOptions['tag'];
			unset($divOptions['tag']);
			$input = $this->Html->tag($tag, $input, $divOptions);
		}
		$out['input'] = $input;

		$format = $format ? $format : array('before', 'label', 'between', 'input', 'after',);
		$output = '';
		foreach ($format as $element) {
			$output .= $out[$element];
			unset($out[$element]);
		}

		if (!empty($clearOptions['tag'])) {
			$tag = $clearOptions['tag'];
			unset($clearOptions['tag']);
			$output = $this->Html->tag($tag, $output, $clearOptions);
		}

		return $output;
	}

	/**
	 * Creates a checkbox input widget.
	 *
	 * Bootstrap specific changes are wrapping the input with the label,
	 * instead of associting the label with a 'for' attribute.
	 *
	 * ### Options:
	 *
	 * - `value` - the value of the checkbox
	 * - `checked` - boolean indicate that this checkbox is checked.
	 * - `hiddenField` - boolean to indicate if you want the results of checkbox() to include
	 *    a hidden input with a value of ''.
	 * - `disabled` - create a disabled input.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $options Array of HTML attributes.
	 * @return string An HTML text input element.
	 * @access public
	 * @link http://book.cakephp.org/view/1414/checkbox
	 */
	function checkbox($fieldName, $options = array()) {
		$options = $this->_initInputField($fieldName, $options) + array('hiddenField' => true);
		$value = current($this->value());
		$output = "";

		if (empty($options['value'])) {
			$options['value'] = 1;
		} elseif (
			(!isset($options['checked']) && !empty($value) && $value === $options['value']) ||
			!empty($options['checked'])
		) {
			$options['checked'] = 'checked';
		}
		$hidden = '';
		if ($options['hiddenField']) {
			$hiddenOptions = array(
				'id' => $options['id'] . '_', 'name' => $options['name'],
				'value' => '0', 'secure' => false
			);
			if (isset($options['disabled']) && $options['disabled'] == true) {
				$hiddenOptions['disabled'] = 'disabled';
			}
			$hidden = $this->hidden($fieldName, $hiddenOptions);
		}
		unset($options['hiddenField']);

		$input =  sprintf(
			$this->Html->tags['checkbox'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		);

		$labelText = null;
		if (isset($options['label'])) {
			if (is_array($options['label'])) {
				$labelText = $options['label']['text'];
			}
			else {
				$labelText = $options['label'];
			}
		}

		// Snippet from FormHelper::label() to generate friendly label text
		// if not provided.
		if ($labelText === null) {
			if (strpos($fieldName, '.') !== false) {
				$labelText = array_pop(explode('.', $fieldName));
			} else {
				$labelText = $fieldName;
			}
			if (substr($labelText, -3) == '_id') {
				$labelText = substr($labelText, 0, -3);
			}
			$labelText = __(Inflector::humanize(Inflector::underscore($labelText)), true);
		}

		$labelText = $this->Html->tag('span', $labelText);

		$output = $this->label($fieldName, $input . $labelText, array('for' => $options['id']));

		$output = $this->Html->tag('li', $output);
		$output = $this->Html->tag('ul', $output, array('class' => 'inputs-list'));

		return $hidden . $output;
	}

	// TODO checkbox / radio lists
}
