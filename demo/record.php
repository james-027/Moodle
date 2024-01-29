<?php
/**
 * Version details
 *
 * @package    local_demo
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/demo/second_db.php');


global $DB, $PAGE, $USER;
$PAGE->set_url(new moodle_url('/local/demo/record.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Record Display');

$second_db_config = array(
    'dbtype' => $CFG2->dbtype,
    'dblibrary' => $CFG2->dblibrary,
    'dbhost' => $CFG2->dbhost,
    'dbname' => $CFG2->dbname,
    'dbuser' => $CFG2->dbuser,
    'dbpass' => $CFG2->dbpass,
);



// Fetch users from the second database
$external_db_users = fetch_data_from_bel_db($second_db_config);
// Fetch division from the second databasE
$external_db_section = fetch_sections_bel_db($second_db_config);
$external_db_field = fetch_field_bel_db($second_db_config);


echo $OUTPUT->header();

//no action 
//start form
echo '<form method="POST" action="" class="needs-validation" enctype="multipart/form-data" id="transaction-form" novalidate>';

echo'

        <div class="container">
        <div class="bs-stepper-content">
        <hr>

        <input type="hidden" value="<?= encode($form_id) ?>" name="form_id" required>
        <input type="hidden" value="<?= $expiration ?>" name="expiration" required>';

        foreach ($external_db_section as $section):
            echo '<fieldset>';
            echo '<legend>' . $section['section_name'] . '</legend>';

            foreach ($external_db_field as $field):
                $i=0;
                $i++;
                if ($field['section_id'] == $section['section_id']):
                    $field_name = str_replace(' ', '', strtolower($field['form_field_name'])) . $i;
                
                    // Start of the switch case
                    switch ($field['field_type_id']):
                        case 1: ?>
                            <div class="d-flex justify-content-between my-2">
                                <div class="w-100">
                                    <label class="m-0" for="<?= $field_name ?>"><?= $field['form_field_name'] ?><?php if ($field->is_required == 1) { echo '<i style="color:red">*</i>'; } ?></label>
                                    <div class="mt-2">
                                        <textarea class="w-75" id="<?= $field_name ?>" name="<?= $field_name ?>"><?php if (isset($field->response))?></textarea>
                                    </div>
                                </div>
                                <p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
                                <input type="hidden" name="field_name[]" value="<?= $field_name ?>">
                                <input type="hidden" name="field_id[]" value="<?= $field['field_id'] ?>">
                                <!-- 
                                <?php if (isset($field->response)): ?>
                                    <input type="hidden" name="response_id[]" value="<?= $field->response_id ?>">
                                <?php endif; ?>
                                -->
                            </div>
                            <?php break;
                                case 2: ?>
                                    <div class="d-flex justify-content-between my-2">
                                        <div class="">
                                            <label class="m-0" for="<?= $field_name ?>" ><?= $field['form_field_name'] ?><?php if($field->is_required == 1){ echo '<i style="color:red">*</i>'; } ?></label>
                                            <div class="mt-2">
                                                <input id="<?= $field_name ?>" name="<?= $field_name ?>" value="">
                                            </div>
                                        </div>
                                        <p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field->form_field_description ?></p>
                                        <input type="hidden" name="field_name[]" value="<?= $field_name ?>">
                                        <input type="hidden" name="field_id[]" value="<?= $field['field_id'] ?>">
                                
                                    </div>
                                <?php break; 
                            case 3: ?>
                                <div class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <label class="m-0" for="<?= $field_name ?>" ><?= $field['form_field_name'] ?><?php if($field['is_required'] == 1){ echo '<i style="color:red">*</i>'; } ?></label>
                                        <input id="<?= $field_name ?>" name="<?= $field_name ?>" type="number" value="" min="0" inputmode="numeric">
                                    </div>
                                    <p class="m-0 ml-3" style="word-wrap: break-word; max-width: 50%;"><?= $field['form_field_description'] ?></p>
                                    <input type="hidden" name="field_name[]" value="<?= $field_name ?>">
                                    <input type="hidden" name="field_id[]" value="<?= $field['field_id'] ?>">
                                
                                        <input type="hidden" name="response_id[]" value="">
                                
                                    <input type="hidden" name="is_number[]" value="<?= $field['field_id']?>">
                                </div>
                            <?php break;
                        // Add more cases for other field types if needed
                    endswitch;
                    // End of the switch case

                endif;
            endforeach;

            echo '</fieldset>';
        endforeach;

        echo  ' </div>';






// end form
echo '</form>';

// echo '<div class="table-responsive">
//         <table class="table table-bordered table-striped">
//             <thead>
//                 <tr>
//                     <th>Form Name</th>
//                     <th>Time Added</th>`
//                     <th>Expiration</th>
//                 </tr>
//             </thead>
//             <tbody>';


// Display users from the second database
// foreach ($external_db_users as $user) {
//     echo '<tr>
//             <td>' . $user['section_name'] . '</td>
        
           
//           </tr>';
// }


// Close the table
// echo '</tbody></table></div>';
echo $OUTPUT->footer();