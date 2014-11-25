<?php
/**
 * Simple form class to handle config, validation and rendering for fast deployment on a PHP site without any framework
 *
 * @author  Zion Ng <zion@intzone.com>
 * @link    https://github.com/zionsg/ZnPhp-Form for canonical source repository
 * @since   2014-11-05T13:00+08:00
 * @version 1.0.0
 */

class ZnPhp_Form
{
    /**
     * Form config comprising display groups and form elements
     *
     * @example array(
     *              'requiredClass'       => 'required',
     *              'descriptionClass'    => 'description',
     *              'errorClass'          => 'error',
     *              'inputSeparator'      => '<br />', // separator for elements with multiple inputs (checkbox, radio)
     *              'breakChainOnFailure' => false,    // whether to stop validation if one of the elements fail
     *              'groups' => array( // display groups
     *                  'group_1' => array( // group name - used for DOM id as well
     *                      'label' => 'Group 1',
     *                      'class' => 'group-css-class',
     *                      'elements' => '*', // * to include all elements or array('element_1', ...)
     *                  ),
     *              ),
     *              'elements' => array(
     *                  'element_1' => array( // element name - used for DOM id and input name as well
     *                      'label'         => 'Element 1',
     *                      'type'          => 'text',
     *                      'value'         => '', // default value
     *                      'options'       => array(), // value-option pairs for checkbox, radio and select elements
     *                      'optionAsValue' => false, // if true, 'options' array(a, b) = array('a' => 'a', 'b' => 'b')
     *                      'description'   => '',
     *                      'placeholder'   => 'Type text here',
     *                      'attributes'    => array('data-custom' => 'custom-value'),
     *                      'required'      => true, // validator only runs if this is true
     *                      'errorMessage'  => 'Please enter required value', // default error message
     *                      'validator'     => function ($value, $form) { return true; }, // return true or error message
     *                      'labelClass'    => 'label-css-class',
     *                      'elementClass'  => 'element-css-class',
     *                      'labelRenderer'   => function ($name, $form) { return ''; },  // override default renderer
     *                      'elementRenderer' => function ($name, $form) { return ''; },  // override default renderer
     *                   ),
     *              ),
     *              'labelRenderers' => array( // label renderer for each input type
     *                  '*'      => function ($name, $element, $form) { return ''; }, // fallback if type not found
     *                  'button' => function ($name, $element, $form) { return ''; },
     *              ),
     *              'elementRenderers' => array( // element renderer for each input type
     *                  '*'    => function ($name, $element, $form) { return ''; }, // fallback if type not be found
     *                  'text' => function ($name, $element, $form) { return '<input type="text" />'; },
     *              ),
     *          )
     * @var array
     */
    protected $config = array();

    /**
     * Form data
     *
     * @var array Element-value pairs
     */
    protected $data = array();

    /**
     * Element errors
     *
     * @var array Element-message pairs
     */
    protected $errors = array();

    /**
     * Config defaults
     *
     * @var array
     */
    protected $configDefaults = array(
        'requiredClass'       => 'required',
        'descriptionClass'    => 'description',
        'errorClass'          => 'error',
        'inputSeparator'      => '<br />',
        'breakChainOnFailure' => false,
        'groups'              => array(),
        'elements'            => array(),
        'labelRenderers'      => array(), // to be populated in constructor
        'elementRenderers'    => array(), // to be populated in constructor
    );

    /**
     * Group defaults
     *
     * @var array
     */
    protected $groupDefaults = array('label' => '', 'class' => '', 'elements' => array());

    /**
     * Element defaults
     *
     * @var array
     */
    protected $elementDefaults = array(
        'label'         => '',
        'type'          => 'text',
        'value'         => '',
        'options'       => array(),
        'optionAsValue' => false, // ie. value must be stated for each option - array(value => option)
        'description'   => '',
        'placeholder'   => '',
        'attributes'    => array(),
        'required'      => false,
        'errorMessage'  => 'Please enter required value',
        'validator'     => null, // ie. no validator
        'labelClass'    => '',
        'elementClass'  => '',
        'labelRenderer'   => null, // ie. use default renderer
        'elementRenderer' => null, // ie. use default renderer
    );

    /**
     * Constructor
     *
     * @param array $config Form config
     */
    public function __construct(array $config)
    {
        // Populate default label and element renderers
        $this->setDefaultRenderers();

        // Ensure array keys exist before storing config
        $config = array_merge($this->configDefaults, $config);
        foreach ($config['groups'] as $name => $group) {
            $config['groups'][$name] = array_merge($this->groupDefaults, $group);
        }
        foreach ($config['elements'] as $name => $element) {
            $config['elements'][$name] = array_merge($this->elementDefaults, $element);
        }

        $this->config = $config;
    }

    /**
     * Get CSS class for required fields
     *
     * @return string
     */
    public function getRequiredClass()
    {
        return $this->config['requiredClass'];
    }

    /**
     * Get CSS class for description text
     *
     * @return string
     */
    public function getDescriptionClass()
    {
        return $this->config['descriptionClass'];
    }

    /**
     * Get CSS class for error messages
     *
     * @return string
     */
    public function getErrorClass()
    {
        return $this->config['errorClass'];
    }

    /**
     * Get input separator for elements with multiple inputs (checkbox, radio)
     *
     * @return string
     */
    public function getInputSeparator()
    {
        return $this->config['inputSeparator'];
    }

    /**
     * Break validation chain on failure?
     *
     * @return bool
     */
    public function breakChainOnFailure()
    {
        return $this->config['breakChainOnFailure'];
    }

    /**
     * Get config for all groups
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->config['groups'];
    }

    /**
     * Get config for specific group
     *
     * @param  string $groupName Group name
     * @return array
     */
    public function getGroup($groupName)
    {
        return (isset($this->config['groups'][$groupName]) ? $this->config['groups'][$groupName] : array());
    }

    /**
     * Get config for all elements
     *
     * @return array
     */
    public function getElements()
    {
        return $this->config['elements'];
    }

    /**
     * Get config for specific element
     *
     * @param  string $elementName Element name
     * @return array
     */
    public function getElement($elementName)
    {
        return (isset($this->config['elements'][$elementName]) ? $this->config['elements'][$elementName] : array());
    }

    /**
     * Set form data
     *
     * @param  array $data
     * @param  bool  $override Default = true. Whether to override existing data or to merge only those fields in $data
     * @return this
     */
    public function setData($data, $override = true)
    {
        if ($override) {
            $this->data = $data;
        } else {
            $this->data = array_merge($this->data, $data);
        }

        return $this;
    }

    /**
     * Get all form data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set form value for specific element
     *
     * @param  string $elementName
     * @param  mixed  $value
     * @return this
     */
    public function setValue($elementName, $value)
    {
        // Check config for element and not data as data may be empty at point of call
        if (isset($this->config['elements'][$elementName])) {
            $this->data[$elementName] = $value;
        }

        return $this;
    }

    /**
     * Get form value for specific element
     *
     * @param  string $elementName
     * @param  mixed  $default Optional default value to return if value not found
     * @return mixed
     */
    public function getValue($elementName, $default = null)
    {
        return (isset($this->data[$elementName]) ? $this->data[$elementName] : $default);
    }

    /**
     * Get all form element errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get error for specific element
     *
     * @param  string $elementName
     * @return string
     */
    public function getError($elementName)
    {
        return (isset($this->errors[$elementName]) ? $this->errors[$elementName] : '');
    }

    /**
     * Validate form
     *
     * @param  array Form values, eg. array('element_1' => 'Test')
     * @return bool
     */
    public function isValid(array $data)
    {
        // Store form data and clear errors
        $this->setData($data);
        $this->errors = array();

        $isValid = true;
        $breakChainOnFailure = $this->breakChainOnFailure();

        foreach ($this->getElements() as $name => $element) {
            $value = isset($data[$name]) ? $data[$name] : null;
            if ($element['required'] && (null === $value || '' === $value)) {
                $result = $element['errorMessage'];
            } else {
                $validator = $element['validator'];
                if (null === $validator || !is_callable($validator)) {
                    continue;
                }
                $result = $validator($value, $this);
            }

            if ($result !== true) { // note that preg_match() does not always return boolean true/false
                $isValid = false;
                $this->errors[$name] = $result ?: $element['errorMessage'];
                if ($breakChainOnFailure) {
                    break;
                }
            }
        }

        // Clear form data if valid
        if ($isValid) {
            $this->data = array();
        }

        return $isValid;
    }

    /**
     * Render label for element
     *
     * @param  string $name    Element name
     * @param  array  $element Element config
     * @return string
     */
    public function renderLabel($name, $element)
    {
        if (!$element) {
            return '';
        }

        // Get renderer
        $renderer = $element['labelRenderer']; // use element's own renderer if specified
        if (null === $renderer) {
            if (isset($this->config['labelRenderers'][$element['type']])) { // use generic renderer for element's type
                $renderer = $this->config['labelRenderers'][$element['type']];
            } elseif (isset($this->config['labelRenderers']['*'])) { // use fallback renderer
                $renderer = $this->config['labelRenderers']['*'];
            }
        }

        if ($renderer !== null && is_callable($renderer)) {
            return $renderer($name, $element, $this);
        }

        return '';
    }

    /**
     * Render element input, description and error
     *
     * @param  string $name    Element name
     * @param  array  $element Element config
     * @return string
     */
    public function renderElement($name, $element)
    {
        if (!$element) {
            return '';
        }

        // Get renderer
        $renderer = $element['elementRenderer']; // use element's own renderer if specified
        if (null === $renderer) {
            if (isset($this->config['elementRenderers'][$element['type']])) { // use generic renderer for element's type
                $renderer = $this->config['elementRenderers'][$element['type']];
            } elseif (isset($this->config['elementRenderers']['*'])) { // use fallback renderer
                $renderer = $this->config['elementRenderers']['*'];
            }
        }

        if ($renderer !== null && is_callable($renderer)) {
            return $renderer($name, $element, $this);
        }

        return '';
    }

    /**
     * Render elements in Twitter Bootstrap 3 style
     *
     * @param  array  $elementNames Element names
     * @param  string $labelWidth   Default = 'col-sm-3'. Grid class for label
     * @param  string $elementWidth Default = 'col-sm-9'. Grid class for element
     * @return string
     */
    public function renderElements(array $elementNames, $labelWidth = 'col-sm-3', $elementWidth = 'col-sm-9')
    {
        $output = '';

        foreach ($elementNames as $name) {
            // Add Bootstrap class to element config
            $element = $this->getElement($name);
            if (!$element) {
                continue;
            }

            $element['labelClass'] .= $labelWidth . ' control-label';
            if (!in_array($element['type'], array('checkbox', 'radio', 'submit'))) {
                $element['elementClass'] .= 'form-control';
            }
            $output .= sprintf(
                '<div class="form-group" for="%s">%s<div class="%s">%s</div></div>',
                $name,
                $this->renderLabel($name, $element),
                $elementWidth,
                $this->renderElement($name, $element)
            );
        }

        return $output;
    }

    /**
     * Set default label and element renderer callbacks for all input types
     *
     * @return void
     */
    protected function setDefaultRenderers()
    {
        $this->configDefaults['labelRenderers'] = array(
            '*' => $this->getDefaultLabelRenderer(),
        );

        $this->configDefaults['elementRenderers'] = array(
            '*' => $this->getDefaultElementRenderer(),
        );
    }

    /**
     * Generic label renderer
     *
     * @return callback
     */
    protected function getDefaultLabelRenderer()
    {
        return function ($name, $element, $form) {
            $type     = $element['type'];
            $required = ($element['required'] ? ' ' . $form->getRequiredClass() : '');

            if (in_array($type, array('button', 'reset', 'submit'))) {
                // return '';
            }

            return sprintf(
                '<label for="%s" class="%s">%s</label>',
                $name,
                $element['labelClass'] . $required,
                $element['label']
            );
        };
    }

    /**
     * Generic element renderer
     *
     * Renders input, description and error.
     *
     * @return callback
     */
    protected function getDefaultElementRenderer()
    {
        return function ($name, $element, $form) {
            $type        = $element['type'];
            $value       = $form->getValue($name, $element['value']);
            $description = $element['description'];
            $error       = $form->getError($name);

            if ($description) {
                $description = sprintf(
                    '<div class="%s">%s</div>',
                    $form->getDescriptionClass(),
                    $description
                );
            }
            if ($error) {
                $error = sprintf(
                    '<div class="%s">%s</div>',
                    $form->getErrorClass(),
                    $error
                );
            }

            $attrs = '';
            foreach($element['attributes'] as $attr => $attrValue) {
                $attrs .= sprintf('%s="%s" ', $attr, $attrValue);
            }

            $input = '';
            $inputs = array();
            $inputSeparator = $form->getInputSeparator();
            if ('checkbox' == $type) {
                $options = $element['options'];
                if ($element['optionAsValue']) {
                    $options = array_combine($options, $options);
                }
                foreach ($options as $optionValue => $option) {
                    $isChecked = is_array($value) ? in_array($optionValue, $value) : ($value == $optionValue);
                    $inputs[] = sprintf(
                        '<input type="%s" name="%s[]" value="%s" class="%s" %s %s />%s',
                        $element['type'],
                        $name,
                        $optionValue,
                        $element['elementClass'],
                        $attrs,
                        ($isChecked ? 'checked="checked"' : ''),
                        $option
                    );
                }
                $input = implode($inputSeparator, $inputs);
            } elseif ('radio' == $type) {
                $options = $element['options'];
                if ($element['optionAsValue']) {
                    $options = array_combine($options, $options);
                }
                foreach ($options as $optionValue => $option) {
                    $inputs[] = sprintf(
                        '<input type="%s" name="%s" value="%s" class="%s" %s %s />%s',
                        $element['type'],
                        $name,
                        $optionValue,
                        $element['elementClass'],
                        $attrs,
                        ($value == $optionValue ? 'checked="checked"' : ''),
                        $option
                    );
                }
                $input = implode($inputSeparator, $inputs);
            } elseif ('select' == $type) {
                $options = $element['options'];
                if ($element['optionAsValue']) {
                    $options = array_combine($options, $options);
                }
                $input = sprintf(
                    '<select id="%s" name="%s" class="%s" %s />',
                    $name,
                    $name,
                    $element['elementClass'],
                    $attrs
                );
                foreach ($options as $optionValue => $option) {
                    $input .= sprintf(
                        '<option value="%s" %s>%s</option>',
                        $optionValue,
                        ($value == $optionValue ? 'selected="selected"' : ''),
                        $option
                    );
                }
                $input .= '</select>';
            } elseif ('textarea' == $type) {
                $input = sprintf(
                    '<textarea id="%s" name="%s" placeholder="%s" class="%s" %s />%s</textarea>',
                    $name,
                    $name,
                    $element['placeholder'],
                    $element['elementClass'],
                    $attrs,
                    $value
                );
            } elseif ('html' == $type) { // static html
                $input = $value;
            } else {
                $input = sprintf(
                    '<input type="%s" id="%s" name="%s" value="%s" placeholder="%s" class="%s" %s />',
                    $element['type'],
                    $name,
                    $name,
                    $value,
                    $element['placeholder'],
                    $element['elementClass'],
                    $attrs
                );
            }

            return sprintf(
                '%s%s%s',
                $input,
                $description,
                $error
            );
        };
    }
}
