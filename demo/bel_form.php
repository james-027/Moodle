<?php
/**
 * Version details
 *
 * @package    local_demo
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * 
 */global $DB, $PAGE, $USER, $CFG;

// Include necessary Moodle files
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/demo/second_db.php');
require_once($CFG->dirroot . '/local/demo/classes/form/edit.php');

// Switch to the second database configuration
// configure_db_for_second_database();


// Rest of your code remains unchanged
$PAGE->requires->js('/local/demo/js/jquery-3.7.1.min.js', true);
$PAGE->requires->js('/local/demo/js/main.js', true);
$PAGE->requires->css('/local/demo/css/styles.css');
$PAGE->set_url(new moodle_url('/local/demo/edit.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Bel Forms');
$second_db_config = array(
    'dbtype' => $CFG2->dbtype,
    'dblibrary' => $CFG2->dblibrary,
    'dbhost' => $CFG2->dbhost,
    'dbname' => $CFG2->dbname,
    'dbuser' => $CFG2->dbuser,
    'dbpass' => $CFG2->dbpass,
);

// Fetch users from the second database
$external_db_section = fetch_sections_bel_db($second_db_config);
$external_db_field = fetch_field_bel_db($second_db_config);
$external_db_options = fetch_options_bel_db($second_db_config);

// Display form
$mform = new bel_edit($external_db_section,$external_db_field,$external_db_options);

// Form processing and displaying are done here.
if ($mform->is_cancelled()) {
    // go back to manage page
    $cancelMessage = "You cancelled the form";
    redirect($CFG->wwwroot . '/my', \core\notification::error($cancelMessage));
} elseif ($fromform = $mform->get_data()) {

    $second_db_config = array(
        'dbtype' => $CFG2->dbtype,
        'dblibrary' => $CFG2->dblibrary,
        'dbhost' => $CFG2->dbhost,
        'dbname' => $CFG2->dbname,
        'dbuser' => $CFG2->dbuser,
        'dbpass' => $CFG2->dbpass,
    );
    
    $record = new stdClass();
    $record->user_email = $fromform->user_email;
    $record->user_name = $fromform->user_name;
    $record->phone_number = $fromform->phone_number;

    // Handling captured image
    $capturedImageData = $fromform->capturedImageData;

    if (!empty($capturedImageData)) {
        // Save the image data to a file
        $imageFilePath = "upload/captured_image_" . date("Ymd_His") . ".jpg";
        file_put_contents($imageFilePath, file_get_contents($capturedImageData));
        $record->file_path = $imageFilePath;
    } else {
        // Handle the case when capturedImageData is empty
        redirect($CFG->wwwroot . '/local/demo/edit.php', \core\notification::error('Error: Captured image data is empty.'));
    }

    $record->added_by = $USER->id;
    $record->added_time = date("Y-m-d H:i:s", time());

    // Insert the record into the second database using SQL query
    $insertID = insert_record_to_second_db($record, $second_db_config);

    $successMessage = "You successfully inserted the data";
    redirect($CFG->wwwroot . '/my', \core\notification::success($successMessage));
}


echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();