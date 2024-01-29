<?php
/**
 * Version details
 *
 * @package    local_forms
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");
require_once("$CFG->libdir/moodlelib.php");
require_once("$CFG->libdir/filelib.php");
require_once($CFG->dirroot . '/local/demo/second_db.php');


// Fetch users from the second database




class edit extends moodleform
{


    function definition()
    {
        $mform = $this->_form; // Don't forget the underscore!

        // Set form attributes
        $attr = $mform->getAttributes();
        $attr['enctype'] = " multipart/form-data";
        $mform->setAttributes($attr);

        // Add form elements
        $mform->addElement('text', 'user_email', get_string('email'), 'maxlength="100" size="25" ');
        $mform->setType('user_email', PARAM_NOTAGS);
        // $mform->addRule('user_email', get_string('required'),'required', null,'');

        $mform->addElement('text', 'user_name', get_string('username'), 'maxlength="100" size="25" ');
        $mform->setType('user_name', PARAM_NOTAGS);
        // $mform->addRule('user_name', get_string('required'),'required', null,'');

        $mform->addElement('text', 'phone_number', get_string('phone'), 'maxlength="12" size="25" ');
        $mform->setType('phone_number', PARAM_NOTAGS);
        // $mform->addRule('phone_number', get_string('required'),'required', null,'');


        $mform->addElement('hidden', 'latitude', ''); // Hidden input for latitude
        $mform->setType('latitude', PARAM_RAW);

        $mform->addElement('hidden', 'longitude', ''); // Hidden input for longitude
        $mform->setType('longitude', PARAM_RAW);

        $mform->addElement('hidden', 'capturedImageData', ''); // Existing hidden input for image data
        $mform->setType('capturedImageData', PARAM_RAW);

        // Add a custom HTML element for capturing images from the mobile camera
        $mform->addElement('html', '
            <input type="file" id="picture" name="capturedImage" accept="image/*" capture="environment" />
            <input type="hidden" id="capturedImageData" name="capturedImageData" />
            <input type="hidden" id="latitude" name="latitude" />
            <input type="hidden" id="longitude" name="longitude" />
            <input type="hidden" id="city" name="city" />
        ');


        $btnstring = get_string('submit', 'local_demo');
        $this->add_action_buttons(true, $btnstring);

    }
    function validation($data, $files)
    {
        return array();
    }
}


class bel_edit extends moodleform
{
    private $external_db_section;
    private $external_db_field;
    private $external_db_options;

    public function __construct($external_db_section, $external_db_field, $external_db_options)
    {
        $this->external_db_section = $external_db_section;
        $this->external_db_field = $external_db_field;
        $this->external_db_options = $external_db_options;
        parent::__construct();
    }

    function definition()
    {
        $mform = $this->_form;


        $mform->addElement('html', '<div class="container">');
        foreach ($this->external_db_section as $section) {
            $mform->addElement('html', '<fieldset><legend>' . $section['section_name'] . '</legend>');

            foreach ($this->external_db_field as $field) {
                $i = 0;
                $i++;

                if ($field['section_id'] == $section['section_id']) {
                    $field_name = str_replace(' ', '', strtolower($field['form_field_name'])) . $i;

                    // Start of the switch case
                    switch ($field['field_type_id']) {
                        case 1:
                            $mform->addElement('textarea', $field_name, $field['form_field_name'], array('class' => 'w-75', 'rows' => 5));
                            $mform->setType($field_name, PARAM_TEXT);

                            $mform->addElement('hidden', 'field_name[]', $field_name);
                            $mform->setType('field_name[]', PARAM_TEXT);
                            $mform->addElement('hidden', 'field_id[]', $field['field_id']);
                            $mform->setType('field_id[]', PARAM_INT);
                            break;
                        case 2:
                            $mform->addElement('text', $field_name, $field['form_field_name']);
                            $mform->setType($field_name, PARAM_TEXT);

                            $mform->addElement('hidden', 'field_name[]', $field_name);
                            $mform->setType('field_name[]', PARAM_TEXT);
                            $mform->addElement('hidden', 'field_id[]', $field['field_id']);
                            $mform->setType('field_id[]', PARAM_INT);
                            break;
                        case 3:
                            $field_name = str_replace(' ', '', strtolower($field['form_field_name'])) . $i;

                            $mform->addElement('html', '<div class="my-2">');

                            // Label for the field
                            $mform->addElement('html', '<label for="' . $field_name . '">' . $field['form_field_name'] . ($field['is_required'] == 1 ? '<i style="color:red">*</i>' : '') . '</label>');

                            // Number input field
                            $mform->addElement('text', $field_name, '', array('type' => 'number', 'min' => 0, 'inputmode' => 'numeric', 'default' => isset($field['response']) ? $field['response'] : ''));
                            $mform->setType($field_name, PARAM_TEXT);



                            $mform->addElement('hidden', 'field_name[]', $field_name);
                            $mform->setType('field_name[]', PARAM_TEXT);
                            $mform->addElement('hidden', 'field_id[]', $field['field_id']);
                            $mform->setType('field_id[]', PARAM_INT);
                            $mform->addElement('hidden', 'response_id[]', '');
                            $mform->setType('response_id[]', PARAM_INT); // Add this line to set the type

                            $mform->addElement('hidden', 'is_number[]', $field['field_id']);
                            $mform->setType('is_number[]', PARAM_INT); // Add this line to set the type

                            $mform->addElement('html', '</div>');
                            $mform->addElement('html', '<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;">' . $field['form_field_description'] . '</p>');
                            $mform->addElement('html', '<hr>');
                            break;
                        // Add more cases for other field types if needed

                        case 4:
                            $mform->addElement('html', '<div class="d-flex justify-content-between my-2">');
                            $mform->addElement('html', '<div class="">');
                            $mform->addElement('html', '<label for="' . $field_name . '">' . $field['form_field_name'] . ($field['is_required'] == 1 ? '<i style="color:red">*</i>' : '') . '</label>');
                            $mform->addElement('html', '<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;">' . $field['form_field_description'] . '</p>');

                            $radioOptions = array();

                            // Populate $radioOptions with values
                            foreach ($this->external_db_options as $option) {
                                if ($option['field_id'] == $field['field_id']) {
                                    $radioOptions[$option['option_name']] = $option['option_name'];
                                }
                            }

                            $mform->addElement('html', '<div class="row mb-3">');
                            $mform->addElement('html', '<label class="col-sm-2 col-form-label">' . $field['form_field_name'] . ($field['is_required'] == 1 ? '<i style="color:red">*</i>' : '') . '</label>');
                            $mform->addElement('html', '<div class="col-sm-10">');

                            foreach ($radioOptions as $optionValue => $optionLabel) {
                                $radioAttributes = array(
                                    'id' => $field_name . '_' . $optionValue,
                                    'name' => $field_name,
                                    'value' => $optionValue,
                                    'checked' => (isset($field['response_id']) && $optionValue == $field['response'])
                                );
                                $mform->addElement('checkbox', $field_name, '', $optionLabel, $radioAttributes);
                            }

                            $mform->addElement('html', '</div>');
                            $mform->addElement('html', '</div>');

                            // Existing code...
                            break;
                        // Add more cases for other field types if needed

                        case 5:
                            $mform->addElement('html', '<div class="d-flex justify-content-between my-2">');
                            $mform->addElement('html', '<div class="">');
                            $mform->addElement('html', '<label for="' . $field_name . '">' . $field['form_field_name'] . ($field->is_required == 1 ? '<i style="color:red">*</i>' : '') . '</label>');
                            $mform->addElement('html', '<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;">' . $field['form_field_description'] . '</p>');

                            $select = array();
                            $select[0] = get_string('choose') . '...';
                            foreach ($this->external_db_options as $option) {
                                if ($option['field_id'] == $field['field_id']) {
                                    $select[$option['option_name']] = $option['option_name'];
                                }
                            }
                            $mform->addElement('select', $field_name, '', $select);

                            $mform->addElement('html', '</div>');
                            $mform->addElement('hidden', 'field_name[]', $field_name);
                            $mform->setType('field_name[]', PARAM_TEXT);
                            $mform->addElement('hidden', 'field_id[]', $field['field_id']);
                            $mform->setType('field_id[]', PARAM_INT);
                            ;
                            $mform->addElement('html', '</div>');
                            $mform->addElement('html', '<hr>');
                            break;

                        case 6:
                            $mform->addElement('html', '<div class="d-flex justify-content-between my-2">');
                            $mform->addElement('html', '<div class="">');
                            $mform->addElement('html', '<label for="' . $field_name . '">' . $field['form_field_name'] . ($field['is_required'] == 1 ? '<i style="color:red">*</i>' : '') . '</label>');
                            $mform->addElement('html', '<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;">' . $field['form_field_description'] . '</p>');

                            foreach ($this->external_db_options as $option) {
                                if ($option['field_id'] == $field['field_id']) {
                                    $checkboxName = $field_name . '-' . $option['option_id'];
                                    $mform->addElement('checkbox', $checkboxName, '', $option['option_name'], array('id' => $checkboxName, 'value' => $option['option_name'], 'checked' => (isset($field['response_id']) && in_array($option['option_name'], explode(',', $field['response'])))));
                                    $mform->addElement('html', '<label class="mr-1" for="' . $checkboxName . '">' . $option['option_name'] . '</label>');
                                    $mform->addElement('html', '<input type="hidden" name="field_name[]" value="' . $checkboxName . '">');
                                    $mform->setType($checkboxName, PARAM_TEXT);
                                }
                                $mform->addElement('html', '</div>');
                                $mform->addElement('html', '<hr>');
                                break;


                            }

                        case 7:
                            $mform->addElement('html', '<div class="my-2">');
                            $mform->addElement('html', '<div class="d-flex justify-content-between">');
                            $mform->addElement('html', '<label class="m-0" for="' . $field_name . '">' . $field['form_field_name'] . '</label>');
                            $mform->addElement('filepicker', $field_name, $field['form_field_name'], null, array('maxbytes' => 0, 'accepted_types' => '*'));
                            $mform->addElement('html', '</div>');
                            $mform->addElement('html', '<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;">' . $field['form_field_description'] . '</p>');
                            $mform->addElement('hidden', 'field_name[]', $field_name);
                            $mform->addElement('hidden', (isset($field['response_id']) ? 'response_id[]' : 'field_id[]'), (isset($field['response_id']) ? $field['response_id'] : $field['field_id']));
                            $mform->addElement('html', '</div>');
                            break;


                        case 8:

                            $mform->addElement('html', '<div class="d-flex justify-content-between my-2">');
                            $mform->addElement('html', '<div class="">');
                            $mform->addElement('html', '<label for="" >' . $field['form_field_name'] . ($field['is_required'] == 1 ? '<i style="color:red">*</i>' : '') . '</label>');
                            $mform->addElement('html', '<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;">' . $field['form_field_description'] . '</p>');

                            $mform->addElement('hidden', 'latitude', ''); // Hidden input for latitude
                            $mform->setType('latitude', PARAM_RAW);

                            $mform->addElement('hidden', 'longitude', ''); // Hidden input for longitude
                            $mform->setType('longitude', PARAM_RAW);

                            $mform->addElement('hidden', 'capturedImageData', ''); // Existing hidden input for image data
                            $mform->setType('capturedImageData', PARAM_RAW);

                            // Add a custom HTML element for capturing images from the mobile camera
                            $mform->addElement('html', '
                            <input type="file" id="picture" name="capturedImage" accept="image/*" capture="environment" />
                            <input type="hidden" id="capturedImageData" name="capturedImageData" />
                            <input type="hidden" id="latitude" name="latitude" />
                            <input type="hidden" id="longitude" name="longitude" />
                            <input type="hidden" id="city" name="city" />
                             ');

                            $mform->addElement('hidden', 'field_name[]', $field_name);
                            $mform->setType('field_name[]', PARAM_TEXT);
                            $mform->addElement('hidden', 'field_id[]', $field['field_id']);
                            $mform->setType('field_id[]', PARAM_INT);


                            break;


                        case 9:
                            $mform->addElement('date_selector', $field_name, $field['form_field_name'], null, array('optional' => !$field['is_required']));
                            $mform->setType($field_name, PARAM_RAW);

                            $mform->addElement('hidden', 'field_name[]', $field_name);
                            $mform->setType('field_name[]', PARAM_RAW);

                            $mform->addElement('hidden', 'field_id[]', $field['field_id']);
                            $mform->setType('field_id[]', PARAM_INT);

                            $mform->addElement('html', '<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;">' . $field['form_field_description'] . '</p>');
                            break;


                        case 10:
                            // Hour selection
                            $hourOptions = range(0, 23);
                            $mform->addElement('select', $field_name . '_hour', $field['form_field_name'] . ' - Hour', $hourOptions);
                            $mform->setType($field_name . '_hour', PARAM_INT);

                            // Minute selection
                            $minuteOptions = range(0, 59);
                            $mform->addElement('select', $field_name . '_minute', 'Minute', $minuteOptions);
                            $mform->setType($field_name . '_minute', PARAM_INT);

                            $mform->addElement('hidden', 'field_name[]', $field_name);
                            $mform->setType('field_name[]', PARAM_RAW);

                            $mform->addElement('hidden', 'field_id[]', $field['field_id']);
                            $mform->setType('field_id[]', PARAM_INT);

                            $mform->addElement('html', '<p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;">' . $field['form_field_description'] . '</p>');
                            break;

                    }
                    // End of the switch case
                }
            }

            $mform->addElement('html', '</fieldset>');
        }


        $mform->addElement('html', '</div>');
        // ... Other form elements ...

        // Add action buttons
        $btnstring = get_string('submit', 'local_demo');
        $this->add_action_buttons(true, $btnstring);
        ;
    }

    function validation($data, $files)
    {
        $errors = array();

        // Perform validation if needed

        return $errors;
    }
}







