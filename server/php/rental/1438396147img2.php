<?php
ob_start();
mb_internal_encoding('utf-8');
$message = ""; //initialize $message here so can .= at any point later.
// OneFileCMS - github.com/Self-Evident/OneFileCMS
$OFCMS_version = '3.5.17';
//******************************************************************************
//Some basic security & error log settings
//
//ob_start(); //Catch any early output. Closed prior to page output. Moved to top of file.
ini_set('session.use_trans_sid', 0); //make sure URL supplied SESSID's are not used
ini_set('session.use_only_cookies', 1); //make sure URL supplied SESSID's are not used
error_reporting(E_ALL & ~ E_STRICT); //(E_ALL &~ E_STRICT) for everything, 0 for none.
ini_set('display_errors', 'on');
ini_set('log_errors', 'off');
ini_set('error_log', $_SERVER['SCRIPT_FILENAME'] . '.ERROR.log');
//Determine good folder for session file? Default is tmp/, which is not secure, but it may not be a serious concern.
//session_save_path($safepath)  or  ini_set('session.save_path', $safepath)
//*****************************************************************************
// USER CONFIGURABLE INFO ******************************************************
$config_title = "One";
$USERNAME = "admin";
$HASHWORD = "18350bc2181858e679605434735b1c2db6e7e4bb72b50a6d93d9ad1362f3e1c2";
$SALT = 'somerandomsalt';
$MAX_ATTEMPTS = 3; //Max failed login attempts before LOGIN_DELAY starts.
$LOGIN_DELAY = 10; //In seconds.
$MAX_IDLE_TIME = 600; //In seconds. 600 = 10 minutes.  Other PHP settings (like gc) may limit its max effective value.
$TO_WARNING = 120; //In seconds. When idle time remaining is less than this value, a TimeOut warning is displayed.
$LOG_LOGINS = true; //Keep log of login attempts.
$MAIN_WIDTH = '810px'; //Width of main <div> defining page layout.          Can be px, pt, em, or %.  Assumes px otherwise.
$WIDE_VIEW_WIDTH = '97%'; //Width to set Edit page if [Wide View] is clicked.  Can be px, pt, em, or %.  Assumes px otherwise.
$MAX_EDIT_SIZE = 200000; // Edit gets flaky with large files in some browsers.  Trial and error your's.
$MAX_VIEW_SIZE = 1000000; // If file > $MAX_EDIT_SIZE, don't even view in OneFileCMS.
// The default max view size is completely arbitrary. Basically, it was 2am, and seemed like a good idea at the time.
$MAX_IMG_W = 810; //Max width (in px) to display images. (main width is 810)
$MAX_IMG_H = 1000; //Max height (in px).  I don't know, it just looks reasonable.
$UPLOAD_FIELDS = 100000; //Number of upload fields on Upload File(s) page. Max value is ini_get('max_file_uploads').
$config_favicon = "favicon.ico"; //Path is relative to root of website.
$config_excluded = ""; //files to exclude from directory listings- CaSe sEnsiTive!
$config_etypes = "svg,asp,cfg,conf,csv,css,dtd,htm,html,xhtml,htaccess,ini,js,log,markdown,md,php,pl,txt,text"; //Editable file types.
$config_stypes = "*"; // Shown types; only files of the given types should show up in the file-listing
// Use $config_stypes exactly like $config_etypes (list of extensions separated by commas).
// If $config_stypes is set to null - by intention or by error - only folders will be shown.
// If $config_stypes is set to the *-wildcard (the default), all files will show up.
// If $config_stypes is set to "html,htm" for example, only file with the extension "html" or "htm" will get listed.
$config_itypes = "jpg,gif,png,bmp,ico"; //image types to display on edit page.
//File types (extensions).  _ftypes & _fclass must have the same number of values. bin is default.
$config_ftypes = "bin,z,gz,7z,zip,jpg,gif,png,bmp,ico,svg,asp,cfg,conf,csv,css,dtd,htm,html,xhtml,htaccess,ini,js,log,markdown,md,php,pl,txt,text";
//Cooresponding file classes to _ftypes - used to determine icons for directory listing.
$config_fclass = "bin,z,z ,z ,z  ,img,img,img,img,img,svg,txt,txt,cfg ,txt,css,txt,htm,htm ,htm  ,txt     ,txt,txt,txt,txt   ,txt,php,php,txt,txt";
$EX = '<b>( ! )</b> '; //EXclaimation point "icon" Used in $message's
$PAGEUPDOWN = 10; //Number of rows to jump using Page Up/Page Down keys on directory listing.
$SESSION_NAME = 'OFCMS'; //Name of session cookie. Change if using multiple copies of OneFileCMS concurrently.
//Restrict access to a particular folder.  Leave empty for access to entire website.
// "some/path/" is relative to root of website (with no leading slash).
//$ACCESS_ROOT = 'some/path/';
//URL of optional external style sheet.  Used as an href in <link ...>
//If file is not found, or is incomplete, built-in defaults will be used.
//$CSS_FILE = 'OneFileCMS.css';
//Notes for $LANGUAGE_FILE, $WYSIWYG_PLUGIN, and $CONFIG_FILE:
//
// Filename paths can be:
//  1) Absolute to the filesystem:  "/some/path/from/system/root/somefile.php"  or
//  2) Relative to root of website: "some/path/from/web/root/somefile.php"
//Name of optional external language file.  If file is not found, the built-in defaults will be used.
//$LANGUAGE_FILE = "OneFileCMS.LANG.EN.php";
//Init file for optional external wysiwyg editor.
//Sample init files are availble in the "extras\" folder of the OneFileCMS repo, but the actual editors are not.
//$WYSIWYG_PLUGIN = 'plugins/plugin-tinymce_init.php';
//$WYSIWYG_PLUGIN = 'plugins/plugin-ckeditor_init.php';
//Name of optional external config file.  Any settings it contains will supersede those above.
//See the sample file in the OneFileCMS github repo for format example.
//$CONFIG_FILE = 'OneFileCMS.config.SAMPLE.php';
//end CONFIGURABLE INFO ********************************************************
function System_Setup() {
//*****************************************************
  global $config_title, $_, $MAX_IDLE_TIME, $LOGIN_ATTEMPTS, $LOGIN_DELAYED, $MAIN_WIDTH, $WIDE_VIEW_WIDTH, $MAX_EDIT_SIZE, $MAX_VIEW_SIZE, $config_excluded, $config_etypes, $config_stypes, $config_itypes, $config_ftypes, $config_fclass, $SHOWALLFILES, $etypes, $itypes, $ftypes, $fclasses, $excluded_list, $LANGUAGE_FILE, $ACCESS_ROOT, $ACCESS_ROOT_len, $WYSIWYG_PLUGIN, $WYSIWYG_VALID, $WYSIWYG_PLUGIN_OS, $INVALID_CHARS, $WHSPC_SLASH, $VALID_PAGES, $LOGIN_LOG_url, $LOGIN_LOG_file, $ONESCRIPT, $ONESCRIPT_file, $ONESCRIPT_backup, $ONESCRIPT_file_backup, $CONFIG_backup, $CONFIG_FILE, $CONFIG_FILE_backup, $VALID_CONFIG_FILE, $DOC_ROOT, $DOC_ROOT_OS, $WEB_ROOT, $WEBSITE, $PRE_ITERATIONS, $EX, $message, $ENC_OS, $DELAY_Expired_Reload, $DELAY_Sort_and_Show_msgs, $DELAY_Start_Countdown, $DELAY_final_messages, $MIN_DIR_ITEMS;
//Requires PHP 5.1 or newer, due to changes in explode() (and maybe others).
  define('PHP_VERSION_ID_REQUIRED', 50100); //Ex: 5.1.23 is 50123
  define('PHP_VERSION_REQUIRED', '5.1 + '); //Used in exit() message.
//The predefined constant PHP_VERSION_ID has only been available since 5.2.7.
//So, if needed, convert PHP_VERSION (a string) to PHP_VERSION_ID (an integer).
//Ex: 5.1.23 converts to 50123.
  if (!defined('PHP_VERSION_ID')) {
    $phpversion = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($phpversion[0] * 10000 + $phpversion[1] * 100 + $phpversion[2]));
  }
  if (PHP_VERSION_ID < PHP_VERSION_ID_REQUIRED) {
    exit ('PHP ' . PHP_VERSION . '<br>' . hsc($_['OFCMS_requires']) . ' ' . PHP_VERSION_REQUIRED);
  }
  mb_detect_order("UTF-8, ASCII, Windows-1252, ISO-8859-1");
//Get server's File System encoding.  Windows NTFS uses ISO-8859-1 / Windows-1252.
//Needed when working with non-ascii filenames.
  if (php_uname("s") == 'Windows NT') {
    $ENC_OS = 'Windows-1252';
  }
  else {
    $ENC_OS = 'UTF-8';
  }
  $DOC_ROOT = $_SERVER['DOCUMENT_ROOT'] . '/'; //root folder of website.
  $DOC_ROOT_OS = Convert_encoding($DOC_ROOT);
//Allow OneFileCMS.php to be started from any dir on the site.
//This also effects the path in an include("path/somefile.php")
  chdir($DOC_ROOT);
  $INVALID_CHARS = '< > ? * : " | / \\'; //Illegal characters for file & folder names.  Space deliminated.
  $WHSPC_SLASH = "\x00..\x20/"; //Whitespace & forward slash. For trimming file & folder name inputs.
  $WEB_ROOT = basename($DOC_ROOT) . '/'; //Used only for screen output - Non-url use.
  $WEBSITE = $_SERVER['HTTP_HOST'] . '/';
  $ONESCRIPT = URLencode_path($_SERVER['SCRIPT_NAME']); //Used for URL's in HTML attributes
  $ONESCRIPT_file = $_SERVER['SCRIPT_FILENAME']; //Non-url file system use.
  $ONESCRIPT_backup = $ONESCRIPT . '-BACKUP.txt'; //used for p/w & u/n updates.
  $ONESCRIPT_file_backup = $ONESCRIPT_file . '-BACKUP.txt'; //used for p/w & u/n updates.
  $LOGIN_ATTEMPTS = $ONESCRIPT_file . '.invalid_login_attempts';
//Non-url file system use.
  $LOGIN_LOG_url = $ONESCRIPT . '-LOGIN.log';
  $LOGIN_LOG_file = $ONESCRIPT_file . '-LOGIN.log';
//If specified & found, include $CONFIG_FILE.
  $VALID_CONFIG_FILE = 0;
  if (isset ($CONFIG_FILE)) {
    $CONFIG_FILE_OS = Convert_encoding($CONFIG_FILE);
    if (is_file($CONFIG_FILE_OS)) {
      $VALID_CONFIG_FILE = 1;
      include ($CONFIG_FILE_OS);
      $CONFIG_backup = URLencode_path($CONFIG_FILE) . '-BACKUP.txt'; //used for p/w & u/n updates.
      $CONFIG_FILE_backup = $CONFIG_FILE . '-BACKUP.txt'; //used for p/w & u/n updates.
    }
    else {
      $message .= $EX . '<b>$CONFIG_FILE ' . hsc($_['Not_found']) . ':</b> ' . $CONFIG_FILE . '<br>';
      $CONFIG_FILE = $CONFIG_FILE_OS = '';
    }
  }
//If specified, check for & load $LANGUAGE_FILE
  if (isset ($LANGUAGE_FILE)) {
    $LANGUAGE_FILE_OS = Convert_encoding($LANGUAGE_FILE);
    if (is_file($LANGUAGE_FILE_OS)) {
      include ($LANGUAGE_FILE_OS);
    }
  }
//If specified, validate $WYSIWYG_PLUGIN. Actual include() is at end of OneFileCMS.
  $WYSIWYG_VALID = 0; //Default to invalid.
  if (isset ($WYSIWYG_PLUGIN)) {
    $WYSIWYG_PLUGIN_OS = Convert_encoding($WYSIWYG_PLUGIN); //Also used for include()
    if (is_file($WYSIWYG_PLUGIN_OS)) {
      $WYSIWYG_VALID = 1;
    }
  }
//If specified, clean up & validate $ACCESS_ROOT
  if (!isset ($ACCESS_ROOT)) {
    $ACCESS_ROOT = '';
  } //At least make sure it's set.
  $ACCESS_ROOT_OS = Convert_encoding($ACCESS_ROOT);
  if (!is_dir($DOC_ROOT_OS . $ACCESS_ROOT_OS) || (Check_path($ACCESS_ROOT, 1) === false)) {
    $message .= __LINE__ . $EX . '<b>$ACCESS_ROOT ' . hsc($_['Invalid_path']) . ': </b>' . $ACCESS_ROOT . '<br>';
    $ACCESS_ROOT = $ACCESS_ROOT_OS = '';
  }
  if ($ACCESS_ROOT != '') {
    $ACCESS_ROOT = trim($ACCESS_ROOT, ' /') . '/'; //make sure only a single trailing '/'
    $ACCESS_ROOT_OS = Convert_encoding($ACCESS_ROOT);
  }
  $ACCESS_ROOT_enc = mb_detect_encoding($ACCESS_ROOT);
  $ACCESS_ROOT_len = mb_strlen($ACCESS_ROOT, $ACCESS_ROOT_enc);
  $MAIN_WIDTH = validate_units($MAIN_WIDTH);
  $WIDE_VIEW_WIDTH = validate_units($WIDE_VIEW_WIDTH);
  ini_set('session.gc_maxlifetime', $MAX_IDLE_TIME + 100); //in case the default is less.
  $VALID_PAGES = array("login", "logout", "admin", "hash", "changepw", "changeun", "index", "edit", "upload", "uploaded", "newfile", "renamefile", "copyfile", "deletefile", "deletefolder", "newfolder", "renamefolder", "copyfolder", "mcdaction", "phpinfo", "raw_view");
//Make arrays out of a few $config_variables for actual use later.
//First, remove spaces and make lowercase (for *types).
  $SHOWALLFILES = $stypes = false;
  if ($config_stypes == '*') {
    $SHOWALLFILES = true;
  }
  else {
    $stypes = explode(',', mb_strtolower(str_replace(' ', '', $config_stypes)));
  }
//shown file types
  $etypes = explode(',', mb_strtolower(str_replace(' ', '', $config_etypes))); //editable file types
  $itypes = explode(',', mb_strtolower(str_replace(' ', '', $config_itypes))); //images types to display
  $ftypes = explode(',', mb_strtolower(str_replace(' ', '', $config_ftypes))); //file types with icons
  $fclasses = explode(',', mb_strtolower(str_replace(' ', '', $config_fclass))); //for file types with icons
  $excluded_list = explode(',', str_replace(' ', '', $config_excluded));
//A few variables for values that were otherwise hardcoded in js.
//$DELAY_... values are in milliseconds.
//The values were determined thru quick experimentation, and may be tweaked if desired, except as noted.
  $DELAY_Sort_and_Show_msgs = 20; //Needed so "Working..." message shows during directory sorts. Mostly for Firefox.
  $DELAY_Start_Countdown = 25; //Needs to be > than $Sort_and_Show_msgs. Used in Timeout_Timer().
  $DELAY_final_messages = 25; //Needs to be > than $Sort_and_Show_msgs. Delays final Display_Messages().
  $DELAY_Expired_Reload = 10000; //Delay from Session Expired to page load of login screen. Ten seconds, but can be less.
  $MIN_DIR_ITEMS = 25; //Minimum number of directory items before "Working..." message is needed/displayed.
//Used in hashit() and js_hash_scripts().  IE<9 is WAY slow, so keep it low.
//For 200 iterations: (time on IE8) > (37 x time on FF). And the difference grows with the iterations.
//If you change this, or any other aspect of either hashit() or js_hash_scripts(), do so while logged in.
//Then, manually update your password as instructed on the Admin/Generate Hash page.
  $PRE_ITERATIONS = 1000;
}
//end  System_Setup() //*******************************************************
function Default_Language() { // ***********************************************
  global $_;
// OneFileCMS Language Settings v3.5.17
  $_['LANGUAGE'] = 'English';
  $_['LANG'] = 'EN';
// If no translation or value is desired for a particular setting, do not delete
// the actual setting variable, just set it to an empty string.
// For example:  $_['some_unused_setting'] = '';
//
// Remember to slash-escape any single quotes that may be within the text:  \'
// The back-slash itself may or may not also need to be escaped:  \\
//
// If present as a trailing comment, "## NT ##" means 'Need Translation'.
//
// These first few settings control a few font and layout settings.
// In some instances, some langauges may use significantly longer words or phrases than others.
// So, a smaller font or less spacing may be desirable in those places to preserve page layout.
  $_['front_links_font_size'] = '14px'; //Buttons on Index page.
  $_['front_links_margin_L'] = '1.0em';
  $_['MCD_margin_R'] = '1.0em'; //[Move] [Copy] [Delete] buttons
  $_['button_font_size'] = '14px'; //Buttons on Edit page.
  $_['button_margin_L'] = '0.7em';
  $_['button_padding'] = '4px 7px 4px 7px'; //T R B L
  $_['image_info_font_size'] = '1em'; //show_img_msg_01  &  _02
  $_['image_info_pos'] = ''; //If 1 or true, moves the info down a line for more space.
  $_['select_all_label_size'] = '.84em'; //Font size of $_['Select_All']
  $_['select_all_label_width'] = '72px'; //Width of space for $_['Select_All']
  $_['HTML'] = 'HTML';
  $_['WYSIWYG'] = 'WYSIWYG';
  $_['Admin'] = 'Admin';
  $_['bytes'] = 'bytes';
  $_['Cancel'] = 'Cancel';
  $_['cancelled'] = 'cancelled'; //## NT ## as of 3.5.07
  $_['Close'] = 'Close';
  $_['Copy'] = 'Copy';
  $_['Copied'] = 'Copied';
  $_['Create'] = 'Create';
  $_['Date'] = 'Date';
  $_['Delete'] = 'Delete';
  $_['DELETE'] = 'DELETE';
  $_['Deleted'] = 'Deleted';
  $_['Edit'] = 'Edit';
  $_['Enter'] = 'Enter';
  $_['Error'] = 'Error';
  $_['errors'] = 'errors';
  $_['ext'] = '.ext'; //## NT ## filename[.ext]ension
  $_['File'] = 'File';
  $_['files'] = 'files';
  $_['Folder'] = 'Folder';
  $_['folders'] = 'folders';
  $_['From'] = 'From';
  $_['Hash'] = 'Hash';
  $_['Move'] = 'Move';
  $_['Moved'] = 'Moved';
  $_['Name'] = 'Name';
  $_['on'] = 'on';
  $_['Password'] = 'Password';
  $_['Rename'] = 'Rename';
  $_['reset'] = 'Reset';
  $_['save_1'] = 'Save';
  $_['save_2'] = 'SAVE CHANGES';
  $_['Size'] = 'Size';
  $_['Source'] = 'Source';
  $_['successful'] = 'successful';
  $_['To'] = 'To';
  $_['Upload'] = 'Upload';
  $_['Username'] = 'Username';
  $_['View'] = 'View';
  $_['Working'] = 'Working - please wait...';
  $_['Log_In'] = 'Log In';
  $_['Log_Out'] = 'Log Out';
  $_['Admin_Options'] = 'Administration Options';
  $_['Are_you_sure'] = 'Are you sure?';
  $_['View_Raw'] = 'View Raw'; //## NT ### as of 3.5.07
  $_['Open_View'] = 'Open/View in browser window';
  $_['Edit_View'] = 'Edit / View';
  $_['Wide_View'] = 'Wide View';
  $_['Normal_View'] = 'Normal View';
  $_['Upload_File'] = 'Upload File';
  $_['New_File'] = 'New File';
  $_['Ren_Move'] = 'Rename / Move';
  $_['Ren_Moved'] = 'Renamed / Moved';
  $_['folders_first'] = 'folders first'; //## NT ##
  $_['folders_first_info'] = 'Sort folders first, but don\'t change primary sort.'; //## NT ##
  $_['New_Folder'] = 'New Folder';
  $_['Ren_Folder'] = 'Rename / Move Folder';
  $_['Submit'] = 'Submit Request';
  $_['Move_Files'] = 'Move File(s)';
  $_['Copy_Files'] = 'Copy File(s)';
  $_['Del_Files'] = 'Delete File(s)';
  $_['Selected_Files'] = 'Selected Folders and Files';
  $_['Select_All'] = 'Select All';
  $_['Clear_All'] = 'Clear All';
  $_['New_Location'] = 'New Location';
  $_['No_files'] = 'No files selected.';
  $_['Not_found'] = 'Not found';
  $_['Invalid_path'] = 'Invalid path';
  $_['verify_msg_01'] = 'Session expired.';
  $_['verify_msg_02'] = 'INVALID POST';
  $_['get_get_msg_01'] = 'File does not exist:';
  $_['get_get_msg_02'] = 'Invalid page request:';
  $_['check_path_msg_02'] = '"dot" or "dot dot" path segments are not permitted.';
  $_['check_path_msg_03'] = 'Path or filename contains an invalid character:';
  $_['ord_msg_01'] = 'A file with that name already exists in the target directory.';
  $_['ord_msg_02'] = 'Saving as';
  $_['rCopy_msg_01'] = 'A folder can not be copied into one of its own sub-folders.';
  $_['show_img_msg_01'] = 'Image shown at ~';
  $_['show_img_msg_02'] = '% of full size (W x H =';
  $_['hash_txt_01'] = 'The hashes generated by this page may be used to manually update $HASHWORD in OneFileCMS, or in an external config file.  In either case, make sure you remember the password used to generate the hash!';
  $_['hash_txt_06'] = 'Type your desired password in the input field above and hit Enter.';
  $_['hash_txt_07'] = 'The hash will be displayed in a yellow message box above that.';
  $_['hash_txt_08'] = 'Copy and paste the new hash to the $HASHWORD variable in the config section.';
  $_['hash_txt_09'] = 'Make sure to copy ALL of, and ONLY, the hash (no leading or trailing spaces etc).';
  $_['hash_txt_10'] = 'A double-click should select it...';
  $_['hash_txt_12'] = 'When ready, logout and login.';
  $_['pass_to_hash'] = 'Password to hash:';
  $_['Generate_Hash'] = 'Generate Hash';
  $_['login_txt_01'] = 'Username:';
  $_['login_txt_02'] = 'Password:';
  $_['login_msg_01a'] = 'There have been';
  $_['login_msg_01b'] = 'invalid login attempts.';
  $_['login_msg_02a'] = 'Please wait';
  $_['login_msg_02b'] = 'seconds to try again.';
  $_['login_msg_03'] = 'INVALID LOGIN ATTEMPT #';
  $_['edit_note_00'] = 'NOTES:';
  $_['edit_note_01a'] = 'Remember- ';
  $_['edit_note_01b'] = 'is';
  $_['edit_note_02'] = 'So save changes before the clock runs out, or the changes will be lost!';
  $_['edit_note_03'] = 'With some browsers, such as Chrome, if you click the browser [Back] then browser [Forward], the file state may not be accurate. To correct, click the browser\'s [Reload].';
  $_['edit_h2_1'] = 'Viewing:';
  $_['edit_h2_2'] = 'Editing:';
  $_['edit_txt_00'] = 'Edit disabled.'; //## NT ## as of 3.5.07
  $_['edit_txt_01'] = 'Non-text or unkown file type. Edit disabled.';
  $_['edit_txt_02'] = 'File possibly contains an invalid character. Edit and view disabled.';
  $_['edit_txt_03'] = 'htmlspecialchars() returned an empty string from what may be an otherwise valid file.';
  $_['edit_txt_04'] = 'This behavior can be inconsistant from version to version of php.';
  $_['too_large_to_edit_01'] = 'Edit disabled. Filesize >';
  $_['too_large_to_edit_02'] = 'Some browsers (ie: IE) bog down or become unstable while editing a large file in an HTML <textarea>.';
  $_['too_large_to_edit_03'] = 'Adjust $MAX_EDIT_SIZE in the configuration section of OneFileCMS as needed.';
  $_['too_large_to_edit_04'] = 'A simple trial and error test can determine a practical limit for a given browser/computer.';
  $_['too_large_to_view_01'] = 'View disabled. Filesize >';
  $_['too_large_to_view_02'] = 'Click [View Raw] to view the raw/"plain text" file contents in a seperate browser window.'; //** NT ** changed wording as of 3.5.07
  $_['too_large_to_view_03'] = 'Adjust $MAX_VIEW_SIZE in the configuration section of OneFileCMS as needed.';
  $_['too_large_to_view_04'] = '(The default value for $MAX_VIEW_SIZE is completely arbitrary, and may be adjusted as desired.)';
  $_['meta_txt_01'] = 'Filesize:';
  $_['meta_txt_03'] = 'Updated:';
  $_['edit_msg_01'] = 'File saved:';
  $_['edit_msg_02'] = 'bytes written.';
  $_['edit_msg_03'] = 'There was an error saving file.';
  $_['upload_txt_03'] = 'Maximum size of each file:';
  $_['upload_txt_01'] = '(php.ini: upload_max_filesize)';
  $_['upload_txt_04'] = 'Maximum total upload size:';
  $_['upload_txt_02'] = '(php.ini: post_max_size)';
  $_['upload_txt_05'] = 'For uploaded files that already exist: ';
  $_['upload_txt_06'] = 'Rename (to filename.ext.001 etc...)';
  $_['upload_txt_07'] = 'Overwrite';
  $_['upload_err_01'] = 'Error 1: File too large. From php.ini:';
  $_['upload_err_02'] = 'Error 2: File too large. (Exceeds MAX_FILE_SIZE HTML form element)';
  $_['upload_err_03'] = 'Error 3: The uploaded file was only partially uploaded.';
  $_['upload_err_04'] = 'Error 4: No file was uploaded.';
  $_['upload_err_05'] = 'Error 5:';
  $_['upload_err_06'] = 'Error 6: Missing a temporary folder.';
  $_['upload_err_07'] = 'Error 7: Failed to write file to disk.';
  $_['upload_err_08'] = 'Error 8: A PHP extension stopped the file upload.';
  $_['upload_error_01a'] = 'Upload Error. Total POST data (mostly filesize) exceeded post_max_size =';
  $_['upload_error_01b'] = '(from php.ini)';
  $_['upload_msg_02'] = 'Destination folder invalid:';
  $_['upload_msg_03'] = 'Upload cancelled.';
  $_['upload_msg_04'] = 'Uploading:';
  $_['upload_msg_05'] = 'Upload successful!';
  $_['upload_msg_06'] = 'Upload failed:';
  $_['upload_msg_07'] = 'A pre-existing file was overwritten.';
  $_['new_file_txt_01'] = 'File or Folder will be created in the current folder.';
  $_['new_file_txt_02'] = 'Some invalid characters are:';
  $_['new_file_msg_01'] = 'File or folder not created:';
  $_['new_file_msg_02'] = 'Name contains an invalid character:';
  $_['new_file_msg_04'] = 'File or folder already exists:';
  $_['new_file_msg_05'] = 'Created file:';
  $_['new_file_msg_07'] = 'Created folder:';
  $_['CRM_txt_02'] = 'The new location must already exist.';
  $_['CRM_txt_04'] = 'New Name';
  $_['CRM_msg_01'] = 'Error - new parent location does not exist:';
  $_['CRM_msg_02'] = 'Error - source file does not exist:';
  $_['CRM_msg_03'] = 'Error - new file or folder already exists:';
  $_['CRM_msg_05'] = 'Error during';
  $_['delete_msg_03'] = 'Delete error:';
  $_['session_warning'] = 'Warning: Session timeout soon!';
  $_['session_expired'] = 'SESSION EXPIRED';
  $_['unload_unsaved'] = ' Unsaved changes will be lost!';
  $_['confirm_reset'] = 'Reset file and loose unsaved changes?';
  $_['OFCMS_requires'] = 'OneFileCMS requires PHP';
  $_['logout_msg'] = 'You have successfully logged out.';
  $_['edit_caution_01'] = 'CAUTION'; //##### No longer used as of 3.5.07
  $_['edit_caution_02'] = 'You are viewing the active copy of OneFileCM.'; //## NT ## changed wording 3.5.07
  $_['time_out_txt'] = 'Session time out in:';
  $_['error_reporting_01'] = 'Display errors is';
  $_['error_reporting_02'] = 'Log errors is';
  $_['error_reporting_03'] = 'Error reporting is set to';
  $_['error_reporting_04'] = 'Showing error types';
  $_['error_reporting_05'] = 'Unexpected early output';
  $_['error_reporting_06'] = '(nothing, not even white-space, should have been output yet)';
  $_['admin_txt_00'] = 'Old Backup Found';
  $_['admin_txt_01'] = 'A backup file was created in case of an error during a username or password change. Therefore, it may contain old information and should be deleted if not needed. In any case, it will be automatically overwritten on the next password or username change.';
  $_['admin_txt_02'] = 'General Information';
  $_['admin_txt_14'] = 'For a small improvement to security, change the default salt and/or method used by OneFileCMS to hash the password (and keep them secret, of course). Every little bit helps...';
  $_['admin_txt_16'] = 'OneFileCMS can not be used to edit itself directly.  However, you can make a copy & edit it.  Then simply run the copy.'; //## NT ## Changed wording in 3.5.07
  $_['pw_current'] = 'Current Password';
  $_['pw_change'] = 'Change Password';
  $_['pw_new'] = 'New Password';
  $_['pw_confirm'] = 'Confirm New Password';
  $_['un_change'] = 'Change Username';
  $_['un_new'] = 'New Username';
  $_['un_confirm'] = 'Confirm New Username';
  $_['pw_txt_02'] = 'Password / Username rules:';
  $_['pw_txt_04'] = 'Case-sensitive: "A" =/= "a"';
  $_['pw_txt_06'] = 'Must contain at least one non-space character.';
  $_['pw_txt_08'] = 'May contain spaces in the middle. Ex: "This is a password or username!"';
  $_['pw_txt_10'] = 'Leading and trailing spaces are ignored.';
  $_['pw_txt_12'] = 'In recording the change, only one file is updated: either the active copy of OneFileCMS, or - if specified, an external configuration file.';
  $_['pw_txt_14'] = 'If an incorrect current password is entered, you will be logged out, but you may log back in.';
  $_['change_pw_01'] = 'Password changed!';
  $_['change_pw_02'] = 'Password NOT changed.';
  $_['change_pw_03'] = 'Incorrect current password. Login to try again.';
  $_['change_pw_04'] = '"New" and "Confirm New" values do not match.';
  $_['change_pw_05'] = 'Updating';
  $_['change_pw_06'] = 'external config file';
  $_['change_pw_07'] = 'All fields are required.';
  $_['change_un_01'] = 'Username changed!';
  $_['change_un_02'] = 'Username NOT changed.';
  $_['update_failed'] = 'Update failed - could not save file.';
  $_['mcd_msg_01'] = 'file(s) and/or folder(s) moved.'; //#####
  $_['mcd_msg_02'] = 'file(s) and/or folder(s) copied.'; //#####
  $_['mcd_msg_03'] = 'file(s) and/or folder(s) deleted.'; //#####
}
//end Default_Language() //****************************************************
function validate_units($cssvalue) {
//******************************************
//Determine if valid units are set for $cssvalue:  px, pt, em, or %.
  $main_units = mb_substr($cssvalue, - 2);
  if (($main_units != "px") && ($main_units != "pt") && ($main_units != "em") && (mb_substr($cssvalue, - 1) != '%')) {
    $cssvalue = ($cssvalue * 1) . 'px'; //If not, assume px.
  }
  return $cssvalue;
}
//end valid_units() //*********************************************************
function hsc($input) {
//********************************************************
  $enc = mb_detect_encoding($input); //It should always be UTF-8 (or ASCII), but, just in case...
  if ($enc == 'ASCII') {
    $enc = 'UTF-8';
  } //htmlspecialchars() doesn't recognize "ASCII"
  return htmlspecialchars($input, ENT_QUOTES, $enc);
}
//end hsc() //*****************************************************************
function Convert_encoding($string, $to_enc = "") {
//****************************
  global $ENC_OS;
//mb_convert_encoding($string, $to_enc, $from_enc)
  if ($to_enc == 'UTF-8') {
    return mb_convert_encoding($string, 'UTF-8', $ENC_OS);
  } // Convert to UTF-8
  else
/* default */
    {
    return mb_convert_encoding($string, $ENC_OS, 'UTF-8');
  } // Convert to server's/OS's filesystem enc
}
//end Convert_encoding() //****************************************************
function Session_Startup() {
//**************************************************
  global $SESSION_NAME, $page, $VALID_POST;
  $limit = 0; //0 = session.
  $path = '';
  $domain = ''; // '' = hostname
  $https = false;
  $httponly = true; //true = unaccessable via javascript. Some XSS protection.
  session_set_cookie_params($limit, $path, $domain, $https, $httponly);
  session_name($SESSION_NAME);
  session_start();
//Set initial defaults...
  $page = 'login';
  $VALID_POST = 0;
  if (!isset ($_SESSION['valid'])) {
    $_SESSION['valid'] = 0;
  }
//Logging in?
  if (isset ($_POST['username']) && isset ($_POST['password'])) {
    Login_response();
  }
  session_regenerate_id(true); //Helps prevent session fixation & hijacking.
  if ($_SESSION['valid']) {
    Verify_IDLE_POST_etc();
  }
  $_SESSION['nuonce'] = sha1(mt_rand() . microtime()); //provided in <forms> to verify POST
}
//end Session_Startup() //*****************************************************
function Verify_IDLE_POST_etc() {
//*********************************************
  global $_, $page, $EX, $message, $VALID_POST, $MAX_IDLE_TIME;
//Verify consistant user agent. This is set during login. (every little bit helps every little bit)
  if (!isset ($_SESSION['user_agent']) || ($_SESSION['user_agent'] != $_SERVER['HTTP_USER_AGENT'])) {
    Logout();
  }
//Check idle time
  if (isset ($_SESSION['last_active_time'])) {
    $idle_time = (time() - $_SESSION['last_active_time']);
    if ($idle_time > $MAX_IDLE_TIME) {
      Logout();
      $message .= hsc($_['verify_msg_01']) . '<br>';
      return;
    }
  }
  $_SESSION['last_active_time'] = time();
//If POSTing, verify...
  if (isset ($_POST['nuonce'])) {
    if ($_POST['nuonce'] == $_SESSION['nuonce']) {
      $VALID_POST = 1;
    }
    else { //If it exists but doesn't match - something's wrong. Probably a page reload.
      $page = "index";
      $_POST = "";
      $message .= $EX . '<b>' . hsc($_['verify_msg_02']) . '</b><br>';
    }
  }
}
//end Verify_IDLE_POST_etc() //************************************************
function hashit($key, $pre = false) {
//******************************************
//This is the super-secret stuff - Keep it secret, keep it safe!
//If you change anything here, or the $SALT, manually update the hash for your password from the Generate Hash page.
  global $SALT, $PRE_ITERATIONS;
  $hash = trim($key); // trim off leading & trailing whitespace.
//If generating a hash from the Hash_Page(), also do the "pre-hash".  Generally,
//the "pre-hash" is done client-side during a login attempt, or when changing p/w or u/n.
  if ($pre) {
    for ($x = 0; $x < $PRE_ITERATIONS; $x++) {
      $hash = hash('sha256', $hash . $SALT);
    }
  }
  for ($x = 0; $x < 10001; $x++) {
    $hash = hash('sha256', $hash . $SALT);
  }
  return $hash;
}
//end hashit() //**************************************************************
function Error_reporting_status_and_early_output($show_status = 0, $show_types = 0) {
//
//Display the status of error_reporting(), and ini_get() of display_errors & log_errors.
//Also displays any early output caught by ob_start().
  global $_, $early_output;
  $E_level = error_reporting();
  $E_types = '';
  $spc = ' &nbsp; '; // or '<br>' or PHP_EOL or whatever...
  if ($E_level & 1) {
    $E_types .= 'E_ERROR' . $spc;
  }
  if ($E_level & 2) {
    $E_types .= 'E_WARNING' . $spc;
  }
  if ($E_level & 4) {
    $E_types .= 'E_PARSE' . $spc;
  }
  if ($E_level & 8) {
    $E_types .= 'E_NOTICE' . $spc;
  }
  if ($E_level & 16) {
    $E_types .= 'E_CORE_ERROR' . $spc;
  }
  if ($E_level & 32) {
    $E_types .= 'E_CORE_WARNING' . $spc;
  }
  if ($E_level & 64) {
    $E_types .= 'E_COMPILE_ERROR' . $spc;
  }
  if ($E_level & 128) {
    $E_types .= 'E_COMPILE_WARNING' . $spc;
  }
  if ($E_level & 256) {
    $E_types .= 'E_USER_ERROR' . $spc;
  }
  if ($E_level & 512) {
    $E_types .= 'E_USER_WARNING' . $spc;
  }
  if ($E_level & 1024) {
    $E_types .= 'E_USER_NOTICE' . $spc;
  }
  if ($E_level & 2048) {
    $E_types .= 'E_STRICT' . $spc;
  }
  if ($E_level & 4096) {
    $E_types .= 'E_RECOVERABLE_ERROR' . $spc;
  }
  if ($E_level & 8192) {
    $E_types .= 'E_DEPRECATED' . $spc;
  }
  if ($E_level & 16384) {
    $E_types .= 'E_USER_DEPRECATED' . $spc;
  }
  if ($show_status && ((error_reporting() != 0) || (ini_get('display_errors') == 'on') || (ini_get('log_errors') == 'on'))) {
    ?>
		<style>
		.E_box {margin: 0;	 background-color: #F00; font-size: 1em; color: white;
				padding: 2px 5px 2px 5px; border: 1px solid white; }
		</style>
    <?php
    echo '<p class="E_box"><b>PHP ' . PHP_VERSION . $spc;
    echo hsc($_['error_reporting_01']) . ': ' . ini_get('display_errors') . '.' . $spc;
    echo hsc($_['error_reporting_02']) . ': ' . ini_get('log_errors') . '.' . $spc;
    echo hsc($_['error_reporting_03']) . ': ' . error_reporting() . '.' . $spc;
    echo 'E_ALL = ' . E_ALL . $spc . '</b>';
    if ($show_types) {
      echo '<br><b>' . hsc($_['error_reporting_04']) . ': </b>';
      echo '<span style="font: 400 .8em arial">' . $E_types . '</span>';
    }
    echo '</p>';
  }
//end if (error reporting on)
//$early_output is contents of ob_get_clean(), just before page output.
  if (mb_strlen($early_output) > 0) {
    echo '<pre style="background-color: #F00; border: 0px solid #F00;"><b>';
    echo hsc($_['error_reporting_05']) . '</b> ';
    echo hsc($_['error_reporting_06']) . '<b>:</b> ';
    echo '<span style="background-color: white; border: 1px solid white">';
    echo hsc($early_output) . '</span></pre>';
  }
}
//end Error_reporting_status_and_early_output() //*****************************
function Update_Recent_Pages() {
//**********************************************
  global $page;
  if (!isset ($_SESSION['recent_pages'])) {
    $_SESSION['recent_pages'] = array($page);
  }
  $pages = count($_SESSION['recent_pages']);
//Only update if actually a new page
  if ($page != $_SESSION['recent_pages'][0]) {
    array_unshift($_SESSION['recent_pages'], $page);
    $pages = count($_SESSION['recent_pages']);
  }
//Only need 3 most recent pages (increase if needed)
  if ($pages > 3) {
    array_pop($_SESSION['recent_pages']);
  }
}
//end Update_Recent_Pages() //*************************************************
function undo_magic_quotes() {
//************************************************
  function strip_array($var) {
//stripslashes() also handles cases when magic_quotes_sybase is on.
    if (is_array($var)) {
      return array_map("strip_array", $var);
    }
    else {
      return stripslashes($var);
    }
  }
//end strip_array()
  if (get_magic_quotes_gpc()) {
    if (isset ($_GET)) {
      $_GET = strip_array($_GET);
    }
    if (isset ($_POST)) {
      $_POST = strip_array($_POST);
    }
    if (isset ($_COOKIE)) {
      $_COOKIE = strip_array($_COOKIE);
    }
  }
}
//end undo_magic_quotes() //***************************************************
function Validate_params() {
//**************************************************
  global $_, $ipath, $filename, $page, $param1, $param2, $param3, $IS_OFCMS, $EX, $message;
//Pages that require a valid $filename
  $file_pages = array("edit", "renamefile", "copyfile", "deletefile");
//Make sure $filename & $page go together
  if (($filename != "") && !in_array($page, $file_pages)) {
    $filename = "";
  }
  if (($filename == "") && in_array($page, $file_pages)) {
    $page = "index";
  }
//Init $param's used in <a> href's & <form> actions
  $param1 = '?i=' . URLencode_path($ipath); //$param1 must not be blank.
  if ($filename == "") {
    $param2 = "";
  }
  else {
    $param2 = '&amp;f=' . rawurlencode(basename($filename));
  }
  if ($page == "") {
    $param3 = "";
  }
  else {
    $param3 = '&amp;p=' . $page;
  }
//Used to restrict edit/del/etc. on active copy of OneFileCMS.
  $IS_OFCMS = 0;
  if ($filename == trim($_SERVER['SCRIPT_NAME'], '/')) {
    $IS_OFCMS = true;
  }
}
//end Validate_params() //*****************************************************
function Valid_Path($path, $gotoroot = true) {
//**********************************
//$gotoroot: if true, return to index page of $ACCESS_ROOT.
  global $ipath, $ipath_OS, $filename, $param1, $param2, $param3, $ACCESS_ROOT, $ACCESS_ROOT_len, $message;
//Limit access to the folder $ACCESS_ROOT:
//$ACCESS_ROOT = some/root/path/
//$path        = some/root/path/...(or deeper)   : good
//$path        = some/root/                      : bad
//$path        = some/other/path/                : bad
  $path_len = mb_strlen($path);
  $path_root = mb_substr($path, 0, $ACCESS_ROOT_len);
  $good_path = false;
  if (isset ($_SESSION['admin_page']) && $_SESSION['admin_page']) {
//Permit Admin actions: changing p/w, u/n, viewing OneFile...
    $ACCESS_ROOT == '';
    return true;
  }
  elseif ($path_len < $ACCESS_ROOT_len) {
    $good_path = false;
  }
  else {
    $good_path = ($path_root == $ACCESS_ROOT);
  }
  if (!$good_path && $gotoroot) {
    $ipath = $ACCESS_ROOT;
    $ipath_OS = Convert_encoding($ipath);
    $filename = '';
//$page     = 'index';  //#### If set to index here, can't logout.
    $param1 = '?i=' . $ipath;
    $param2 = '';
    $param3 = '';
    $_GET = '';
    $_POST = '';
  }
  return $good_path;
}
//end Valid_Path() //**********************************************************
function Get_GET() {
//**** Get URL passed parameters ***************************
  global $_, $ipath, $ipath_OS, $filename, $filename_OS, $page, $VALID_PAGES, $EX, $message;
// i=some/path/,  f=somefile.xyz,          p=somepage,  m=somemessage
// $ipath = i  ,  $filename = $ipath.f  ,  $page = p ,  $message
//   (NOTE: in some functions $filename = just the file's name, ie: $_GET['f'], with no path/)
//#####  (Normalize $filename program-wide??)
// Perform initial, basic, validation.
// Get_GET() should not be called unless $_SESSION['valid'] == 1 (or true)
//Initialize & validate $ipath
  $ipath = $ipath_OS = "";
  if (isset ($_GET["i"])) {
    $ipath = Check_path($_GET["i"], 1);
    $ipath_OS = Convert_encoding($ipath);
    if ($ipath === false || !is_dir($ipath_OS)) {
      $ipath = $ipath_OS = '';
    }
  }
//Initialize & validate $filename
  if (isset ($_GET["f"])) {
    $filename = $ipath . $_GET["f"];
  }
  else {
    $filename = "";
  }
  $filename_OS = Convert_encoding($filename);
  if (($filename != "") && !is_file($filename_OS)) {
    $message .= $EX . '<b>' . hsc($_['get_get_msg_01']) . '</b> ';
    $message .= hsc(dir_name($filename)) . '<b>' . hsc(basename($filename)) . '</b><br>';
    $filename = $filename_OS = "";
  }
//Initialize & validate $page
  if (isset ($_GET["p"])) {
    $page = $_GET["p"];
  }
  else {
    $page = "index";
  }
  if (!in_array($page, $VALID_PAGES)) {
    $message .= $EX . hsc($_['get_get_msg_02']) . ' <b>' . hsc($page) . '</b><br>';
    $page = "index"; //If invalid $_GET["p"]
  }
//Sanitize any message. Initialized on line 1 / top of this file.
  if (isset ($_GET["m"])) {
    $message .= hsc($_GET["m"]);
  }
}
//end Get_GET() //*************************************************************
function Verify_Page_Conditions() {
//*******************************************
  global $_, $ONESCRIPT_file, $ipath, $ipath_OS, $param1, $filename, $filename_OS, $page, $EX, $message, $VALID_POST, $IS_OFCMS;
//If exited admin pages, restore $ipath
  if (($page == "index") && $_SESSION['admin_page']) {
//...unless clicked www/some/path/ from edit or copy page while in admin pages.
    if (($_SESSION['recent_pages'][0] != 'edit') && ($_SESSION['recent_pages'][0] != 'copyfile')) {
      $ipath = $_SESSION['admin_ipath'];
      $param1 = '?i=' . URLencode_path($ipath);
    }
    $_SESSION['admin_page'] = false;
    $_SESSION['admin_ipath'] = '';
  }
//Don't load login screen when already in a valid session.
//$_SESSION['valid'] may be false after Respond_to_POST()
  elseif (($page == "login") && $_SESSION['valid']) {
    $page = "index";
  }
  elseif ($page == "logout") {
    Logout();
    $message .= hsc($_['logout_msg']);
  }
//Don't load rename or delete folder pages at webroot.
  elseif (($page == "deletefolder" || $page == "renamefolder") && ($ipath == "")) {
    $page = "index";
  }
//Prep MCD_Page() to delete a single folder selected via (x) icon on index page.
  elseif ($page == "deletefolder") {
    $_POST['files'][1] = basename($ipath); //Must precede next line (change of $ipath).
    $ipath = dir_name($ipath);
    $ipath_OS = Convert_encoding($ipath);
    $param1 = '?i=' . $ipath;
  }
//There must be at least one 'file', and 'mcdaction' must = "move", "copy", or "delete"
  elseif ($page == "mcdaction") {
    if (!isset ($_POST['mcdaction'])) {
      $page = "index";
    }
    elseif (!isset ($_POST['files'])) {
      $page = "index";
    }
    elseif (($_POST['mcdaction'] != "move") && ($_POST['mcdaction'] != "copy") && ($_POST['mcdaction'] != "delete")) {
      $page = "index";
    }
  }
//if size of $_POST > post_max_size, PHP only returns empty $_POST & $_FILE arrays.
  elseif (($page == "uploaded") && !$VALID_POST) {
    $message .= $EX . '<b> ' . hsc($_['upload_error_01a']) . ' ' . ini_get('post_max_size') . '</b> ' . hsc($_['upload_error_01b']) . '<br>';
    $page = "index";
  }
//[View Raw] file contents in a browser window (in plain text, NOT HTML).
  if ($page == "raw_view") {
    $raw_contents = file_get_contents($filename_OS);
    $file_ENC = mb_detect_encoding($raw_contents); //ASCII, UTF-8, etc...
    header('Content-type: text/plain; charset=utf-8');
    echo mb_convert_encoding($raw_contents, 'UTF-8', $file_ENC);
    die;
  }
}
//end Verify_Page_Conditions() //**********************************************
function has_invalid_char($string) {
//******************************************
  global $INVALID_CHARS;
  $INVALID_CHARS_array = explode(' ', $INVALID_CHARS);
  foreach ($INVALID_CHARS_array as $bad_char) {
    if (mb_strpos($string, $bad_char) !== false) {
      return true;
    }
  }
  return false;
}
//end has_invalid_char() //****************************************************
function URLencode_path($path) { // don't encode the forward slashes ************
  $path = str_replace('\\', '/', $path); //Make sure all forward slashes.
  $TS = ''; // Trailing Slash/
  if (mb_substr($path, - 1) == '/') {
    $TS = '/';
  } //start with a $TS?
  $path_array = explode('/', $path);
  $path = "";
  foreach ($path_array as $level) {
    $path .= rawurlencode($level) . '/';
  }
  $path = rtrim($path, '/') . $TS; //end with $TS only if started with one
  return $path;
}
//end URLencode_path() //******************************************************
function dir_name($path) {
//****************************************************
//Modified dirname().
  $parent = dirname($path);
  if ($parent == "." || $parent == "/" || $parent == '\\' || $parent == "") {
    return "";
  }
  else {
    return $parent . '/';
  }
}
//end dir_name() //************************************************************
function Check_path($path, $show_msg = false) {
//*******************************
// check for invalid characters & "dot" or "dot dot" path segments.
// Does NOT check if exists - only if of valid construction.
  global $_, $message, $EX, $INVALID_CHARS, $WHSPC_SLASH;
  $path = str_replace('\\', '/', $path); //Make sure all forward slashes.
  $path = trim($path, $WHSPC_SLASH); // trim whitespace & slashes
  if (($path == "") || ($path == ".")) {
    return "";
  } // At root.
  $err_msg = "";
  $errors = 0;
  $pathparts = explode('/', $path);
  foreach ($pathparts as $part) {
//Check for any '.' and '..' parts of the path to protect directories outside webroot.
//They also cause issues in <h2>www / current / path /</h2>
    if (($part == '.') || ($part == '..')) {
      $err_msg .= $EX . ' <b>' . hsc($_['check_path_msg_02']) . '</b><br>';
      $errors++;
      break;
    }
//Check for invalid characters
    $invalid_chars = str_replace(' /', '', $INVALID_CHARS); //The forward slash is not present, or invalid, at this point.
    if (has_invalid_char($part)) {
      $err_msg .= $EX . ' <b>' . hsc($_['check_path_msg_03']) . ' &nbsp; <span class="mono"> ' . $invalid_chars . '</span></b><br>';
      $errors++;
      break;
    }
  }
  if ($errors > 0) {
    if ($show_msg) {
      $message .= $err_msg;
    }
    return false;
  }
  return $path . '/';
}
//end Check_path() //**********************************************************
function Sort_Seperate($path, $full_list) {
//***********************************
//Sort list, then seperate folders & files
  natcasesort($full_list);
  $files = array();
  $folders = array();
  $F = 1;
  $D = 1; //indexes
  foreach ($full_list as $item) {
    if (($item == '.') || ($item == '..') || ($item == "")) {
      continue;
    }
    $fullpath_OS = Convert_encoding($path . $item);
    if (is_dir($fullpath_OS)) {
      $folders[$D++] = $item;
    }
    else {
      $files[$F++] = $item;
    }
  }
  return array_merge($folders, $files);
}
//end Sort_Seperate() //*******************************************************
function add_serial_num($filename, & $msg) {
//***********************************
//if file_exists(file.txt), add serial# to filename until it doesn't
//ie: file.txt.001,  file.txt.002, file.txt.003  etc...
  global $_, $EX;
  $ordinal = 0;
//Convert $filename to server's File Syetem encoding
  $savefile = $filename;
  $savefile_OS = Convert_encoding($savefile);
  if (file_exists($savefile_OS)) {
    $msg .= $EX . hsc($_['ord_msg_01']) . '<br>';
    while (file_exists($savefile_OS)) {
      $ordinal = sprintf("%03d",++$ordinal); //  001, 002, 003, etc...
      $savefile = $filename . '.' . $ordinal;
      $savefile_OS = Convert_encoding($savefile);
    }
    $msg .= '<b>' . hsc($_['ord_msg_02']) . ':</b> <span class="filename">' . hsc(basename($savefile)) . '</span>';
  }
  return $savefile;
}
//end add_serial_num() //******************************************************
function supports_svg() {
//*****************************************************
//IE < 9 is the only browser checked for currently.
//EX: Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)
  $USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
  $pos_MSIE = mb_strpos($USER_AGENT, 'MSIE ');
  $old_ie = false;
  if ($pos_MSIE !== false) {
    $ie_ver = mb_substr($USER_AGENT, ($pos_MSIE + 5), 1);
    $old_ie = ($ie_ver < 9);
  }
  return !$old_ie;
}
//end supports_svg() //********************************************************
function rCopy($old_path, $new_path) {
//**************************************
  global $_, $WHSPC_SLASH, $EX, $message;
//Recursively copy $old_path to $new_path
//Both $old_ & $new_path must ALREADY be in OS/file system's encoding.
//(ie: usually UTF-8, but often ISO-8859-1 for Windows.)
//Return number of successful copy's + mkdir's, or 0 on error.
//$old_path & $new_path must already be in OS/filesystem's file name encoding
//Avoid a bottomless pit of sub-directories:
//    ok: copy root/1/ to root/1/Copy_of_1/
//NOT OK: copy root/1/ to root/1/2/Copy_of_1/
//
  $error_code = 0;
//First, trim / and white-space that will mess up strlen() check.
  $old_path = trim($old_path, $WHSPC_SLASH);
  $new_path = trim($new_path, $WHSPC_SLASH);
//
  $test_path = dirname($new_path);
  while (mb_strlen($test_path) >= mb_strlen($old_path)) {
    $test_path = dirname($test_path);
    if ($test_path == $old_path) {
      $message .= $EX . ' <b>' . hsc($_['rCopy_msg_01']) . '</b><br>';
      return 0;
    }
  }
  if (is_file($old_path)) {
    return (copy($old_path, $new_path) * 1);
  }
  if (is_dir($old_path)) {
    $dir_list = scandir($old_path); //MUST come before mkdir().
    $error_code = (mkdir($new_path, 0755) * 1);
    if (sizeof($dir_list) > 0) {
      foreach ($dir_list as $file) {
        if ($file == "." || $file == "..") {
          continue;
        }
        $error_code += rCopy($old_path . '/' . $file, $new_path . '/' . $file);
      }
    }
    return $error_code;
  }
  return 0; //$old_path doesn't exist, or, I don't know what it is.
}
//end rCopy() //***************************************************************
function rDel($path) {
//********************************************************
//Recursively delete $path & all sub-folders & files.
//Returns number of successful unlinks & rmdirs.
  $path = trim($path, '/'); //Protect against deleting files outside of webroot.
  if ($path == "") {
    $path = '.';
  }
  $path_OS = Convert_encoding($path);
  $count = 0;
  if (is_file($path_OS)) {
    return (unlink($path_OS) * 1);
  }
  if (is_dir($path_OS)) {
    $dir_list = scandir($path_OS);
    foreach ($dir_list as $dir_item) {
      $dir_item_OS = Convert_encoding($dir_item);
      if (($dir_item == '.') || ($dir_item == '..')) {
        continue;
      }
      $count += rDel($path . '/' . $dir_item);
    }
    $count += rmdir($path_OS);
    return $count;
  }
  return false; //$path doesn't exists, or, I don't know what it is...
}
//end rDel() //****************************************************************
function Current_Path_Header() {
//**********************************************
// Current path. ie: webroot/current/path/
// Each level is a link to that level.
  global $ONESCRIPT, $ipath, $WEB_ROOT, $ACCESS_ROOT, $ACCESS_ROOT_len, $TABINDEX, $message;
  $unaccessable = '';
  $_1st_accessable = trim($WEB_ROOT, ' /');
  $remaining_path = trim(mb_substr($ipath, $ACCESS_ROOT_len), ' /');
  if ($ACCESS_ROOT != '') {
    $unaccessable = dirname($ACCESS_ROOT);
    $_1st_accessable = basename($ACCESS_ROOT);
    if ($unaccessable == '.') {
      $unaccessable = $WEB_ROOT;
    }
    else {
      $unaccessable = $WEB_ROOT . dirname($ACCESS_ROOT) . '/';
    }
    $unaccessable = '&nbsp;' . hsc(trim(str_replace('/', ' / ', $unaccessable)));
  }
  echo '<h2 id="path_header">';
//Root (or $ACCESS_ROOT) folder of web site.
  $p1 = '?i=' . URLencode_path($ACCESS_ROOT);
  echo $unaccessable . '<a id=path_0 tabindex=' . $TABINDEX++. ' href="' . $ONESCRIPT . $p1 . '" class="path">' . hsc($_1st_accessable) . '</a>/';
  $x = 0; //need here for focus() in case at webroot.
  if ($remaining_path != "") { //if not at root, show the rest
    $path_levels = explode("/", trim($remaining_path, '/'));
    $levels = count($path_levels); //If levels=3, indexes = 0, 1, 2  etc...
    $current_path = "";
    for ($x = 0; $x < $levels; $x++) {
      $current_path .= $path_levels[$x] . '/';
      $p1 = '?i=' . URLencode_path($ACCESS_ROOT . $current_path);
      echo '<a id="path_' . ($x + 1) . '" tabindex=' . $TABINDEX++. ' href="' . $ONESCRIPT . $p1 . '" class="path">';
      echo hsc($path_levels[$x]) . '</a>/';
    }
  }
//end if(not at root)
  echo '</h2>';
}
//end Current_Path_Header() //*************************************************
function Page_Header() {
//******************************************************
  global $_, $DOC_ROOT, $ONESCRIPT, $page, $WEBSITE, $config_title, $OFCMS_version, $config_favicon, $TABINDEX, $message;
  $TABINDEX = 1; //Initial tabindex
  $favicon = '';
  if (file_exists($DOC_ROOT . trim($config_favicon, '/'))) {
    $favicon = '<img src="/' . URLencode_path($config_favicon) . '" alt="">';
  }
  echo '<div id="header">';
  echo '<a href="' . $ONESCRIPT . '" id="logo" tabindex=' . $TABINDEX++. '>' . $config_title . '</a> ' . $OFCMS_version . ' ';
  $on_php = '(' . hsc($_['on']) . '&nbsp;php&nbsp;' . phpversion() . ')';
  if ($_SESSION["valid"]) {
    $on_php = '<a id=on_php tabindex=' . $TABINDEX++. ' href="' . $ONESCRIPT . '?p=phpinfo' . '" target=_blank>' . $on_php . '</a>';
  }
  echo $on_php;
  echo '<div class="nav">';
  echo '<b><a id=website href="/" tabindex=' . $TABINDEX++. ' target="_blank">' . $favicon . ' ' . hsc($WEBSITE) . '</a></b>';
  if ($page != "login") {
    echo ' | <a id=logout tabindex=' . $TABINDEX++. ' href="' . $ONESCRIPT . '?p=logout">' . hsc($_['Log_Out']) . '</a>';
  }
  echo '</div><div class=clear></div>';
  echo '</div>';
//<!-- end header -->
}
//end Page_Header() //*********************************************************
function Cancel_Submit_Buttons($submit_label) {
//*******************************
//$submit_label = Rename, Copy, Delete, etc...
  global $_, $ONESCRIPT, $ipath, $param1, $param2, $page;
  $params = $param1 . $param2 . '&amp;p=' . $_SESSION['recent_pages'][1]; //.'&amp;m='.hsc($_['cancelled']) not sure I like this.
  ?>
	<p>
	<button type="button" class="button" id="cancel" onclick="parent.location = '<?php echo $ONESCRIPT . $params ?>'">
		<?php echo hsc($_['Cancel']) ?></button>
	<button type="submit" class="button" id="submitty" style="margin-left: 1em;"><?php echo hsc($submit_label);?></button>
	<script>document.getElementById("cancel").focus();</script>
  <?php
}
//end Cancel_Submit_Buttons() //***********************************************
function show_image() {
//*******************************************************
  global $_, $filename, $MAX_IMG_W, $MAX_IMG_H;
  $IMG = $filename;
  $img_info = getimagesize($IMG);
  $W = 0;
  $H = 1; //indexes for $img_info[]
  $SCALE = 1;
  $SCALE_W = 1;
  $SCALE_H = 1;
  if ($img_info[$W] > $MAX_IMG_W) {
    $SCALE_W = ($MAX_IMG_W / $img_info[$W]);
  }
  if ($img_info[$H] > $MAX_IMG_H) {
    $SCALE_H = ($MAX_IMG_H / $img_info[$H]);
  }
//Set $SCALE to the more restrictive scale.
  if ($SCALE_W > $SCALE_H) {
    $SCALE = $SCALE_H;
  } //ex: if (.90 > .50)
  else {
    $SCALE = $SCALE_W;
  } //If _H >= _W, or both are 1
//For languages with longer words that don't fit next to [Wide] & [Close] buttons.
  if ($_['image_info_pos']) {
    echo '<div class=clear></div>' . "\n";
  }
  echo '<p class="image_info">';
  echo hsc($_['show_img_msg_01']) . round($SCALE * 100) . hsc($_['show_img_msg_02']) . ' ' . $img_info[0] . ' x ' . $img_info[1] . ').</p>';
  echo '<div class=clear></div>' . "\n";
  echo '<a  href="/' . URLencode_path($IMG) . '" target="_blank">' . "\n";
  echo '<img src="/' . URLencode_path($IMG) . '" width="' . ($img_info[$W] * $SCALE) . '"></a>' . "\n";
}
//end show_image() //**********************************************************
function Timeout_Timer($COUNT, $ID, $ACTION = "") {
//*****************************
  global $DELAY_Start_Countdown;
  return '<script>setTimeout(\'Start_Countdown(' . $COUNT . ',"' . $ID . '","' . $ACTION . '")\',' . $DELAY_Start_Countdown . ');</script>';
}
//end Timeout_Timer() //*******************************************************
function Init_Macros() {
//**** ($varibale="some reusable chunk of code")********
  global $_, $ONESCRIPT, $param1, $param2, $INPUT_NUONCE, $FORM_COMMON, $PWUN_RULES;
  $INPUT_NUONCE = '<input type="hidden" name="nuonce" value="' . $_SESSION['nuonce'] . '">' . "\n";
  $FORM_COMMON = '<form method="post" action="' . $ONESCRIPT . $param1 . $param2 . '">' . $INPUT_NUONCE . "\n";
  $PWUN_RULES = '<p>' . hsc($_['pw_txt_02']) . '<ol><li>' . hsc($_['pw_txt_04']) . '<li>' . hsc($_['pw_txt_06']);
  $PWUN_RULES .= '<li>' . hsc($_['pw_txt_10']) . '<li>' . hsc($_['pw_txt_08']) . '</ol>';
}
//end Init_Macros() //*********************************************************
function Init_ICONS() {
//********************************************************
  global $ICONS;
//*********************************************************************
  function icon_txt($border = '#333', $lines = '#000', $fill = '#FFF', $extra1 = "", $extra2 = "") {
    return '<svg version="1.1" width="14" height="16">' . '<rect x = "0" y = "0" width = "14" height = "16" fill="' . $fill . '" stroke="' . $border . '" stroke-width="2" />' . $extra2 . '<line x1="3" y1="3.5"  x2="11" y2="3.5"  stroke="' . $lines . '" stroke-width=".6"/>' . '<line x1="3" y1="6.5"  x2="11" y2="6.5"  stroke="' . $lines . '" stroke-width=".6"/>' . '<line x1="3" y1="9.5"  x2="11" y2="9.5"  stroke="' . $lines . '" stroke-width=".6"/>' . '<line x1="3" y1="12.5" x2="11" y2="12.5" stroke="' . $lines . '" stroke-width=".6"/>' . $extra1 . '</svg>';
  }
//end icon_txt() //***************************************************
  function icon_folder($extra = "") {
//**********************************
    return '<svg version="1.1" width="18" height="16"><g transform="translate(0,2)">' . '<path  d="M0.5, 1  L8,1  L9,2  L9,3  L16.5,3  L17,3.5  L17,13.5  L.5,13.5  L.5,.5" ' . 'fill="#F0CD28" stroke="rgb(200,170,15)" stroke-width="1" />' . '<path  d="M1.5, 8  L7, 8  L8.5,6.3  L16,6.3  L7.5, 6.3   L6.5,7.5  L1.5,7.5" ' . 'fill="transparent" stroke="white" stroke-width="1" />' . '<path  d="M1.5,13  L1.5,2  L7.5,2  L8.5,3  L8.5,4  L15.5,4 L16,4.5  L16,13" ' . 'fill="transparent" stroke="white" stroke-width="1" />' . $extra . '</g></svg>';
  }
//end icon_folder() //************************************************
//Some common components
  $circle_x = '<circle cx="5" cy="5" r="5" stroke="#D00" stroke-width="1.3" fill="#D00"/>' . '<line x1="2.5" y1="2.5" x2="7.5" y2="7.5" stroke="white" stroke-width="1.5"/>' . '<line x1="7.5" y1="2.5" x2="2.5" y2="7.5" stroke="white" stroke-width="1.5"/>';
  $circle_plus = '<circle cx="5" cy="5" r="5" stroke="#080" stroke-width="0" fill="#080"/>' . '<line x1="2" y1="5" x2="8" y2="5" stroke="white" stroke-width="1.5" />' . '<line x1="5" y1="2" x2="5" y2="8" stroke="white" stroke-width="1.5" />';
  $circle_plus_rev = '<circle cx="5" cy="5" r="5" stroke="#080" stroke-width="1.3" fill="white"/>' . '<line x1="2" y1="5" x2="8" y2="5" stroke="#080" stroke-width="1.5" />' . '<line x1="5" y1="2" x2="5" y2="8" stroke="#080" stroke-width="1.5" />';
  $pencil = '<polygon points="2,0 9,7 7,9 0,2" stroke-width="1" stroke="darkgoldenrod" fill="rgb(246,222,100)"/>' . '<path  d="M0,2    L0,0  L2,0"   stroke="tan"    stroke-width="1" fill="tan"/>' . '<path  d="M0,1.5  L0,0  L1.5,0" stroke="black"  stroke-width="1.5" fill="transparent"/>' . '<line x1="7.3" y1="10"  x2="10" y2="7.3" stroke="silver" stroke-width="1"/>' . '<line x1="8.1" y1="10.8"  x2="10.8" y2="8.1"  stroke="red" stroke-width="1"/>';
  $img_0 = '<rect x="0"    y="0"   width="14" height="16" fill="#FF8" stroke="#44F" stroke-width="2"/>' . '<rect x="2"    y="2"   width="5"  height="5"  fill="#F66" stroke-width="0" />' . '<rect x="7.5"  y="6"   width="5"  height="5"  fill="#6F6" stroke-width="0" />' . '<rect x="2"    y="10"  width="5"  height="5"  fill="#66F" stroke-width="0" />';
  $arc_arrow = '<path d="M 3.5,12 a 30,30 0 0,1  9,-9  l -1.5,-2.4  l 6,1.3  l -1.6,6 l -1.5,-2.4' . ' a 30,30 0 0,0 -9,6.5 Z"  fill="white" stroke="blue" stroke-width="1.1" />';
  $up_arrow = '<polygon points="6,0  12,6  8,6  8,11  4,11  4,6  0,6" stroke-width="1" stroke="white" fill="green" />';
  $zero = '<rect x="0"  y="0"  width="3" height="6" fill="transparent" stroke="#555" stroke-width="1" />';
  $one = '<line x1="0" y1="-.5"   x2="0" y2="6.5"  stroke="#555" stroke-width="1"/>';
  $extra_up = '<g transform="scale(1.1) translate(1.75,4)">' . $up_arrow . '</g>';
  $extra_new = '<g transform="translate(4,6)">' . $circle_plus . '</g>';
  $extra_z = '<text x="4" y="12" style="font-size:8pt;font-weight:900;fill:blue ;font-family:Arial;">z</text>';
//The icons
  $ICONS['bin'] = '<svg version="1.1" width="14" height="16">' . '<g transform="translate( 0.5,0.5)">' . $one . '</g>' . '<g transform="translate( 3.5,0.5)">' . $zero . '</g>' . '<g transform="translate( 9.5,0.5)">' . $one . '</g>' . '<g transform="translate(12.5,0.5)">' . $one . '</g>' . '<g transform="translate( 0.5,9.5)">' . $zero . '</g>' . '<g transform="translate( 6.5,9.5)">' . $one . '</g>' . '<g transform="translate( 9.5,9.5)">' . $zero . '</g>' . '</svg>';
  $ICONS['z'] = icon_txt('#333', '#FFF', '#FFF', $extra_z);
  $ICONS['img'] = '<svg version="1.1" width="14" height="16">' . $img_0 . '</svg>';
  $ICONS['svg'] = icon_txt('#333', '#444', '#FFF', "", $img_0);
  $ICONS['txt'] = icon_txt('#333', '#000', '#FFF');
  $ICONS['htm'] = icon_txt('#444', '#222', '#FABEAA'); //rgb(250,190,170)
  $ICONS['php'] = icon_txt('#333', '#111', '#C3C3FF'); //rgb(195,195,225)
  $ICONS['css'] = icon_txt('#333', '#111', '#FFE1A5'); //rgb(255,225,165)
  $ICONS['cfg'] = icon_txt('#444', '#111', '#DDD');
  $ICONS['dir'] = icon_folder();
  $ICONS['folder'] = icon_folder();
  $ICONS['folder_new'] = icon_folder('<g transform="translate(7.5,4)">' . $circle_plus . '</g>');
  $ICONS['upload'] = icon_txt('#333', 'black', 'white', $extra_up);
  $ICONS['file_new'] = icon_txt('#444', 'black', 'white', $extra_new);
  $ICONS['ren_mov'] = icon_folder('<g transform="translate(2.5,3)">' . $pencil . '</g>' . $arc_arrow);
  $ICONS['move'] = icon_folder($arc_arrow);
  $ICONS['copy'] = '<svg version="1.1" width="12" height="12"><g transform="translate(1,1)">' . $circle_plus_rev . '</g></svg>';
  $ICONS['delete'] = '<svg version="1.1" width="12" height="12"><g transform="translate(1,1)">' . $circle_x . '</g></svg>';
  $ICONS['up_dir'] = icon_folder('<g transform="scale(1.1) translate(1.75,2) rotate(-45, 5, 5)">' . $up_arrow . '</g>');
  if (!supports_svg()) { //Text "icons" if SVG not supported.  Mostly for IE < 9
    foreach ($ICONS as $key => $value) {
      $ICONS[$key] = "";
    }
    $ICONS['up_dir'] = '[&lt;]';
    $ICONS['dir'] = '[+]';
    $ICONS['folder'] = '[+]';
    $ICONS['ren_mov'] = '<span class="RCD1 R">&gt;</span>';
    $ICONS['move'] = '<span class="RCD1 R">&gt;</span>';
    $ICONS['copy'] = '<span class="RCD1 C">+</span>';
    $ICONS['delete'] = '<span class="RCD1 D">x</span>';
  }
}
//end Init_ICONS() {//*********************************************************
function List_File($file, $file_url) {
//****************************************
  global $_, $ONESCRIPT, $ICONS;
  $file_OS = Convert_encoding($file);
  clearstatcache();
  $href = $ONESCRIPT . '?i=' . dir_name(trim($file_url, '/')) . '&amp;f=' . basename($file_url);
  $edit_link = '<a href="' . $href . '&amp;p=edit' . '" id="old_backup">' . hsc(basename($file)) . '</a>';
  ?>
	<tr>
	<td><a href="<?php echo $href . '&amp;p=deletefile' ?>" class="button" id="del_backup">
	<?php echo $ICONS['delete'] . '&nbsp;' . hsc($_['Delete']) ?></a></td>
	<td class="file_name"><?php echo $edit_link;?></td>
	<td class="meta_T file_size">&nbsp;	<?php echo number_format(filesize($file_OS));?> B	</td>
	<td class="meta_T file_time"> &nbsp;<script>FileTimeStamp(<?php echo filemtime($file_OS);?>, 1, 0, 1);</script></td>
	</tr>
  <?php
}
//end List_File() //***********************************************************
function List_Backups_and_Logs() {
//********************************************
  global $_, $ONESCRIPT_backup, $ONESCRIPT_file, $ONESCRIPT_file_backup, $CONFIG_backup, $CONFIG_FILE_backup, $LOGIN_LOG_url, $LOGIN_LOG_file;
//Indicate if a login log or backups (from a prior p/w or u/n change) exist.
  $CONFIG_FILE_backup_OS = Convert_encoding($CONFIG_FILE_backup);
  $ONESCRIPT_file_backup_OS = Convert_encoding($ONESCRIPT_file_backup);
  $LOGIN_LOG_file_OS = Convert_encoding($LOGIN_LOG_file);
  clearstatcache();
  $backup_found = $log_found = false;
  if (is_file($ONESCRIPT_file_backup_OS) || is_file($CONFIG_FILE_backup_OS)) {
    $backup_found = true;
  }
  if (is_file($LOGIN_LOG_file_OS)) {
    $log_found = true;
  }
  if ($backup_found || $log_found) {
    echo '<table class="index_T">';
    if ($log_found) {
      List_File($LOGIN_LOG_file, $LOGIN_LOG_url);
    }
    if (is_file($ONESCRIPT_file_backup_OS)) {
      List_File($ONESCRIPT_file_backup, $ONESCRIPT_backup);
    }
    if (is_file($CONFIG_FILE_backup_OS)) {
      List_File($CONFIG_FILE_backup, $CONFIG_backup);
    }
    echo '</table>';
    if ($backup_found) {
      echo '<p style="margin-top: .5em"><b>' . hsc($_['admin_txt_00']) . '</b></p>';
      echo '<p>' . hsc($_['admin_txt_01']);
    }
    echo '<hr>';
  }
//end of check for backup
}
//end List_Backups_and_Logs() //***********************************************
function Admin_Page() {
//*******************************************************
  global $_, $ONESCRIPT, $ipath, $filename, $param1, $param2, $config_title;
// Restore/Preserve $ipath prior to admin page in case OneFileCMS is edited (which would change $ipath).
  if ($_SESSION['admin_page']) {
    $ipath = $_SESSION['admin_ipath'];
    $param1 = '?i=' . URLencode_path($ipath);
  }
  else {
    $_SESSION['admin_page'] = true;
    $_SESSION['admin_ipath'] = $ipath;
  }
// [Close] returns to either the index or edit page.
  $params = "";
  if ($filename != "") {
    $params = $param2 . '&amp;p=edit';
  }
  $button_attribs = '<button type="button" class="button" onclick="parent.location =\'' . $ONESCRIPT;
  $edit_params = '?i=' . dir_name($ONESCRIPT) . '&amp;f=' . basename($ONESCRIPT) . '&amp;p=edit';
  echo '<h2>' . hsc($_['Admin_Options']) . '</h2>';
  echo '<span class="admin_buttons">';
  echo $button_attribs . $param1 . $params . '\'" id="close">' . hsc($_['Close']) . '</button>';
  echo $button_attribs . $param1 . '&amp;p=changepw\'">' . hsc($_['pw_change']) . '</button>';
  echo $button_attribs . $param1 . '&amp;p=changeun\'">' . hsc($_['un_change']) . '</button>';
  echo $button_attribs . $param1 . '&amp;p=hash\'">' . hsc($_['Generate_Hash']) . '</button>';
  echo $button_attribs . $edit_params . '\'">' . hsc($_['View'] . ' ' . $config_title) . '</button>';
  echo '</span>';
  echo '<div class="info">';
  List_Backups_and_Logs();
  echo '<p><b>' . hsc($_['admin_txt_02']) . '</b>';
  echo '<p>' . hsc($_['admin_txt_16']);
  echo '<p>' . hsc($_['admin_txt_14']);
  echo '</div>'; //end class=info
  echo '<script>document.getElementById("close").focus();</script>';
}
//end Admin_Page() //**********************************************************
function Hash_Page() {
//********************************************************
  global $_, $ONESCRIPT, $param1, $param3, $INPUT_NUONCE, $PWUN_RULES;
  if (!isset ($_POST['whattohash'])) {
    $_POST['whattohash'] = '';
  }
  ?>
	<style>#message_box {font-family: courier; min-height: 3.1em;}</style>

	<h2><?php echo hsc($_['Generate_Hash']) ?></h2>

	<form id="hash" name="hash" method="post" action="<?php echo $ONESCRIPT . $param1 . $param3;?>">
		<?php echo $INPUT_NUONCE;?>
		<?php echo hsc($_['pass_to_hash']) ?>
		<input type="text" name="whattohash" id="whattohash" value="<?php echo hsc($_POST['whattohash']) ?>">
		<p><?php Cancel_Submit_Buttons($_['Generate_Hash']) ?>
		<script>document.getElementById('whattohash').focus()</script>
	</form>

	<div class="info">
		<p><?php echo hsc($_['hash_txt_01']) ?><br>
		<ol><li><?php echo hsc($_['hash_txt_06']) ?><br>
				<?php echo hsc($_['hash_txt_07']) ?>
			<li><?php echo hsc($_['hash_txt_08']) ?><br>
				<?php echo hsc($_['hash_txt_09']) ?><br>
				<?php echo hsc($_['hash_txt_10']) ?><br>
			<li><?php echo hsc($_['hash_txt_12']) ?>
		</ol>
		<?php echo $PWUN_RULES ?>
	</div>
  <?php
}
//end Hash_Page() //***********************************************************
function Hash_response() {
//****************************************************
  global $_, $message;
  $_POST['whattohash'] = trim($_POST['whattohash']); // trim whitespace.
//Ignore/don't hash an empty string - passwords can't be blank.
  if ($_POST['whattohash'] == "") {
    return;
  }
//The second parameter to hashit(), 1, tells hashit() to also do the "pre-hash", which is
//normally done client-side during a login attempt, p/w change, or u/n change.
  $message .= hsc($_['Password']) . ': ' . hsc($_POST['whattohash']) . '<br>';
  $message .= hsc($_['Hash']) . ': ' . hashit($_POST['whattohash'], 1) . '<br>';
}
//end Hash_response() //*******************************************************
//******************************************************************************
function Change_PWUN_Page($pwun, $type, $page_title, $label_new, $label_confirm) {
//$pwun must = "pw" or "un"
  global $_, $EX, $ONESCRIPT, $param1, $param2, $param3, $INPUT_NUONCE, $config_title, $PWUN_RULES;
  $params = $param1 . $param2 . '&amp;p=' . $_SESSION['recent_pages'][1];
  ?>
	<?php ?>
	<style>#message_box {min-height: 2em;}</style>

	<h2><?php echo hsc($page_title) ?></h2>

	<form id="change" method="post" action="<?php echo $ONESCRIPT . $param1 . $param3;?>">
		<input type="hidden" name="<?php echo $pwun ?>" value="">

		<?php echo $INPUT_NUONCE;?>

		<p><?php echo hsc($_['pw_current']) ?><br>
		<input type="password" name="password" id="password" value="">

		<p><?php echo hsc($label_new) ?><br>
		<input type="<?php echo $type ?>" name="new1" id="new1" value="">

		<p><?php echo hsc($label_confirm) ?><br>
		<input type="<?php echo $type ?>" name="new2" id="new2" value="">

		<p><input type="button" class="button" id="cancel" value="<?php echo hsc($_['Cancel']) ?>"
			onclick="parent.location = '<?php echo $ONESCRIPT . $params ?>'">
		<input type="button" class="button"    id="submitty" value="<?php echo hsc($_['Submit']) ?>" style="margin-left: 1em;">

		<script>document.getElementById('password').focus()</script>
	</form>

	<div class="info">
	<?php echo $PWUN_RULES ?>
	<p><?php echo hsc($_['pw_txt_12']) ?>
	<p><?php echo hsc($_['pw_txt_14']) ?>
	</div>
  <?php
//Note: The button with id="submitty" above must NOT be of type="submit",
//NOR have an id="submit", or the event_scripts won't work.
  pwun_event_scripts('change', 'submitty', $pwun); //Doesn't work if an id="submit"
  js_hash_scripts();
}
//end Change_PWUN_Page() //****************************************************
//******************************************************************************
function Update_config($search_for, $replace_with, $search_file, $backup_file) {
  global $_, $EX, $message;
  $search_file_OS = Convert_encoding($search_file);
  $backup_file_OS = Convert_encoding($backup_file);
  if (!is_file($search_file_OS)) {
    $message .= $EX . ' <b>' . hsc($_['Not_found']) . ': </b>' . hsc($search_file) . '<br>';
    return false;
  }
//Read file into an array for searching.
  $search_lines = file($search_file_OS, FILE_IGNORE_NEW_LINES);
//Search start of each $line in (array)$search_lines for (string)$search_for.
//If match found, replace $line with $replace_with, end search.
  $search_len = mb_strlen($search_for);
  $found = false;
  foreach ($search_lines as $key => $line) {
    if (mb_substr($line, 0, $search_len) == $search_for) {
      $found = true;
      $search_lines[$key] = $replace_with;
      break 1; //only replace first occurrance of $search_for
    }
  }
//This should not happen, but just in case...
  if (!$found) {
    $message .= $EX . ' <b>' . hsc($_['Not_found']) . ': </b>' . hsc($search_for) . '<br>';
    return false;
  }
  copy($search_file_OS, $backup_file_OS); // Just in case...
  $updated_contents = implode("\n", $search_lines);
  if (file_put_contents($search_file_OS, $updated_contents, LOCK_EX) === false) {
    $message .= $EX . '<b>' . hsc($_['update_failed']) . '</b><br>';
    return false;
  }
  else {
    return true;
  }
}
//end Update_config() //*******************************************************
function Change_PWUN_response($PWUN, $msg) {
//**********************************
//Update $USERNAME or $HASHWORD. Default $page = changepw or changeun
  global $_, $ONESCRIPT, $USERNAME, $HASHWORD, $EX, $message, $page, $ONESCRIPT_file, $ONESCRIPT_file_backup, $CONFIG_FILE, $CONFIG_FILE_backup, $VALID_CONFIG_FILE;
// trim white-space from input values
  $current_pass = trim($_POST['password']);
  $new_pwun = trim($_POST['new1']);
  $confirm_pwun = trim($_POST['new2']);
  $error_msg = $EX . '<b>' . hsc($msg) . '</b> ';
//If all fields are blank, do nothing.
  if (($current_pass == "") && ($new_pwun == "") && ($confirm_pwun == "")) {
    return;
  }
//If any field is blank...
  elseif (($current_pass == "") || ($new_pwun == "") || ($confirm_pwun == "")) {
    $message .= $error_msg . hsc($_['change_pw_07']) . '<br>';
  }
//If new & Confirm values don't match...
  elseif ($new_pwun != $confirm_pwun) {
    $message .= $error_msg . hsc($_['change_pw_04']) . '<br>';
  }
//If incorrect current p/w, logout.  (new == confirm at this point)
  elseif (hashit($current_pass) != $HASHWORD) {
    $message .= $error_msg . '<br>' . hsc($_['change_pw_03']) . '<br>';
    Logout();
  }
//Else change username or password
  else {
    if ($PWUN == "pw") {
      $search_for = '$HASHWORD '; //include space after $HASHWORD
      $replace_with = '$HASHWORD = "' . hashit($new_pwun) . '";';
      $success_msg = '<b>' . hsc($_['change_pw_01']) . '</b>';
    }
    else { //$PWUN = "un"
      $search_for = '$USERNAME '; //include space after $USERNAME
      $replace_with = '$USERNAME = "' . $new_pwun . '";';
      $success_msg = '<b>' . hsc($_['change_un_01']) . '</b>';
    }
//If specified & it exists, update external config file.
    if ($VALID_CONFIG_FILE) {
      $message .= hsc($_['change_pw_05']) . ' ' . hsc($_['change_pw_06']) . '. . . ';
      $updated = Update_config($search_for, $replace_with, $CONFIG_FILE, $CONFIG_FILE_backup);
    }
    else { //Update OneFileCMS
      $message .= hsc($_['change_pw_05']) . ' OneFileCMS . . . ';
      $updated = Update_config($search_for, $replace_with, $ONESCRIPT_file, $ONESCRIPT_file_backup);
    }
    if ($updated === false) {
      $message .= $error_msg . '<br>';
    }
    else {
      $message .= $success_msg . '<br>';
    }
    $page = "admin"; //Return to Admin page.
  }
}
//end Change_PWUN_response() //************************************************
function Logout() {
//***********************************************************
  global $page;
  session_regenerate_id(true);
  session_unset();
  session_destroy();
  session_write_close();
  unset ($_GET);
  unset ($_POST);
  $_SESSION = array();
  $_SESSION['valid'] = 0;
  $page = 'login';
}
//end Logout() //**************************************************************
function Login_Page() {
//*******************************************************
  global $_, $ONESCRIPT;
  ?>
	<?php ?>
	<style>#message_box {height: 3.1em;}</style>
     <br><br>
	<h2><?php echo hsc($_['Log_In']) ?></h2>
	<form method="post" id="login_form" name="login_form" action="<?php echo $ONESCRIPT;?>">
		<label for ="username"><?php echo hsc($_['login_txt_01']) ?></label>
		<input name="username" class="form-control" type="text" id="username">
		<label for ="password"><?php echo hsc($_['login_txt_02']) ?></label>
		<input name="password" class="form-control" type="password" id="password">
		<input type="button"  class="btn btn-primary"   id="login" value="<?php echo hsc($_['Enter']) ?>">
	</form>
	<script>document.getElementById('username').focus();</script>
  <?php
//Note: The "login" button above must NOT be of type="submit", NOR have an id="submit", or the event_scripts won't work.
  pwun_event_scripts('login_form', 'login');
  js_hash_scripts();
}
//end Login_Page() //**********************************************************
function Login_response() {
//***************************************************
  global $_, $EX, $ONESCRIPT_file, $message, $page, $USERNAME, $HASHWORD, $LOGIN_ATTEMPTS, $MAX_ATTEMPTS, $LOGIN_DELAY, $LOGIN_DELAYED, $LOG_LOGINS, $LOGIN_LOG_file;
  $_SESSION = array(); //make sure it's empty
  $_SESSION['valid'] = 0; //Default to failed login.
  $attempts = 0;
  $elapsed = 0;
  $LOGIN_ATTEMPTS = Convert_encoding($LOGIN_ATTEMPTS); //$LOGIN_ATTEMPTS only used for filesystem access.
  $LOGIN_DELAYED = 0; //used to start Countdown at end of file
//Check for prior login attempts (but don't increment count just yet)
  if (is_file($LOGIN_ATTEMPTS)) {
    $attempts = (int) file_get_contents($LOGIN_ATTEMPTS);
    $elapsed = time() - filemtime($LOGIN_ATTEMPTS);
  }
  if ($attempts > 0) {
    $message .= '<b>' . hsc($_['login_msg_01a']) . ' ' . $attempts . ' ' . hsc($_['login_msg_01b']) . '</b><br>';
  }
  if (($attempts >= $MAX_ATTEMPTS) && ($elapsed < $LOGIN_DELAY)) {
    $LOGIN_DELAYED = ($LOGIN_DELAY - $elapsed);
    $message .= hsc($_['login_msg_02a']) . ' <span id=timer0></span> ' . hsc($_['login_msg_02b']);
    return;
  }
//Trim any incidental whitespace before validating.
  $_POST['password'] = trim($_POST['password']);
  $_POST['username'] = trim($_POST['username']);
//validate login.
  if (($_POST['password'] == "") || ($_POST['username'] == "")) {
    return; //Ignore login attempt if either username or password is blank.
  }
  elseif ((hashit($_POST['password']) == $HASHWORD) && ($_POST['username'] == $USERNAME)) {
    session_regenerate_id(true);
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT']; //for user consistancy check.
    $_SESSION['valid'] = 1;
    $page = "index";
    if (is_file($LOGIN_ATTEMPTS)) {
      unlink($LOGIN_ATTEMPTS);
    } //delete count/file of $LOGIN_ATTEMPTS
  }
  else {
    file_put_contents($LOGIN_ATTEMPTS,++$attempts); //increment attempts
    $message = $EX . '<b>' . hsc($_['login_msg_03']) . $attempts . '</b><br>';
    if ($attempts >= $MAX_ATTEMPTS) {
      $LOGIN_DELAYED = $LOGIN_DELAY;
      $message .= hsc($_['login_msg_02a']) . ' <span id=timer0></span> ' . hsc($_['login_msg_02b']);
    }
  }
//Log login attempts
  if ($LOG_LOGINS) {
    $log_file = Convert_encoding($LOGIN_LOG_file);
    $pass_fail = $_SESSION['valid'] . ' ';
    $timestamp = date("Y-m-d H:i:s") . ' ';
    $client_IP = $_SERVER['REMOTE_ADDR'] . ' ';
    $client_port = $_SERVER['REMOTE_PORT'] . ' ';
    $client = '"' . $_SERVER['HTTP_USER_AGENT'] . '"';
    file_put_contents($log_file, $pass_fail . $timestamp . $client_IP . $client_port . $client . "\n", FILE_APPEND);
  }
//
}
//end Login_response() //******************************************************
function Create_Table_for_Listing() {
//*****************************************
  global $_, $ONEFILECMS, $ipath, $ipath_OS, $DOC_ROOT_OS, $ICONS, $TABINDEX, $ACCESS_ROOT;
//Header row: | Select All|[ ]|[X](folders first)      Name      (ext) |   Size   |    Date    |
  $new_path = URLencode_path(dir_name($ipath)); //for "../" entry in dir list.
  $new_path_OS = $DOC_ROOT_OS . dir_name($ipath_OS);
//.dir_name($ipath_OS);
//<input hidden> is a dummy input to make sure files[] is always an array for Select_All() & Confirm_Ready().
  ?>
	<INPUT TYPE=hidden NAME="files[]" VALUE="">

	  <?php 	//RE: $TABINDEX's below
	// In order to have ['Name'] (it's background) expand to fill available space in header,
	// (ext) is float'ed right, but has to be listed first, before ['Name'].
	// However, tabindex's need to be in order as displayed, not in order as listed in source.
	  ?>
     <br><br>
	<table class="table table-bordered table-striped">
	<tr>
	<th colspan=3><LABEL for=select_all_ckbox id=select_all_label><?php echo hsc($_['Select_All']) ?></LABEL></th>
	<th><div class=ckbox>
			<INPUT id=select_all_ckbox tabindex=<?php echo $TABINDEX++ ?> TYPE=checkbox NAME=select_all VALUE=select_all>
		</div>
	</th>
	<th class=file_name>
		<div id=ff_ckbox_div class=ckbox>
			<INPUT tabindex=<?php echo $TABINDEX++ ?> TYPE=checkbox id=folders_first_ckbox NAME=folders_first VALUE=folders_first checked>
		</div>
		<label for=folders_first_ckbox id=folders_first_label title="<?php echo hsc($_['folders_first_info']) ?>">
			(<?php echo hsc($_['folders_first']) ?>)
		</label>
		<a tabindex=<?php echo ($TABINDEX + 1) ?> href="#" id=header_sorttype>(<?php echo hsc($_['ext']) ?>)</a>
		<a tabindex=<?php echo $TABINDEX++ ?>     href="#" id=header_filename><?php echo hsc($_['Name']) ?></a>
		<?php $TABINDEX++ ?>
	</th>
	<th class=file_size><a tabindex=<?php echo $TABINDEX++ ?> href="#" id=header_filesize><?php echo hsc($_['Size']) ?></a></th>
	<th class=file_time><a tabindex=<?php echo $TABINDEX++ ?> href="#" id=header_filedate><?php echo hsc($_['Date']) ?></a></th>
	</tr>

	<tr><?php ?>
		<td colspan=4></td>
		<td>
  <?php
  if ($ipath == $ACCESS_ROOT) {
    echo '<a id=f0 tabindex=' . $TABINDEX++. '>&nbsp;</a>';
  }
  else {
    echo '<a id=f0 tabindex=' . $TABINDEX++. ' href="' . $ONEFILECMS . '?i=' . $new_path . '"> <b>..</b> /</a>'; //#### '.$ICONS['up_dir'].'
  }
  ?>
		</td>
		<td></td>
		<td></td>
	<tr>

	<?php ?>
	<tbody id=DIRECTORY_LISTING></tbody>
	<tr><td id=DIRECTORY_FOOTER colspan=7></td</tr>
	</table>
  <?php
}
//Create_Table_for_Listing() //************************************************
function Get_DIRECTORY_DATA($raw_list) {
//**************************************
  global $_, $ONESCRIPT, $ipath, $ipath_OS, $param1, $ICONS, $message, $ftypes, $fclasses, $excluded_list, $stypes, $SHOWALLFILES, $DIRECTORY_COUNT, $DIRECTORY_DATA, $ENC_OS;
//Doesn't use global $filename or $filename_OS in this function (because they shouldn't exist on the Index page)
//$filename below is JUST the file's name.  In some functions, it's the full/path/filename
  $DIRECTORY_COUNT = 0; //final count to exclude . & .., and possibly $excluded file names
  foreach ($raw_list as $raw_filename) { //$raw_list is in server's File System encoding
    if (($raw_filename == '.') || ($raw_filename == '..')) {
      continue;
    }
    $filename_OS = $ipath_OS . $raw_filename; //for is_dir() & file_exists() below
//Normalize filename encoding for general use & display. (UTF-8, which may not be same as the server's File System)
    if ($ENC_OS == 'UTF-8') {
      $filename = $raw_filename;
    }
    else {
      $filename = Convert_encoding($raw_filename, 'UTF-8');
    }
//Get file .ext & check against $stypes (files types to show)
    $filename_parts = explode(".", mb_strtolower($filename));
//Check for no $ext:  "filename"  or ".filename"
    $segments = count($filename_parts);
    if ($segments === 1 || (($segments === 2) && ($filename_parts[0] === ""))) {
      $ext = '';
    }
    else {
      $ext = end($filename_parts);
    }
//Check $filename & $ext against white & black lists. If not to be shown, get next $filename...
    if ($SHOWALLFILES || in_array($ext, $stypes)) {
      $SHOWTYPE = TRUE;
    }
    else {
      $SHOWTYPE = FALSE;
    }
    if (in_array($filename, $excluded_list)) {
      $excluded = TRUE;
    }
    else {
      $excluded = FALSE;
    }
    if (!$SHOWTYPE || $excluded) {
      continue;
    }
//Used to hide rename & delete options for active copy of OneFileCMS.
    $IS_OFCMS = 0;
    if ($ipath . $filename == trim($_SERVER['SCRIPT_NAME'], '/')) {
      $IS_OFCMS = 1;
    }
//Set icon type based on if dir, or file type ($ext).
    if (is_dir($filename_OS)) {
      $type = 'dir';
    }
    else {
      $type = $fclasses[array_search($ext, $ftypes)];
    }
//Determine icon to show
    if (in_array($type, $fclasses)) {
      $icon = $ICONS[$type];
    }
    elseif ($type == 'dir') {
      $icon = $ICONS['folder'];
    }
    else {
      $icon = $ICONS['bin'];
    } //default
//Get file size & date.
    $file_size_raw = filesize($filename_OS);
    $file_time_raw = filemtime($filename_OS);
//Store data
    $DIRECTORY_DATA[$DIRECTORY_COUNT] = array('', '', 0, 0, 0, '');
    $DIRECTORY_DATA[$DIRECTORY_COUNT][0] = $type; //used to determine icon & f_or_f
    $DIRECTORY_DATA[$DIRECTORY_COUNT][1] = $filename;
    $DIRECTORY_DATA[$DIRECTORY_COUNT][2] = $file_size_raw;
    $DIRECTORY_DATA[$DIRECTORY_COUNT][3] = $file_time_raw;
    $DIRECTORY_DATA[$DIRECTORY_COUNT][4] = $IS_OFCMS; //If = 1, Don't show ren, del, ckbox.
    $DIRECTORY_DATA[$DIRECTORY_COUNT][5] = $ext;
    $DIRECTORY_COUNT++;
  }
//end foreach file
  return $DIRECTORY_COUNT;
}
//end Get_DIRECTORY_DATA() //**************************************************
function Send_data_to_js_and_display() {
//**************************************
  global $DIRECTORY_DATA, $DIRECTORY_COUNT;
//"send" DIRECTORY_DATA to javascript.
  $data_for_js = "<script>\n";
  $row = 0; //index after filter of . & ..
  for ($x = 0; $x < $DIRECTORY_COUNT; $x++) {
    $filename = $DIRECTORY_DATA[$x][1];
    if (($filename != '.') && ($filename != '..')) {
      ; // skip . & ..
      $data_for_js .= 'DIRECTORY_DATA[' . $row++. '] = new Array(';
      $data_for_js .= ' "' . $DIRECTORY_DATA[$x][0] . '"'; // "type"
      $data_for_js .= ',"' . addslashes($DIRECTORY_DATA[$x][1]) . '"'; // "file name"
      $data_for_js .= ', ' . $DIRECTORY_DATA[$x][2]; // filesize
      $data_for_js .= ', ' . $DIRECTORY_DATA[$x][3]; // timestamp
      $data_for_js .= ', ' . $DIRECTORY_DATA[$x][4]; // is_ofcms
      $data_for_js .= ',"' . addslashes($DIRECTORY_DATA[$x][5]) . '"'; // "ext"
      $data_for_js .= ");\n";
    }
//end skip . & ..
  }
//end for x
//Initial sort & display of the directory, by (filename, ascending).
  $data_for_js .= "var DIRECTORY_ITEMS = DIRECTORY_DATA.length;\n";
  $data_for_js .= 'Sort_and_Show();' . "\n";
  $data_for_js .= "</script>\n";
  echo $data_for_js;
}
//end Send_data_to_js_and_display() {//****************************************
function Index_Page_buttons_top($file_count) {
//********************************
  global $_, $ONESCRIPT, $param1, $ICONS, $TABINDEX;
  echo '<div id=index_page_buttons>' . "\n";
  echo '<div id=mcd_submit>' . "\n";
  if ($file_count > 0) {
    $onclick_m = 'onclick="Confirm_Submit( \'move\');   "';
    $onclick_c = 'onclick="Confirm_Submit( \'copy\');   "';
    $onclick_d = 'onclick="Confirm_Submit( \'delete\' );"';
    echo '<button class="btn btn-primary" id=b1 tabindex=' . $TABINDEX++. ' type=button ' . $onclick_m . '>' . $ICONS['move'] . '&nbsp;' . hsc($_['Move']) . "</button\n>";
    echo '<button class="btn btn-success" id=b2 tabindex=' . $TABINDEX++. ' type=button ' . $onclick_c . '>' . $ICONS['copy'] . '&nbsp;' . hsc($_['Copy']) . "</button\n>";
    echo '<button class="btn btn-danger" id=b3 tabindex=' . $TABINDEX++. ' type=button ' . $onclick_d . '>' . $ICONS['delete'] . '&nbsp;' . hsc($_['Delete']) . "</button\n>";
  }
  echo '</div>' . "\n"; //end mcd_submit
  echo '<div class="front_links">' . "\n";
  echo '<a class="btn btn-primary" id=b4 tabindex=' . $TABINDEX++. ' href="' . $ONESCRIPT . $param1 . '&amp;p=newfolder">' . $ICONS['folder_new'] . '&nbsp;' . hsc($_['New_Folder']) . '</a>';
  echo '<a class="btn btn-success" id=b5 tabindex=' . $TABINDEX++. ' href="' . $ONESCRIPT . $param1 . '&amp;p=newfile">' . $ICONS['file_new'] . '&nbsp;' . hsc($_['New_File']) . '</a>';
  echo '<a class="btn btn-warning" id=b6 tabindex=' . $TABINDEX++. ' href="' . $ONESCRIPT . $param1 . '&amp;p=upload">' . $ICONS['upload'] . '&nbsp;' . hsc($_['Upload_File']) . '</a>';
  echo '</div>'; //end front_links
  echo '</div>' . "\n"; //end index_page_buttons
} //end Index_Page_buttons_top() //*********************************************
function Index_Page() {
//*******************************************************
  global $ONESCRIPT, $ipath_OS, $param1;
  init_ICONS_js();
  $raw_list = scandir('./' . $ipath_OS); //Get current directory list  (unsorted)
  $file_count = Get_DIRECTORY_DATA($raw_list);
//<form> to contain directory, including buttons at top.
  echo '<form method="post" name="mcdselect" action="' . $ONESCRIPT . $param1 . '&amp;p=mcdaction">';
  echo '<input type="hidden" name="mcdaction" value="">'; //along with $page, affects response
  Index_Page_buttons_top($file_count);
  Create_Table_for_Listing(); //sets up table with empty <tbody></tbody>
  echo "</form>\n";
  Index_Page_scripts();
  Send_data_to_js_and_display();
  Index_Page_events();
}
//end Index_Page() //**********************************************************
function Edit_Page_buttons_top($text_editable, $file_ENC) {
//********************
  global $_, $ONESCRIPT, $param1, $param2, $filename, $filename_OS, $IS_OFCMS, $WYSIWYG_VALID, $EDIT_WYSIWYG, $WYSIWYG_label, $message;
  clearstatcache();
//[View Raw] button.
  if ($text_editable) {
    $view_raw_button = '<button type=button id=view_raw class=button>' . hsc('View Raw') . '</button>';
  }
  else {
    $view_raw_button = '';
  }
//[Wide View] / [Normal View] button.
  $wide_view_button = '<button type=button id=wide_view class=button>' . hsc($_['Wide_View']) . '</button>';
//[Edit WYSIWYG] / [Edit Source] button.
  $WYSIWYG_button = '';
  if ($text_editable && $WYSIWYG_VALID && !$IS_OFCMS) { //Only show when needed/applicable
//Set current mode for Edit page, and label for [Edit WYSIWIG/Source] button
    if (isset ($_COOKIE['edit_wysiwyg']) && ($_COOKIE['edit_wysiwyg'] == '1')) {
      $EDIT_WYSIWYG = '1';
      $WYSIWYG_label = $_['Source'];
    } //wysiwyg mode
    else {
      $EDIT_WYSIWYG = '0';
      $WYSIWYG_label = $_['WYSIWYG'];
    } //plain text mode
    $WYSIWYG_button = '<button type=button id=edit_WYSIWYG class=button>';
    $WYSIWYG_button .= hsc($_['Edit']) . ' ' . hsc($WYSIWYG_label) . '</button>';
  }
//[Close] button
  $close_button = '<button type=button id=close1 class=button>' . hsc($_['Close']) . '</button>';
  ?>
	<div class="edit_btns_top">
		<div class="file_meta">
			<span class="file_size">
				<?php echo hsc($_['meta_txt_01']) . ' ' . number_format(filesize($filename_OS)) . ' ' . hsc($_['bytes']);?>
			</span>	&nbsp;
			<span class="file_time">
				<?php echo hsc($_['meta_txt_03']) . ' <script>FileTimeStamp(' . filemtime($filename_OS) . ', 1, 1, 1);</script>';?>
				<?php echo '&nbsp; ' . $file_ENC;?>
			</span><br>
		</div>

		<div class="buttons_right">
			<?php echo $view_raw_button ?>
			<?php echo $wide_view_button ?>
			<?php echo $WYSIWYG_button ?>
			<?php echo $close_button ?>
		</div>
		<div class=clear></div>
	</div>
  <?php
}
//end Edit_Page_buttons_top() //***********************************************
function Edit_Page_buttons($text_editable, $too_large_to_edit) {
//**************
  global $_, $message, $ICONS, $MAX_IDLE_TIME, $IS_OFCMS, $WYSIWYG_VALID, $EDIT_WYSIWYG;
//Using ckeditor WYSIWYG editor, <input type=reset> button doesn't work. (I don't know why.)
  $reset_button = '<input type=reset  id="reset" class=button value="' . hsc($_['reset']) . '" onclick="return Reset_File();">';
  if ($WYSIWYG_VALID && $EDIT_WYSIWYG) {
    $reset_button = '';
  }
  echo '<div class="edit_btns_bottom">';
  if ($text_editable && !$too_large_to_edit && !$IS_OFCMS) { //Show save & reset only if editable file
    echo '<span id=timer1  class="timer"></span>';
    echo '<button type="submit" class="button" id="save_file">' . hsc($_['save_1']) . '</button>'; //Submit Button
    echo $reset_button;
  }
//end if editable
  function RCD_button($action, $icon, $label) {
//***************
    global $ICONS;
    echo '<button type=button id="' . $action . '_btn" class="button RCD">' . $ICONS[$icon] . '&nbsp;' . hsc($label) . '</button>';
  }
//end RCD_button() //****************************************
//Don't show [Rename] or [Delete] if viewing OneFileCMS itself.
  if (!$IS_OFCMS) {
    RCD_button('renamefile', 'ren_mov', $_['Ren_Move']);
  }
/*Always show Copy*/
  {
    RCD_button('copyfile', 'copy', $_['Copy']);
  }
  if (!$IS_OFCMS) {
    RCD_button('deletefile', 'delete', $_['Delete']);
  }
  echo '</div>';
}
//end Edit_Page_buttons() //***************************************************
//******************************************************************************
function Edit_Page_form($ext, $text_editable, $too_large_to_edit, $too_large_to_view, $file_ENC) {
  global $_, $ONESCRIPT, $param1, $param2, $param3, $filename, $filename_OS, $itypes, $INPUT_NUONCE, $EX, $message, $FILECONTENTS, $WYSIWYG_VALID, $EDIT_WYSIWYG, $IS_OFCMS, $MAX_EDIT_SIZE, $MAX_VIEW_SIZE;
  $too_large_to_edit_message = '<b>' . hsc($_['too_large_to_edit_01']) . ' ' . number_format($MAX_EDIT_SIZE) . ' ' . hsc($_['bytes']) . '</b><br>' . hsc($_['too_large_to_edit_02']) . '<br>' . hsc($_['too_large_to_edit_03']) . '<br>' . hsc($_['too_large_to_edit_04']);
  $too_large_to_view_message = '<b>' . hsc($_['too_large_to_view_01']) . ' ' . number_format($MAX_VIEW_SIZE) . ' ' . hsc($_['bytes']) . '</b><br>' . hsc($_['too_large_to_view_02']) . '<br>' . hsc($_['too_large_to_view_03']) . '<br>';
  echo '<form id="edit_form" name="edit_form" method="post" action="' . $ONESCRIPT . $param1 . $param2 . $param3 . '">';
  echo $INPUT_NUONCE;
  Edit_Page_buttons_top($text_editable, $file_ENC);
  if (!in_array(mb_strtolower($ext), $itypes)) { //If non-image...
    if (!$text_editable) {
      $message .= hsc($_['edit_txt_01']) . '<br><br>';
    }
    elseif ($too_large_to_edit) {
      $message .= $too_large_to_edit_message;
    }
    elseif (!$IS_OFCMS) {
//Did htmlspecialchars return an empty string from a non-empty file?
      $bad_chars = (($FILECONTENTS == "") && (filesize($filename_OS) > 0));
      if ($bad_chars) { //Show message: may be a bad character in file
        echo '<pre class="edit_disabled">' . $EX . hsc($_['edit_txt_02']) . '<br>';
        echo hsc($_['edit_txt_03']) . '<br>';
        echo hsc($_['edit_txt_04']) . '<br></pre>' . "\n";
      }
      else { //show editable <textarea>
//<input name=filename> is used only to signal an Edit_response().
        echo '<input type="hidden" name="filename" value="' . rawurlencode($filename) . '">';
        echo '<textarea id=file_editor name=contents cols=70 rows=25>';
        echo $FILECONTENTS . '</textarea>' . "\n";
      }
    }
//end if/elseif...
    if ($text_editable && $too_large_to_view) //This condition must come first.
      {
      echo '<p class="message_box_contents">' . $too_large_to_view_message . '</p>';
    }
    elseif ($IS_OFCMS || $too_large_to_edit) {
      echo '<pre class="edit_disabled view_file">' . $FILECONTENTS . '</pre>' . "\n";
    }
  }
//end if non-image
  Edit_Page_buttons($text_editable, $too_large_to_edit);
  echo '</form>';
  Edit_Page_scripts();
  if (!$IS_OFCMS && $text_editable && !$too_large_to_edit && !$bad_chars) {
    Edit_Page_Notes();
  }
}
//end Edit_Page_form() //******************************************************
function Edit_Page_Notes() {
//**************************************************
  global $_, $MAX_IDLE_TIME;
  $SEC = $MAX_IDLE_TIME;
  $HRS = floor($SEC / 3600);
  $SEC = fmod($SEC, 3600);
  $MIN = floor($SEC / 60);
  if ($MIN < 10) {
    $MIN = "0" . $MIN;
  }
  ;
  $SEC = fmod($SEC, 60);
  if ($SEC < 10) {
    $SEC = "0" . $SEC;
  }
  ;
  $HRS_MIN_SEC = $HRS . ':' . $MIN . ':' . $SEC;
  ?>
			<div id="edit_notes">
				<div class="notes"><?php echo hsc($_['edit_note_00']) ?></div>
				<div class="notes"><b>1)
					<?php echo hsc($_['edit_note_01a']) . ' $MAX_IDLE_TIME ' . hsc($_['edit_note_01b']) ?>
					<?php echo ' ' . $HRS_MIN_SEC . '. ' . hsc($_['edit_note_02']) ?></b>
				</div>
				<div class="notes"><b>2) </b> <?php echo hsc($_['edit_note_03']) ?></div>
			</div>
  <?php
}
//end Edit_Page_Notes() //*****************************************************
function Edit_Page() {
//********************************************************
  global $_, $filename, $filename_OS, $FILECONTENTS, $etypes, $itypes, $EX, $message, $page, $MAX_EDIT_SIZE, $MAX_VIEW_SIZE, $WYSIWYG_VALID, $IS_OFCMS;
  $filename_parts = explode(".", mb_strtolower($filename));
  $ext = end($filename_parts);
//Determine if a text editable file type
  if (in_array($ext, $etypes)) {
    $text_editable = TRUE;
  }
  else {
    $text_editable = FALSE;
  }
  $too_large_to_edit = (filesize($filename_OS) > $MAX_EDIT_SIZE);
  $too_large_to_view = (filesize($filename_OS) > $MAX_VIEW_SIZE);
//Don't load $WYSIWYG_PLUGIN if not needed
  if (!$text_editable || $too_large_to_edit) {
    $WYSIWYG_VALID = 0;
  }
//Get file contents
  if (($text_editable && !$too_large_to_view) || $IS_OFCMS) {
    $raw_contents = file_get_contents($filename_OS);
    $file_ENC = mb_detect_encoding($raw_contents); //ASCII, UTF-8, ISO-8859-1, etc...
    if ($file_ENC != 'UTF-8') {
      $raw_contents = mb_convert_encoding($raw_contents, 'UTF-8', $file_ENC);
    }
  }
  else {
    $file_ENC = "";
    $raw_contents = "";
  }
  if (PHP_VERSION_ID < 50400) {
    $FILECONTENTS = hsc($raw_contents);
  }
  else {
    $FILECONTENTS = htmlspecialchars($raw_contents, ENT_SUBSTITUTE | ENT_QUOTES, 'UTF-8');
  }
  if ($too_large_to_view || !$text_editable) {
    $header2 = "";
  }
  elseif ($text_editable && !$too_large_to_edit && !$IS_OFCMS) {
    $header2 = hsc($_['edit_h2_2']);
  }
  else {
    $header2 = hsc($_['edit_h2_1']);
  }
  echo '<h2 id="edit_header">' . $header2 . ' ';
  echo '<a class="h2_filename" href="/' . URLencode_path($filename) . '" target="_blank" title="' . hsc($_['Open_View']) . '">';
  echo hsc(basename($filename)) . '</a>';
  echo '</h2>' . "\n";
  Edit_Page_form($ext, $text_editable, $too_large_to_edit, $too_large_to_view, $file_ENC);
  if (in_array($ext, $itypes)) {
    show_image();
  } //If image, show below the [Rename/Move] [Copy] [Delete] buttons
  echo '<div class=clear></div>';
//If viewing OneFileCMS itself, show Edit Disabled message.
  if ($IS_OFCMS && $page == "edit") {
    $message .= '<style>.message_box_contents {background: red;}</style>';
    $message .= '<style>#message_box          {color: white;}   </style>';
    $message .= '<b>' . $EX . hsc($_['edit_caution_02']) . ' &nbsp; ' . $_['edit_txt_00'] . '</b><br>';
  }
}
//end Edit_Page() //***********************************************************
function Edit_response() {
//***If on Edit page, and [Save] clicked *************
  global $_, $EX, $message, $filename, $filename_OS;
  $contents = $_POST['contents'];
  $contents = str_replace("\r\n", "\n", $contents); //Normalize EOL
  $contents = str_replace("\r", "\n", $contents); //Normalize EOL
  $bytes = file_put_contents($filename_OS, $contents);
  if ($bytes !== false) {
    $message .= '<b>' . hsc($_['edit_msg_01']) . ' ' . number_format($bytes) . ' ' . hsc($_['edit_msg_02']) . '</b><br>';
  }
  else {
    $message .= $EX . '<b>' . hsc($_['edit_msg_03']) . '</b><br>';
  }
}
//end Edit_response() //*******************************************************
function Upload_Page() {
//******************************************************
  global $_, $ONESCRIPT, $ipath, $param1, $INPUT_NUONCE, $UPLOAD_FIELDS, $MAIN_WIDTH;
  $max_file_uploads = ini_get('max_file_uploads');
  if ($max_file_uploads < 1) {
    $max_file_uploads = $UPLOAD_FIELDS;
  }
  if ($max_file_uploads < $UPLOAD_FIELDS) {
    $UPLOAD_FIELDS = $max_file_uploads;
  }
//$main_width is used below to determine size (width) of <input type=file> in FF.
  $main_width = $MAIN_WIDTH * 1; //set in config section. Default is 810px.
  $main_units = mb_substr($MAIN_WIDTH, - 2); //should be px, pt, or em.
//convert to px.  16px = 12pt = 1em
  if ($main_units == "em") {
    $main_width = $main_width * 16;
  }
  elseif ($main_units == "pt") {
    $main_width = $main_width * (16 / 12);
  }
  echo '<h2>' . hsc($_['Upload_File']) . '</h2>';
  echo '<p>';
  echo hsc($_['upload_txt_03']) . ' ' . ini_get('upload_max_filesize') . ' ' . hsc($_['upload_txt_01']) . '<br>';
  echo hsc($_['upload_txt_04']) . ' ' . ini_get('post_max_size') . ' ' . hsc($_['upload_txt_02']) . '<br>';
  echo '<form enctype="multipart/form-data" action="' . $ONESCRIPT . $param1 . '&amp;p=uploaded" method="post">';
  echo $INPUT_NUONCE;
  echo '<div class="action"><LABEL>' . hsc($_['upload_txt_05']) . '</LABEL></div>';
  echo '<div class="ren_over">'; //So <LABEL>'s wrap w/o word breaks if $MAIN_WIDTH is narrow.
  echo '<label><INPUT TYPE=radio NAME=ifexists VALUE=rename checked> ' . hsc($_['upload_txt_06']) . '</label>';
  echo '<label><INPUT TYPE=radio NAME=ifexists VALUE=overwrite     > ' . hsc($_['upload_txt_07']) . '</label>';
  echo '</div>';
  for ($x = 0; $x < $UPLOAD_FIELDS; $x++) {
//size attibute is for FF (and is not em, px, pt, or %).
//width attribute is for IE & Chrome, and can be set via css (in style_sheet()).
//In FF, width of <input type="file" size=1> is 121px. If size=2, width = 128, etc. The base value is 114px.
    echo '<input type="file" name="upload_file[]" size="' . floor(($main_width - 114) / 7) . '"><br>' . "\n";
  }
  echo '<p>';
  Cancel_Submit_Buttons($_['Upload']);
  echo '</form>';
}
//end Upload_Page() //*********************************************************
function Upload_response() {
//**************************************************
  global $_, $ipath, $ipath_OS, $page, $EX, $message, $UPLOAD_FIELDS;
  $page = "index"; //return to index.
  $filecount = 0;
  foreach ($_FILES['upload_file']['name'] as $N => $name) {
    if ($name == "") {
      continue;
    } //ignore empty upload fields
    $filecount++;
    $filename_up = $ipath . $_FILES['upload_file']['name'][$N]; //just filename, no path.
    $filename_OS = Convert_encoding($filename_up);
    $savefile_msg = '';
    $MAXUP1 = ini_get('upload_max_filesize');
//$MAXUP2 = ''; //number_format($_POST['MAX_FILE_SIZE']).' '.hsc($_['bytes']);
    $ERROR = $_FILES['upload_file']['error'][$N];
    if ($ERROR == 1) {
      $ERRMSG = hsc($_['upload_err_01']) . ' upload_max_filesize = ' . $MAXUP1;
    }
    elseif (($ERROR > 1) && ($ERROR < 9)) {
      $ERRMSG = hsc($_['upload_err_0' . $ERROR]);
    }
    else {
      $ERRMSG = '';
    }
    if (($ipath === false) || (($ipath != "") && !is_dir($ipath_OS))) {
      $message .= $EX . '<b>' . hsc($_['upload_msg_02']) . '</b><br>';
      $message .= '<span class="filename">' . hsc($ipath) . '</span></b><br>';
      $message .= hsc($_['upload_msg_03']) . '</b><br>';
    }
    else {
      $message .= '<b>' . hsc($_['upload_msg_04']) . '</b> <span class="filename">' . hsc(basename($filename_up)) . '</span><br>';
      if (isset ($_POST['ifexists']) && ($_POST['ifexists'] == 'overwrite')) {
        if (is_file($filename_OS)) {
          $savefile_msg .= hsc($_['upload_msg_07']);
        }
      }
      else { //rename to "file.etc.001"  etc...
        $filename_up = add_serial_num($filename_up, $savefile_msg);
      }
      $filename_OS = Convert_encoding($filename_up);
      if (move_uploaded_file($_FILES['upload_file']['tmp_name'][$N], $filename_OS)) {
        $message .= '<b>' . hsc($_['upload_msg_05']) . '</b> ' . $savefile_msg . '<br>';
      }
      else {
        $message .= '<b>' . $EX . hsc($_['upload_msg_06']) . '</b> ' . $ERRMSG . '</b><br>';
      }
    }
  }
//end foreach $_FILES
  if ($filecount == 0) {
    $page = "upload";
  } //If nothing selected, just reload Upload page.
}
//end Upload_response() //*****************************************************
function New_Page($title, $new_f_or_f) {
//**********************************************
  global $_, $FORM_COMMON, $INVALID_CHARS;
  echo '<h2>' . hsc($title) . '</h2>';
  echo $FORM_COMMON;
  echo '<p>' . hsc($_['new_file_txt_01'] . ' ' . $_['new_file_txt_02']);
  echo '<span class="mono"> ' . hsc($INVALID_CHARS) . '</span></p>';
  echo '<input type="text" name="' . $new_f_or_f . '" id="' . $new_f_or_f . '" value=""><p>';
  Cancel_Submit_Buttons($_['Create']);
  echo '</form>';
}
//end New_Page() //************************************************************
function New_response($post, $isfile) {
//***************************************
  global $_, $ipath, $ipath_OS, $filename, $filename_OS, $page, $param1, $param2, $param3, $message, $EX, $INVALID_CHARS, $WHSPC_SLASH;
  $page = "index"; //Return to index if folder, or on error.
  $new_name = trim($_POST[$post], $WHSPC_SLASH); //Trim whitespace & slashes.
  $filename = $ipath . $new_name;
  $filename_OS = Convert_encoding($filename);
  if ($isfile) {
    $f_or_f = "file";
  }
  else {
    $f_or_f = "folder";
  }
  $msg_new = '<span class="filename">' . hsc($new_name) . '</span><br>';
  if (has_invalid_char($new_name)) {
    $message .= $EX . '<b>' . hsc($_['new_file_msg_01']) . '</b> ' . $msg_new;
    $message .= '<b>' . hsc($_['new_file_msg_02']) . '<span class="mono"> ' . hsc($INVALID_CHARS) . '</span></b>';
  }
  elseif ($new_name == "") { //No new name given.
    $page = "new" . $f_or_f;
    $param3 = '&amp;p=index'; //For [Cancel] button
  }
  elseif (file_exists($filename_OS)) { //Does file or folder already exist ?
    $message .= $EX . '<b>' . hsc($_['new_file_msg_04']) . ' ' . $msg_new;
  }
  elseif ($isfile && touch($filename_OS)) { //Create File
    $message .= '<b>' . hsc($_['new_file_msg_05']) . '</b> ' . $msg_new; //New File success.
    $page = "edit"; //Return to edit page.
    $param2 = '&amp;f=' . rawurlencode(basename($filename)); //for Edit_Page() buttons
    $param3 = '&amp;p=edit'; //for Edit_Page() buttons
  }
  elseif (!$isfile && mkdir($filename_OS, 0755)) { //Create Folder
    $message .= '<b>' . hsc($_['new_file_msg_07']) . '</b> ' . $msg_new; //New folder success
    $ipath = $filename; //return to new folder
    $ipath_OS = Convert_encoding($filename);
    $param1 = '?i=' . URLencode_path($ipath);
  }
  else {
    $message .= $EX . '<b>' . hsc($_['new_file_msg_01']) . ':</b><br>' . $msg_new; //'Error - new file not created:'
  }
}
//end New_response() //********************************************************
function Set_Input_width() {
//**************************************************
  global $_, $WEB_ROOT, $MAIN_WIDTH, $ACCESS_ROOT;
// (width of <input type=text>) = $MAIN_WIDTH - (Width of <label>) - (width of <span>$WEB_ROOT</span>)
// $MAIN_WIDTH: Set in config section, may be in em, px, pt, or %. Ignoring % for now.
// Width of 1 character = .625em = 10px = 7.5pt  (1em = 16px = 12pt)
  $main_units = mb_substr($MAIN_WIDTH, - 2);
  $main_width = $MAIN_WIDTH * 1;
  $root_width = mb_strlen($WEB_ROOT . $ACCESS_ROOT);
  $label_width = mb_strlen($_['New_Location']);
//convert to em
  $root_width *= .625;
  $label_width *= .625;
  if ($main_units == "px") {
    $main_width = $main_width / 16;
  }
  elseif ($main_units == "pt") {
    $main_width = $main_width / 12;
  }
//The .4 at the end is needed for some rounding erros above. Or something... I don't know.
  $input_type_text_width = ($main_width - $label_width - $root_width - .4) . 'em';
  echo '<style>input[type="text"] {width: ' . $input_type_text_width . ';}';
  echo 'label {display: inline-block; width: ' . $label_width . 'em; }</style>';
}
//end Set_Input_width() //*****************************************************
function CRM_Page($action, $title, $action_id, $old_full_name) {
//*******************
//$action    = 'Copy' or 'Rename'.
//$action_id = 'copy_file' or 'rename_file'
  global $_, $WEB_ROOT, $ipath, $param1, $filename, $FORM_COMMON, $ACCESS_ROOT, $ACCESS_PATH;
  $new_full_name = $old_full_name; //default
  if (is_dir(Convert_encoding($old_full_name))) {
    $param1 = '?i=' . dir_name($ipath); //If dir, return to parent on [Cancel]
    $ACCESS_PATH = dir_name($ACCESS_PATH);
  }
  Set_Input_width();
  echo '<h2>' . hsc($action . ' ' . $title) . '</h2>';
  echo $FORM_COMMON;
  echo '<input type="hidden" name="' . hsc($action_id) . '"  value="' . hsc($action_id) . '">';
  echo '<input type="hidden" name=old_full_name     value="' . hsc($old_full_name) . '">';
  echo '<label>' . hsc($_['CRM_txt_04']) . ':</label>';
  echo '<input type=text name=new_name id=new_name value="' . hsc(basename($new_full_name)) . '"><br>';
  echo '<label>' . hsc($_['New_Location']) . ':</label>';
  echo '<span class="web_root">' . hsc($WEB_ROOT . $ACCESS_ROOT) . '</span>';
  echo '<input type=text name=new_location id=new_location value="' . hsc($ACCESS_PATH) . '"><br>';
  echo '(' . hsc($_['CRM_txt_02']) . ')<p>';
  Cancel_Submit_Buttons($action);
  echo '</form>';
}
//end CRM_Page() //************************************************************
function CRM_response($action, $msg1, $show_message = 3) {
//********************
//$action = 'rCopy' or 'rename'.  Returns 0 if successful, 1 on error.
//$show_message: 0 = none; 1 = errors only; 2 = successes only; 3 = all messages (default).
  global $_, $ONESCRIPT, $ipath, $ipath_OS, $filename, $page, $param1, $param2, $param3, $message, $EX, $INVALID_CHARS, $WHSPC_SLASH;
  $old_full_name = trim($_POST['old_full_name'], $WHSPC_SLASH); //Trim whitespace & slashes.
  $new_name_only = trim($_POST['new_name'], $WHSPC_SLASH);
  $new_location = trim($_POST['new_location'], $WHSPC_SLASH);
  if ($new_location != "") {
    $new_location .= '/';
  }
  $new_full_name = $new_location . $new_name_only;
  $filename = $old_full_name; //default if error.
//for function calls that access the server file system, such as rCopy, rename, file_exists, etc...
  $old_full_name_OS = Convert_encoding($old_full_name);
  $new_full_name_OS = Convert_encoding($new_full_name);
  $new_location_OS = Convert_encoding($new_location);
  $isfile = 0;
  if (is_file($old_full_name_OS)) {
    $isfile = 1;
  } //File or folder?
//Common message lines
  $com_msg = '<div id="message_left">' . hsc($_['From']) . '<br>' . hsc($_['To']) . '</div>';
  $com_msg .= '<b>: </b><span class="filename">' . hsc($old_full_name) . '</span><br>';
  $com_msg .= '<b>: </b><span class="filename">' . hsc($new_full_name) . '</span><br>';
  $bad_name = ""; //bad file or folder name (can be either old_ or new_)
  $err_msg = ''; //Error message.
  $scs_msg = ''; //Success message.
  $error_code = 0; //1 = success (no error), 0 = an error. Used for return value.
//Check old name for invalid chars (like .. ) (Unlikely to be false outside a malicious attempt)
  if (Check_path($old_full_name, $show_message) === false) {
    $bad_name = $old_full_name;
  }
  elseif (!file_exists($old_full_name_OS)) {
    $err_msg .= $EX . '<b>' . hsc($msg1 . ' ' . hsc($_['CRM_msg_02'])) . '</b><br>';
    $bad_name = $old_full_name;
//Ignore if new name is blank.
  }
  elseif (mb_strlen($new_name_only) == 0) {
    $page = 'copyfile';
    $param3 = '&amp;p=copyfile';
    return 0;
//Check new name for invalid chars, including slashes.
  }
  elseif (has_invalid_char($new_name_only)) {
    $err_msg .= $EX . '<b>' . hsc($_['new_file_msg_02']) . '<span class="filename"> ' . hsc($INVALID_CHARS) . '</span></b><br>';
    $bad_name = $new_name_only;
//Check new location for invalid chars etc.
  }
  elseif (Check_path($new_location, $show_message) === false) {
    $bad_name = $new_location;
//$new_location must already exist as a directory
  }
  elseif (($new_location != "") && !is_dir($new_location_OS)) {
    $err_msg .= $EX . '<b>' . hsc($msg1 . ' ' . hsc($_['CRM_msg_01'])) . '</b><br>';
    $bad_name = $new_location;
//Don't overwrite existing files.
  }
  elseif (file_exists($new_full_name_OS)) {
    $bad_name = $new_full_name;
    $err_msg .= $EX . '<b>' . hsc($msg1 . ' ' . hsc($_['CRM_msg_03'])) . '</b><br>';
  }
  else { //attempt $action
    $error_code = $action($old_full_name_OS, $new_full_name_OS);
    if ($error_code > 0) {
      $scs_msg .= '<b>' . hsc($msg1 . ' ' . hsc($_['successful'])) . '</b><br>' . $com_msg;
      if ($isfile) {
        $filename = $new_full_name;
      }
      $ipath = $new_location;
      $ipath_OS = $new_location_OS;
    }
    else {
      $err_msg .= $EX . '<b>' . hsc($_['CRM_msg_05'] . ' ' . $msg1) . '</b><br>' . $com_msg;
    }
  }
//
  if (($bad_name != '') && ($error_code == 0)) {
    $err_msg .= '<span class="filename">' . hsc($bad_name) . '</span><br>';
  }
  if (($show_message & 1) && ($error_code == 0)) {
    $message .= $err_msg;
  } //Show error message.
  if ($show_message & 2) {
    $message .= $scs_msg;
  } //Show success message.
//Prior page should be either index or edit
  $page = $_SESSION['recent_pages'][1];
  $param1 = '?i=' . URLencode_path($ipath);
  if ($isfile & $page == "edit") {
    $param2 = '&amp;f=' . rawurlencode(basename($filename));
  }
  return $error_code; //
}
//end CRM_response() //********************************************************
function Delete_response($target, $show_message = 3) {
//**************************
  global $_, $ipath, $ipath_OS, $param1, $filename, $param2, $page, $message, $EX;
  if ($target == "") {
    return 0;
  } //Prevent accidental delete of entire website.
  $target = Check_path($target, $show_message); //Make sure $target is within $WEB_ROOT
  $target = trim($target, '/');
  $page = "index"; //Return to index
//If came from admin page, return there.
  if ($_SESSION['admin_page']) {
    $page = 'admin';
  }
  $err_msg = ''; //On error, set this message.
  $scs_msg = ''; //On success, set this message.
  $error_code = rDel($target);
  if ($error_code > 0) { // 0 = error, > 0 is number of successes
    $scs_msg .= '<b>' . hsc($_['Deleted']) . ':</b> ';
    $scs_msg .= '<span class="filename">' . hsc(basename($target)) . '</span></br>';
    $ipath = dir_name($target); //Return to parent dir.
    $ipath_OS = Convert_encoding($ipath);
    $param1 = '?i=' . URLencode_path($ipath);
    $filename = "";
    $param2 = "";
  }
  else { //Error
    $err_msg .= $EX . '<b>' . hsc($_['delete_msg_03']) . '</b> <span class="filename">' . hsc($target) . '</span><br>';
    $page = $_SESSION['recent_pages'][1];
    if ($page == "edit") {
      $filename = $target;
      $param2 = '&amp;f=' . basename($filename);
    }
  }
  if ($show_message & 1) {
    $message .= $err_msg;
  } //Show error message.
  if ($show_message & 2) {
    $message .= $scs_msg;
  } //Show success message.
  return $error_code;
}
//end Delete_response() //*****************************************************
function MCD_Page($action, $page_title, $classes = '') {
//**********************
//$action = mcd_mov or mcd_cpy or mcd_del
  global $_, $WEB_ROOT, $ONESCRIPT, $ipath, $ipath_OS, $param1, $filename, $page, $ICONS, $ACCESS_ROOT, $ACCESS_PATH, $INPUT_NUONCE, $message;
//Prep for a single file or folder
  if ($page == "deletefile" || $page == "deletefolder") {
    $_POST['mcdaction'] = 'delete'; //set mcdaction != copy or move (see below).
    if ($page == "deletefile") {
      $_POST['files'][1] = basename($filename);
    }
//If  $page == deletefolder,   $_POST['files'][1] is set in Verify_Page_Conditions()
  }
  Set_Input_width();
  echo '<h2>' . hsc($page_title) . '</h2>';
  echo '<form method="post" action="' . $ONESCRIPT . $param1 . '">' . $INPUT_NUONCE;
  echo '<input type="hidden" name="' . hsc($action) . '" value="' . hsc($action) . '">' . "\n";
  if (($_POST['mcdaction'] == 'copy') || ($_POST['mcdaction'] == 'move')) {
    echo '<label>' . hsc($_['New_Location']) . ':</label>';
    echo '<span class="web_root">' . hsc($WEB_ROOT . $ACCESS_ROOT) . '</span>';
    echo '<input type=text   name=new_location  id=new_location value="' . hsc($ACCESS_PATH) . '">';
    echo '<p>(' . hsc($_['CRM_txt_02']) . ')</p>';
  }
  echo '<p><b>' . hsc($_['Are_you_sure']) . '</b></p>';
  Cancel_Submit_Buttons($page_title);
//List selected folders & files
  $full_list = Sort_Seperate($ipath, $_POST['files']);
  echo '<table class="verify ' . $classes . '">';
  echo '<tr><th>' . hsc($_['Selected_Files']) . ':</th></tr>' . "\n";
  foreach ($full_list as $file) {
    $file_OS = Convert_encoding($file);
    if (is_dir($ipath_OS . $file_OS)) {
      echo '<tr><td>' . $ICONS['folder'] . '&nbsp;' . hsc($file) . ' /</td></tr>';
    }
    else {
      echo '<tr><td>' . hsc($file) . '</td></tr>';
    }
    echo '<input type=hidden  name="files[]" value="' . hsc($file) . '">' . "\n";
  }
  echo '</table>';
  echo '</form>';
}
//end MCD_Page() //************************************************************
function MCD_response($action, $msg1, $success_msg = '') {
//********************
  global $_, $ipath, $ipath_OS, $EX, $message, $WHSPC_SLASH;
  $files = $_POST['files']; //List of files to delete (path not included)
  $errors = 0; //number of failed moves, copies, or deletes
  $successful = 0;
  $new_location = "";
  if (isset ($_POST['new_location'])) {
    $new_location = $_POST['new_location'];
    $new_location_OS = Convert_encoding($_POST['new_location']);
  }
  $show_message = 1; //1= show error msg only.
  if (($new_location != "") && !is_dir($new_location_OS)) {
    $message .= $EX . '<b>' . hsc($msg1 . ' ' . $_['CRM_msg_01']) . '</b><br>';
    $message .= '<span class="filename">' . hsc($_POST['new_location']) . '</span><br>';
    return;
  }
  elseif ($action == 'rDel') {
    foreach ($files as $file) {
      if ($file == "") {
        continue;
      } //a blank file name would cause $ipath to be deleted.
      $error_code = Delete_response($ipath . $file, $show_message);
      $successful += $error_code;
      if ($error_code == 0) {
        $errors++;
      }
    }
  }
  else { //move or rCopy
    $mcd_ipath = $ipath; //CRM_response() changes $ipath to $new_location
    foreach ($files as $file) {
      $_POST['old_full_name'] = $mcd_ipath . $file;
      $_POST['new_name'] = $file;
//$_POST['new_location'] should already be set by the client ( via MCD_Page() ).
      $error_code = CRM_response($action, $msg1, $show_message);
      $successful += $error_code;
      if ($error_code == 0) {
        $errors++;
      }
    }
  }
  if ($errors) {
    $message .= $EX . ' <b>' . $errors . ' ' . hsc($_['errors']) . '.</b><br>';
  }
  $message .= '<b>' . $successful . ' ' . hsc($success_msg) . '</b><br>';
  if ($action != 'rDel') {
    if ($successful > 0) { //"From:" & "To:" lines if any successes.
      $message .= '<div id="message_left"><b>' . hsc($_['From']) . '<br>' . hsc($_['To']) . '</b></div>';
      $message .= '<b>:</b><span class="filename"> ' . hsc($mcd_ipath) . '</span><br>';
      $message .= '<b>:</b><span class="filename"> ' . hsc($ipath) . '</span><br>';
    }
  }
}
//end MCD_response() //********************************************************
function Page_Title() {
//***<title>Page_Title()</title>*************************
  global $_, $page;
  if (!$_SESSION['valid']) {
    return $_['Log_In'];
  }
  elseif ($page == "admin") {
    return $_['Admin_Options'];
  }
  elseif ($page == "hash") {
    return $_['Generate_Hash'];
  }
  elseif ($page == "changepw") {
    return $_['pw_change'];
  }
  elseif ($page == "changeun") {
    return $_['un_change'];
  }
  elseif ($page == "edit") {
    return $_['Edit_View'];
  }
  elseif ($page == "upload") {
    return $_['Upload_File'];
  }
  elseif ($page == "newfile") {
    return $_['New_File'];
  }
  elseif ($page == "copyfile") {
    return $_['Copy_Files'];
  }
  elseif ($page == "copyfolder") {
    return $_['Copy_Files'];
  }
  elseif ($page == "renamefile") {
    return $_['Ren_Move'] . ' ' . $_['File'];
  }
  elseif ($page == "deletefile") {
    return $_['Del_Files'];
  }
  elseif ($page == "deletefolder") {
    return $_['Del_Files'];
  }
  elseif ($page == "newfolder") {
    return $_['New_Folder'];
  }
  elseif ($page == "renamefolder") {
    return $_['Ren_Folder'];
  }
  elseif ($page == "mcdaction" && ($_POST['mcdaction'] == "copy")) {
    return $_['Copy_Files'];
  }
  elseif ($page == "mcdaction" && ($_POST['mcdaction'] == "move")) {
    return $_['Move_Files'];
  }
  elseif ($page == "mcdaction" && ($_POST['mcdaction'] == "delete")) {
    return $_['Del_Files'];
  }
  else {
    return $_SERVER['SERVER_NAME'];
  }
}
//end Page_Title() //**********************************************************
function Load_Selected_Page() {
//***********************************************
  global $_, $ONESCRIPT, $ipath, $filename, $page;
  if (!$_SESSION['valid']) {
    Login_Page();
  }
  elseif ($page == "admin") {
    Admin_Page();
  }
  elseif ($page == "hash") {
    Hash_Page();
  }
  elseif ($page == "changepw") {
    Change_PWUN_Page('pw', 'password', $_['pw_change'], $_['pw_new'], $_['pw_confirm']);
  }
  elseif ($page == "changeun") {
    Change_PWUN_Page('un', 'text', $_['un_change'], $_['un_new'], $_['un_confirm']);
  }
  elseif ($page == "edit") {
    Edit_Page();
  }
  elseif ($page == "upload") {
    Upload_Page();
  }
  elseif ($page == "newfile") {
    New_Page($_['New_File'], "new_file");
  }
  elseif ($page == "newfolder") {
    New_Page($_['New_Folder'], "new_folder");
  }
  elseif ($page == "copyfile") {
    CRM_Page($_['Copy'], $_['File'], 'copy_file', $filename);
  }
  elseif ($page == "copyfolder") {
    CRM_Page($_['Copy'], $_['Folder'], 'copy_file', $ipath);
  }
  elseif ($page == "renamefile") {
    CRM_Page($_['Ren_Move'], $_['File'], 'rename_file', $filename);
  }
  elseif ($page == "renamefolder") {
    CRM_Page($_['Ren_Move'], $_['Folder'], 'rename_file', $ipath);
  }
  elseif ($page == "deletefile") {
    MCD_Page('mcd_del', $_['Del_Files'], 'verify_del');
  }
  elseif ($page == "deletefolder") {
    MCD_Page('mcd_del', $_['Del_Files'], 'verify_del');
  }
  elseif ($page == "mcdaction") {
    if ($_POST['mcdaction'] == 'move') {
      MCD_Page('mcd_mov', $_['Move_Files']);
    }
    if ($_POST['mcdaction'] == 'copy') {
      MCD_Page('mcd_cpy', $_['Copy_Files']);
    }
    if ($_POST['mcdaction'] == 'delete') {
      MCD_Page('mcd_del', $_['Del_Files'], 'verify_del');
    }
  }
  else {
    Index_Page();
  } //default if valid session.
}
//end Load_Selected_Page() //**************************************************
function Respond_to_POST() {
//**************************************************
  global $_, $VALID_POST, $ipath, $page, $EX, $ACCESS_ROOT, $message;
  if (!$VALID_POST) {
    return;
  }
//First, validate any $_POST'ed paths against $ACCESS_ROOT.
  if (isset ($_POST["old_full_name"]) && !Valid_Path($_POST["old_full_name"], false)) {
//unlikely, but just in case
    $message .= $EX . '<b>' . hsc($_['Invalid_path']) . ': </b><span class=filename>' . hsc($_POST["old_full_name"]) . '</span>';
    $VALID_POST = 0;
    return;
  }
  if (isset ($_POST["new_location"])) {
    $_POST["new_location"] = $ACCESS_ROOT . $_POST["new_location"];
    if (!Valid_Path($_POST["new_location"], false)) {
      $message .= $EX . '<b>' . hsc($_['Invalid_path']) . ': </b><span class=filename>' . hsc($_POST["new_location"]) . '</span>';
      $VALID_POST = 0;
      return;
    }
  }
  if (isset ($_POST['mcd_mov'])) {
    MCD_response('rename', $_['Ren_Move'], $_['mcd_msg_01']);
  } //move == rename
  elseif (isset ($_POST['mcd_cpy'])) {
    MCD_response('rCopy', $_['Copy'], $_['mcd_msg_02']);
  }
  elseif (isset ($_POST['mcd_del'])) {
    MCD_response('rDel', $_['Delete'], $_['mcd_msg_03']);
  }
  elseif (isset ($_POST['whattohash'])) {
    Hash_response();
  }
  elseif (isset ($_POST['pw'])) {
    Change_PWUN_response('pw', $_['change_pw_02']);
  }
  elseif (isset ($_POST['un'])) {
    Change_PWUN_response('un', $_['change_un_02']);
  }
  elseif (isset ($_POST['filename'])) {
    Edit_response();
  }
  elseif (isset ($_POST['new_file'])) {
    New_response('new_file', 1);
  } //1=file
  elseif (isset ($_POST['new_folder'])) {
    New_response('new_folder', 0);
  } //0=folder
  elseif (isset ($_POST['rename_file'])) {
    CRM_response('rename', $_['Ren_Move']);
  }
  elseif (isset ($_POST['copy_file'])) {
    CRM_response('rCopy', $_['Copy']);
  }
  elseif (isset ($_FILES['upload_file']['name'])) {
    Upload_response();
  }
//If Changed p/w, u/n, or other Admin Page action, make sure to not return to a folder outside of $ACCESS_ROOT.
  Valid_Path($ipath, true);
}
//end Respond_to_POST() //*****************************************************
function init_ICONS_js() {
//****************************************************
  global $ICONS;
//Currently, only icons for dir listing are needed in js
  ?>
<script>
var ICONS = new Array();
ICONS['bin']	 = '<?php echo $ICONS["bin"] ?>';
ICONS['z']		 = '<?php echo $ICONS["z"] ?>';
ICONS['img']	 = '<?php echo $ICONS["img"] ?>';
ICONS['svg']	 = '<?php echo $ICONS["svg"] ?>';
ICONS['txt']	 = '<?php echo $ICONS["txt"] ?>';
ICONS['htm']	 = '<?php echo $ICONS["htm"] ?>';
ICONS['php']	 = '<?php echo $ICONS["php"] ?>';
ICONS['css']	 = '<?php echo $ICONS["css"] ?>';
ICONS['cfg']	 = '<?php echo $ICONS["cfg"] ?>';
ICONS['dir']     = '<?php echo $ICONS["dir"] ?>';
ICONS['folder']  = '<?php echo $ICONS["folder"] ?>';
ICONS['ren_mov'] = '<?php echo $ICONS["ren_mov"] ?>';
ICONS['move']    = '<?php echo $ICONS["move"] ?>';
ICONS['copy']    = '<?php echo $ICONS["copy"] ?>';
ICONS['delete']  = '<?php echo $ICONS["delete"] ?>';
</script>
  <?php
}
//end init_ICONS_js() //*******************************************************
function common_scripts() {
//***************************************************
  global $_, $TO_WARNING, $message, $page, $DELAY_Expired_Reload;
  ?>
<script>

function pad(num){ if ( num < 10 ){ num = "0" + num; }; return num; }



function hsc(text) {//************************************************
	//A basic htmlspecialchars()
	return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}//end hsc() //*******************************************************



function trim($string) {//********************************************

	//trim leading whitespace
	$len = $string.length;
	$trimmed = "";
	for (var $x=0; $x < $len; $x++) {
		$charcode = $string.charCodeAt($x);
		if ( $charcode > 32) { $trimmed += $string.substr($x); $x = $len; }
	}

	//trim trailing whitespace
	$string = $trimmed;
	$len = $string.length;
	$trimmed = "";
	for ($x=($len-1); $x >= 0; $x--) {
		$charcode = $string.charCodeAt($x);
		if ( $charcode > 32) { $trimmed += $string.substr(0,$x+1); $x = -1; }
	}

	return $trimmed;
}//end trim() //******************************************************



function FormatTime(Seconds) {//**************************************
	var Hours = Math.floor(Seconds / 3600); Seconds = Seconds % 3600;
	var Minutes = Math.floor(Seconds / 60); Seconds = Seconds % 60;
	if ((Hours == 0) && (Minutes == 0)) { Minutes = "" } else { Minutes = pad(Minutes) }
	if (Hours == 0) { Hours = ""} else { Hours = pad(Hours) + ":"}

	return (Hours + Minutes + ":" + pad(Seconds));
}//end FormatTime() //************************************************



function format_number(number, sep) {//*******************************
	sep = typeof sep !== 'undefined' ? sep : ','; //default to a comma
	var number	= number + "";   // 1234567890     convert number to a string
	var result  = "";            // 1,234,567,890  sample result

	for (var x = 0; x < number.length ; x += 3) {
		a = number.length - x - 3;
		b = number.length - x;
		result = number.substring(a,b) + result;
		if (a > 0) {result = sep + result} //add sep if still have more digits
	}
	return result;
}//end format_number() //*********************************************



//********************************************************************
function Countdown(count, End_Time, Timer_ID, Action){
	var Timer        = document.getElementById(Timer_ID);
	var Current_Time = Math.round(new Date().getTime()/1000); //js uses milliseconds
	    count        = End_Time - Current_Time;
	var params = count + ', "' + End_Time + '", "' + Timer_ID + '", "' + Action + '"';

	$message_box = document.getElementById('message_box');

	Timer.innerHTML = FormatTime(count);

	if ((count == <?php echo $TO_WARNING ?>) && (Action != "")) { //Two minute warning...

		var timeout_warning  = '<div class="message_box_contents"><b><?php echo hsc($_['session_warning']) ?></b> ';
			timeout_warning += '<b><span id=timer2>:--</span></b></div>';
		$message_box.innerHTML  = timeout_warning;
		setTimeout('Start_Countdown(' + count + ',"timer2","")',25);

		var Timer2 = document.getElementById('timer2');
		Timer.style.color           = Timer2.style.color           = "red";
		Timer.style.fontWeight      = Timer2.style.fontWeight      = "900";
		Timer.style.backgroundColor = Timer2.style.backgroundColor = "white";
	}

	if ( count < 1 ) {
		if ( Action == 'LOGOUT') {
			Timer.innerHTML        = '<?php echo hsc($_['session_expired']) ?>';
			$message_box.innerHTML = '<div class=message_box_contents><b><?php echo hsc($_['session_expired']) ?></b></div>';
			//Load login screen, but delay first to make sure really expired:
			setTimeout('window.location = window.location.pathname', <?php echo $DELAY_Expired_Reload ?>);
		}
		return;
	}
	setTimeout('Countdown(' + params + ')',1000); //1000 = one second
}//end Countdown() //*************************************************



function Start_Countdown(count, ID, Action) {//***********************

	var Time_Start  = Math.round(new Date().getTime()/1000); //in seconds
	var Time_End    = Time_Start + count;

	Countdown(count, Time_End, ID, Action); //(seconds to count, id of element)
}//end Start_Countdown() //*******************************************



//********************************************************************
function FileTimeStamp(php_filemtime, show_date, show_offset, write_return){

	//php's filemtime returns seconds, javascript's date() uses milliseconds.
	var FileMTime = php_filemtime * 1000;

	var TIMESTAMP  = new Date(FileMTime);
	var YEAR  = TIMESTAMP.getFullYear();
	var	MONTH = pad(TIMESTAMP.getMonth() + 1);
	var DATE  = pad(TIMESTAMP.getDate());
	var HOURS = TIMESTAMP.getHours();
	var MINS  = pad(TIMESTAMP.getMinutes());
	var SECS  = pad(TIMESTAMP.getSeconds());

	if ( HOURS < 12) { AMPM = "am"; } else { AMPM = "pm"; }
	if ( HOURS > 12 ) {HOURS = HOURS - 12; }
	HOURS = pad(HOURS);

	var GMT_offset = -(TIMESTAMP.getTimezoneOffset()); //Yes, I know- seems wrong, but its works.

	if (GMT_offset < 0) { NEG = -1; SIGN = "-"; } else { NEG = 1; SIGN = "+"; }

	var offset_HOURS = Math.floor(NEG*GMT_offset/60);
	var offset_MINS  = pad( NEG * (GMT_offset % 60) );
	var offset_FULL  = "UTC " + SIGN + offset_HOURS + ":" + offset_MINS;

	var FULLDATE = YEAR + "-" + MONTH + "-" + DATE;
	var FULLTIME = HOURS + ":" + MINS + ":" + SECS + " " + AMPM;

	var               DATETIME = FULLTIME;
	if (show_date)  { DATETIME = FULLDATE + " &nbsp;" + FULLTIME;}
	if (show_offset){ DATETIME += " ("+offset_FULL+")"; }

	if (write_return) { document.write(DATETIME); }
	else 			  { return DATETIME; }
}//end FileTimeStamp() //*********************************************



function Display_Messages($msg, take_focus) {//***********************

	$tabindex_xbox = typeof $tabindex_xbox !== 'undefined' ? $tabindex_xbox : 0;

	var $page     = '<?php echo $page ?>';
	var new_focus = '';

	take_focus = typeof new_focus == 'undefined' ? 0 : take_focus ;//default is X_box doesn't take focus()

	if      ($page == 'index') { new_focus = 'header_filename'; }
	else if ($page == 'edit')  { new_focus = 'close1'; }
	else if ($page == 'login') { new_focus = 'username'; }
	else if ($page == 'hash')  { new_focus = 'whattohash'; }
	else if ($page == 'admin') { new_focus = 'close'; }

	var $X_box		 = '<button tabindex=' + $tabindex_xbox + ' type=button id="X_box">x</button>';
	var $MESSAGE	 = '<div class=message_box_contents>' + $msg + '</div>';
	var $message_box = document.getElementById("message_box");
	var $new_focus	 = document.getElementById(new_focus)

	if ($msg == '') {$message_box.innerHTML = ' ';} //innerHTML must be given a space or $message_box won't clear.
	else				{
		$message_box.innerHTML = $X_box + $MESSAGE;
		var $X_box_btn	 = document.getElementById('X_box');
		if (take_focus) {$X_box_btn.focus();}
		$X_box_btn.onclick = function () { $message_box.innerHTML = " "; $new_focus.focus();}
	}

}//end Display_Messages() //******************************************

</script>
  <?php
}
//end common_scripts() //******************************************************
function Index_Page_events() {
//************************************************
  global $_, $PAGEUPDOWN, $EX;
  ?>
<script>
//onclick events
var Select_All_ckbox	= document.getElementById('select_all_ckbox');
var Header_Sorttype		= document.getElementById('header_sorttype');
var Folders_First_Ckbox = document.getElementById('folders_first_ckbox');
var Header_Filename		= document.getElementById('header_filename');
var Header_Filesize		= document.getElementById('header_filesize');
var Header_Filedate		= document.getElementById('header_filedate');

Select_All_ckbox.onclick	 = function () {Select_All();}
Folders_First_Ckbox.onclick  = function () {Sort_and_Show(SORT_by, SORT_order); this.focus()}
Header_Filename.onclick  = function () {Sort_and_Show(1, FLIP_IF); this.focus(); return false;}
Header_Filesize.onclick  = function () {Sort_and_Show(2, FLIP_IF); this.focus(); return false;}
Header_Filedate.onclick  = function () {Sort_and_Show(3, FLIP_IF); this.focus(); return false;}
Header_Sorttype.onclick	 = function () {Sort_and_Show(5, FLIP_IF); this.focus(); return false;}

Header_Filename.focus();



document.onmousedown = function (event) { //************************
	//Mouse clicks may remove focus from focused elements, including checkboxes,
	//but don't clear the manual "highlight" of the parent div's & label's of checkbox's

	//Clear parent div of a checkbox
	var ID = document.activeElement.id;
	if (document.activeElement.type == 'checkbox') {
		document.getElementById(ID).parentNode.style.backgroundColor = "";
	}

	//Clear labels...  (don't check, just clear 'em)
	document.getElementById('select_all_label').style.backgroundColor = "";
	document.getElementById('folders_first_label').style.backgroundColor = "";

}//end onmousedown() //***********************************************



function on_Tab_down(ID, FR,shifted) { //*****************************
	//Handle the background colors of checkboxes' parent <div>'s & <label>'s.
	//(checkboxes generally don't have "background colors" as far as css goes...)
	//Current checkbox already cleared by onkeydown().

	//Prep for Tab key...
	//Default tab action occurrs on keyUP, so "new" location is not known in onkeydown().
	//So, if current focus is ck_box, clear bg, else if we're heading there, set bg.
	//Tab from L, Current ID will be "f<FR>c2"
	//Tab from R: Current ID is "f<FR>"
	var fFR   = "f" + FR        //Filename
	var ckbox = "f" + FR + "c3" //[ ] Checkbox
	var del   = "f" + FR + "c2" //(x) Delete

	var ck_box = document.getElementById(ckbox);
	var highlight = "rgb(255,250,150)";

	if      (!shifted)  { //just Tab
		if      (ID == ckbox) { ck_box.parentNode.style.backgroundColor = "";}
		else if (ID == del  ) { ck_box.parentNode.style.backgroundColor = highlight; }
		else if (ID == "b6" ) { //[New Folder]
			document.getElementById('select_all_ckbox').parentNode.style.backgroundColor = highlight;
			document.getElementById('select_all_label').style.backgroundColor = highlight;
		}
		else if (ID == "select_all_ckbox" ) {
			document.getElementById('select_all_label').style.backgroundColor = "";
			document.getElementById('folders_first_ckbox').parentNode.style.backgroundColor = highlight;
			document.getElementById('folders_first_label').style.backgroundColor = highlight;
		}
		return;
	}
	else if (shifted)  { //Shift-Tab
		if       (ID == ckbox){ ck_box.parentNode.style.backgroundColor = "";}
		else if ((ID == fFR) && (FR > 0) ) { ck_box.parentNode.style.backgroundColor = highlight; }
		else if  (ID == "header_filename")  {
			document.getElementById('folders_first_ckbox').parentNode.style.backgroundColor = highlight;
			document.getElementById('folders_first_label').style.backgroundColor = highlight;
		}
		else if (ID == "folders_first_ckbox") {
			document.getElementById('folders_first_label').style.backgroundColor = "";
			document.getElementById('select_all_ckbox').parentNode.style.backgroundColor = highlight;
			document.getElementById('select_all_label').style.backgroundColor = highlight;
		}
		return;
	}

}//end on_Tab_down() { //*********************************************



document.onkeydown = function(event) { //*****************************
	//Control cursor keys to navigate index page. (Arrows, Page, Home, End)

	var jump = <?php echo $PAGEUPDOWN ?>;//# of rows to jump with Page Up/Page Down.
	var highlight = "rgb(255,250,150)";

	//Get key pressed...
	if (!event) {var event = window.event;} //for IE
	var key = event.keyCode;

	//Assign a few handy "constants": Arrow U/D/L/R, Page Up/Down, etc...
	var AU = 38, AD = 40, AL = 37, AR = 39, PU = 33, PD = 34; END = 35, HOME = 36, ESC = 27, TAB = 9;

	//Ignore any other key presses...
	if ((key != AU) && (key != AD) && (key != AL) && (key != AR) && (key != PU) && (key != PD) &&
		(key != HOME) && (key != END) && (key != ESC) && (key != TAB)) { return }

	//File Rows. For these events, "../" is 0, and files are indexed 1 to DIRECTORY_ITEMS.
	var FROWS     = DIRECTORY_ITEMS;
	var LAST_FILE = "f" + FROWS;

	//Get id of current focus (before this event). If focus is in file list, ID = 'fn', or 'fnn', etc.
	var ID      = document.activeElement.id;
	var x_focus = ID.substr(0,1);

	//(F)ile (R)ow = 0,1, ... FROWS
	var FR = parseInt(ID.substr(1));
	if (isNaN(FR) || (x_focus != "f")) {FR = -1;} //If not in file list...

	//If current ID/element is checkbox, clear bgcolor of parent div (ckboxes don't have background colors).
	var is_ckbox = (document.activeElement.type == "checkbox");
	if (is_ckbox) {document.getElementById(ID).parentNode.style.backgroundColor = ""; }

	//Always clear these labels (simply losing focus() of their child checkboxes won't).
	document.getElementById('select_all_label').style.backgroundColor = "";
	document.getElementById('folders_first_label').style.backgroundColor = "";

	//If no files in current folder, [Move][Copy][Delete] won't exist (id's b1 b2 b3). Use [New Folder] (id="b4").
	if (document.getElementById("b2")) {var button_row = "b2"} else {var button_row = "b4"}

	//Indicate if current focus is on one of the elements of the table header row. (true / false)
	//Select All[ ] | [x](folders first) Name  (.ext) | Size |  Date  |
	var focus_header = ((ID == "select_all_ckbox") || (ID == "folders_first_ckbox") || (ID == "header_filename") ||
						(ID == "header_sorttype")  || (ID == "header_filesize")     || (ID == "header_filedate"));

	//Prep for Arrow Left/Right keys ...
	//To simulate Tab/Shift-tab, get list of all tab-able tags.
	//Below, will compare each tabindex to current tabindex, and allow for skips in tabindex.
	//All tab-able tags should have a tabindex = 1, 2, 3... etc, with no duplicates.
	if ((key == AL) || (key == AR)){
		var focus_tabindex = document.activeElement.tabIndex;

		//Need to check all elements on each onkeydown(), in case things change (like closing of message box).
		var all_tags     = document.getElementsByTagName('*');
		var tag_count    = all_tags.length;
		var tabindex_IDs = []; //Array of ID's of all tags with a tabindex, indexed by tabindex.

		//Create array of the ID's of all tags with a tabindex. (All tabable elements should have a tabindex set.)
		for (var x = 0; x < tag_count; x++) {
			ti = all_tags[x].tabIndex;
			if (ti > 0) { tabindex_IDs[ti] = all_tags[x].id; }
		}
	}

	//PROCESS THE KEYDOWN EVENT... In general:
	//  Tab- handle checkbox's (parent <div>'s & <label>'s), otherwise allow default action.
	//  Esc simply removes focus from active element.
	//	Home toggles between [webroot]/current/path/ and [../] (first file is list)
	//	End goes only to last file in list.
	//	Arrow Up/Down will loop from (top to bottom)/(bottom to top) of page (no hard stops).
	//  Page Up/Down will likewise loop thru page, with soft-stops at first/last filenames.
	//  Arrow Left/Right will function similarly to Tab/Shift-Tab, but hard stop at first/last link on page.

	if      (key == TAB)  { on_Tab_down(ID, FR, event.shiftKey); return; }
	else if (key == ESC)  { document.activeElement.blur();    return; }
	else if (key == END)  { if (ID != LAST_FILE) {ID = LAST_FILE} else {return} }
	else if (key == HOME) {
		if      (ID == "f0")     {ID = "path_0"}
		else if (ID == "path_0") {ID = "f0"}
		else if (FR  > 0)        {ID = "f0";}
		else					 {ID = "path_0"}
	}
	else if (key == AL) {
		//Find first tab-able element to the left (usually just (focus_tabindex - 1))
		for (var new_index = (focus_tabindex - 1); new_index > 0; new_index--) {
			if (tabindex_IDs[new_index]) { ID = tabindex_IDs[new_index]; break; }
		}
	}
	else if (key == AR) {
		//Find first tab-able element to the right (usually just (focus_tabindex + 1))
		for (var new_index = (focus_tabindex + 1); new_index < tabindex_IDs.length; new_index++) {
			if (tabindex_IDs[new_index]) { ID = tabindex_IDs[new_index]; break; }
		}
	}
	else if (ID == "admin") {
		if      (key == AU) {ID = LAST_FILE}
		else if (key == PU) {ID = LAST_FILE}
		else if (key == AD) {ID = "path_0"}
		else if (key == PD) {ID = "path_0"}
	}
	else if (x_focus == 'p') { //In path_header: webroot/current/path/
		if      (key == AU)   {ID = LAST_FILE}
		else if (key == PU)   {ID = LAST_FILE}
		else if (key == AD)   {ID = button_row}
		else if (key == PD)   {ID = "f0"}
	}
	else if (x_focus == "b") { //[Move][Copy][Delete]  [New Folder][New File][Upload File]
		if 		(key == AU) {ID = "path_0"		   }
		else if (key == PU) {ID = "path_0"		   }
		else if (key == AD) {ID = "header_filename"}
		else if (key == PD) {ID = "f0"			   }
	}
	else if (focus_header) { //Table header row
		if      (key == AU) {ID = button_row}
		else if (key == PU) {ID = "path_0"}
		else if	(key == AD) {ID = "f0"}
		else if	(key == PD) {FR += jump; if (FR < FROWS) {ID = "f" + FR} else {ID = LAST_FILE}}
	}
	else if ((FROWS == 0) && (key == PD) && (ID == 'f0')) {ID = "path_0"} //no files, only "../"
	else if (FR == 0) { //First row
		if		(key == AU) {ID = "header_filename"}
		else if	(key == PU) {ID = "path_0"}
		else if (key == AD) {FR++      ; if (FR <= FROWS) {ID = "f" + FR} else {ID = "path_0";}  }
		else if (key == PD) {FR += jump; if (FR <= FROWS) {ID = "f" + FR} else {ID = LAST_FILE;} }
	}
	else if (FR == FROWS) { //Last row
		if		(key == AU) { FR--      ; if (FR >= 0) {ID = "f" + FR} else {ID = "header_filename" } }
		else if	(key == PU) { FR -= jump; if (FR >= 0) {ID = "f" + FR} else {ID = "f0"} }
		else if (key == AD) { ID = "path_0" }
		else if (key == PD) { ID = "path_0" }
	}
	else if (FR > 0){ //Middle rows...
		if		(key == AU) { FR--;							ID = "f" + FR						  }
		else if	(key == PU) { FR -= jump; if (FR >= 0)     {ID = "f" + FR} else {ID = "f0"}	  	  }
		else if (key == AD) { FR++; 	  if (FR <= FROWS) {ID = "f" + FR} else {ID = "path_0"; } }
		else if (key == PD) { FR += jump; if (FR <= FROWS) {ID = "f" + FR} else {ID = LAST_FILE;} }
	}
	else if (FR == -1) {ID = "path_0"}     //Anyplace other than path_header, buttons, table
	else {
		//just in case I missed something...
		var error_message  = '<?php echo __LINE__ . $EX . '<b>' . hsc($_['Error']) . '</b> onkeydown(): ' ?>';
		    error_message += "key = " + key + ", FR = " + FR + ", ID = " + ID + ",x_focus = " + x_focus
		Display_Messages(error_message);
		return;
	}

	document.getElementById(ID).focus();

	//If new ID/element is checkbox, set bgcolor of parent div & it's label (ckboxes don't have background colors).
	if (document.activeElement.type == "checkbox") {document.getElementById(ID).parentNode.style.backgroundColor = highlight;}
	if (ID == "select_all_ckbox")    {document.getElementById('select_all_label').style.backgroundColor = highlight;}
	if (ID == "folders_first_ckbox") {document.getElementById('folders_first_label').style.backgroundColor = highlight;}

	//Prevent default browser scrolling via arrow & Page keys, so focus()'d element stays visible/in view port.
	//(A few exceptions skip this via a return in the above  if/else's.)
	if ( (ID != 'path_0') || ((ID == 'path_0') && (key == AD)) || ((ID == 'path_0') && (key == PD))) {
		if (event.preventDefault) { event.preventDefault() } else { event.returnValue = false }
	}
}//end onkeydown() //*************************************************
</script>
  <?php
}
//end Index_Page_events() //***************************************************
function Index_Page_scripts() {
//***********************************************
  global $_, $ONESCRIPT, $param1, $ipath, $message, $DELAY_Sort_and_Show_msgs, $MIN_DIR_ITEMS, $TABINDEX;
  ?>
<script>
//  DIRECTORY_DATA[x] = ("type", "file name", filesize, timestamp, is_ofcms, "ext")
var DIRECTORY_DATA	  = new Array();

var ONESCRIPT	= "<?php echo $ONESCRIPT ?>";
var PARAM1		= "<?php echo $param1 ?>";  //capitalized here as it is used as a constant.
var TABINDEX    = <?php echo $TABINDEX ?>;  //TABINDEX only used by js from this point on...

//a few usefull constants for using sort_DIRECTORY()
var DESCENDING	= 0;
var ASCENDING	= 1;
var FLIP		= 2; //Reverse the current direction (ascending <-> descending)
var FLIP_IF		= 3; //Flip only if new column selected.

//A few flags for using sort_DIRECTORY(). These are not constants.
var SORT_by		     = '1';   // Sort key (column) from DIRECTORY_DATA[x][key].
var SORT_order       = true;  // Default to "normal" sort orders (ascending). Set to false for reverse (descending).
var SORT_folders_1st = true;  // Initially set to true. false = did not consider folders during prior sort.




function Sort_Folders_First() {//*************************************

	//DIRECTORY_DATA[x] = ("type", "file name", filesize, timestamp, is_ofcms)

	var type = ""; //= row_data[0] = DIRECTORY_DATA[x][0]
	var files   = new Array();
	var folders = new Array();
	var row_data = new Array();
	var F = D = row = 0;  //indexes

	for (row = 0; row < DIRECTORY_DATA.length; row++) {;
		row_data = DIRECTORY_DATA[row];
		type     = row_data[0];
		if (type == "dir") { folders[D++] = row_data; }
		else 			   { files[F++]   = row_data; }
	}//end for

	//Replace contents of DIRECTORY_DATA[] with a "merged" folders[] & files[].
	DIRECTORY_DATA = new Array();
	row = 0
	for (D = 0; D < folders.length; D++) { DIRECTORY_DATA[row++] = folders[D]; }
	for (F = 0; F < files.length;   F++) { DIRECTORY_DATA[row++] = files[F];   }

	SORT_folders_1st = true;

}//end Sort_Folders_First() //****************************************




function sort_DIRECTORY(col, direction) {//***************************

	if (DIRECTORY_DATA.length < 2) {return} //can't sort 1 or zero items.

	//sort DIRECTORY_DATA[] by col and direction

	//col: 1 for "file name", 2 for filesize, 3 for timestamp, 5 for "ext"
	//derection: 0 = desending, 1 = ascending, 2 = flip, 3 = flip only if new col != SORT_by

	//SORT_by, SORT_order, and SORT_folders_1st: values set by prior (or initial) sort.

	//If needed, set default col and/or direction.
	col       = typeof col       !== 'undefined' ? col       : 1;
	direction = typeof direction !== 'undefined' ? direction : ASCENDING;

	//Filename ckboxes are cleared automatically on a resort, in Assemble_Insert_row(), so this needs cleared also.
	Select_All_ckbox.checked = false;

	//If new sort column, sort ascending. (FLIP overides, but is not currently used.)
	if ((col != SORT_by) && (direction != FLIP)) { direction = ASCENDING; SORT_by = col; }

	//Get status of [x](folders first) checkbox
	var folders_first_checked = document.getElementById('folders_first_ckbox').checked;

	//If '[x](folders first)' is now checked, but was previously unchecked,
	//no need to re-sort by col, just sort by folders. Otherwise, first resort by column.
	if ( !(folders_first_checked && !SORT_folders_1st) ) {

		if      ( direction == ASCENDING  ) { SORT_order = true;  }
		else if ( direction == DESCENDING ) { SORT_order = false; }
		else if ( direction == FLIP       ) { SORT_order = !SORT_order; }
		else if ( direction == FLIP_IF    ) { SORT_order = !SORT_order; }
		else                                { SORT_order = true; }

		if (col == 1 || col == 5) {  // If "file name" or "ext", sort alphabetically
			if (SORT_order) { DIRECTORY_DATA.sort( function (a, b) {return a[col].localeCompare(b[col]);} ); }
			else            { DIRECTORY_DATA.sort( function (b, a) {return a[col].localeCompare(b[col]);} ); }
		} else { //sort numerically
			if (SORT_order) { DIRECTORY_DATA.sort( function (a, b) {return a[col]       -       b[col] ;} ); }
			else            { DIRECTORY_DATA.sort( function (b, a) {return a[col]       -       b[col] ;} ); }
		}
	}//end if folders first only / full resort

	if (folders_first_checked == true) { Sort_Folders_First(); }

}//end sort_DIRECTORY() //********************************************




function Init_Dir_table_rows(DIR_LIST) {//****************************

	var row, cell, cells, tr, td;

	for (row = 0; row < DIRECTORY_ITEMS; row++){

		//initialize <tr> with empty <td>'s
		tr = DIR_LIST.insertRow(row);
		for (cell = 0; cell < 7; cell++) {td = tr.insertCell(-1);}
		cells = tr.cells;

		//assign css classes
		cells[4].className = 'file_name';
		cells[5].className = 'file_size meta_T';
		cells[6].className = 'file_time meta_T';
	}
}//end Init_Dir_table_rows() {//**************************************




//********************************************************************
function Assemble_Insert_row(IS_OFCMS, row, trow, href, f_or_f, filename, file_name, file_size, file_time){

	//While DIRECTORY_DATA, and the table rows created to list the data, are indexed from 0 (zero),
	//the id's of files in the directory list are indexed from 1 (f1, f2...), as "../" is listed first with id=f0 (f-zero).
	//The id's are used in Index_Page_events() "cursor" control.
	row++;

	//[Move] [Copy] [Delete]  [x]
	var ren_mov  = copy = del = checkbox = '';

	//Assemble [move] [copy] [delete] [x]   ([copy] is always available)
	//The empty <a>'s are to accommodate keyboard nav via onkeydown() in Index_Page_events()...
	if (!IS_OFCMS) {
		ren_mov  = '<a id=f' + row + 'c0 tabindex='+ (TABINDEX++) +' class=MCD href="' + href + '&amp;p=rename' + f_or_f + '" title="<?php echo hsc($_['Ren_Move']) ?>">' + ICONS['ren_mov'] + '</a>';
	} else {
		ren_mov  = '<a id=f' + row + 'c0 tabindex='+ (TABINDEX++) +'>&nbsp;</a>'
	}

	copy         = '<a id=f' + row + 'c1 tabindex='+ (TABINDEX++) +' class=MCD href="' + href + '&amp;p=copy'   + f_or_f + '" title="<?php echo hsc($_['Copy']) ?>">' + ICONS['copy']    + '</a>';

	if (!IS_OFCMS) {
		del      = '<a id=f' + row + 'c2 tabindex='+ (TABINDEX++) +' class=MCD href="' + href + '&amp;p=delete' + f_or_f + '" title="<?php echo hsc($_['Delete']) ?>">' + ICONS['delete']  + '</a>';
		checkbox = '<div class=ckbox><INPUT id=f' + row + 'c3 tabindex='+ (TABINDEX++) +' TYPE=checkbox class=select_file NAME="files[]"  VALUE="'+ hsc(filename) +'"></div>';
	} else {
		del      = '<a id=f' + row + 'c2 tabindex='+ (TABINDEX++) +'>&nbsp;</a>'
		checkbox = '<a id=f' + row + 'c3 tabindex='+ (TABINDEX++) +'>&nbsp;</a>'
	}

	//fill the <td>'s
	cells = trow.cells;
	cells[0].innerHTML = ren_mov;
	cells[1].innerHTML = copy;
	cells[2].innerHTML = del;
	cells[3].innerHTML = checkbox;
	cells[4].innerHTML = file_name;
	cells[5].innerHTML = file_size;
	cells[6].innerHTML = file_time;

}//end Assemble_Insert_row() //***************************************




function Build_Directory() {//****************************************

	TABINDEX    = <?php echo $TABINDEX ?>;  //Rest TABINDEX

	var DIR_LIST = document.getElementById("DIRECTORY_LISTING");

	if (DIR_LIST.rows.length < 1) {Init_Dir_table_rows(DIR_LIST);}

	for (var row = 0; row < DIRECTORY_ITEMS; row++) {

		var filetype = DIRECTORY_DATA[row][0];
		var filename = DIRECTORY_DATA[row][1];
		var filesize = DIRECTORY_DATA[row][2];
		var filetime = DIRECTORY_DATA[row][3];

		//folder or file?
		if (filetype == "dir"){
			var DS        = ' /';
			var f_or_f    = 'folder';
			var href      = ONESCRIPT + PARAM1 + encodeURIComponent(filename);
			var file_size = '';
		} else {
			var DS        = '';
			var f_or_f    = 'file';
			var href      = ONESCRIPT + PARAM1 + '&amp;f=' + encodeURIComponent(filename) + '&amp;p=edit';
			var file_size = format_number(filesize) + ' B';
		}

		//For file, (TABINDEX + 4) to account for [m][c][d][x] which are added in Assemble_Insert_Row()
		var file_name  = '<a id=f'+(row + 1)+' tabindex='+ (TABINDEX + 4) +' href="' + href  + '"';
			file_name += ' title="<?php echo hsc($_['Edit_View']) ?>: ' + hsc(filename) + '" >';
			file_name += ICONS[filetype] + '&nbsp;' + hsc(filename) + DS + '</a>';
		var file_time  = FileTimeStamp(filetime, 1, 0, 0);

		var IS_OFCMS = DIRECTORY_DATA[row][4];
		var trow = DIR_LIST.rows[row];

		Assemble_Insert_row(IS_OFCMS, row, trow, href, f_or_f, filename, file_name, file_size, file_time);
		TABINDEX++; //To accuont for file_name above
	}//end for (row...
}//end Build_Directory() //*******************************************




function Directory_Summary() {//**************************************

	var total_items  = DIRECTORY_DATA.length;
	var folder_count = 0;
	var total_bytes  = 0;
	var SUMMARY      = "";

	//Add up file sizes...
	for (x=0; x < DIRECTORY_DATA.length; x++) {
		filetype = DIRECTORY_DATA[x][0];
		filename = DIRECTORY_DATA[x][1];
		if (filetype == "dir"){ folder_count++; }
		total_bytes += DIRECTORY_DATA[x][2];
	}

	//Directory Summary
	SUMMARY += folder_count + " <?php echo hsc($_['folders']) ?>, &nbsp; ";
	SUMMARY += total_items - folder_count + ' <?php echo hsc($_['files']) ?>, ';
	SUMMARY += '&nbsp; ' + format_number(total_bytes) + " <?php echo hsc($_['bytes']) ?>";

	return SUMMARY;

}//end Directory_Summary() //*****************************************




function Sort_and_Show(col, direction) {//****************************

	var DELAY = 0;
	if (DIRECTORY_ITEMS > <?php echo $MIN_DIR_ITEMS ?>) { //
		//(Any pre-existing $message will be displayed after directory is displayed.)
		Display_Messages('<b><?php echo $_['Working'] ?></b>');

		DELAY = <?php echo $DELAY_Sort_and_Show_msgs ?>;
	}

	setTimeout( function () { //setTimeout() needed so 'Working' message will actually get displayed *before* the sort.
		sort_DIRECTORY(col, direction); //Sort DIRECTORY_DATA
		Build_Directory();
		document.getElementById('DIRECTORY_FOOTER').innerHTML = Directory_Summary();
		Display_Messages('');
	}, DELAY);

}//end Sort_and_Show() //*********************************************




function Select_All() {//********************************************

	//Does not work in IE if the variable name is spelled the same as the Element Id
	//So, prefix with a dollar sign (a valid character in JS for variable names).
	$select_all_label = document.getElementById('select_all_label');

	var files = document.mcdselect.elements['files[]'];
	var last  = files.length; //number of files
	var select_all = document.mcdselect.select_all;

	if (select_all.checked) {
		$select_all_label.innerHTML = '<?php echo hsc($_['Clear_All']) ?>';
	}else{
		$select_all_label.innerHTML = '<?php echo hsc($_['Select_All']) ?>';
	}

	//Start x at 1 as files[0] is a dummy <input> used to force an array even if only one file is in a folder.
	for (var x = 1; x < last ; x++) { files[x].checked = select_all.checked; }
}//end Select_All() //************************************************



function Confirm_Submit(action) {//***********************************

	var files = document.mcdselect.elements['files[]'];
	var last  = files.length;   //number of files
	var no_files = true;
	var f_msg    = "<?php echo hsc($_['No_files']) ?>";

	document.mcdselect.mcdaction.value = action;

	//Confirm at least one file is checked
	for (var x = 0; x < last ; x++) {
		if (files[x].checked) { no_files = false ; break; }
	}

	//Don't submit form if no files are checked.
	if ( no_files ) { Display_Messages(f_msg, 1); return false; }

	document.mcdselect.submit(); //submit form.
}//end Confirm_Submit() //********************************************

</script>
  <?php
}
//end Index_Page_scripts() //**************************************************
function Edit_Page_scripts() {
//************************************************
  global $_, $ONESCRIPT, $ONESCRIPT_file, $ipath, $param1, $param2, $filename, $MAIN_WIDTH, $WIDE_VIEW_WIDTH, $current_view, $WYSIWYG_VALID, $EDIT_WYSIWYG;
//Determine edit_view width.
  $current_view = $MAIN_WIDTH;
  if (isset ($_COOKIE['edit_view'])) {
    if (($_COOKIE['edit_view'] == $MAIN_WIDTH) || ($_COOKIE['edit_view'] == $WIDE_VIEW_WIDTH)) {
      $current_view = $_COOKIE['edit_view'];
    }
  }
//For [Edit WYSIWYG/Source] button
  $set_cookie = "document.cookie='edit_wysiwyg=" . (!$EDIT_WYSIWYG * 1) . "';";
  $WYSIWYG_onclick = "parent.location = onclick_params + 'edit'; " . $set_cookie;
//For [Close] button
  $close_params = $ONESCRIPT . $param1;
  if ($_SESSION['admin_page']) {
    $close_params .= '&p=admin';
  } //If came from admin page, return there.
  ?>
<script>
var onclick_params = '<?php echo $ONESCRIPT . $param1 . '&f=' . rawurlencode(basename($filename)) . '&p=' ?>';

var Main_div		   = document.getElementById('main');
var File_textarea      = document.getElementById('file_editor');
var View_Raw_button    = document.getElementById('view_raw');
var Wide_View_button   = document.getElementById('wide_view');
var WYSIWYG_button	   = document.getElementById('edit_WYSIWYG');
var Close_button       = document.getElementById('close1');
var Save_File_button   = document.getElementById('save_file');
var Reset_button       = document.getElementById('reset');
var Rename_File_button = document.getElementById('renamefile_btn');
var Copy_File_button   = document.getElementById('copyfile_btn');
var Delete_File_button = document.getElementById('deletefile_btn');

if (File_textarea) { var start_value = File_textarea.value; }

var submitted  = false;
var changed    = false;

//[Close], and [Copy], should always be present on Edit Page.
Close_button.onclick     = function () { parent.location = '<?php echo $close_params ?>'; }
Close_button.focus();
Copy_File_button.onclick = function () { parent.location = onclick_params + 'copyfile';   }

Main_div.style.width = "<?php echo $current_view ?>"; //Set current width

if ( Main_div.style.width == '<?php echo $WIDE_VIEW_WIDTH ?>' ) {
	Wide_View_button.innerHTML = '<?php echo hsc($_['Normal_View']) ?>';
}

//These elements do not exist if file is not editable, or maybe if in WYSIWYG mode.
if (View_Raw_button)    { View_Raw_button.onclick  = function ()   {window.open(onclick_params + 'raw_view'); } }
if (Wide_View_button)   { Wide_View_button.onclick = function ()   {Wide_View();}    }
if (Save_File_button)   { Save_File_button.onclick = function ()   {submitted=true;} }
if (WYSIWYG_button  )   { WYSIWYG_button.onclick = function ()     {<?php echo $WYSIWYG_onclick ?>} }
if (Rename_File_button) { Rename_File_button.onclick = function () {parent.location = onclick_params + 'renamefile';} }
if (Delete_File_button) { Delete_File_button.onclick = function () {parent.location = onclick_params + 'deletefile';} }
if (File_textarea)      { File_textarea.onkeyup = function(event)  {Check_for_changes(event);} }



window.onbeforeunload = function() {
	if ( changed && !submitted ) {
		//FF4+ Ingores the supplied msg below & only uses a system msg for the prompt.
		<?php ?>
		return "<?php echo addslashes($_['unload_unsaved']) ?>";
	}
}


window.onunload = function() {
	//without this, a browser back then forward would reload file with local/
	// unsaved changes, but with a green b/g as tho that's the file's saved contents.
	if (!submitted) {
		File_textarea.value = start_value;
		Reset_file_status_indicators();
	}
}



function Wide_View() {

	var main_width_default = '<?php echo $MAIN_WIDTH ?>';

	if ( File_textarea ) { File_textarea.style.width = '99.8%'; }

	if (Main_div.style.width == '<?php echo $WIDE_VIEW_WIDTH ?>') {
		Main_div.style.width       = main_width_default;
		Wide_View_button.innerHTML = "<?php echo hsc($_['Wide_View']) ?>";
		document.cookie            = 'edit_view=' + main_width_default;
	}else{
		Main_div.style.width       = '<?php echo $WIDE_VIEW_WIDTH ?>';
		Wide_View_button.innerHTML = '<?php echo hsc($_['Normal_View']) ?>';
		document.cookie            = 'edit_view=<?php echo $WIDE_VIEW_WIDTH ?>';
	}
}



function Reset_file_status_indicators() {
	changed = false;
	File_textarea.style.backgroundColor = "#F5FFF5";  //light green
	Save_File_button.style.borderColor  = "";
	Save_File_button.innerHTML          = "<?php echo hsc($_['save_1']) ?>";
	Reset_button.disabled               = "disabled";
}



//With selStart & selEnd == 0, moves cursor to start of text field.
function setSelRange(inputEl, selStart, selEnd) {
	if (inputEl.setSelectionRange) {
		inputEl.focus();
		inputEl.setSelectionRange(selStart, selEnd);
	} else if (inputEl.createTextRange) {
		var range = inputEl.createTextRange();
		range.collapse(true);
		range.moveEnd('character', selEnd);
		range.moveStart('character', selStart);
		range.select();
	}
}



function Check_for_changes(event){
	if (!event) {var event = window.event;} //if IE
	var keycode = event.keyCode? event.keyCode : event.charCode;
	changed = (File_textarea.value != start_value);
	if (changed){
		document.getElementById('message_box').innerHTML = " "; //Must have a space, or it won't clear the msg.
		File_textarea.style.backgroundColor    = "white";
		Save_File_button.style.borderColor	   = "#F33";
		Save_File_button.innerHTML			   = "<?php echo hsc($_['save_2']) ?>";
		Reset_button.disabled				   = "";
	}else{
		Reset_file_status_indicators()
	}
}


//Reset textarea value to when page was loaded.
//Used by [Reset] button, and when page unloads (browser back, etc).
//Needed becuase if the page is reloaded (ctl-r, or browser back/forward, etc.),
//the text stays changed, but "changed" gets set to false, which looses warning.
function Reset_File() {
    <?php ?>
	if (changed) { if ( !(confirm("<?php echo addslashes($_['confirm_reset']) ?>")) ) { return false; } }
	File_textarea.value = start_value;
	Reset_file_status_indicators();
	setSelRange(File_textarea, 0, 0) //Move cursor to start of textarea.
}


Reset_file_status_indicators();
</script>
  <?php
}
//end Edit_Page_scripts() //***************************************************
function pwun_event_scripts($form_id, $button_id, $pwun = '') {
//*****************
  global $_;
//pre-hash "new1" & "new2" only if changing p/w (not if changing u/n).
  $hash_new_new = '';
  if ($pwun == 'pw') {
    $hash_new_new = " hash('new1'); hash('new2');";
  }
//end if changing p/w --------------------------------------
  ?>
<script>
var $form          = document.getElementById('<?php echo $form_id ?>');
var $submit_button = document.getElementById('<?php echo $button_id ?>');
var $message_box   = document.getElementById('message_box');
var $thispage      = false; //Used to ignore keyup if keydown started on prior page.
var $submitdown    = false; //Used in document.mouseup event


//Key or mouse down events trigger "Working..." message.
$form.onkeydown            = function(event) {events_down(event, 13);} //Form captures Enter key (13)
$submit_button.onkeydown   = function(event) {events_down(event, 32);} //Submit button captures Space key (32)
$submit_button.onmousedown = function(event) {$submitdown = true; events_down(event,  0);}

//Key or mouse up events trigger hash and submit.
$form.onkeyup              = function(event) {events_up(event, 13);}
$submit_button.onkeyup     = function(event) {events_up(event, 32);}
$submit_button.onmouseup   = function(event) {events_up(event,  0);} //For mouse events, keyCode is 0 or undefined, and ignored.


function events_down(event, capture_key) {
	if (!event) {var event = window.event;} //if IE
	$thispage = true; //Make sure keydown was on this page.
	if ((event.type.substr(0,3) == 'key') && (event.keyCode != capture_key)) {return true;}
	$message_box.innerHTML = '<div class="message_box_contents"><b><?php echo hsc($_['Working']) ?></b>';
}


function events_up(event, capture_key) {
	if (!event) {var event = window.event;} //if IE
	if (!$thispage) {return false;} //Ignore keyup if there was no keydown on this page.
	if ((event.type.substr(0,3) == 'key') && (event.keyCode != capture_key)) {return true;}
	if (!pre_validate_pwun()) {return false};
	$submit_button.disabled = "disabled";  //Prevent extra clicks
	hash('password');
	<?php echo $hash_new_new ?>
	$form.submit();
}


document.onmouseup = function(event) {
	if (!event) {var event = window.event;} //if IE

	//if mousedown was on submit button, but mouseup wasn't, clear message.
	var target = event.target || event.srcElement; //target = most brosers || IE
	if ($submitdown && ($submit_button.id != target.id) ) { $message_box.innerHTML = ''; }
	$submitdown = false;
}


function pre_validate_pwun() {
	var $pw = document.getElementById('password');

	//These must exist for checks below.
	var $username = $pw;    var $new1 = $pw;    var $new2 = $pw;

	if (document.getElementById('username')){
		var $username = document.getElementById('username');
	}
	if (document.getElementById('new1')){
		var $new1 = document.getElementById('new1');
		var $new2 = document.getElementById('new2');
	}


	//If any field is blank..
	if (($username.value == '') || ($pw.value == '') || ($new1.value == '') || ($new2.value == '')) {
		$message_box.innerHTML = '<div class="message_box_contents"><b><?php echo hsc($_['change_pw_07']) ?></b>';
		return false;
	}
	//If new & confirm new values do not match...
	if (trim($new1.value) != trim($new2.value)) {
		$message_box.innerHTML = '<div class="message_box_contents"><b><?php echo hsc($_['change_pw_04']) ?></b>';
		return false;
	}
	return true;
}//end pre_validate_pwun()
</script>
  <?php
}
//end pwun_event_scripts() //**************************************************
function js_hash_scripts() {
//**************************************************
  global $SALT, $PRE_ITERATIONS;
//Used to hash p/w's client side.  This does not really add any security to the
//server side application that uses it, as the "pre-hash" becomes the actual p/w
//as far as the server is concerned, and is just as vulnerable to exposure while
//in transit. However, this does help to protect the user's plain-text p/w, which
//may be used elsewhere.
  ?>
<script>
/* hex_sha256() (and directly associated functions)
 *
 * A JavaScript implementation of SHA-256, as defined in FIPS 180-2
 * Version 2.2 Copyright Angel Marin, Paul Johnston 2000 - 2009.
 * Other contributors: Greg Holt, Andrew Kepert, Ydnar, Lostinet
 * Distributed under the BSD License
 * See http://pajhome.org.uk/crypt/md5 for details.
 * Also http://anmar.eu.org/projects/jssha2/
 */
var hexcase=0;function hex_sha256(a){return rstr2hex(rstr_sha256(str2rstr_utf8(a)))}function sha256_vm_test(){return hex_sha256("abc").toLowerCase()=="ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad"}function rstr_sha256(a){return binb2rstr(binb_sha256(rstr2binb(a),a.length*8))}function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}function rstr2binb(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(24-c%32)}return a}function binb2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(24-c%32))&255)}return a}function sha256_S(b,a){return(b>>>a)|(b<<(32-a))}function sha256_R(b,a){return(b>>>a)}function sha256_Ch(a,c,b){return((a&c)^((~a)&b))}function sha256_Maj(a,c,b){return((a&c)^(a&b)^(c&b))}function sha256_Sigma0256(a){return(sha256_S(a,2)^sha256_S(a,13)^sha256_S(a,22))}function sha256_Sigma1256(a){return(sha256_S(a,6)^sha256_S(a,11)^sha256_S(a,25))}function sha256_Gamma0256(a){return(sha256_S(a,7)^sha256_S(a,18)^sha256_R(a,3))}function sha256_Gamma1256(a){return(sha256_S(a,17)^sha256_S(a,19)^sha256_R(a,10))}function sha256_Sigma1512(a){return(sha256_S(a,14)^sha256_S(a,18)^sha256_S(a,41))}function sha256_Gamma1512(a){return(sha256_S(a,19)^sha256_S(a,61)^sha256_R(a,6))}var sha256_K=new Array(1116352408,1899447441,-1245643825,-373957723,961987163,1508970993,-1841331548,-1424204075,-670586216,310598401,607225278,1426881987,1925078388,-2132889090,-1680079193,-1046744716,-459576895,-272742522,264347078,604807628,770255983,1249150122,1555081692,1996064986,-1740746414,-1473132947,-1341970488,-1084653625,-958395405,-710438585,113926993,338241895,666307205,773529912,1294757372,1396182291,1695183700,1986661051,-2117940946,-1838011259,-1564481375,-1474664885,-1035236496,-949202525,-778901479,-694614492,-200395387,275423344,430227734,506948616,659060556,883997877,958139571,1322822218,1537002063,1747873779,1955562222,2024104815,-2067236844,-1933114872,-1866530822,-1538233109,-1090935817,-965641998);function binb_sha256(n,o){var p=new Array(1779033703,-1150833019,1013904242,-1521486534,1359893119,-1694144372,528734635,1541459225);var k=new Array(64);var B,A,z,y,w,u,t,s;var r,q,x,v;n[o>>5]|=128<<(24-o%32);n[((o+64>>9)<<4)+15]=o;for(r=0;r<n.length;r+=16){B=p[0];A=p[1];z=p[2];y=p[3];w=p[4];u=p[5];t=p[6];s=p[7];for(q=0;q<64;q++){if(q<16){k[q]=n[q+r]}else{k[q]=safe_add(safe_add(safe_add(sha256_Gamma1256(k[q-2]),k[q-7]),sha256_Gamma0256(k[q-15])),k[q-16])}x=safe_add(safe_add(safe_add(safe_add(s,sha256_Sigma1256(w)),sha256_Ch(w,u,t)),sha256_K[q]),k[q]);v=safe_add(sha256_Sigma0256(B),sha256_Maj(B,A,z));s=t;t=u;u=w;w=safe_add(y,x);y=z;z=A;A=B;B=safe_add(x,v)}p[0]=safe_add(B,p[0]);p[1]=safe_add(A,p[1]);p[2]=safe_add(z,p[2]);p[3]=safe_add(y,p[3]);p[4]=safe_add(w,p[4]);p[5]=safe_add(u,p[5]);p[6]=safe_add(t,p[6]);p[7]=safe_add(s,p[7])}return p}function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)};
</script>


<script>
//OneFileCMS wrapper function for using the hex_sha256() functions
function hash($element_id) {
	var $input = document.getElementById($element_id);
	var $hash = trim($input.value); //trim() defined in common_scripts()
	var $SALT = '<?php echo $SALT ?>';
	var $PRE_ITERATIONS = <?php echo $PRE_ITERATIONS ?>; //$PRE_ITERATIONS also used in hashit()
	if ($hash.length < 1) {$input.value = $hash; return;} //Don't hash nothing.
	for ( $x=0; $x < $PRE_ITERATIONS; $x++ ) { $hash = hex_sha256($hash + $SALT); } ;
	$input.value = $hash;
}//end hash()
</script>
  <?php
}
//end js_hash_scripts() //*****************************************************
function style_sheet() {
//******************************************************
  ?>

<style>
html{font-family:sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}body{margin:0}article,aside,details,figcaption,figure,footer,header,hgroup,main,menu,nav,section,summary{display:block}audio,canvas,progress,video{display:inline-block;vertical-align:baseline}audio:not([controls]){display:none;height:0}[hidden],template{display:none}a{background-color:transparent}a:active,a:hover{outline:0}abbr[title]{border-bottom:1px dotted}b,strong{font-weight:700}dfn{font-style:italic}h1{margin:.67em 0;font-size:2em}mark{color:#000;background:#ff0}small{font-size:80%}sub,sup{position:relative;font-size:75%;line-height:0;vertical-align:baseline}sup{top:-.5em}sub{bottom:-.25em}img{border:0}svg:not(:root){overflow:hidden}figure{margin:1em 40px}hr{height:0;-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box}pre{overflow:auto}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}button,input,optgroup,select,textarea{margin:0;font:inherit;color:inherit}button{overflow:visible}button,select{text-transform:none}button,html input[type=button],input[type=reset],input[type=submit]{-webkit-appearance:button;cursor:pointer}button[disabled],html input[disabled]{cursor:default}button::-moz-focus-inner,input::-moz-focus-inner{padding:0;border:0}input{line-height:normal}input[type=checkbox],input[type=radio]{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;padding:0}input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button{height:auto}input[type=search]{-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;-webkit-appearance:textfield}input[type=search]::-webkit-search-cancel-button,input[type=search]::-webkit-search-decoration{-webkit-appearance:none}fieldset{padding:.35em .625em .75em;margin:0 2px;border:1px solid silver}legend{padding:0;border:0}textarea{overflow:auto}optgroup{font-weight:700}table{border-spacing:0;border-collapse:collapse}td,th{padding:0}/*! Source: https://github.com/h5bp/html5-boilerplate/blob/master/src/css/main.css */@media print{*,:after,:before{color:#000!important;text-shadow:none!important;background:0 0!important;-webkit-box-shadow:none!important;box-shadow:none!important}a,a:visited{text-decoration:underline}a[href]:after{content:" (" attr(href) ")"}abbr[title]:after{content:" (" attr(title) ")"}a[href^="javascript:"]:after,a[href^="#"]:after{content:""}blockquote,pre{border:1px solid #999;page-break-inside:avoid}thead{display:table-header-group}img,tr{page-break-inside:avoid}img{max-width:100%!important}h2,h3,p{orphans:3;widows:3}h2,h3{page-break-after:avoid}select{background:#fff!important}.navbar{display:none}.btn>.caret,.dropup>.btn>.caret{border-top-color:#000!important}.label{border:1px solid #000}.table{border-collapse:collapse!important}.table td,.table th{background-color:#fff!important}.table-bordered td,.table-bordered th{border:1px solid #ddd!important}}@font-face{font-family:'Glyphicons Halflings';src:url(../fonts/glyphicons-halflings-regular.eot);src:url(../fonts/glyphicons-halflings-regular.eot?#iefix) format('embedded-opentype'),url(../fonts/glyphicons-halflings-regular.woff2) format('woff2'),url(../fonts/glyphicons-halflings-regular.woff) format('woff'),url(../fonts/glyphicons-halflings-regular.ttf) format('truetype'),url(../fonts/glyphicons-halflings-regular.svg#glyphicons_halflingsregular) format('svg')}.glyphicon{position:relative;top:1px;display:inline-block;font-family:'Glyphicons Halflings';font-style:normal;font-weight:400;line-height:1;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.glyphicon-asterisk:before{content:"\2a"}.glyphicon-plus:before{content:"\2b"}.glyphicon-eur:before,.glyphicon-euro:before{content:"\20ac"}.glyphicon-minus:before{content:"\2212"}.glyphicon-cloud:before{content:"\2601"}.glyphicon-envelope:before{content:"\2709"}.glyphicon-pencil:before{content:"\270f"}.glyphicon-glass:before{content:"\e001"}.glyphicon-music:before{content:"\e002"}.glyphicon-search:before{content:"\e003"}.glyphicon-heart:before{content:"\e005"}.glyphicon-star:before{content:"\e006"}.glyphicon-star-empty:before{content:"\e007"}.glyphicon-user:before{content:"\e008"}.glyphicon-film:before{content:"\e009"}.glyphicon-th-large:before{content:"\e010"}.glyphicon-th:before{content:"\e011"}.glyphicon-th-list:before{content:"\e012"}.glyphicon-ok:before{content:"\e013"}.glyphicon-remove:before{content:"\e014"}.glyphicon-zoom-in:before{content:"\e015"}.glyphicon-zoom-out:before{content:"\e016"}.glyphicon-off:before{content:"\e017"}.glyphicon-signal:before{content:"\e018"}.glyphicon-cog:before{content:"\e019"}.glyphicon-trash:before{content:"\e020"}.glyphicon-home:before{content:"\e021"}.glyphicon-file:before{content:"\e022"}.glyphicon-time:before{content:"\e023"}.glyphicon-road:before{content:"\e024"}.glyphicon-download-alt:before{content:"\e025"}.glyphicon-download:before{content:"\e026"}.glyphicon-upload:before{content:"\e027"}.glyphicon-inbox:before{content:"\e028"}.glyphicon-play-circle:before{content:"\e029"}.glyphicon-repeat:before{content:"\e030"}.glyphicon-refresh:before{content:"\e031"}.glyphicon-list-alt:before{content:"\e032"}.glyphicon-lock:before{content:"\e033"}.glyphicon-flag:before{content:"\e034"}.glyphicon-headphones:before{content:"\e035"}.glyphicon-volume-off:before{content:"\e036"}.glyphicon-volume-down:before{content:"\e037"}.glyphicon-volume-up:before{content:"\e038"}.glyphicon-qrcode:before{content:"\e039"}.glyphicon-barcode:before{content:"\e040"}.glyphicon-tag:before{content:"\e041"}.glyphicon-tags:before{content:"\e042"}.glyphicon-book:before{content:"\e043"}.glyphicon-bookmark:before{content:"\e044"}.glyphicon-print:before{content:"\e045"}.glyphicon-camera:before{content:"\e046"}.glyphicon-font:before{content:"\e047"}.glyphicon-bold:before{content:"\e048"}.glyphicon-italic:before{content:"\e049"}.glyphicon-text-height:before{content:"\e050"}.glyphicon-text-width:before{content:"\e051"}.glyphicon-align-left:before{content:"\e052"}.glyphicon-align-center:before{content:"\e053"}.glyphicon-align-right:before{content:"\e054"}.glyphicon-align-justify:before{content:"\e055"}.glyphicon-list:before{content:"\e056"}.glyphicon-indent-left:before{content:"\e057"}.glyphicon-indent-right:before{content:"\e058"}.glyphicon-facetime-video:before{content:"\e059"}.glyphicon-picture:before{content:"\e060"}.glyphicon-map-marker:before{content:"\e062"}.glyphicon-adjust:before{content:"\e063"}.glyphicon-tint:before{content:"\e064"}.glyphicon-edit:before{content:"\e065"}.glyphicon-share:before{content:"\e066"}.glyphicon-check:before{content:"\e067"}.glyphicon-move:before{content:"\e068"}.glyphicon-step-backward:before{content:"\e069"}.glyphicon-fast-backward:before{content:"\e070"}.glyphicon-backward:before{content:"\e071"}.glyphicon-play:before{content:"\e072"}.glyphicon-pause:before{content:"\e073"}.glyphicon-stop:before{content:"\e074"}.glyphicon-forward:before{content:"\e075"}.glyphicon-fast-forward:before{content:"\e076"}.glyphicon-step-forward:before{content:"\e077"}.glyphicon-eject:before{content:"\e078"}.glyphicon-chevron-left:before{content:"\e079"}.glyphicon-chevron-right:before{content:"\e080"}.glyphicon-plus-sign:before{content:"\e081"}.glyphicon-minus-sign:before{content:"\e082"}.glyphicon-remove-sign:before{content:"\e083"}.glyphicon-ok-sign:before{content:"\e084"}.glyphicon-question-sign:before{content:"\e085"}.glyphicon-info-sign:before{content:"\e086"}.glyphicon-screenshot:before{content:"\e087"}.glyphicon-remove-circle:before{content:"\e088"}.glyphicon-ok-circle:before{content:"\e089"}.glyphicon-ban-circle:before{content:"\e090"}.glyphicon-arrow-left:before{content:"\e091"}.glyphicon-arrow-right:before{content:"\e092"}.glyphicon-arrow-up:before{content:"\e093"}.glyphicon-arrow-down:before{content:"\e094"}.glyphicon-share-alt:before{content:"\e095"}.glyphicon-resize-full:before{content:"\e096"}.glyphicon-resize-small:before{content:"\e097"}.glyphicon-exclamation-sign:before{content:"\e101"}.glyphicon-gift:before{content:"\e102"}.glyphicon-leaf:before{content:"\e103"}.glyphicon-fire:before{content:"\e104"}.glyphicon-eye-open:before{content:"\e105"}.glyphicon-eye-close:before{content:"\e106"}.glyphicon-warning-sign:before{content:"\e107"}.glyphicon-plane:before{content:"\e108"}.glyphicon-calendar:before{content:"\e109"}.glyphicon-random:before{content:"\e110"}.glyphicon-comment:before{content:"\e111"}.glyphicon-magnet:before{content:"\e112"}.glyphicon-chevron-up:before{content:"\e113"}.glyphicon-chevron-down:before{content:"\e114"}.glyphicon-retweet:before{content:"\e115"}.glyphicon-shopping-cart:before{content:"\e116"}.glyphicon-folder-close:before{content:"\e117"}.glyphicon-folder-open:before{content:"\e118"}.glyphicon-resize-vertical:before{content:"\e119"}.glyphicon-resize-horizontal:before{content:"\e120"}.glyphicon-hdd:before{content:"\e121"}.glyphicon-bullhorn:before{content:"\e122"}.glyphicon-bell:before{content:"\e123"}.glyphicon-certificate:before{content:"\e124"}.glyphicon-thumbs-up:before{content:"\e125"}.glyphicon-thumbs-down:before{content:"\e126"}.glyphicon-hand-right:before{content:"\e127"}.glyphicon-hand-left:before{content:"\e128"}.glyphicon-hand-up:before{content:"\e129"}.glyphicon-hand-down:before{content:"\e130"}.glyphicon-circle-arrow-right:before{content:"\e131"}.glyphicon-circle-arrow-left:before{content:"\e132"}.glyphicon-circle-arrow-up:before{content:"\e133"}.glyphicon-circle-arrow-down:before{content:"\e134"}.glyphicon-globe:before{content:"\e135"}.glyphicon-wrench:before{content:"\e136"}.glyphicon-tasks:before{content:"\e137"}.glyphicon-filter:before{content:"\e138"}.glyphicon-briefcase:before{content:"\e139"}.glyphicon-fullscreen:before{content:"\e140"}.glyphicon-dashboard:before{content:"\e141"}.glyphicon-paperclip:before{content:"\e142"}.glyphicon-heart-empty:before{content:"\e143"}.glyphicon-link:before{content:"\e144"}.glyphicon-phone:before{content:"\e145"}.glyphicon-pushpin:before{content:"\e146"}.glyphicon-usd:before{content:"\e148"}.glyphicon-gbp:before{content:"\e149"}.glyphicon-sort:before{content:"\e150"}.glyphicon-sort-by-alphabet:before{content:"\e151"}.glyphicon-sort-by-alphabet-alt:before{content:"\e152"}.glyphicon-sort-by-order:before{content:"\e153"}.glyphicon-sort-by-order-alt:before{content:"\e154"}.glyphicon-sort-by-attributes:before{content:"\e155"}.glyphicon-sort-by-attributes-alt:before{content:"\e156"}.glyphicon-unchecked:before{content:"\e157"}.glyphicon-expand:before{content:"\e158"}.glyphicon-collapse-down:before{content:"\e159"}.glyphicon-collapse-up:before{content:"\e160"}.glyphicon-log-in:before{content:"\e161"}.glyphicon-flash:before{content:"\e162"}.glyphicon-log-out:before{content:"\e163"}.glyphicon-new-window:before{content:"\e164"}.glyphicon-record:before{content:"\e165"}.glyphicon-save:before{content:"\e166"}.glyphicon-open:before{content:"\e167"}.glyphicon-saved:before{content:"\e168"}.glyphicon-import:before{content:"\e169"}.glyphicon-export:before{content:"\e170"}.glyphicon-send:before{content:"\e171"}.glyphicon-floppy-disk:before{content:"\e172"}.glyphicon-floppy-saved:before{content:"\e173"}.glyphicon-floppy-remove:before{content:"\e174"}.glyphicon-floppy-save:before{content:"\e175"}.glyphicon-floppy-open:before{content:"\e176"}.glyphicon-credit-card:before{content:"\e177"}.glyphicon-transfer:before{content:"\e178"}.glyphicon-cutlery:before{content:"\e179"}.glyphicon-header:before{content:"\e180"}.glyphicon-compressed:before{content:"\e181"}.glyphicon-earphone:before{content:"\e182"}.glyphicon-phone-alt:before{content:"\e183"}.glyphicon-tower:before{content:"\e184"}.glyphicon-stats:before{content:"\e185"}.glyphicon-sd-video:before{content:"\e186"}.glyphicon-hd-video:before{content:"\e187"}.glyphicon-subtitles:before{content:"\e188"}.glyphicon-sound-stereo:before{content:"\e189"}.glyphicon-sound-dolby:before{content:"\e190"}.glyphicon-sound-5-1:before{content:"\e191"}.glyphicon-sound-6-1:before{content:"\e192"}.glyphicon-sound-7-1:before{content:"\e193"}.glyphicon-copyright-mark:before{content:"\e194"}.glyphicon-registration-mark:before{content:"\e195"}.glyphicon-cloud-download:before{content:"\e197"}.glyphicon-cloud-upload:before{content:"\e198"}.glyphicon-tree-conifer:before{content:"\e199"}.glyphicon-tree-deciduous:before{content:"\e200"}.glyphicon-cd:before{content:"\e201"}.glyphicon-save-file:before{content:"\e202"}.glyphicon-open-file:before{content:"\e203"}.glyphicon-level-up:before{content:"\e204"}.glyphicon-copy:before{content:"\e205"}.glyphicon-paste:before{content:"\e206"}.glyphicon-alert:before{content:"\e209"}.glyphicon-equalizer:before{content:"\e210"}.glyphicon-king:before{content:"\e211"}.glyphicon-queen:before{content:"\e212"}.glyphicon-pawn:before{content:"\e213"}.glyphicon-bishop:before{content:"\e214"}.glyphicon-knight:before{content:"\e215"}.glyphicon-baby-formula:before{content:"\e216"}.glyphicon-tent:before{content:"\26fa"}.glyphicon-blackboard:before{content:"\e218"}.glyphicon-bed:before{content:"\e219"}.glyphicon-apple:before{content:"\f8ff"}.glyphicon-erase:before{content:"\e221"}.glyphicon-hourglass:before{content:"\231b"}.glyphicon-lamp:before{content:"\e223"}.glyphicon-duplicate:before{content:"\e224"}.glyphicon-piggy-bank:before{content:"\e225"}.glyphicon-scissors:before{content:"\e226"}.glyphicon-bitcoin:before{content:"\e227"}.glyphicon-btc:before{content:"\e227"}.glyphicon-xbt:before{content:"\e227"}.glyphicon-yen:before{content:"\00a5"}.glyphicon-jpy:before{content:"\00a5"}.glyphicon-ruble:before{content:"\20bd"}.glyphicon-rub:before{content:"\20bd"}.glyphicon-scale:before{content:"\e230"}.glyphicon-ice-lolly:before{content:"\e231"}.glyphicon-ice-lolly-tasted:before{content:"\e232"}.glyphicon-education:before{content:"\e233"}.glyphicon-option-horizontal:before{content:"\e234"}.glyphicon-option-vertical:before{content:"\e235"}.glyphicon-menu-hamburger:before{content:"\e236"}.glyphicon-modal-window:before{content:"\e237"}.glyphicon-oil:before{content:"\e238"}.glyphicon-grain:before{content:"\e239"}.glyphicon-sunglasses:before{content:"\e240"}.glyphicon-text-size:before{content:"\e241"}.glyphicon-text-color:before{content:"\e242"}.glyphicon-text-background:before{content:"\e243"}.glyphicon-object-align-top:before{content:"\e244"}.glyphicon-object-align-bottom:before{content:"\e245"}.glyphicon-object-align-horizontal:before{content:"\e246"}.glyphicon-object-align-left:before{content:"\e247"}.glyphicon-object-align-vertical:before{content:"\e248"}.glyphicon-object-align-right:before{content:"\e249"}.glyphicon-triangle-right:before{content:"\e250"}.glyphicon-triangle-left:before{content:"\e251"}.glyphicon-triangle-bottom:before{content:"\e252"}.glyphicon-triangle-top:before{content:"\e253"}.glyphicon-console:before{content:"\e254"}.glyphicon-superscript:before{content:"\e255"}.glyphicon-subscript:before{content:"\e256"}.glyphicon-menu-left:before{content:"\e257"}.glyphicon-menu-right:before{content:"\e258"}.glyphicon-menu-down:before{content:"\e259"}.glyphicon-menu-up:before{content:"\e260"}*{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}:after,:before{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}html{font-size:10px;-webkit-tap-highlight-color:rgba(0,0,0,0)}body{font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;line-height:1.42857143;color:#333;background-color:#fff}button,input,select,textarea{font-family:inherit;font-size:inherit;line-height:inherit}a{color:#337ab7;text-decoration:none}a:focus,a:hover{color:#23527c;text-decoration:underline}a:focus{outline:thin dotted;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px}figure{margin:0}img{vertical-align:middle}.carousel-inner>.item>a>img,.carousel-inner>.item>img,.img-responsive,.thumbnail a>img,.thumbnail>img{display:block;max-width:100%;height:auto}.img-rounded{border-radius:6px}.img-thumbnail{display:inline-block;max-width:100%;height:auto;padding:4px;line-height:1.42857143;background-color:#fff;border:1px solid #ddd;border-radius:4px;-webkit-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;transition:all .2s ease-in-out}.img-circle{border-radius:50%}hr{margin-top:20px;margin-bottom:20px;border:0;border-top:1px solid #eee}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);border:0}.sr-only-focusable:active,.sr-only-focusable:focus{position:static;width:auto;height:auto;margin:0;overflow:visible;clip:auto}[role=button]{cursor:pointer}.h1,.h2,.h3,.h4,.h5,.h6,h1,h2,h3,h4,h5,h6{font-family:inherit;font-weight:500;line-height:1.1;color:inherit}.h1 .small,.h1 small,.h2 .small,.h2 small,.h3 .small,.h3 small,.h4 .small,.h4 small,.h5 .small,.h5 small,.h6 .small,.h6 small,h1 .small,h1 small,h2 .small,h2 small,h3 .small,h3 small,h4 .small,h4 small,h5 .small,h5 small,h6 .small,h6 small{font-weight:400;line-height:1;color:#777}.h1,.h2,.h3,h1,h2,h3{margin-top:20px;margin-bottom:10px}.h1 .small,.h1 small,.h2 .small,.h2 small,.h3 .small,.h3 small,h1 .small,h1 small,h2 .small,h2 small,h3 .small,h3 small{font-size:65%}.h4,.h5,.h6,h4,h5,h6{margin-top:10px;margin-bottom:10px}.h4 .small,.h4 small,.h5 .small,.h5 small,.h6 .small,.h6 small,h4 .small,h4 small,h5 .small,h5 small,h6 .small,h6 small{font-size:75%}.h1,h1{font-size:36px}.h2,h2{font-size:30px}.h3,h3{font-size:24px}.h4,h4{font-size:18px}.h5,h5{font-size:14px}.h6,h6{font-size:12px}p{margin:0 0 10px}.lead{margin-bottom:20px;font-size:16px;font-weight:300;line-height:1.4}@media (min-width:768px){.lead{font-size:21px}}.small,small{font-size:85%}.mark,mark{padding:.2em;background-color:#fcf8e3}.text-left{text-align:left}.text-right{text-align:right}.text-center{text-align:center}.text-justify{text-align:justify}.text-nowrap{white-space:nowrap}.text-lowercase{text-transform:lowercase}.text-uppercase{text-transform:uppercase}.text-capitalize{text-transform:capitalize}.text-muted{color:#777}.text-primary{color:#337ab7}a.text-primary:hover{color:#286090}.text-success{color:#3c763d}a.text-success:hover{color:#2b542c}.text-info{color:#31708f}a.text-info:hover{color:#245269}.text-warning{color:#8a6d3b}a.text-warning:hover{color:#66512c}.text-danger{color:#a94442}a.text-danger:hover{color:#843534}.bg-primary{color:#fff;background-color:#337ab7}a.bg-primary:hover{background-color:#286090}.bg-success{background-color:#dff0d8}a.bg-success:hover{background-color:#c1e2b3}.bg-info{background-color:#d9edf7}a.bg-info:hover{background-color:#afd9ee}.bg-warning{background-color:#fcf8e3}a.bg-warning:hover{background-color:#f7ecb5}.bg-danger{background-color:#f2dede}a.bg-danger:hover{background-color:#e4b9b9}.page-header{padding-bottom:9px;margin:40px 0 20px;border-bottom:1px solid #eee}ol,ul{margin-top:0;margin-bottom:10px}ol ol,ol ul,ul ol,ul ul{margin-bottom:0}.list-unstyled{padding-left:0;list-style:none}.list-inline{padding-left:0;margin-left:-5px;list-style:none}.list-inline>li{display:inline-block;padding-right:5px;padding-left:5px}dl{margin-top:0;margin-bottom:20px}dd,dt{line-height:1.42857143}dt{font-weight:700}dd{margin-left:0}@media (min-width:768px){.dl-horizontal dt{float:left;width:160px;overflow:hidden;clear:left;text-align:right;text-overflow:ellipsis;white-space:nowrap}.dl-horizontal dd{margin-left:180px}}abbr[data-original-title],abbr[title]{cursor:help;border-bottom:1px dotted #777}.initialism{font-size:90%;text-transform:uppercase}blockquote{padding:10px 20px;margin:0 0 20px;font-size:17.5px;border-left:5px solid #eee}blockquote ol:last-child,blockquote p:last-child,blockquote ul:last-child{margin-bottom:0}blockquote .small,blockquote footer,blockquote small{display:block;font-size:80%;line-height:1.42857143;color:#777}blockquote .small:before,blockquote footer:before,blockquote small:before{content:'\2014 \00A0'}.blockquote-reverse,blockquote.pull-right{padding-right:15px;padding-left:0;text-align:right;border-right:5px solid #eee;border-left:0}.blockquote-reverse .small:before,.blockquote-reverse footer:before,.blockquote-reverse small:before,blockquote.pull-right .small:before,blockquote.pull-right footer:before,blockquote.pull-right small:before{content:''}.blockquote-reverse .small:after,.blockquote-reverse footer:after,.blockquote-reverse small:after,blockquote.pull-right .small:after,blockquote.pull-right footer:after,blockquote.pull-right small:after{content:'\00A0 \2014'}address{margin-bottom:20px;font-style:normal;line-height:1.42857143}code,kbd,pre,samp{font-family:Menlo,Monaco,Consolas,"Courier New",monospace}code{padding:2px 4px;font-size:90%;color:#c7254e;background-color:#f9f2f4;border-radius:4px}kbd{padding:2px 4px;font-size:90%;color:#fff;background-color:#333;border-radius:3px;-webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,.25);box-shadow:inset 0 -1px 0 rgba(0,0,0,.25)}kbd kbd{padding:0;font-size:100%;font-weight:700;-webkit-box-shadow:none;box-shadow:none}pre{display:block;padding:9.5px;margin:0 0 10px;font-size:13px;line-height:1.42857143;color:#333;word-break:break-all;word-wrap:break-word;background-color:#f5f5f5;border:1px solid #ccc;border-radius:4px}pre code{padding:0;font-size:inherit;color:inherit;white-space:pre-wrap;background-color:transparent;border-radius:0}.pre-scrollable{max-height:340px;overflow-y:scroll}.container{padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto}@media (min-width:768px){.container{width:750px}}@media (min-width:992px){.container{width:970px}}@media (min-width:1200px){.container{width:1170px}}.container-fluid{padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto}.row{margin-right:-15px;margin-left:-15px}.col-lg-1,.col-lg-10,.col-lg-11,.col-lg-12,.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.col-lg-7,.col-lg-8,.col-lg-9,.col-md-1,.col-md-10,.col-md-11,.col-md-12,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9,.col-sm-1,.col-sm-10,.col-sm-11,.col-sm-12,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9,.col-xs-1,.col-xs-10,.col-xs-11,.col-xs-12,.col-xs-2,.col-xs-3,.col-xs-4,.col-xs-5,.col-xs-6,.col-xs-7,.col-xs-8,.col-xs-9{position:relative;min-height:1px;padding-right:15px;padding-left:15px}.col-xs-1,.col-xs-10,.col-xs-11,.col-xs-12,.col-xs-2,.col-xs-3,.col-xs-4,.col-xs-5,.col-xs-6,.col-xs-7,.col-xs-8,.col-xs-9{float:left}.col-xs-12{width:100%}.col-xs-11{width:91.66666667%}.col-xs-10{width:83.33333333%}.col-xs-9{width:75%}.col-xs-8{width:66.66666667%}.col-xs-7{width:58.33333333%}.col-xs-6{width:50%}.col-xs-5{width:41.66666667%}.col-xs-4{width:33.33333333%}.col-xs-3{width:25%}.col-xs-2{width:16.66666667%}.col-xs-1{width:8.33333333%}.col-xs-pull-12{right:100%}.col-xs-pull-11{right:91.66666667%}.col-xs-pull-10{right:83.33333333%}.col-xs-pull-9{right:75%}.col-xs-pull-8{right:66.66666667%}.col-xs-pull-7{right:58.33333333%}.col-xs-pull-6{right:50%}.col-xs-pull-5{right:41.66666667%}.col-xs-pull-4{right:33.33333333%}.col-xs-pull-3{right:25%}.col-xs-pull-2{right:16.66666667%}.col-xs-pull-1{right:8.33333333%}.col-xs-pull-0{right:auto}.col-xs-push-12{left:100%}.col-xs-push-11{left:91.66666667%}.col-xs-push-10{left:83.33333333%}.col-xs-push-9{left:75%}.col-xs-push-8{left:66.66666667%}.col-xs-push-7{left:58.33333333%}.col-xs-push-6{left:50%}.col-xs-push-5{left:41.66666667%}.col-xs-push-4{left:33.33333333%}.col-xs-push-3{left:25%}.col-xs-push-2{left:16.66666667%}.col-xs-push-1{left:8.33333333%}.col-xs-push-0{left:auto}.col-xs-offset-12{margin-left:100%}.col-xs-offset-11{margin-left:91.66666667%}.col-xs-offset-10{margin-left:83.33333333%}.col-xs-offset-9{margin-left:75%}.col-xs-offset-8{margin-left:66.66666667%}.col-xs-offset-7{margin-left:58.33333333%}.col-xs-offset-6{margin-left:50%}.col-xs-offset-5{margin-left:41.66666667%}.col-xs-offset-4{margin-left:33.33333333%}.col-xs-offset-3{margin-left:25%}.col-xs-offset-2{margin-left:16.66666667%}.col-xs-offset-1{margin-left:8.33333333%}.col-xs-offset-0{margin-left:0}@media (min-width:768px){.col-sm-1,.col-sm-10,.col-sm-11,.col-sm-12,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9{float:left}.col-sm-12{width:100%}.col-sm-11{width:91.66666667%}.col-sm-10{width:83.33333333%}.col-sm-9{width:75%}.col-sm-8{width:66.66666667%}.col-sm-7{width:58.33333333%}.col-sm-6{width:50%}.col-sm-5{width:41.66666667%}.col-sm-4{width:33.33333333%}.col-sm-3{width:25%}.col-sm-2{width:16.66666667%}.col-sm-1{width:8.33333333%}.col-sm-pull-12{right:100%}.col-sm-pull-11{right:91.66666667%}.col-sm-pull-10{right:83.33333333%}.col-sm-pull-9{right:75%}.col-sm-pull-8{right:66.66666667%}.col-sm-pull-7{right:58.33333333%}.col-sm-pull-6{right:50%}.col-sm-pull-5{right:41.66666667%}.col-sm-pull-4{right:33.33333333%}.col-sm-pull-3{right:25%}.col-sm-pull-2{right:16.66666667%}.col-sm-pull-1{right:8.33333333%}.col-sm-pull-0{right:auto}.col-sm-push-12{left:100%}.col-sm-push-11{left:91.66666667%}.col-sm-push-10{left:83.33333333%}.col-sm-push-9{left:75%}.col-sm-push-8{left:66.66666667%}.col-sm-push-7{left:58.33333333%}.col-sm-push-6{left:50%}.col-sm-push-5{left:41.66666667%}.col-sm-push-4{left:33.33333333%}.col-sm-push-3{left:25%}.col-sm-push-2{left:16.66666667%}.col-sm-push-1{left:8.33333333%}.col-sm-push-0{left:auto}.col-sm-offset-12{margin-left:100%}.col-sm-offset-11{margin-left:91.66666667%}.col-sm-offset-10{margin-left:83.33333333%}.col-sm-offset-9{margin-left:75%}.col-sm-offset-8{margin-left:66.66666667%}.col-sm-offset-7{margin-left:58.33333333%}.col-sm-offset-6{margin-left:50%}.col-sm-offset-5{margin-left:41.66666667%}.col-sm-offset-4{margin-left:33.33333333%}.col-sm-offset-3{margin-left:25%}.col-sm-offset-2{margin-left:16.66666667%}.col-sm-offset-1{margin-left:8.33333333%}.col-sm-offset-0{margin-left:0}}@media (min-width:992px){.col-md-1,.col-md-10,.col-md-11,.col-md-12,.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6,.col-md-7,.col-md-8,.col-md-9{float:left}.col-md-12{width:100%}.col-md-11{width:91.66666667%}.col-md-10{width:83.33333333%}.col-md-9{width:75%}.col-md-8{width:66.66666667%}.col-md-7{width:58.33333333%}.col-md-6{width:50%}.col-md-5{width:41.66666667%}.col-md-4{width:33.33333333%}.col-md-3{width:25%}.col-md-2{width:16.66666667%}.col-md-1{width:8.33333333%}.col-md-pull-12{right:100%}.col-md-pull-11{right:91.66666667%}.col-md-pull-10{right:83.33333333%}.col-md-pull-9{right:75%}.col-md-pull-8{right:66.66666667%}.col-md-pull-7{right:58.33333333%}.col-md-pull-6{right:50%}.col-md-pull-5{right:41.66666667%}.col-md-pull-4{right:33.33333333%}.col-md-pull-3{right:25%}.col-md-pull-2{right:16.66666667%}.col-md-pull-1{right:8.33333333%}.col-md-pull-0{right:auto}.col-md-push-12{left:100%}.col-md-push-11{left:91.66666667%}.col-md-push-10{left:83.33333333%}.col-md-push-9{left:75%}.col-md-push-8{left:66.66666667%}.col-md-push-7{left:58.33333333%}.col-md-push-6{left:50%}.col-md-push-5{left:41.66666667%}.col-md-push-4{left:33.33333333%}.col-md-push-3{left:25%}.col-md-push-2{left:16.66666667%}.col-md-push-1{left:8.33333333%}.col-md-push-0{left:auto}.col-md-offset-12{margin-left:100%}.col-md-offset-11{margin-left:91.66666667%}.col-md-offset-10{margin-left:83.33333333%}.col-md-offset-9{margin-left:75%}.col-md-offset-8{margin-left:66.66666667%}.col-md-offset-7{margin-left:58.33333333%}.col-md-offset-6{margin-left:50%}.col-md-offset-5{margin-left:41.66666667%}.col-md-offset-4{margin-left:33.33333333%}.col-md-offset-3{margin-left:25%}.col-md-offset-2{margin-left:16.66666667%}.col-md-offset-1{margin-left:8.33333333%}.col-md-offset-0{margin-left:0}}@media (min-width:1200px){.col-lg-1,.col-lg-10,.col-lg-11,.col-lg-12,.col-lg-2,.col-lg-3,.col-lg-4,.col-lg-5,.col-lg-6,.col-lg-7,.col-lg-8,.col-lg-9{float:left}.col-lg-12{width:100%}.col-lg-11{width:91.66666667%}.col-lg-10{width:83.33333333%}.col-lg-9{width:75%}.col-lg-8{width:66.66666667%}.col-lg-7{width:58.33333333%}.col-lg-6{width:50%}.col-lg-5{width:41.66666667%}.col-lg-4{width:33.33333333%}.col-lg-3{width:25%}.col-lg-2{width:16.66666667%}.col-lg-1{width:8.33333333%}.col-lg-pull-12{right:100%}.col-lg-pull-11{right:91.66666667%}.col-lg-pull-10{right:83.33333333%}.col-lg-pull-9{right:75%}.col-lg-pull-8{right:66.66666667%}.col-lg-pull-7{right:58.33333333%}.col-lg-pull-6{right:50%}.col-lg-pull-5{right:41.66666667%}.col-lg-pull-4{right:33.33333333%}.col-lg-pull-3{right:25%}.col-lg-pull-2{right:16.66666667%}.col-lg-pull-1{right:8.33333333%}.col-lg-pull-0{right:auto}.col-lg-push-12{left:100%}.col-lg-push-11{left:91.66666667%}.col-lg-push-10{left:83.33333333%}.col-lg-push-9{left:75%}.col-lg-push-8{left:66.66666667%}.col-lg-push-7{left:58.33333333%}.col-lg-push-6{left:50%}.col-lg-push-5{left:41.66666667%}.col-lg-push-4{left:33.33333333%}.col-lg-push-3{left:25%}.col-lg-push-2{left:16.66666667%}.col-lg-push-1{left:8.33333333%}.col-lg-push-0{left:auto}.col-lg-offset-12{margin-left:100%}.col-lg-offset-11{margin-left:91.66666667%}.col-lg-offset-10{margin-left:83.33333333%}.col-lg-offset-9{margin-left:75%}.col-lg-offset-8{margin-left:66.66666667%}.col-lg-offset-7{margin-left:58.33333333%}.col-lg-offset-6{margin-left:50%}.col-lg-offset-5{margin-left:41.66666667%}.col-lg-offset-4{margin-left:33.33333333%}.col-lg-offset-3{margin-left:25%}.col-lg-offset-2{margin-left:16.66666667%}.col-lg-offset-1{margin-left:8.33333333%}.col-lg-offset-0{margin-left:0}}table{background-color:transparent}caption{padding-top:8px;padding-bottom:8px;color:#777;text-align:left}th{text-align:left}.table{width:100%;max-width:100%;margin-bottom:20px}.table>tbody>tr>td,.table>tbody>tr>th,.table>tfoot>tr>td,.table>tfoot>tr>th,.table>thead>tr>td,.table>thead>tr>th{padding:8px;line-height:1.42857143;vertical-align:top;border-top:1px solid #ddd}.table>thead>tr>th{vertical-align:bottom;border-bottom:2px solid #ddd}.table>caption+thead>tr:first-child>td,.table>caption+thead>tr:first-child>th,.table>colgroup+thead>tr:first-child>td,.table>colgroup+thead>tr:first-child>th,.table>thead:first-child>tr:first-child>td,.table>thead:first-child>tr:first-child>th{border-top:0}.table>tbody+tbody{border-top:2px solid #ddd}.table .table{background-color:#fff}.table-condensed>tbody>tr>td,.table-condensed>tbody>tr>th,.table-condensed>tfoot>tr>td,.table-condensed>tfoot>tr>th,.table-condensed>thead>tr>td,.table-condensed>thead>tr>th{padding:5px}.table-bordered{border:1px solid #ddd}.table-bordered>tbody>tr>td,.table-bordered>tbody>tr>th,.table-bordered>tfoot>tr>td,.table-bordered>tfoot>tr>th,.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border:1px solid #ddd}.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border-bottom-width:2px}.table-striped>tbody>tr:nth-of-type(odd){background-color:#f9f9f9}.table-hover>tbody>tr:hover{background-color:#f5f5f5}table col[class*=col-]{position:static;display:table-column;float:none}table td[class*=col-],table th[class*=col-]{position:static;display:table-cell;float:none}.table>tbody>tr.active>td,.table>tbody>tr.active>th,.table>tbody>tr>td.active,.table>tbody>tr>th.active,.table>tfoot>tr.active>td,.table>tfoot>tr.active>th,.table>tfoot>tr>td.active,.table>tfoot>tr>th.active,.table>thead>tr.active>td,.table>thead>tr.active>th,.table>thead>tr>td.active,.table>thead>tr>th.active{background-color:#f5f5f5}.table-hover>tbody>tr.active:hover>td,.table-hover>tbody>tr.active:hover>th,.table-hover>tbody>tr:hover>.active,.table-hover>tbody>tr>td.active:hover,.table-hover>tbody>tr>th.active:hover{background-color:#e8e8e8}.table>tbody>tr.success>td,.table>tbody>tr.success>th,.table>tbody>tr>td.success,.table>tbody>tr>th.success,.table>tfoot>tr.success>td,.table>tfoot>tr.success>th,.table>tfoot>tr>td.success,.table>tfoot>tr>th.success,.table>thead>tr.success>td,.table>thead>tr.success>th,.table>thead>tr>td.success,.table>thead>tr>th.success{background-color:#dff0d8}.table-hover>tbody>tr.success:hover>td,.table-hover>tbody>tr.success:hover>th,.table-hover>tbody>tr:hover>.success,.table-hover>tbody>tr>td.success:hover,.table-hover>tbody>tr>th.success:hover{background-color:#d0e9c6}.table>tbody>tr.info>td,.table>tbody>tr.info>th,.table>tbody>tr>td.info,.table>tbody>tr>th.info,.table>tfoot>tr.info>td,.table>tfoot>tr.info>th,.table>tfoot>tr>td.info,.table>tfoot>tr>th.info,.table>thead>tr.info>td,.table>thead>tr.info>th,.table>thead>tr>td.info,.table>thead>tr>th.info{background-color:#d9edf7}.table-hover>tbody>tr.info:hover>td,.table-hover>tbody>tr.info:hover>th,.table-hover>tbody>tr:hover>.info,.table-hover>tbody>tr>td.info:hover,.table-hover>tbody>tr>th.info:hover{background-color:#c4e3f3}.table>tbody>tr.warning>td,.table>tbody>tr.warning>th,.table>tbody>tr>td.warning,.table>tbody>tr>th.warning,.table>tfoot>tr.warning>td,.table>tfoot>tr.warning>th,.table>tfoot>tr>td.warning,.table>tfoot>tr>th.warning,.table>thead>tr.warning>td,.table>thead>tr.warning>th,.table>thead>tr>td.warning,.table>thead>tr>th.warning{background-color:#fcf8e3}.table-hover>tbody>tr.warning:hover>td,.table-hover>tbody>tr.warning:hover>th,.table-hover>tbody>tr:hover>.warning,.table-hover>tbody>tr>td.warning:hover,.table-hover>tbody>tr>th.warning:hover{background-color:#faf2cc}.table>tbody>tr.danger>td,.table>tbody>tr.danger>th,.table>tbody>tr>td.danger,.table>tbody>tr>th.danger,.table>tfoot>tr.danger>td,.table>tfoot>tr.danger>th,.table>tfoot>tr>td.danger,.table>tfoot>tr>th.danger,.table>thead>tr.danger>td,.table>thead>tr.danger>th,.table>thead>tr>td.danger,.table>thead>tr>th.danger{background-color:#f2dede}.table-hover>tbody>tr.danger:hover>td,.table-hover>tbody>tr.danger:hover>th,.table-hover>tbody>tr:hover>.danger,.table-hover>tbody>tr>td.danger:hover,.table-hover>tbody>tr>th.danger:hover{background-color:#ebcccc}.table-responsive{min-height:.01%;overflow-x:auto}@media screen and (max-width:767px){.table-responsive{width:100%;margin-bottom:15px;overflow-y:hidden;-ms-overflow-style:-ms-autohiding-scrollbar;border:1px solid #ddd}.table-responsive>.table{margin-bottom:0}.table-responsive>.table>tbody>tr>td,.table-responsive>.table>tbody>tr>th,.table-responsive>.table>tfoot>tr>td,.table-responsive>.table>tfoot>tr>th,.table-responsive>.table>thead>tr>td,.table-responsive>.table>thead>tr>th{white-space:nowrap}.table-responsive>.table-bordered{border:0}.table-responsive>.table-bordered>tbody>tr>td:first-child,.table-responsive>.table-bordered>tbody>tr>th:first-child,.table-responsive>.table-bordered>tfoot>tr>td:first-child,.table-responsive>.table-bordered>tfoot>tr>th:first-child,.table-responsive>.table-bordered>thead>tr>td:first-child,.table-responsive>.table-bordered>thead>tr>th:first-child{border-left:0}.table-responsive>.table-bordered>tbody>tr>td:last-child,.table-responsive>.table-bordered>tbody>tr>th:last-child,.table-responsive>.table-bordered>tfoot>tr>td:last-child,.table-responsive>.table-bordered>tfoot>tr>th:last-child,.table-responsive>.table-bordered>thead>tr>td:last-child,.table-responsive>.table-bordered>thead>tr>th:last-child{border-right:0}.table-responsive>.table-bordered>tbody>tr:last-child>td,.table-responsive>.table-bordered>tbody>tr:last-child>th,.table-responsive>.table-bordered>tfoot>tr:last-child>td,.table-responsive>.table-bordered>tfoot>tr:last-child>th{border-bottom:0}}fieldset{min-width:0;padding:0;margin:0;border:0}legend{display:block;width:100%;padding:0;margin-bottom:20px;font-size:21px;line-height:inherit;color:#333;border:0;border-bottom:1px solid #e5e5e5}label{display:inline-block;max-width:100%;margin-bottom:5px;font-weight:700}input[type=search]{-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}input[type=checkbox],input[type=radio]{margin:4px 0 0;margin-top:1px \9;line-height:normal}input[type=file]{display:block}input[type=range]{display:block;width:100%}select[multiple],select[size]{height:auto}input[type=file]:focus,input[type=checkbox]:focus,input[type=radio]:focus{outline:thin dotted;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px}output{display:block;padding-top:7px;font-size:14px;line-height:1.42857143;color:#555}.form-control{display:block;width:100%;height:34px;padding:6px 12px;font-size:14px;line-height:1.42857143;color:#555;background-color:#fff;background-image:none;border:1px solid #ccc;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075);-webkit-transition:border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;-o-transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s;transition:border-color ease-in-out .15s,box-shadow ease-in-out .15s}.form-control:focus{border-color:#66afe9;outline:0;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6);box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 8px rgba(102,175,233,.6)}.form-control::-moz-placeholder{color:#999;opacity:1}.form-control:-ms-input-placeholder{color:#999}.form-control::-webkit-input-placeholder{color:#999}.form-control[disabled],.form-control[readonly],fieldset[disabled] .form-control{background-color:#eee;opacity:1}.form-control[disabled],fieldset[disabled] .form-control{cursor:not-allowed}textarea.form-control{height:auto}input[type=search]{-webkit-appearance:none}@media screen and (-webkit-min-device-pixel-ratio:0){input[type=date],input[type=time],input[type=datetime-local],input[type=month]{line-height:34px}.input-group-sm input[type=date],.input-group-sm input[type=time],.input-group-sm input[type=datetime-local],.input-group-sm input[type=month],input[type=date].input-sm,input[type=time].input-sm,input[type=datetime-local].input-sm,input[type=month].input-sm{line-height:30px}.input-group-lg input[type=date],.input-group-lg input[type=time],.input-group-lg input[type=datetime-local],.input-group-lg input[type=month],input[type=date].input-lg,input[type=time].input-lg,input[type=datetime-local].input-lg,input[type=month].input-lg{line-height:46px}}.form-group{margin-bottom:15px}.checkbox,.radio{position:relative;display:block;margin-top:10px;margin-bottom:10px}.checkbox label,.radio label{min-height:20px;padding-left:20px;margin-bottom:0;font-weight:400;cursor:pointer}.checkbox input[type=checkbox],.checkbox-inline input[type=checkbox],.radio input[type=radio],.radio-inline input[type=radio]{position:absolute;margin-top:4px \9;margin-left:-20px}.checkbox+.checkbox,.radio+.radio{margin-top:-5px}.checkbox-inline,.radio-inline{position:relative;display:inline-block;padding-left:20px;margin-bottom:0;font-weight:400;vertical-align:middle;cursor:pointer}.checkbox-inline+.checkbox-inline,.radio-inline+.radio-inline{margin-top:0;margin-left:10px}fieldset[disabled] input[type=checkbox],fieldset[disabled] input[type=radio],input[type=checkbox].disabled,input[type=checkbox][disabled],input[type=radio].disabled,input[type=radio][disabled]{cursor:not-allowed}.checkbox-inline.disabled,.radio-inline.disabled,fieldset[disabled] .checkbox-inline,fieldset[disabled] .radio-inline{cursor:not-allowed}.checkbox.disabled label,.radio.disabled label,fieldset[disabled] .checkbox label,fieldset[disabled] .radio label{cursor:not-allowed}.form-control-static{min-height:34px;padding-top:7px;padding-bottom:7px;margin-bottom:0}.form-control-static.input-lg,.form-control-static.input-sm{padding-right:0;padding-left:0}.input-sm{height:30px;padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px}select.input-sm{height:30px;line-height:30px}select[multiple].input-sm,textarea.input-sm{height:auto}.form-group-sm .form-control{height:30px;padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px}select.form-group-sm .form-control{height:30px;line-height:30px}select[multiple].form-group-sm .form-control,textarea.form-group-sm .form-control{height:auto}.form-group-sm .form-control-static{height:30px;min-height:32px;padding:5px 10px;font-size:12px;line-height:1.5}.input-lg{height:46px;padding:10px 16px;font-size:18px;line-height:1.3333333;border-radius:6px}select.input-lg{height:46px;line-height:46px}select[multiple].input-lg,textarea.input-lg{height:auto}.form-group-lg .form-control{height:46px;padding:10px 16px;font-size:18px;line-height:1.3333333;border-radius:6px}select.form-group-lg .form-control{height:46px;line-height:46px}select[multiple].form-group-lg .form-control,textarea.form-group-lg .form-control{height:auto}.form-group-lg .form-control-static{height:46px;min-height:38px;padding:10px 16px;font-size:18px;line-height:1.3333333}.has-feedback{position:relative}.has-feedback .form-control{padding-right:42.5px}.form-control-feedback{position:absolute;top:0;right:0;z-index:2;display:block;width:34px;height:34px;line-height:34px;text-align:center;pointer-events:none}.input-lg+.form-control-feedback{width:46px;height:46px;line-height:46px}.input-sm+.form-control-feedback{width:30px;height:30px;line-height:30px}.has-success .checkbox,.has-success .checkbox-inline,.has-success .control-label,.has-success .help-block,.has-success .radio,.has-success .radio-inline,.has-success.checkbox label,.has-success.checkbox-inline label,.has-success.radio label,.has-success.radio-inline label{color:#3c763d}.has-success .form-control{border-color:#3c763d;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075)}.has-success .form-control:focus{border-color:#2b542c;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #67b168;box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #67b168}.has-success .input-group-addon{color:#3c763d;background-color:#dff0d8;border-color:#3c763d}.has-success .form-control-feedback{color:#3c763d}.has-warning .checkbox,.has-warning .checkbox-inline,.has-warning .control-label,.has-warning .help-block,.has-warning .radio,.has-warning .radio-inline,.has-warning.checkbox label,.has-warning.checkbox-inline label,.has-warning.radio label,.has-warning.radio-inline label{color:#8a6d3b}.has-warning .form-control{border-color:#8a6d3b;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075)}.has-warning .form-control:focus{border-color:#66512c;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #c0a16b;box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #c0a16b}.has-warning .input-group-addon{color:#8a6d3b;background-color:#fcf8e3;border-color:#8a6d3b}.has-warning .form-control-feedback{color:#8a6d3b}.has-error .checkbox,.has-error .checkbox-inline,.has-error .control-label,.has-error .help-block,.has-error .radio,.has-error .radio-inline,.has-error.checkbox label,.has-error.checkbox-inline label,.has-error.radio label,.has-error.radio-inline label{color:#a94442}.has-error .form-control{border-color:#a94442;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075);box-shadow:inset 0 1px 1px rgba(0,0,0,.075)}.has-error .form-control:focus{border-color:#843534;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #ce8483;box-shadow:inset 0 1px 1px rgba(0,0,0,.075),0 0 6px #ce8483}.has-error .input-group-addon{color:#a94442;background-color:#f2dede;border-color:#a94442}.has-error .form-control-feedback{color:#a94442}.has-feedback label~.form-control-feedback{top:25px}.has-feedback label.sr-only~.form-control-feedback{top:0}.help-block{display:block;margin-top:5px;margin-bottom:10px;color:#737373}@media (min-width:768px){.form-inline .form-group{display:inline-block;margin-bottom:0;vertical-align:middle}.form-inline .form-control{display:inline-block;width:auto;vertical-align:middle}.form-inline .form-control-static{display:inline-block}.form-inline .input-group{display:inline-table;vertical-align:middle}.form-inline .input-group .form-control,.form-inline .input-group .input-group-addon,.form-inline .input-group .input-group-btn{width:auto}.form-inline .input-group>.form-control{width:100%}.form-inline .control-label{margin-bottom:0;vertical-align:middle}.form-inline .checkbox,.form-inline .radio{display:inline-block;margin-top:0;margin-bottom:0;vertical-align:middle}.form-inline .checkbox label,.form-inline .radio label{padding-left:0}.form-inline .checkbox input[type=checkbox],.form-inline .radio input[type=radio]{position:relative;margin-left:0}.form-inline .has-feedback .form-control-feedback{top:0}}.form-horizontal .checkbox,.form-horizontal .checkbox-inline,.form-horizontal .radio,.form-horizontal .radio-inline{padding-top:7px;margin-top:0;margin-bottom:0}.form-horizontal .checkbox,.form-horizontal .radio{min-height:27px}.form-horizontal .form-group{margin-right:-15px;margin-left:-15px}@media (min-width:768px){.form-horizontal .control-label{padding-top:7px;margin-bottom:0;text-align:right}}.form-horizontal .has-feedback .form-control-feedback{right:15px}@media (min-width:768px){.form-horizontal .form-group-lg .control-label{padding-top:14.33px}}@media (min-width:768px){.form-horizontal .form-group-sm .control-label{padding-top:6px}}.btn{display:inline-block;padding:6px 12px;margin-bottom:0;font-size:14px;font-weight:400;line-height:1.42857143;text-align:center;white-space:nowrap;vertical-align:middle;-ms-touch-action:manipulation;touch-action:manipulation;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background-image:none;border:1px solid transparent;border-radius:4px}.btn.active.focus,.btn.active:focus,.btn.focus,.btn:active.focus,.btn:active:focus,.btn:focus{outline:thin dotted;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px}.btn.focus,.btn:focus,.btn:hover{color:#333;text-decoration:none}.btn.active,.btn:active{background-image:none;outline:0;-webkit-box-shadow:inset 0 3px 5px rgba(0,0,0,.125);box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}.btn.disabled,.btn[disabled],fieldset[disabled] .btn{pointer-events:none;cursor:not-allowed;filter:alpha(opacity=65);-webkit-box-shadow:none;box-shadow:none;opacity:.65}.btn-default{color:#333;background-color:#fff;border-color:#ccc}.btn-default.active,.btn-default.focus,.btn-default:active,.btn-default:focus,.btn-default:hover,.open>.dropdown-toggle.btn-default{color:#333;background-color:#e6e6e6;border-color:#adadad}.btn-default.active,.btn-default:active,.open>.dropdown-toggle.btn-default{background-image:none}.btn-default.disabled,.btn-default.disabled.active,.btn-default.disabled.focus,.btn-default.disabled:active,.btn-default.disabled:focus,.btn-default.disabled:hover,.btn-default[disabled],.btn-default[disabled].active,.btn-default[disabled].focus,.btn-default[disabled]:active,.btn-default[disabled]:focus,.btn-default[disabled]:hover,fieldset[disabled] .btn-default,fieldset[disabled] .btn-default.active,fieldset[disabled] .btn-default.focus,fieldset[disabled] .btn-default:active,fieldset[disabled] .btn-default:focus,fieldset[disabled] .btn-default:hover{background-color:#fff;border-color:#ccc}.btn-default .badge{color:#fff;background-color:#333}.btn-primary{color:#fff;background-color:#337ab7;border-color:#2e6da4}.btn-primary.active,.btn-primary.focus,.btn-primary:active,.btn-primary:focus,.btn-primary:hover,.open>.dropdown-toggle.btn-primary{color:#fff;background-color:#286090;border-color:#204d74}.btn-primary.active,.btn-primary:active,.open>.dropdown-toggle.btn-primary{background-image:none}.btn-primary.disabled,.btn-primary.disabled.active,.btn-primary.disabled.focus,.btn-primary.disabled:active,.btn-primary.disabled:focus,.btn-primary.disabled:hover,.btn-primary[disabled],.btn-primary[disabled].active,.btn-primary[disabled].focus,.btn-primary[disabled]:active,.btn-primary[disabled]:focus,.btn-primary[disabled]:hover,fieldset[disabled] .btn-primary,fieldset[disabled] .btn-primary.active,fieldset[disabled] .btn-primary.focus,fieldset[disabled] .btn-primary:active,fieldset[disabled] .btn-primary:focus,fieldset[disabled] .btn-primary:hover{background-color:#337ab7;border-color:#2e6da4}.btn-primary .badge{color:#337ab7;background-color:#fff}.btn-success{color:#fff;background-color:#5cb85c;border-color:#4cae4c}.btn-success.active,.btn-success.focus,.btn-success:active,.btn-success:focus,.btn-success:hover,.open>.dropdown-toggle.btn-success{color:#fff;background-color:#449d44;border-color:#398439}.btn-success.active,.btn-success:active,.open>.dropdown-toggle.btn-success{background-image:none}.btn-success.disabled,.btn-success.disabled.active,.btn-success.disabled.focus,.btn-success.disabled:active,.btn-success.disabled:focus,.btn-success.disabled:hover,.btn-success[disabled],.btn-success[disabled].active,.btn-success[disabled].focus,.btn-success[disabled]:active,.btn-success[disabled]:focus,.btn-success[disabled]:hover,fieldset[disabled] .btn-success,fieldset[disabled] .btn-success.active,fieldset[disabled] .btn-success.focus,fieldset[disabled] .btn-success:active,fieldset[disabled] .btn-success:focus,fieldset[disabled] .btn-success:hover{background-color:#5cb85c;border-color:#4cae4c}.btn-success .badge{color:#5cb85c;background-color:#fff}.btn-info{color:#fff;background-color:#5bc0de;border-color:#46b8da}.btn-info.active,.btn-info.focus,.btn-info:active,.btn-info:focus,.btn-info:hover,.open>.dropdown-toggle.btn-info{color:#fff;background-color:#31b0d5;border-color:#269abc}.btn-info.active,.btn-info:active,.open>.dropdown-toggle.btn-info{background-image:none}.btn-info.disabled,.btn-info.disabled.active,.btn-info.disabled.focus,.btn-info.disabled:active,.btn-info.disabled:focus,.btn-info.disabled:hover,.btn-info[disabled],.btn-info[disabled].active,.btn-info[disabled].focus,.btn-info[disabled]:active,.btn-info[disabled]:focus,.btn-info[disabled]:hover,fieldset[disabled] .btn-info,fieldset[disabled] .btn-info.active,fieldset[disabled] .btn-info.focus,fieldset[disabled] .btn-info:active,fieldset[disabled] .btn-info:focus,fieldset[disabled] .btn-info:hover{background-color:#5bc0de;border-color:#46b8da}.btn-info .badge{color:#5bc0de;background-color:#fff}.btn-warning{color:#fff;background-color:#f0ad4e;border-color:#eea236}.btn-warning.active,.btn-warning.focus,.btn-warning:active,.btn-warning:focus,.btn-warning:hover,.open>.dropdown-toggle.btn-warning{color:#fff;background-color:#ec971f;border-color:#d58512}.btn-warning.active,.btn-warning:active,.open>.dropdown-toggle.btn-warning{background-image:none}.btn-warning.disabled,.btn-warning.disabled.active,.btn-warning.disabled.focus,.btn-warning.disabled:active,.btn-warning.disabled:focus,.btn-warning.disabled:hover,.btn-warning[disabled],.btn-warning[disabled].active,.btn-warning[disabled].focus,.btn-warning[disabled]:active,.btn-warning[disabled]:focus,.btn-warning[disabled]:hover,fieldset[disabled] .btn-warning,fieldset[disabled] .btn-warning.active,fieldset[disabled] .btn-warning.focus,fieldset[disabled] .btn-warning:active,fieldset[disabled] .btn-warning:focus,fieldset[disabled] .btn-warning:hover{background-color:#f0ad4e;border-color:#eea236}.btn-warning .badge{color:#f0ad4e;background-color:#fff}.btn-danger{color:#fff;background-color:#d9534f;border-color:#d43f3a}.btn-danger.active,.btn-danger.focus,.btn-danger:active,.btn-danger:focus,.btn-danger:hover,.open>.dropdown-toggle.btn-danger{color:#fff;background-color:#c9302c;border-color:#ac2925}.btn-danger.active,.btn-danger:active,.open>.dropdown-toggle.btn-danger{background-image:none}.btn-danger.disabled,.btn-danger.disabled.active,.btn-danger.disabled.focus,.btn-danger.disabled:active,.btn-danger.disabled:focus,.btn-danger.disabled:hover,.btn-danger[disabled],.btn-danger[disabled].active,.btn-danger[disabled].focus,.btn-danger[disabled]:active,.btn-danger[disabled]:focus,.btn-danger[disabled]:hover,fieldset[disabled] .btn-danger,fieldset[disabled] .btn-danger.active,fieldset[disabled] .btn-danger.focus,fieldset[disabled] .btn-danger:active,fieldset[disabled] .btn-danger:focus,fieldset[disabled] .btn-danger:hover{background-color:#d9534f;border-color:#d43f3a}.btn-danger .badge{color:#d9534f;background-color:#fff}.btn-link{font-weight:400;color:#337ab7;border-radius:0}.btn-link,.btn-link.active,.btn-link:active,.btn-link[disabled],fieldset[disabled] .btn-link{background-color:transparent;-webkit-box-shadow:none;box-shadow:none}.btn-link,.btn-link:active,.btn-link:focus,.btn-link:hover{border-color:transparent}.btn-link:focus,.btn-link:hover{color:#23527c;text-decoration:underline;background-color:transparent}.btn-link[disabled]:focus,.btn-link[disabled]:hover,fieldset[disabled] .btn-link:focus,fieldset[disabled] .btn-link:hover{color:#777;text-decoration:none}.btn-group-lg>.btn,.btn-lg{padding:10px 16px;font-size:18px;line-height:1.3333333;border-radius:6px}.btn-group-sm>.btn,.btn-sm{padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px}.btn-group-xs>.btn,.btn-xs{padding:1px 5px;font-size:12px;line-height:1.5;border-radius:3px}.btn-block{display:block;width:100%}.btn-block+.btn-block{margin-top:5px}input[type=button].btn-block,input[type=reset].btn-block,input[type=submit].btn-block{width:100%}.fade{opacity:0;-webkit-transition:opacity .15s linear;-o-transition:opacity .15s linear;transition:opacity .15s linear}.fade.in{opacity:1}.collapse{display:none}.collapse.in{display:block}tr.collapse.in{display:table-row}tbody.collapse.in{display:table-row-group}.collapsing{position:relative;height:0;overflow:hidden;-webkit-transition-timing-function:ease;-o-transition-timing-function:ease;transition-timing-function:ease;-webkit-transition-duration:.35s;-o-transition-duration:.35s;transition-duration:.35s;-webkit-transition-property:height,visibility;-o-transition-property:height,visibility;transition-property:height,visibility}.caret{display:inline-block;width:0;height:0;margin-left:2px;vertical-align:middle;border-top:4px dashed;border-right:4px solid transparent;border-left:4px solid transparent}.dropdown,.dropup{position:relative}.dropdown-toggle:focus{outline:0}.dropdown-menu{position:absolute;top:100%;left:0;z-index:1000;display:none;float:left;min-width:160px;padding:5px 0;margin:2px 0 0;font-size:14px;text-align:left;list-style:none;background-color:#fff;-webkit-background-clip:padding-box;background-clip:padding-box;border:1px solid #ccc;border:1px solid rgba(0,0,0,.15);border-radius:4px;-webkit-box-shadow:0 6px 12px rgba(0,0,0,.175);box-shadow:0 6px 12px rgba(0,0,0,.175)}.dropdown-menu.pull-right{right:0;left:auto}.dropdown-menu .divider{height:1px;margin:9px 0;overflow:hidden;background-color:#e5e5e5}.dropdown-menu>li>a{display:block;padding:3px 20px;clear:both;font-weight:400;line-height:1.42857143;color:#333;white-space:nowrap}.dropdown-menu>li>a:focus,.dropdown-menu>li>a:hover{color:#262626;text-decoration:none;background-color:#f5f5f5}.dropdown-menu>.active>a,.dropdown-menu>.active>a:focus,.dropdown-menu>.active>a:hover{color:#fff;text-decoration:none;background-color:#337ab7;outline:0}.dropdown-menu>.disabled>a,.dropdown-menu>.disabled>a:focus,.dropdown-menu>.disabled>a:hover{color:#777}.dropdown-menu>.disabled>a:focus,.dropdown-menu>.disabled>a:hover{text-decoration:none;cursor:not-allowed;background-color:transparent;background-image:none;filter:progid:DXImageTransform.Microsoft.gradient(enabled=false)}.open>.dropdown-menu{display:block}.open>a{outline:0}.dropdown-menu-right{right:0;left:auto}.dropdown-menu-left{right:auto;left:0}.dropdown-header{display:block;padding:3px 20px;font-size:12px;line-height:1.42857143;color:#777;white-space:nowrap}.dropdown-backdrop{position:fixed;top:0;right:0;bottom:0;left:0;z-index:990}.pull-right>.dropdown-menu{right:0;left:auto}.dropup .caret,.navbar-fixed-bottom .dropdown .caret{content:"";border-top:0;border-bottom:4px solid}.dropup .dropdown-menu,.navbar-fixed-bottom .dropdown .dropdown-menu{top:auto;bottom:100%;margin-bottom:2px}@media (min-width:768px){.navbar-right .dropdown-menu{right:0;left:auto}.navbar-right .dropdown-menu-left{right:auto;left:0}}.btn-group,.btn-group-vertical{position:relative;display:inline-block;vertical-align:middle}.btn-group-vertical>.btn,.btn-group>.btn{position:relative;float:left}.btn-group-vertical>.btn.active,.btn-group-vertical>.btn:active,.btn-group-vertical>.btn:focus,.btn-group-vertical>.btn:hover,.btn-group>.btn.active,.btn-group>.btn:active,.btn-group>.btn:focus,.btn-group>.btn:hover{z-index:2}.btn-group .btn+.btn,.btn-group .btn+.btn-group,.btn-group .btn-group+.btn,.btn-group .btn-group+.btn-group{margin-left:-1px}.btn-toolbar{margin-left:-5px}.btn-toolbar .btn-group,.btn-toolbar .input-group{float:left}.btn-toolbar>.btn,.btn-toolbar>.btn-group,.btn-toolbar>.input-group{margin-left:5px}.btn-group>.btn:not(:first-child):not(:last-child):not(.dropdown-toggle){border-radius:0}.btn-group>.btn:first-child{margin-left:0}.btn-group>.btn:first-child:not(:last-child):not(.dropdown-toggle){border-top-right-radius:0;border-bottom-right-radius:0}.btn-group>.btn:last-child:not(:first-child),.btn-group>.dropdown-toggle:not(:first-child){border-top-left-radius:0;border-bottom-left-radius:0}.btn-group>.btn-group{float:left}.btn-group>.btn-group:not(:first-child):not(:last-child)>.btn{border-radius:0}.btn-group>.btn-group:first-child:not(:last-child)>.btn:last-child,.btn-group>.btn-group:first-child:not(:last-child)>.dropdown-toggle{border-top-right-radius:0;border-bottom-right-radius:0}.btn-group>.btn-group:last-child:not(:first-child)>.btn:first-child{border-top-left-radius:0;border-bottom-left-radius:0}.btn-group .dropdown-toggle:active,.btn-group.open .dropdown-toggle{outline:0}.btn-group>.btn+.dropdown-toggle{padding-right:8px;padding-left:8px}.btn-group>.btn-lg+.dropdown-toggle{padding-right:12px;padding-left:12px}.btn-group.open .dropdown-toggle{-webkit-box-shadow:inset 0 3px 5px rgba(0,0,0,.125);box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}.btn-group.open .dropdown-toggle.btn-link{-webkit-box-shadow:none;box-shadow:none}.btn .caret{margin-left:0}.btn-lg .caret{border-width:5px 5px 0;border-bottom-width:0}.dropup .btn-lg .caret{border-width:0 5px 5px}.btn-group-vertical>.btn,.btn-group-vertical>.btn-group,.btn-group-vertical>.btn-group>.btn{display:block;float:none;width:100%;max-width:100%}.btn-group-vertical>.btn-group>.btn{float:none}.btn-group-vertical>.btn+.btn,.btn-group-vertical>.btn+.btn-group,.btn-group-vertical>.btn-group+.btn,.btn-group-vertical>.btn-group+.btn-group{margin-top:-1px;margin-left:0}.btn-group-vertical>.btn:not(:first-child):not(:last-child){border-radius:0}.btn-group-vertical>.btn:first-child:not(:last-child){border-top-right-radius:4px;border-bottom-right-radius:0;border-bottom-left-radius:0}.btn-group-vertical>.btn:last-child:not(:first-child){border-top-left-radius:0;border-top-right-radius:0;border-bottom-left-radius:4px}.btn-group-vertical>.btn-group:not(:first-child):not(:last-child)>.btn{border-radius:0}.btn-group-vertical>.btn-group:first-child:not(:last-child)>.btn:last-child,.btn-group-vertical>.btn-group:first-child:not(:last-child)>.dropdown-toggle{border-bottom-right-radius:0;border-bottom-left-radius:0}.btn-group-vertical>.btn-group:last-child:not(:first-child)>.btn:first-child{border-top-left-radius:0;border-top-right-radius:0}.btn-group-justified{display:table;width:100%;table-layout:fixed;border-collapse:separate}.btn-group-justified>.btn,.btn-group-justified>.btn-group{display:table-cell;float:none;width:1%}.btn-group-justified>.btn-group .btn{width:100%}.btn-group-justified>.btn-group .dropdown-menu{left:auto}[data-toggle=buttons]>.btn input[type=checkbox],[data-toggle=buttons]>.btn input[type=radio],[data-toggle=buttons]>.btn-group>.btn input[type=checkbox],[data-toggle=buttons]>.btn-group>.btn input[type=radio]{position:absolute;clip:rect(0,0,0,0);pointer-events:none}.input-group{position:relative;display:table;border-collapse:separate}.input-group[class*=col-]{float:none;padding-right:0;padding-left:0}.input-group .form-control{position:relative;z-index:2;float:left;width:100%;margin-bottom:0}.input-group-lg>.form-control,.input-group-lg>.input-group-addon,.input-group-lg>.input-group-btn>.btn{height:46px;padding:10px 16px;font-size:18px;line-height:1.3333333;border-radius:6px}select.input-group-lg>.form-control,select.input-group-lg>.input-group-addon,select.input-group-lg>.input-group-btn>.btn{height:46px;line-height:46px}select[multiple].input-group-lg>.form-control,select[multiple].input-group-lg>.input-group-addon,select[multiple].input-group-lg>.input-group-btn>.btn,textarea.input-group-lg>.form-control,textarea.input-group-lg>.input-group-addon,textarea.input-group-lg>.input-group-btn>.btn{height:auto}.input-group-sm>.form-control,.input-group-sm>.input-group-addon,.input-group-sm>.input-group-btn>.btn{height:30px;padding:5px 10px;font-size:12px;line-height:1.5;border-radius:3px}select.input-group-sm>.form-control,select.input-group-sm>.input-group-addon,select.input-group-sm>.input-group-btn>.btn{height:30px;line-height:30px}select[multiple].input-group-sm>.form-control,select[multiple].input-group-sm>.input-group-addon,select[multiple].input-group-sm>.input-group-btn>.btn,textarea.input-group-sm>.form-control,textarea.input-group-sm>.input-group-addon,textarea.input-group-sm>.input-group-btn>.btn{height:auto}.input-group .form-control,.input-group-addon,.input-group-btn{display:table-cell}.input-group .form-control:not(:first-child):not(:last-child),.input-group-addon:not(:first-child):not(:last-child),.input-group-btn:not(:first-child):not(:last-child){border-radius:0}.input-group-addon,.input-group-btn{width:1%;white-space:nowrap;vertical-align:middle}.input-group-addon{padding:6px 12px;font-size:14px;font-weight:400;line-height:1;color:#555;text-align:center;background-color:#eee;border:1px solid #ccc;border-radius:4px}.input-group-addon.input-sm{padding:5px 10px;font-size:12px;border-radius:3px}.input-group-addon.input-lg{padding:10px 16px;font-size:18px;border-radius:6px}.input-group-addon input[type=checkbox],.input-group-addon input[type=radio]{margin-top:0}.input-group .form-control:first-child,.input-group-addon:first-child,.input-group-btn:first-child>.btn,.input-group-btn:first-child>.btn-group>.btn,.input-group-btn:first-child>.dropdown-toggle,.input-group-btn:last-child>.btn-group:not(:last-child)>.btn,.input-group-btn:last-child>.btn:not(:last-child):not(.dropdown-toggle){border-top-right-radius:0;border-bottom-right-radius:0}.input-group-addon:first-child{border-right:0}.input-group .form-control:last-child,.input-group-addon:last-child,.input-group-btn:first-child>.btn-group:not(:first-child)>.btn,.input-group-btn:first-child>.btn:not(:first-child),.input-group-btn:last-child>.btn,.input-group-btn:last-child>.btn-group>.btn,.input-group-btn:last-child>.dropdown-toggle{border-top-left-radius:0;border-bottom-left-radius:0}.input-group-addon:last-child{border-left:0}.input-group-btn{position:relative;font-size:0;white-space:nowrap}.input-group-btn>.btn{position:relative}.input-group-btn>.btn+.btn{margin-left:-1px}.input-group-btn>.btn:active,.input-group-btn>.btn:focus,.input-group-btn>.btn:hover{z-index:2}.input-group-btn:first-child>.btn,.input-group-btn:first-child>.btn-group{margin-right:-1px}.input-group-btn:last-child>.btn,.input-group-btn:last-child>.btn-group{margin-left:-1px}.nav{padding-left:0;margin-bottom:0;list-style:none}.nav>li{position:relative;display:block}.nav>li>a{position:relative;display:block;padding:10px 15px}.nav>li>a:focus,.nav>li>a:hover{text-decoration:none;background-color:#eee}.nav>li.disabled>a{color:#777}.nav>li.disabled>a:focus,.nav>li.disabled>a:hover{color:#777;text-decoration:none;cursor:not-allowed;background-color:transparent}.nav .open>a,.nav .open>a:focus,.nav .open>a:hover{background-color:#eee;border-color:#337ab7}.nav .nav-divider{height:1px;margin:9px 0;overflow:hidden;background-color:#e5e5e5}.nav>li>a>img{max-width:none}.nav-tabs{border-bottom:1px solid #ddd}.nav-tabs>li{float:left;margin-bottom:-1px}.nav-tabs>li>a{margin-right:2px;line-height:1.42857143;border:1px solid transparent;border-radius:4px 4px 0 0}.nav-tabs>li>a:hover{border-color:#eee #eee #ddd}.nav-tabs>li.active>a,.nav-tabs>li.active>a:focus,.nav-tabs>li.active>a:hover{color:#555;cursor:default;background-color:#fff;border:1px solid #ddd;border-bottom-color:transparent}.nav-tabs.nav-justified{width:100%;border-bottom:0}.nav-tabs.nav-justified>li{float:none}.nav-tabs.nav-justified>li>a{margin-bottom:5px;text-align:center}.nav-tabs.nav-justified>.dropdown .dropdown-menu{top:auto;left:auto}@media (min-width:768px){.nav-tabs.nav-justified>li{display:table-cell;width:1%}.nav-tabs.nav-justified>li>a{margin-bottom:0}}.nav-tabs.nav-justified>li>a{margin-right:0;border-radius:4px}.nav-tabs.nav-justified>.active>a,.nav-tabs.nav-justified>.active>a:focus,.nav-tabs.nav-justified>.active>a:hover{border:1px solid #ddd}@media (min-width:768px){.nav-tabs.nav-justified>li>a{border-bottom:1px solid #ddd;border-radius:4px 4px 0 0}.nav-tabs.nav-justified>.active>a,.nav-tabs.nav-justified>.active>a:focus,.nav-tabs.nav-justified>.active>a:hover{border-bottom-color:#fff}}.nav-pills>li{float:left}.nav-pills>li>a{border-radius:4px}.nav-pills>li+li{margin-left:2px}.nav-pills>li.active>a,.nav-pills>li.active>a:focus,.nav-pills>li.active>a:hover{color:#fff;background-color:#337ab7}.nav-stacked>li{float:none}.nav-stacked>li+li{margin-top:2px;margin-left:0}.nav-justified{width:100%}.nav-justified>li{float:none}.nav-justified>li>a{margin-bottom:5px;text-align:center}.nav-justified>.dropdown .dropdown-menu{top:auto;left:auto}@media (min-width:768px){.nav-justified>li{display:table-cell;width:1%}.nav-justified>li>a{margin-bottom:0}}.nav-tabs-justified{border-bottom:0}.nav-tabs-justified>li>a{margin-right:0;border-radius:4px}.nav-tabs-justified>.active>a,.nav-tabs-justified>.active>a:focus,.nav-tabs-justified>.active>a:hover{border:1px solid #ddd}@media (min-width:768px){.nav-tabs-justified>li>a{border-bottom:1px solid #ddd;border-radius:4px 4px 0 0}.nav-tabs-justified>.active>a,.nav-tabs-justified>.active>a:focus,.nav-tabs-justified>.active>a:hover{border-bottom-color:#fff}}.tab-content>.tab-pane{display:none}.tab-content>.active{display:block}.nav-tabs .dropdown-menu{margin-top:-1px;border-top-left-radius:0;border-top-right-radius:0}.navbar{position:relative;min-height:50px;margin-bottom:20px;border:1px solid transparent}@media (min-width:768px){.navbar{border-radius:4px}}@media (min-width:768px){.navbar-header{float:left}}.navbar-collapse{padding-right:15px;padding-left:15px;overflow-x:visible;-webkit-overflow-scrolling:touch;border-top:1px solid transparent;-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,.1);box-shadow:inset 0 1px 0 rgba(255,255,255,.1)}.navbar-collapse.in{overflow-y:auto}@media (min-width:768px){.navbar-collapse{width:auto;border-top:0;-webkit-box-shadow:none;box-shadow:none}.navbar-collapse.collapse{display:block!important;height:auto!important;padding-bottom:0;overflow:visible!important}.navbar-collapse.in{overflow-y:visible}.navbar-fixed-bottom .navbar-collapse,.navbar-fixed-top .navbar-collapse,.navbar-static-top .navbar-collapse{padding-right:0;padding-left:0}}.navbar-fixed-bottom .navbar-collapse,.navbar-fixed-top .navbar-collapse{max-height:340px}@media (max-device-width:480px)and (orientation:landscape){.navbar-fixed-bottom .navbar-collapse,.navbar-fixed-top .navbar-collapse{max-height:200px}}.container-fluid>.navbar-collapse,.container-fluid>.navbar-header,.container>.navbar-collapse,.container>.navbar-header{margin-right:-15px;margin-left:-15px}@media (min-width:768px){.container-fluid>.navbar-collapse,.container-fluid>.navbar-header,.container>.navbar-collapse,.container>.navbar-header{margin-right:0;margin-left:0}}.navbar-static-top{z-index:1000;border-width:0 0 1px}@media (min-width:768px){.navbar-static-top{border-radius:0}}.navbar-fixed-bottom,.navbar-fixed-top{position:fixed;right:0;left:0;z-index:1030}@media (min-width:768px){.navbar-fixed-bottom,.navbar-fixed-top{border-radius:0}}.navbar-fixed-top{top:0;border-width:0 0 1px}.navbar-fixed-bottom{bottom:0;margin-bottom:0;border-width:1px 0 0}.navbar-brand{float:left;height:50px;padding:15px 15px;font-size:18px;line-height:20px}.navbar-brand:focus,.navbar-brand:hover{text-decoration:none}.navbar-brand>img{display:block}@media (min-width:768px){.navbar>.container .navbar-brand,.navbar>.container-fluid .navbar-brand{margin-left:-15px}}.navbar-toggle{position:relative;float:right;padding:9px 10px;margin-top:8px;margin-right:15px;margin-bottom:8px;background-color:transparent;background-image:none;border:1px solid transparent;border-radius:4px}.navbar-toggle:focus{outline:0}.navbar-toggle .icon-bar{display:block;width:22px;height:2px;border-radius:1px}.navbar-toggle .icon-bar+.icon-bar{margin-top:4px}@media (min-width:768px){.navbar-toggle{display:none}}.navbar-nav{margin:7.5px -15px}.navbar-nav>li>a{padding-top:10px;padding-bottom:10px;line-height:20px}@media (max-width:767px){.navbar-nav .open .dropdown-menu{position:static;float:none;width:auto;margin-top:0;background-color:transparent;border:0;-webkit-box-shadow:none;box-shadow:none}.navbar-nav .open .dropdown-menu .dropdown-header,.navbar-nav .open .dropdown-menu>li>a{padding:5px 15px 5px 25px}.navbar-nav .open .dropdown-menu>li>a{line-height:20px}.navbar-nav .open .dropdown-menu>li>a:focus,.navbar-nav .open .dropdown-menu>li>a:hover{background-image:none}}@media (min-width:768px){.navbar-nav{float:left;margin:0}.navbar-nav>li{float:left}.navbar-nav>li>a{padding-top:15px;padding-bottom:15px}}.navbar-form{padding:10px 15px;margin-top:8px;margin-right:-15px;margin-bottom:8px;margin-left:-15px;border-top:1px solid transparent;border-bottom:1px solid transparent;-webkit-box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(255,255,255,.1);box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(255,255,255,.1)}@media (min-width:768px){.navbar-form .form-group{display:inline-block;margin-bottom:0;vertical-align:middle}.navbar-form .form-control{display:inline-block;width:auto;vertical-align:middle}.navbar-form .form-control-static{display:inline-block}.navbar-form .input-group{display:inline-table;vertical-align:middle}.navbar-form .input-group .form-control,.navbar-form .input-group .input-group-addon,.navbar-form .input-group .input-group-btn{width:auto}.navbar-form .input-group>.form-control{width:100%}.navbar-form .control-label{margin-bottom:0;vertical-align:middle}.navbar-form .checkbox,.navbar-form .radio{display:inline-block;margin-top:0;margin-bottom:0;vertical-align:middle}.navbar-form .checkbox label,.navbar-form .radio label{padding-left:0}.navbar-form .checkbox input[type=checkbox],.navbar-form .radio input[type=radio]{position:relative;margin-left:0}.navbar-form .has-feedback .form-control-feedback{top:0}}@media (max-width:767px){.navbar-form .form-group{margin-bottom:5px}.navbar-form .form-group:last-child{margin-bottom:0}}@media (min-width:768px){.navbar-form{width:auto;padding-top:0;padding-bottom:0;margin-right:0;margin-left:0;border:0;-webkit-box-shadow:none;box-shadow:none}}.navbar-nav>li>.dropdown-menu{margin-top:0;border-top-left-radius:0;border-top-right-radius:0}.navbar-fixed-bottom .navbar-nav>li>.dropdown-menu{margin-bottom:0;border-top-left-radius:4px;border-top-right-radius:4px;border-bottom-right-radius:0;border-bottom-left-radius:0}.navbar-btn{margin-top:8px;margin-bottom:8px}.navbar-btn.btn-sm{margin-top:10px;margin-bottom:10px}.navbar-btn.btn-xs{margin-top:14px;margin-bottom:14px}.navbar-text{margin-top:15px;margin-bottom:15px}@media (min-width:768px){.navbar-text{float:left;margin-right:15px;margin-left:15px}}@media (min-width:768px){.navbar-left{float:left!important}.navbar-right{float:right!important;margin-right:-15px}.navbar-right~.navbar-right{margin-right:0}}.navbar-default{background-color:#f8f8f8;border-color:#e7e7e7}.navbar-default .navbar-brand{color:#777}.navbar-default .navbar-brand:focus,.navbar-default .navbar-brand:hover{color:#5e5e5e;background-color:transparent}.navbar-default .navbar-text{color:#777}.navbar-default .navbar-nav>li>a{color:#777}.navbar-default .navbar-nav>li>a:focus,.navbar-default .navbar-nav>li>a:hover{color:#333;background-color:transparent}.navbar-default .navbar-nav>.active>a,.navbar-default .navbar-nav>.active>a:focus,.navbar-default .navbar-nav>.active>a:hover{color:#555;background-color:#e7e7e7}.navbar-default .navbar-nav>.disabled>a,.navbar-default .navbar-nav>.disabled>a:focus,.navbar-default .navbar-nav>.disabled>a:hover{color:#ccc;background-color:transparent}.navbar-default .navbar-toggle{border-color:#ddd}.navbar-default .navbar-toggle:focus,.navbar-default .navbar-toggle:hover{background-color:#ddd}.navbar-default .navbar-toggle .icon-bar{background-color:#888}.navbar-default .navbar-collapse,.navbar-default .navbar-form{border-color:#e7e7e7}.navbar-default .navbar-nav>.open>a,.navbar-default .navbar-nav>.open>a:focus,.navbar-default .navbar-nav>.open>a:hover{color:#555;background-color:#e7e7e7}@media (max-width:767px){.navbar-default .navbar-nav .open .dropdown-menu>li>a{color:#777}.navbar-default .navbar-nav .open .dropdown-menu>li>a:focus,.navbar-default .navbar-nav .open .dropdown-menu>li>a:hover{color:#333;background-color:transparent}.navbar-default .navbar-nav .open .dropdown-menu>.active>a,.navbar-default .navbar-nav .open .dropdown-menu>.active>a:focus,.navbar-default .navbar-nav .open .dropdown-menu>.active>a:hover{color:#555;background-color:#e7e7e7}.navbar-default .navbar-nav .open .dropdown-menu>.disabled>a,.navbar-default .navbar-nav .open .dropdown-menu>.disabled>a:focus,.navbar-default .navbar-nav .open .dropdown-menu>.disabled>a:hover{color:#ccc;background-color:transparent}}.navbar-default .navbar-link{color:#777}.navbar-default .navbar-link:hover{color:#333}.navbar-default .btn-link{color:#777}.navbar-default .btn-link:focus,.navbar-default .btn-link:hover{color:#333}.navbar-default .btn-link[disabled]:focus,.navbar-default .btn-link[disabled]:hover,fieldset[disabled] .navbar-default .btn-link:focus,fieldset[disabled] .navbar-default .btn-link:hover{color:#ccc}.navbar-inverse{background-color:#222;border-color:#080808}.navbar-inverse .navbar-brand{color:#9d9d9d}.navbar-inverse .navbar-brand:focus,.navbar-inverse .navbar-brand:hover{color:#fff;background-color:transparent}.navbar-inverse .navbar-text{color:#9d9d9d}.navbar-inverse .navbar-nav>li>a{color:#9d9d9d}.navbar-inverse .navbar-nav>li>a:focus,.navbar-inverse .navbar-nav>li>a:hover{color:#fff;background-color:transparent}.navbar-inverse .navbar-nav>.active>a,.navbar-inverse .navbar-nav>.active>a:focus,.navbar-inverse .navbar-nav>.active>a:hover{color:#fff;background-color:#080808}.navbar-inverse .navbar-nav>.disabled>a,.navbar-inverse .navbar-nav>.disabled>a:focus,.navbar-inverse .navbar-nav>.disabled>a:hover{color:#444;background-color:transparent}.navbar-inverse .navbar-toggle{border-color:#333}.navbar-inverse .navbar-toggle:focus,.navbar-inverse .navbar-toggle:hover{background-color:#333}.navbar-inverse .navbar-toggle .icon-bar{background-color:#fff}.navbar-inverse .navbar-collapse,.navbar-inverse .navbar-form{border-color:#101010}.navbar-inverse .navbar-nav>.open>a,.navbar-inverse .navbar-nav>.open>a:focus,.navbar-inverse .navbar-nav>.open>a:hover{color:#fff;background-color:#080808}@media (max-width:767px){.navbar-inverse .navbar-nav .open .dropdown-menu>.dropdown-header{border-color:#080808}.navbar-inverse .navbar-nav .open .dropdown-menu .divider{background-color:#080808}.navbar-inverse .navbar-nav .open .dropdown-menu>li>a{color:#9d9d9d}.navbar-inverse .navbar-nav .open .dropdown-menu>li>a:focus,.navbar-inverse .navbar-nav .open .dropdown-menu>li>a:hover{color:#fff;background-color:transparent}.navbar-inverse .navbar-nav .open .dropdown-menu>.active>a,.navbar-inverse .navbar-nav .open .dropdown-menu>.active>a:focus,.navbar-inverse .navbar-nav .open .dropdown-menu>.active>a:hover{color:#fff;background-color:#080808}.navbar-inverse .navbar-nav .open .dropdown-menu>.disabled>a,.navbar-inverse .navbar-nav .open .dropdown-menu>.disabled>a:focus,.navbar-inverse .navbar-nav .open .dropdown-menu>.disabled>a:hover{color:#444;background-color:transparent}}.navbar-inverse .navbar-link{color:#9d9d9d}.navbar-inverse .navbar-link:hover{color:#fff}.navbar-inverse .btn-link{color:#9d9d9d}.navbar-inverse .btn-link:focus,.navbar-inverse .btn-link:hover{color:#fff}.navbar-inverse .btn-link[disabled]:focus,.navbar-inverse .btn-link[disabled]:hover,fieldset[disabled] .navbar-inverse .btn-link:focus,fieldset[disabled] .navbar-inverse .btn-link:hover{color:#444}.breadcrumb{padding:8px 15px;margin-bottom:20px;list-style:none;background-color:#f5f5f5;border-radius:4px}.breadcrumb>li{display:inline-block}.breadcrumb>li+li:before{padding:0 5px;color:#ccc;content:"/\00a0"}.breadcrumb>.active{color:#777}.pagination{display:inline-block;padding-left:0;margin:20px 0;border-radius:4px}.pagination>li{display:inline}.pagination>li>a,.pagination>li>span{position:relative;float:left;padding:6px 12px;margin-left:-1px;line-height:1.42857143;color:#337ab7;text-decoration:none;background-color:#fff;border:1px solid #ddd}.pagination>li:first-child>a,.pagination>li:first-child>span{margin-left:0;border-top-left-radius:4px;border-bottom-left-radius:4px}.pagination>li:last-child>a,.pagination>li:last-child>span{border-top-right-radius:4px;border-bottom-right-radius:4px}.pagination>li>a:focus,.pagination>li>a:hover,.pagination>li>span:focus,.pagination>li>span:hover{color:#23527c;background-color:#eee;border-color:#ddd}.pagination>.active>a,.pagination>.active>a:focus,.pagination>.active>a:hover,.pagination>.active>span,.pagination>.active>span:focus,.pagination>.active>span:hover{z-index:2;color:#fff;cursor:default;background-color:#337ab7;border-color:#337ab7}.pagination>.disabled>a,.pagination>.disabled>a:focus,.pagination>.disabled>a:hover,.pagination>.disabled>span,.pagination>.disabled>span:focus,.pagination>.disabled>span:hover{color:#777;cursor:not-allowed;background-color:#fff;border-color:#ddd}.pagination-lg>li>a,.pagination-lg>li>span{padding:10px 16px;font-size:18px}.pagination-lg>li:first-child>a,.pagination-lg>li:first-child>span{border-top-left-radius:6px;border-bottom-left-radius:6px}.pagination-lg>li:last-child>a,.pagination-lg>li:last-child>span{border-top-right-radius:6px;border-bottom-right-radius:6px}.pagination-sm>li>a,.pagination-sm>li>span{padding:5px 10px;font-size:12px}.pagination-sm>li:first-child>a,.pagination-sm>li:first-child>span{border-top-left-radius:3px;border-bottom-left-radius:3px}.pagination-sm>li:last-child>a,.pagination-sm>li:last-child>span{border-top-right-radius:3px;border-bottom-right-radius:3px}.pager{padding-left:0;margin:20px 0;text-align:center;list-style:none}.pager li{display:inline}.pager li>a,.pager li>span{display:inline-block;padding:5px 14px;background-color:#fff;border:1px solid #ddd;border-radius:15px}.pager li>a:focus,.pager li>a:hover{text-decoration:none;background-color:#eee}.pager .next>a,.pager .next>span{float:right}.pager .previous>a,.pager .previous>span{float:left}.pager .disabled>a,.pager .disabled>a:focus,.pager .disabled>a:hover,.pager .disabled>span{color:#777;cursor:not-allowed;background-color:#fff}.label{display:inline;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em}a.label:focus,a.label:hover{color:#fff;text-decoration:none;cursor:pointer}.label:empty{display:none}.btn .label{position:relative;top:-1px}.label-default{background-color:#777}.label-default[href]:focus,.label-default[href]:hover{background-color:#5e5e5e}.label-primary{background-color:#337ab7}.label-primary[href]:focus,.label-primary[href]:hover{background-color:#286090}.label-success{background-color:#5cb85c}.label-success[href]:focus,.label-success[href]:hover{background-color:#449d44}.label-info{background-color:#5bc0de}.label-info[href]:focus,.label-info[href]:hover{background-color:#31b0d5}.label-warning{background-color:#f0ad4e}.label-warning[href]:focus,.label-warning[href]:hover{background-color:#ec971f}.label-danger{background-color:#d9534f}.label-danger[href]:focus,.label-danger[href]:hover{background-color:#c9302c}.badge{display:inline-block;min-width:10px;padding:3px 7px;font-size:12px;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;background-color:#777;border-radius:10px}.badge:empty{display:none}.btn .badge{position:relative;top:-1px}.btn-group-xs>.btn .badge,.btn-xs .badge{top:0;padding:1px 5px}a.badge:focus,a.badge:hover{color:#fff;text-decoration:none;cursor:pointer}.list-group-item.active>.badge,.nav-pills>.active>a>.badge{color:#337ab7;background-color:#fff}.list-group-item>.badge{float:right}.list-group-item>.badge+.badge{margin-right:5px}.nav-pills>li>a>.badge{margin-left:3px}.jumbotron{padding:30px 15px;margin-bottom:30px;color:inherit;background-color:#eee}.jumbotron .h1,.jumbotron h1{color:inherit}.jumbotron p{margin-bottom:15px;font-size:21px;font-weight:200}.jumbotron>hr{border-top-color:#d5d5d5}.container .jumbotron,.container-fluid .jumbotron{border-radius:6px}.jumbotron .container{max-width:100%}@media screen and (min-width:768px){.jumbotron{padding:48px 0}.container .jumbotron,.container-fluid .jumbotron{padding-right:60px;padding-left:60px}.jumbotron .h1,.jumbotron h1{font-size:63px}}.thumbnail{display:block;padding:4px;margin-bottom:20px;line-height:1.42857143;background-color:#fff;border:1px solid #ddd;border-radius:4px;-webkit-transition:border .2s ease-in-out;-o-transition:border .2s ease-in-out;transition:border .2s ease-in-out}.thumbnail a>img,.thumbnail>img{margin-right:auto;margin-left:auto}a.thumbnail.active,a.thumbnail:focus,a.thumbnail:hover{border-color:#337ab7}.thumbnail .caption{padding:9px;color:#333}.alert{padding:15px;margin-bottom:20px;border:1px solid transparent;border-radius:4px}.alert h4{margin-top:0;color:inherit}.alert .alert-link{font-weight:700}.alert>p,.alert>ul{margin-bottom:0}.alert>p+p{margin-top:5px}.alert-dismissable,.alert-dismissible{padding-right:35px}.alert-dismissable .close,.alert-dismissible .close{position:relative;top:-2px;right:-21px;color:inherit}.alert-success{color:#3c763d;background-color:#dff0d8;border-color:#d6e9c6}.alert-success hr{border-top-color:#c9e2b3}.alert-success .alert-link{color:#2b542c}.alert-info{color:#31708f;background-color:#d9edf7;border-color:#bce8f1}.alert-info hr{border-top-color:#a6e1ec}.alert-info .alert-link{color:#245269}.alert-warning{color:#8a6d3b;background-color:#fcf8e3;border-color:#faebcc}.alert-warning hr{border-top-color:#f7e1b5}.alert-warning .alert-link{color:#66512c}.alert-danger{color:#a94442;background-color:#f2dede;border-color:#ebccd1}.alert-danger hr{border-top-color:#e4b9c0}.alert-danger .alert-link{color:#843534}@-webkit-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@-o-keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}@keyframes progress-bar-stripes{from{background-position:40px 0}to{background-position:0 0}}.progress{height:20px;margin-bottom:20px;overflow:hidden;background-color:#f5f5f5;border-radius:4px;-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1)}.progress-bar{float:left;width:0;height:100%;font-size:12px;line-height:20px;color:#fff;text-align:center;background-color:#337ab7;-webkit-box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);box-shadow:inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition:width .6s ease;-o-transition:width .6s ease;transition:width .6s ease}.progress-bar-striped,.progress-striped .progress-bar{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);-webkit-background-size:40px 40px;background-size:40px 40px}.progress-bar.active,.progress.active .progress-bar{-webkit-animation:progress-bar-stripes 2s linear infinite;-o-animation:progress-bar-stripes 2s linear infinite;animation:progress-bar-stripes 2s linear infinite}.progress-bar-success{background-color:#5cb85c}.progress-striped .progress-bar-success{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-info{background-color:#5bc0de}.progress-striped .progress-bar-info{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-warning{background-color:#f0ad4e}.progress-striped .progress-bar-warning{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.progress-bar-danger{background-color:#d9534f}.progress-striped .progress-bar-danger{background-image:-webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:-o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image:linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)}.media{margin-top:15px}.media:first-child{margin-top:0}.media,.media-body{overflow:hidden;zoom:1}.media-body{width:10000px}.media-object{display:block}.media-right,.media>.pull-right{padding-left:10px}.media-left,.media>.pull-left{padding-right:10px}.media-body,.media-left,.media-right{display:table-cell;vertical-align:top}.media-middle{vertical-align:middle}.media-bottom{vertical-align:bottom}.media-heading{margin-top:0;margin-bottom:5px}.media-list{padding-left:0;list-style:none}.list-group{padding-left:0;margin-bottom:20px}.list-group-item{position:relative;display:block;padding:10px 15px;margin-bottom:-1px;background-color:#fff;border:1px solid #ddd}.list-group-item:first-child{border-top-left-radius:4px;border-top-right-radius:4px}.list-group-item:last-child{margin-bottom:0;border-bottom-right-radius:4px;border-bottom-left-radius:4px}a.list-group-item{color:#555}a.list-group-item .list-group-item-heading{color:#333}a.list-group-item:focus,a.list-group-item:hover{color:#555;text-decoration:none;background-color:#f5f5f5}.list-group-item.disabled,.list-group-item.disabled:focus,.list-group-item.disabled:hover{color:#777;cursor:not-allowed;background-color:#eee}.list-group-item.disabled .list-group-item-heading,.list-group-item.disabled:focus .list-group-item-heading,.list-group-item.disabled:hover .list-group-item-heading{color:inherit}.list-group-item.disabled .list-group-item-text,.list-group-item.disabled:focus .list-group-item-text,.list-group-item.disabled:hover .list-group-item-text{color:#777}.list-group-item.active,.list-group-item.active:focus,.list-group-item.active:hover{z-index:2;color:#fff;background-color:#337ab7;border-color:#337ab7}.list-group-item.active .list-group-item-heading,.list-group-item.active .list-group-item-heading>.small,.list-group-item.active .list-group-item-heading>small,.list-group-item.active:focus .list-group-item-heading,.list-group-item.active:focus .list-group-item-heading>.small,.list-group-item.active:focus .list-group-item-heading>small,.list-group-item.active:hover .list-group-item-heading,.list-group-item.active:hover .list-group-item-heading>.small,.list-group-item.active:hover .list-group-item-heading>small{color:inherit}.list-group-item.active .list-group-item-text,.list-group-item.active:focus .list-group-item-text,.list-group-item.active:hover .list-group-item-text{color:#c7ddef}.list-group-item-success{color:#3c763d;background-color:#dff0d8}a.list-group-item-success{color:#3c763d}a.list-group-item-success .list-group-item-heading{color:inherit}a.list-group-item-success:focus,a.list-group-item-success:hover{color:#3c763d;background-color:#d0e9c6}a.list-group-item-success.active,a.list-group-item-success.active:focus,a.list-group-item-success.active:hover{color:#fff;background-color:#3c763d;border-color:#3c763d}.list-group-item-info{color:#31708f;background-color:#d9edf7}a.list-group-item-info{color:#31708f}a.list-group-item-info .list-group-item-heading{color:inherit}a.list-group-item-info:focus,a.list-group-item-info:hover{color:#31708f;background-color:#c4e3f3}a.list-group-item-info.active,a.list-group-item-info.active:focus,a.list-group-item-info.active:hover{color:#fff;background-color:#31708f;border-color:#31708f}.list-group-item-warning{color:#8a6d3b;background-color:#fcf8e3}a.list-group-item-warning{color:#8a6d3b}a.list-group-item-warning .list-group-item-heading{color:inherit}a.list-group-item-warning:focus,a.list-group-item-warning:hover{color:#8a6d3b;background-color:#faf2cc}a.list-group-item-warning.active,a.list-group-item-warning.active:focus,a.list-group-item-warning.active:hover{color:#fff;background-color:#8a6d3b;border-color:#8a6d3b}.list-group-item-danger{color:#a94442;background-color:#f2dede}a.list-group-item-danger{color:#a94442}a.list-group-item-danger .list-group-item-heading{color:inherit}a.list-group-item-danger:focus,a.list-group-item-danger:hover{color:#a94442;background-color:#ebcccc}a.list-group-item-danger.active,a.list-group-item-danger.active:focus,a.list-group-item-danger.active:hover{color:#fff;background-color:#a94442;border-color:#a94442}.list-group-item-heading{margin-top:0;margin-bottom:5px}.list-group-item-text{margin-bottom:0;line-height:1.3}.panel{margin-bottom:20px;background-color:#fff;border:1px solid transparent;border-radius:4px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05)}.panel-body{padding:15px}.panel-heading{padding:10px 15px;border-bottom:1px solid transparent;border-top-left-radius:3px;border-top-right-radius:3px}.panel-heading>.dropdown .dropdown-toggle{color:inherit}.panel-title{margin-top:0;margin-bottom:0;font-size:16px;color:inherit}.panel-title>.small,.panel-title>.small>a,.panel-title>a,.panel-title>small,.panel-title>small>a{color:inherit}.panel-footer{padding:10px 15px;background-color:#f5f5f5;border-top:1px solid #ddd;border-bottom-right-radius:3px;border-bottom-left-radius:3px}.panel>.list-group,.panel>.panel-collapse>.list-group{margin-bottom:0}.panel>.list-group .list-group-item,.panel>.panel-collapse>.list-group .list-group-item{border-width:1px 0;border-radius:0}.panel>.list-group:first-child .list-group-item:first-child,.panel>.panel-collapse>.list-group:first-child .list-group-item:first-child{border-top:0;border-top-left-radius:3px;border-top-right-radius:3px}.panel>.list-group:last-child .list-group-item:last-child,.panel>.panel-collapse>.list-group:last-child .list-group-item:last-child{border-bottom:0;border-bottom-right-radius:3px;border-bottom-left-radius:3px}.panel-heading+.list-group .list-group-item:first-child{border-top-width:0}.list-group+.panel-footer{border-top-width:0}.panel>.panel-collapse>.table,.panel>.table,.panel>.table-responsive>.table{margin-bottom:0}.panel>.panel-collapse>.table caption,.panel>.table caption,.panel>.table-responsive>.table caption{padding-right:15px;padding-left:15px}.panel>.table-responsive:first-child>.table:first-child,.panel>.table:first-child{border-top-left-radius:3px;border-top-right-radius:3px}.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child,.panel>.table:first-child>tbody:first-child>tr:first-child,.panel>.table:first-child>thead:first-child>tr:first-child{border-top-left-radius:3px;border-top-right-radius:3px}.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child td:first-child,.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child th:first-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child td:first-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child th:first-child,.panel>.table:first-child>tbody:first-child>tr:first-child td:first-child,.panel>.table:first-child>tbody:first-child>tr:first-child th:first-child,.panel>.table:first-child>thead:first-child>tr:first-child td:first-child,.panel>.table:first-child>thead:first-child>tr:first-child th:first-child{border-top-left-radius:3px}.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child td:last-child,.panel>.table-responsive:first-child>.table:first-child>tbody:first-child>tr:first-child th:last-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child td:last-child,.panel>.table-responsive:first-child>.table:first-child>thead:first-child>tr:first-child th:last-child,.panel>.table:first-child>tbody:first-child>tr:first-child td:last-child,.panel>.table:first-child>tbody:first-child>tr:first-child th:last-child,.panel>.table:first-child>thead:first-child>tr:first-child td:last-child,.panel>.table:first-child>thead:first-child>tr:first-child th:last-child{border-top-right-radius:3px}.panel>.table-responsive:last-child>.table:last-child,.panel>.table:last-child{border-bottom-right-radius:3px;border-bottom-left-radius:3px}.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child,.panel>.table:last-child>tbody:last-child>tr:last-child,.panel>.table:last-child>tfoot:last-child>tr:last-child{border-bottom-right-radius:3px;border-bottom-left-radius:3px}.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child td:first-child,.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child th:first-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child td:first-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child th:first-child,.panel>.table:last-child>tbody:last-child>tr:last-child td:first-child,.panel>.table:last-child>tbody:last-child>tr:last-child th:first-child,.panel>.table:last-child>tfoot:last-child>tr:last-child td:first-child,.panel>.table:last-child>tfoot:last-child>tr:last-child th:first-child{border-bottom-left-radius:3px}.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child td:last-child,.panel>.table-responsive:last-child>.table:last-child>tbody:last-child>tr:last-child th:last-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child td:last-child,.panel>.table-responsive:last-child>.table:last-child>tfoot:last-child>tr:last-child th:last-child,.panel>.table:last-child>tbody:last-child>tr:last-child td:last-child,.panel>.table:last-child>tbody:last-child>tr:last-child th:last-child,.panel>.table:last-child>tfoot:last-child>tr:last-child td:last-child,.panel>.table:last-child>tfoot:last-child>tr:last-child th:last-child{border-bottom-right-radius:3px}.panel>.panel-body+.table,.panel>.panel-body+.table-responsive,.panel>.table+.panel-body,.panel>.table-responsive+.panel-body{border-top:1px solid #ddd}.panel>.table>tbody:first-child>tr:first-child td,.panel>.table>tbody:first-child>tr:first-child th{border-top:0}.panel>.table-bordered,.panel>.table-responsive>.table-bordered{border:0}.panel>.table-bordered>tbody>tr>td:first-child,.panel>.table-bordered>tbody>tr>th:first-child,.panel>.table-bordered>tfoot>tr>td:first-child,.panel>.table-bordered>tfoot>tr>th:first-child,.panel>.table-bordered>thead>tr>td:first-child,.panel>.table-bordered>thead>tr>th:first-child,.panel>.table-responsive>.table-bordered>tbody>tr>td:first-child,.panel>.table-responsive>.table-bordered>tbody>tr>th:first-child,.panel>.table-responsive>.table-bordered>tfoot>tr>td:first-child,.panel>.table-responsive>.table-bordered>tfoot>tr>th:first-child,.panel>.table-responsive>.table-bordered>thead>tr>td:first-child,.panel>.table-responsive>.table-bordered>thead>tr>th:first-child{border-left:0}.panel>.table-bordered>tbody>tr>td:last-child,.panel>.table-bordered>tbody>tr>th:last-child,.panel>.table-bordered>tfoot>tr>td:last-child,.panel>.table-bordered>tfoot>tr>th:last-child,.panel>.table-bordered>thead>tr>td:last-child,.panel>.table-bordered>thead>tr>th:last-child,.panel>.table-responsive>.table-bordered>tbody>tr>td:last-child,.panel>.table-responsive>.table-bordered>tbody>tr>th:last-child,.panel>.table-responsive>.table-bordered>tfoot>tr>td:last-child,.panel>.table-responsive>.table-bordered>tfoot>tr>th:last-child,.panel>.table-responsive>.table-bordered>thead>tr>td:last-child,.panel>.table-responsive>.table-bordered>thead>tr>th:last-child{border-right:0}.panel>.table-bordered>tbody>tr:first-child>td,.panel>.table-bordered>tbody>tr:first-child>th,.panel>.table-bordered>thead>tr:first-child>td,.panel>.table-bordered>thead>tr:first-child>th,.panel>.table-responsive>.table-bordered>tbody>tr:first-child>td,.panel>.table-responsive>.table-bordered>tbody>tr:first-child>th,.panel>.table-responsive>.table-bordered>thead>tr:first-child>td,.panel>.table-responsive>.table-bordered>thead>tr:first-child>th{border-bottom:0}.panel>.table-bordered>tbody>tr:last-child>td,.panel>.table-bordered>tbody>tr:last-child>th,.panel>.table-bordered>tfoot>tr:last-child>td,.panel>.table-bordered>tfoot>tr:last-child>th,.panel>.table-responsive>.table-bordered>tbody>tr:last-child>td,.panel>.table-responsive>.table-bordered>tbody>tr:last-child>th,.panel>.table-responsive>.table-bordered>tfoot>tr:last-child>td,.panel>.table-responsive>.table-bordered>tfoot>tr:last-child>th{border-bottom:0}.panel>.table-responsive{margin-bottom:0;border:0}.panel-group{margin-bottom:20px}.panel-group .panel{margin-bottom:0;border-radius:4px}.panel-group .panel+.panel{margin-top:5px}.panel-group .panel-heading{border-bottom:0}.panel-group .panel-heading+.panel-collapse>.list-group,.panel-group .panel-heading+.panel-collapse>.panel-body{border-top:1px solid #ddd}.panel-group .panel-footer{border-top:0}.panel-group .panel-footer+.panel-collapse .panel-body{border-bottom:1px solid #ddd}.panel-default{border-color:#ddd}.panel-default>.panel-heading{color:#333;background-color:#f5f5f5;border-color:#ddd}.panel-default>.panel-heading+.panel-collapse>.panel-body{border-top-color:#ddd}.panel-default>.panel-heading .badge{color:#f5f5f5;background-color:#333}.panel-default>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#ddd}.panel-primary{border-color:#337ab7}.panel-primary>.panel-heading{color:#fff;background-color:#337ab7;border-color:#337ab7}.panel-primary>.panel-heading+.panel-collapse>.panel-body{border-top-color:#337ab7}.panel-primary>.panel-heading .badge{color:#337ab7;background-color:#fff}.panel-primary>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#337ab7}.panel-success{border-color:#d6e9c6}.panel-success>.panel-heading{color:#3c763d;background-color:#dff0d8;border-color:#d6e9c6}.panel-success>.panel-heading+.panel-collapse>.panel-body{border-top-color:#d6e9c6}.panel-success>.panel-heading .badge{color:#dff0d8;background-color:#3c763d}.panel-success>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#d6e9c6}.panel-info{border-color:#bce8f1}.panel-info>.panel-heading{color:#31708f;background-color:#d9edf7;border-color:#bce8f1}.panel-info>.panel-heading+.panel-collapse>.panel-body{border-top-color:#bce8f1}.panel-info>.panel-heading .badge{color:#d9edf7;background-color:#31708f}.panel-info>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#bce8f1}.panel-warning{border-color:#faebcc}.panel-warning>.panel-heading{color:#8a6d3b;background-color:#fcf8e3;border-color:#faebcc}.panel-warning>.panel-heading+.panel-collapse>.panel-body{border-top-color:#faebcc}.panel-warning>.panel-heading .badge{color:#fcf8e3;background-color:#8a6d3b}.panel-warning>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#faebcc}.panel-danger{border-color:#ebccd1}.panel-danger>.panel-heading{color:#a94442;background-color:#f2dede;border-color:#ebccd1}.panel-danger>.panel-heading+.panel-collapse>.panel-body{border-top-color:#ebccd1}.panel-danger>.panel-heading .badge{color:#f2dede;background-color:#a94442}.panel-danger>.panel-footer+.panel-collapse>.panel-body{border-bottom-color:#ebccd1}.embed-responsive{position:relative;display:block;height:0;padding:0;overflow:hidden}.embed-responsive .embed-responsive-item,.embed-responsive embed,.embed-responsive iframe,.embed-responsive object,.embed-responsive video{position:absolute;top:0;bottom:0;left:0;width:100%;height:100%;border:0}.embed-responsive-16by9{padding-bottom:56.25%}.embed-responsive-4by3{padding-bottom:75%}.well{min-height:20px;padding:19px;margin-bottom:20px;background-color:#f5f5f5;border:1px solid #e3e3e3;border-radius:4px;-webkit-box-shadow:inset 0 1px 1px rgba(0,0,0,.05);box-shadow:inset 0 1px 1px rgba(0,0,0,.05)}.well blockquote{border-color:#ddd;border-color:rgba(0,0,0,.15)}.well-lg{padding:24px;border-radius:6px}.well-sm{padding:9px;border-radius:3px}.close{float:right;font-size:21px;font-weight:700;line-height:1;color:#000;text-shadow:0 1px 0 #fff;filter:alpha(opacity=20);opacity:.2}.close:focus,.close:hover{color:#000;text-decoration:none;cursor:pointer;filter:alpha(opacity=50);opacity:.5}button.close{-webkit-appearance:none;padding:0;cursor:pointer;background:0 0;border:0}.modal-open{overflow:hidden}.modal{position:fixed;top:0;right:0;bottom:0;left:0;z-index:1050;display:none;overflow:hidden;-webkit-overflow-scrolling:touch;outline:0}.modal.fade .modal-dialog{-webkit-transition:-webkit-transform .3s ease-out;-o-transition:-o-transform .3s ease-out;transition:transform .3s ease-out;-webkit-transform:translate(0,-25%);-ms-transform:translate(0,-25%);-o-transform:translate(0,-25%);transform:translate(0,-25%)}.modal.in .modal-dialog{-webkit-transform:translate(0,0);-ms-transform:translate(0,0);-o-transform:translate(0,0);transform:translate(0,0)}.modal-open .modal{overflow-x:hidden;overflow-y:auto}.modal-dialog{position:relative;width:auto;margin:10px}.modal-content{position:relative;background-color:#fff;-webkit-background-clip:padding-box;background-clip:padding-box;border:1px solid #999;border:1px solid rgba(0,0,0,.2);border-radius:6px;outline:0;-webkit-box-shadow:0 3px 9px rgba(0,0,0,.5);box-shadow:0 3px 9px rgba(0,0,0,.5)}.modal-backdrop{position:fixed;top:0;right:0;bottom:0;left:0;z-index:1040;background-color:#000}.modal-backdrop.fade{filter:alpha(opacity=0);opacity:0}.modal-backdrop.in{filter:alpha(opacity=50);opacity:.5}.modal-header{min-height:16.43px;padding:15px;border-bottom:1px solid #e5e5e5}.modal-header .close{margin-top:-2px}.modal-title{margin:0;line-height:1.42857143}.modal-body{position:relative;padding:15px}.modal-footer{padding:15px;text-align:right;border-top:1px solid #e5e5e5}.modal-footer .btn+.btn{margin-bottom:0;margin-left:5px}.modal-footer .btn-group .btn+.btn{margin-left:-1px}.modal-footer .btn-block+.btn-block{margin-left:0}.modal-scrollbar-measure{position:absolute;top:-9999px;width:50px;height:50px;overflow:scroll}@media (min-width:768px){.modal-dialog{width:600px;margin:30px auto}.modal-content{-webkit-box-shadow:0 5px 15px rgba(0,0,0,.5);box-shadow:0 5px 15px rgba(0,0,0,.5)}.modal-sm{width:300px}}@media (min-width:992px){.modal-lg{width:900px}}.tooltip{position:absolute;z-index:1070;display:block;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:12px;font-weight:400;line-height:1.4;filter:alpha(opacity=0);opacity:0}.tooltip.in{filter:alpha(opacity=90);opacity:.9}.tooltip.top{padding:5px 0;margin-top:-3px}.tooltip.right{padding:0 5px;margin-left:3px}.tooltip.bottom{padding:5px 0;margin-top:3px}.tooltip.left{padding:0 5px;margin-left:-3px}.tooltip-inner{max-width:200px;padding:3px 8px;color:#fff;text-align:center;text-decoration:none;background-color:#000;border-radius:4px}.tooltip-arrow{position:absolute;width:0;height:0;border-color:transparent;border-style:solid}.tooltip.top .tooltip-arrow{bottom:0;left:50%;margin-left:-5px;border-width:5px 5px 0;border-top-color:#000}.tooltip.top-left .tooltip-arrow{right:5px;bottom:0;margin-bottom:-5px;border-width:5px 5px 0;border-top-color:#000}.tooltip.top-right .tooltip-arrow{bottom:0;left:5px;margin-bottom:-5px;border-width:5px 5px 0;border-top-color:#000}.tooltip.right .tooltip-arrow{top:50%;left:0;margin-top:-5px;border-width:5px 5px 5px 0;border-right-color:#000}.tooltip.left .tooltip-arrow{top:50%;right:0;margin-top:-5px;border-width:5px 0 5px 5px;border-left-color:#000}.tooltip.bottom .tooltip-arrow{top:0;left:50%;margin-left:-5px;border-width:0 5px 5px;border-bottom-color:#000}.tooltip.bottom-left .tooltip-arrow{top:0;right:5px;margin-top:-5px;border-width:0 5px 5px;border-bottom-color:#000}.tooltip.bottom-right .tooltip-arrow{top:0;left:5px;margin-top:-5px;border-width:0 5px 5px;border-bottom-color:#000}.popover{position:absolute;top:0;left:0;z-index:1060;display:none;max-width:276px;padding:1px;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.42857143;text-align:left;white-space:normal;background-color:#fff;-webkit-background-clip:padding-box;background-clip:padding-box;border:1px solid #ccc;border:1px solid rgba(0,0,0,.2);border-radius:6px;-webkit-box-shadow:0 5px 10px rgba(0,0,0,.2);box-shadow:0 5px 10px rgba(0,0,0,.2)}.popover.top{margin-top:-10px}.popover.right{margin-left:10px}.popover.bottom{margin-top:10px}.popover.left{margin-left:-10px}.popover-title{padding:8px 14px;margin:0;font-size:14px;background-color:#f7f7f7;border-bottom:1px solid #ebebeb;border-radius:5px 5px 0 0}.popover-content{padding:9px 14px}.popover>.arrow,.popover>.arrow:after{position:absolute;display:block;width:0;height:0;border-color:transparent;border-style:solid}.popover>.arrow{border-width:11px}.popover>.arrow:after{content:"";border-width:10px}.popover.top>.arrow{bottom:-11px;left:50%;margin-left:-11px;border-top-color:#999;border-top-color:rgba(0,0,0,.25);border-bottom-width:0}.popover.top>.arrow:after{bottom:1px;margin-left:-10px;content:" ";border-top-color:#fff;border-bottom-width:0}.popover.right>.arrow{top:50%;left:-11px;margin-top:-11px;border-right-color:#999;border-right-color:rgba(0,0,0,.25);border-left-width:0}.popover.right>.arrow:after{bottom:-10px;left:1px;content:" ";border-right-color:#fff;border-left-width:0}.popover.bottom>.arrow{top:-11px;left:50%;margin-left:-11px;border-top-width:0;border-bottom-color:#999;border-bottom-color:rgba(0,0,0,.25)}.popover.bottom>.arrow:after{top:1px;margin-left:-10px;content:" ";border-top-width:0;border-bottom-color:#fff}.popover.left>.arrow{top:50%;right:-11px;margin-top:-11px;border-right-width:0;border-left-color:#999;border-left-color:rgba(0,0,0,.25)}.popover.left>.arrow:after{right:1px;bottom:-10px;content:" ";border-right-width:0;border-left-color:#fff}.carousel{position:relative}.carousel-inner{position:relative;width:100%;overflow:hidden}.carousel-inner>.item{position:relative;display:none;-webkit-transition:.6s ease-in-out left;-o-transition:.6s ease-in-out left;transition:.6s ease-in-out left}.carousel-inner>.item>a>img,.carousel-inner>.item>img{line-height:1}@media all and (transform-3d),(-webkit-transform-3d){.carousel-inner>.item{-webkit-transition:-webkit-transform .6s ease-in-out;-o-transition:-o-transform .6s ease-in-out;transition:transform .6s ease-in-out;-webkit-backface-visibility:hidden;backface-visibility:hidden;-webkit-perspective:1000;perspective:1000}.carousel-inner>.item.active.right,.carousel-inner>.item.next{left:0;-webkit-transform:translate3d(100%,0,0);transform:translate3d(100%,0,0)}.carousel-inner>.item.active.left,.carousel-inner>.item.prev{left:0;-webkit-transform:translate3d(-100%,0,0);transform:translate3d(-100%,0,0)}.carousel-inner>.item.active,.carousel-inner>.item.next.left,.carousel-inner>.item.prev.right{left:0;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}}.carousel-inner>.active,.carousel-inner>.next,.carousel-inner>.prev{display:block}.carousel-inner>.active{left:0}.carousel-inner>.next,.carousel-inner>.prev{position:absolute;top:0;width:100%}.carousel-inner>.next{left:100%}.carousel-inner>.prev{left:-100%}.carousel-inner>.next.left,.carousel-inner>.prev.right{left:0}.carousel-inner>.active.left{left:-100%}.carousel-inner>.active.right{left:100%}.carousel-control{position:absolute;top:0;bottom:0;left:0;width:15%;font-size:20px;color:#fff;text-align:center;text-shadow:0 1px 2px rgba(0,0,0,.6);filter:alpha(opacity=50);opacity:.5}.carousel-control.left{background-image:-webkit-linear-gradient(left,rgba(0,0,0,.5) 0,rgba(0,0,0,.0001) 100%);background-image:-o-linear-gradient(left,rgba(0,0,0,.5) 0,rgba(0,0,0,.0001) 100%);background-image:-webkit-gradient(linear,left top,right top,from(rgba(0,0,0,.5)),to(rgba(0,0,0,.0001)));background-image:linear-gradient(to right,rgba(0,0,0,.5) 0,rgba(0,0,0,.0001) 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#80000000', endColorstr='#00000000', GradientType=1);background-repeat:repeat-x}.carousel-control.right{right:0;left:auto;background-image:-webkit-linear-gradient(left,rgba(0,0,0,.0001) 0,rgba(0,0,0,.5) 100%);background-image:-o-linear-gradient(left,rgba(0,0,0,.0001) 0,rgba(0,0,0,.5) 100%);background-image:-webkit-gradient(linear,left top,right top,from(rgba(0,0,0,.0001)),to(rgba(0,0,0,.5)));background-image:linear-gradient(to right,rgba(0,0,0,.0001) 0,rgba(0,0,0,.5) 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#00000000', endColorstr='#80000000', GradientType=1);background-repeat:repeat-x}.carousel-control:focus,.carousel-control:hover{color:#fff;text-decoration:none;filter:alpha(opacity=90);outline:0;opacity:.9}.carousel-control .glyphicon-chevron-left,.carousel-control .glyphicon-chevron-right,.carousel-control .icon-next,.carousel-control .icon-prev{position:absolute;top:50%;z-index:5;display:inline-block}.carousel-control .glyphicon-chevron-left,.carousel-control .icon-prev{left:50%;margin-left:-10px}.carousel-control .glyphicon-chevron-right,.carousel-control .icon-next{right:50%;margin-right:-10px}.carousel-control .icon-next,.carousel-control .icon-prev{width:20px;height:20px;margin-top:-10px;font-family:serif;line-height:1}.carousel-control .icon-prev:before{content:'\2039'}.carousel-control .icon-next:before{content:'\203a'}.carousel-indicators{position:absolute;bottom:10px;left:50%;z-index:15;width:60%;padding-left:0;margin-left:-30%;text-align:center;list-style:none}.carousel-indicators li{display:inline-block;width:10px;height:10px;margin:1px;text-indent:-999px;cursor:pointer;background-color:#000 \9;background-color:rgba(0,0,0,0);border:1px solid #fff;border-radius:10px}.carousel-indicators .active{width:12px;height:12px;margin:0;background-color:#fff}.carousel-caption{position:absolute;right:15%;bottom:20px;left:15%;z-index:10;padding-top:20px;padding-bottom:20px;color:#fff;text-align:center;text-shadow:0 1px 2px rgba(0,0,0,.6)}.carousel-caption .btn{text-shadow:none}@media screen and (min-width:768px){.carousel-control .glyphicon-chevron-left,.carousel-control .glyphicon-chevron-right,.carousel-control .icon-next,.carousel-control .icon-prev{width:30px;height:30px;margin-top:-15px;font-size:30px}.carousel-control .glyphicon-chevron-left,.carousel-control .icon-prev{margin-left:-15px}.carousel-control .glyphicon-chevron-right,.carousel-control .icon-next{margin-right:-15px}.carousel-caption{right:20%;left:20%;padding-bottom:30px}.carousel-indicators{bottom:20px}}.btn-group-vertical>.btn-group:after,.btn-group-vertical>.btn-group:before,.btn-toolbar:after,.btn-toolbar:before,.clearfix:after,.clearfix:before,.container-fluid:after,.container-fluid:before,.container:after,.container:before,.dl-horizontal dd:after,.dl-horizontal dd:before,.form-horizontal .form-group:after,.form-horizontal .form-group:before,.modal-footer:after,.modal-footer:before,.nav:after,.nav:before,.navbar-collapse:after,.navbar-collapse:before,.navbar-header:after,.navbar-header:before,.navbar:after,.navbar:before,.pager:after,.pager:before,.panel-body:after,.panel-body:before,.row:after,.row:before{display:table;content:" "}.btn-group-vertical>.btn-group:after,.btn-toolbar:after,.clearfix:after,.container-fluid:after,.container:after,.dl-horizontal dd:after,.form-horizontal .form-group:after,.modal-footer:after,.nav:after,.navbar-collapse:after,.navbar-header:after,.navbar:after,.pager:after,.panel-body:after,.row:after{clear:both}.center-block{display:block;margin-right:auto;margin-left:auto}.pull-right{float:right!important}.pull-left{float:left!important}.hide{display:none!important}.show{display:block!important}.invisible{visibility:hidden}.text-hide{font:0/0 a;color:transparent;text-shadow:none;background-color:transparent;border:0}.hidden{display:none!important}.affix{position:fixed}@-ms-viewport{width:device-width}.visible-lg,.visible-md,.visible-sm,.visible-xs{display:none!important}.visible-lg-block,.visible-lg-inline,.visible-lg-inline-block,.visible-md-block,.visible-md-inline,.visible-md-inline-block,.visible-sm-block,.visible-sm-inline,.visible-sm-inline-block,.visible-xs-block,.visible-xs-inline,.visible-xs-inline-block{display:none!important}@media (max-width:767px){.visible-xs{display:block!important}table.visible-xs{display:table}tr.visible-xs{display:table-row!important}td.visible-xs,th.visible-xs{display:table-cell!important}}@media (max-width:767px){.visible-xs-block{display:block!important}}@media (max-width:767px){.visible-xs-inline{display:inline!important}}@media (max-width:767px){.visible-xs-inline-block{display:inline-block!important}}@media (min-width:768px)and (max-width:991px){.visible-sm{display:block!important}table.visible-sm{display:table}tr.visible-sm{display:table-row!important}td.visible-sm,th.visible-sm{display:table-cell!important}}@media (min-width:768px)and (max-width:991px){.visible-sm-block{display:block!important}}@media (min-width:768px)and (max-width:991px){.visible-sm-inline{display:inline!important}}@media (min-width:768px)and (max-width:991px){.visible-sm-inline-block{display:inline-block!important}}@media (min-width:992px)and (max-width:1199px){.visible-md{display:block!important}table.visible-md{display:table}tr.visible-md{display:table-row!important}td.visible-md,th.visible-md{display:table-cell!important}}@media (min-width:992px)and (max-width:1199px){.visible-md-block{display:block!important}}@media (min-width:992px)and (max-width:1199px){.visible-md-inline{display:inline!important}}@media (min-width:992px)and (max-width:1199px){.visible-md-inline-block{display:inline-block!important}}@media (min-width:1200px){.visible-lg{display:block!important}table.visible-lg{display:table}tr.visible-lg{display:table-row!important}td.visible-lg,th.visible-lg{display:table-cell!important}}@media (min-width:1200px){.visible-lg-block{display:block!important}}@media (min-width:1200px){.visible-lg-inline{display:inline!important}}@media (min-width:1200px){.visible-lg-inline-block{display:inline-block!important}}@media (max-width:767px){.hidden-xs{display:none!important}}@media (min-width:768px)and (max-width:991px){.hidden-sm{display:none!important}}@media (min-width:992px)and (max-width:1199px){.hidden-md{display:none!important}}@media (min-width:1200px){.hidden-lg{display:none!important}}.visible-print{display:none!important}@media print{.visible-print{display:block!important}table.visible-print{display:table}tr.visible-print{display:table-row!important}td.visible-print,th.visible-print{display:table-cell!important}}.visible-print-block{display:none!important}@media print{.visible-print-block{display:block!important}}.visible-print-inline{display:none!important}@media print{.visible-print-inline{display:inline!important}}.visible-print-inline-block{display:none!important}@media print{.visible-print-inline-block{display:inline-block!important}}@media print{.hidden-print{display:none!important}}
</style>


<style>

body {
  background-color: #90CEF4;
  background: radial-gradient(circle,#94d2f8,#94D0F4);
  background: radial-gradient(circle,#E7F5FD,#84BADC);
  overflow-y: scroll;
  font-size: 14px;
}

a {
    font-size: 14px;
}

button {
    background-color: #fff;
}


.table {
    background-color : #fff;
}

.table > tbody > tr:hover > td, .table-hover > tbody > tr:hover > th {
  background-color: #FFF4D1;
  -moz-transition: all 0.5s ease;
  -webkit-transition: all 0.5s ease;
  -o-transition: all 0.5s ease;
  -ms-transition: all 0.5s ease;
}


/* --- reset --- */
* { border : 0; outline: 0; margin: 0; padding: 0;
    font-family: inherit; font-weight: inherit; font-style : inherit;
    vertical-align: baseline; outline: none; }


/* --- general formatting --- */

body { height: 100%; font-size: 1em; font-family: sans-serif; }

p, table, ol { margin-bottom: .6em;}

h1,h2,h3,h4,h5,h6 { font-weight: bold; }
h2, h3 { font-size: 1.2em; margin: .4em 0 .4em 0; } /*TRBL*/

li { margin-left: 2em }

i, em     { font-style : italic; }
b, strong { font-weight: bold;   }

:focus {outline:0;}
:hover {border-color: #333;}

table { border-collapse:separate; border-spacing:0; }
th,td { text-align:left; font-weight:400; }

label { font-size : 1em; font-weight: bold; }

pre { background: white; border: 1px solid #777; padding: .2em; margin: 0; }

input[type="text"]     { width: 100%; border: 1px solid #777; }
input[type="password"] { width: 100%; border: 1px solid #777; }
input[type="file"]     { width: 100%; border: 1px solid #777; background-color: white; margin: 0; }

input[readonly]        { color: #333; background-color: #EEE; }
input[disabled]        { color: #555; background-color: #EEE; }

input:focus  { background-color: rgb(255,250,150); border: 1px solid #333; }
input:hover  { background-color: rgb(255,250,150); }

/*-- Must be after input:focus, as it alters border --*/
input[type="checkbox"] { cursor: pointer; border: none;}

button:hover  { background-color: rgb(255,250,150); border-color: #333;}
button:focus  { background-color: rgb(255,250,150); border-color: #333;}
button:active { background-color: rgb(245,245,50);  border-color: #333;}

/* --- layout --- */

#main {
	border : 0px solid #777;
	width  : 810px;     /*Adjusted by $MAIN_WIDTH config variable*/
	margin : 0 auto 2em auto;
	}


#header {
	border-bottom : 1px solid #777;
	padding: 4px 0px 1px 0px;
	margin : 0 0 0 0;
	}


#logo {
	font-family: 'Trebuchet MS', sans-serif;
	font-size:2em;
	font-weight: bold;
	color: black;
	padding: .1em;
	}


.h2_filename {
	border: 1px solid #777;
	padding: .1em .2em .1em .2em;
	font-weight: 700;
	font-family: courier;
	background-color: #EEE;
	}


#message_box { border: none; margin: .5em 0 0 0; padding: 0; min-height: 1.88em;}

.message_box_contents { border: 1px solid #CC0000; border-radius:4px; padding: 10px; margin-bottom:10px; font-size:14px; background: #FF691F;color:#fff }

#message_box #message_left {
	float  : left;
	margin : 0;
	padding: 0;
	border : none;
	font-weight : 900;
	}

#message_box  #X_box {
	float: right;
  font-size: 24px;
  font-weight: 700;
  line-height: 1;
  color: #000;
  text-shadow: 0 1px 0 #fff;
  filter: alpha(opacity=20);
  opacity: .2;
  position: relative;
 top: 8px;
  right: 15px;
	}

#message_box  #X_box:hover  {background-color: rgb(255,250,150); border-color: #333}
#message_box  #X_box:focus  {background-color: rgb(255,250,150); border-color: #333}
#message_box  #X_box:active {background-color: rgb(245,245,50); }

.filename { font-family: courier; }


/* ------ INDEX Page ------ */

#index_page_buttons     { margin: 0 0 0 0; }
#index_page_buttons div { display: inline-block; vertical-align: bottom; }


/*** Select All [x] ***/
#select_all_label {
	font   : 400 .84em arial;
	color  : #333;
	display: inline-block;
	padding: 4px 0 3px 0;
	cursor : pointer;
	width  : 72px; /*Adjusted by langauge files*/
	border-right: solid transparent 1px;
	}

#select_all_label:hover  { background-color: rgb(255,250,150); }
#select_all_label:active { background-color: rgb(245,245, 50); }


/*** Directory list file select boxes ***/
/*ckbox is assigned to <div>'s etc that contain <input type=checkbox>*/
.ckbox        {padding: 0px 4px 2px 4px; display: inline-block;}

/* Slightly darker colors for [m][c][d] file options since they are small & less noticable*/
.MCD:hover  {background-color: rgb(255,240,100);}
.MCD:focus  {background-color: rgb(250,240,100);}
.MCD:active {background-color: rgb(245,245, 50);}


/*** [x] (folders first) ***/

#ff_ckbox_div {float: left;}

#folders_first_label {
	display: inline-block;
	float: left;
	font-size  : .9em;
	font-weight: normal;
	border     : solid 1px transparent;
	color  : #333;
	margin : 0 0 0 0;
	padding: 4px 3px 2px 0;
	cursor : pointer;
	}

#folders_first_label:hover {
	background-color  : rgb(255,250,150);
	border-left-color : silver;
	border-right-color: silver;
	}

#folders_first_label:active {background-color  : rgb(245,245, 50);}


/* --- Directory Listing --- */

table.index_T {
	min-width: 30em;
	font-size: .95em;
	border         : 1px solid #777;
	border-collapse: collapse;
	margin: .5em 0 0 0;
	background-color: #FFF;
	}

table.index_T tr:hover {border: 1px solid #777; background-color: #EEE}
table.index_T th { border: 1px inset silver; vertical-align: middle; text-align: center; padding: 0 ;}
table.index_T td { border: 1px inset silver; vertical-align: middle;}
table.index_T th:hover { background-color: white;}

.index_T td a {	display: block; border: none; padding: 2px 4px 2px 4px; overflow : hidden; }
.index_T th a { padding: 1px 0 1px 0; border-width: 0px;}

th.file_name {min-width: 15em}

.index_T th.file_name a {
	display: inline-block;
	padding: 4px 1em 3px 1em;
	border-top-width: 0px;
	border-bottom-width: 0px;
	text-align: center;
	}

#header_filename       {border-width: 0 1px 0 1px; display: block; overflow: auto;}
#header_filename:hover {border-color: silver;}
#header_filename:focus {border-color: silver;}

.index_T th.file_size a { display  : block; padding: 4px 0 3px 0; }
.index_T th.file_time a { display  : block; padding: 4px 0 3px 0; }

#header_sorttype       {float: right; padding: 4px 5px 3px 4px; border-width: 0px 0 0 1px;}
#header_sorttype:hover {border-color: silver;}
#header_sorttype:focus {border-color: silver;}

.file_name { max-width: 26em; }
.file_size { min-width:  6em; padding-left: 10px; }
.file_time { min-width: 10em; padding-left: 10px; }

.meta_T { padding-right: 4px; text-align: right; font-family: tahoma; font-size: 12px; color: #222; }

#DIRECTORY_FOOTER {text-align: center; font-size: .9em; color: #333; padding: 3px 0 0 0; }


/*** front_links:  [New File] [New Folder] [Upload File] ***/
.front_links { float: right; }



/*** [Move] [Copy] [Delete] ***/

#mcd_submit { margin: 0; height: 1.8em; }


.buttons_right         { float: right; }
.buttons_right .button { margin-left: .5em; }


#renamefile_btn {padding: 2px 7px 4px 7px;}


/* --- header --- */

.nav   { float: right; display: inline-block; margin-top: 1.35em; font-size : 1em; }
.nav a { border: 1px solid transparent; font-weight: bold; padding: .2em .6em .1em .6em; }
.nav a:hover  { border: 1px solid #333; }
.nav a:focus  { border: 1px solid #333; }


/* --- edit --- */

#edit_header  {margin: .5em 0 0 0;}
#edit_header a:hover  { border: 1px solid #000; }

#edit_form    {margin: 0;}

.edit_disabled {
	border : 1px solid #777;
	width  : 99%;
	height : 35em;
	padding: .2em;
	margin : .5em 0 .6em 0;
	color  : #222;
	background-color: #FFF000;
	line-height: 1.4em;
	white-space: pre-wrap;
	word-wrap: break-word;
	overflow: auto;
	}

.view_file { font: .9em Courier; background-color: #F8F8F8; }

#file_editor {
	border: 1px solid #999;
	font  : 1.4em Courier;
	margin: 0 0 .7em 0;
	width : 99.8%;
	height: 32em;
	}

#file_editor:focus { border: 1px solid #000; }

.file_meta	{ float: left; margin-top: .6em; font-size: .95em; color: #222; }

#edit_notes { font-size: .8em; color: #222 ;margin-top: 1em; clear:both; }

.notes      { margin-bottom: .4em; }


/* --- log in --- */

#login_page {
    background-color: #fff;
	border  : 1px solid #777;
	width   : 370px;
	margin  : 5em auto;
	padding : .5em 1.2em .1em 1em;
	}

#login_page .nav { margin-top: .5em; }

#login_page input {margin: 0 0 .7em 0;}


hr { /*-- -- -- -- -- -- --*/
	line-height  : 0;
	Xfont-size    : 1px;
	display : block;
	position: relative;
	padding : 0;
	margin  : .6em auto;
	width   : 100%;
	clear   : both;
	border  : none;
	border-top   : 1px solid #777;
	Xborder-bottom: 1px solid #eee;
	overflow: visible;
	}


.verify {
	min-width       : 50%;
	font            : 1em Courier;
	border          : 1px solid gray;
	border-collapse : collapse;
	background-color: white;
	}

.verify th {
	border          : 1px solid gray;
	padding         : 0 1em 0 1em;
	text-align      : center;
	font-weight     : 900;
	font-family     : arial;
	background-color: #EEE;
	}

.verify td {
	border : 1px inset silver;
	padding: .1em 1em .1em .5em;
	vertical-align: middle;
	}

.verify_del {
	font  : 1em Courier;
	border: 1px solid #F00;
	padding: .2em .4em;
	color  : #222;
	background-color: #FDD;
	}

.verify_del td { border: 1px solid #F44; }


#admin {padding: 3px 5px;}

.admin_buttons .button {margin-right: .5em;}

.clear {clear:both; padding: 0; margin: 0; border: none}

.web_root { border: 1px solid  #777; border-right: none; font: 1em Courier; padding: 1px; background-color: #EEE;}

.mono {font-family: courier;}

.info {margin: .7em 0 .5em 0; background: #f9f9f9; padding: .2em .5em;}

.path {padding: 1px 5px 1px 5px} /*TRBL*/

.timer {border: 1px solid #eee; padding: 3px .5em 4px .5em;}

.timeout {float:right; font-size: .95em; color: #111;}

.edit_btns_top {margin: .2em 0 .5em 0;}

.image_info {
	color: #222;
	font-size: 1em ; /*Adjusted by langauge files*/
	margin: .7em 0 1em 0;
	}

.edit_btns_bottom {float: right;}
.edit_btns_bottom .button { margin-left: .7em; } /*Adjusted by langauge files*/
.edit_btns_bottom .RCD { padding-left: 5px; padding-right: 6px; }
.edit_btns_bottom svg  { padding : 0 4px 0 0; }

input[type="text"]#new_name {width  : 50%; margin-bottom: .2em;}
#new_location {border-left: none;}

#del_backup   { margin: 0; padding: 2px 5px}

/*** For old IE only: text "icons" for Rename, Copy, and Delete ***/
.RCD1  {font: 900 7pt arial; padding: 0px 3px 0px 3px; margin: 0px; float: left}
.R    {color: #00a;    border: 1px solid #804000}
.C    {color: #006400; border: 1px solid #008400}
.D    {color: #b00;    border: 1px solid #b00}

.action   {display: inline-block}
.ren_over {display: inline-block}
.ren_over input {margin: 0 0 0 2em}
.ren_over label {font-weight: normal}

#path_header{
	display: inline-block;
	background-color:white;
	font-weight: normal;
	padding: 0 .5em 0 0;
	margin: .5em 0 0 0;
	}

#path_header a {
	outline: none;
	border: none;
	border-left : solid 1px transparent;
	border-right: solid 1px transparent;
	display: inline-block;
	padding: 1px 5px 0 5px;
	}

</style>
  <?php
}
//end style_sheet() //*********************************************************
function Language_and_config_adjusted_styles() {
//******************************
  global $_, $MAIN_WIDTH, $message, $page;
  ?>
<style>
#main { width: <?php echo $MAIN_WIDTH ?>; } /*Default 810px*/

.button {
	padding  : <?php echo $_['button_padding'] ?>; /*Default 4px 7px 4px 7px */
	font-size: <?php echo $_['button_font_size'] ?>; /*Default .9em */
	}

.front_links a {
	font-size  : <?php echo $_['front_links_font_size'] ?>; /*Default 1em */
	margin-left: <?php echo $_['front_links_margin_L'] ?>; /*Default 1em */
	}

#mcd_submit button{ margin-right: <?php echo $_['MCD_margin_R'] ?>;}  /*Default 1em*/

.image_info { font-size: <?php echo $_['image_info_font_size'] ?>; }   /*Default 1em*/

.edit_btns_bottom .button {
	margin-left: <?php echo $_['button_margin_L'] ?>; /*Default .7em*/
	}

#select_all_label { font-size: <?php echo $_['select_all_label_size'] ?>; } /*Default .84em */
#select_all_label { width: <?php echo $_['select_all_label_width'] ?>; }    /*Default 72px  */
</style>
  <?php
}
//end Language_and_config_adjusted_styles() //*********************************
function Load_style_sheet() {
//*************************************************
  global $CSS_FILE, $message;
  style_sheet(); //first load built-in defaults
  if (isset ($CSS_FILE)) { //Check for external file
    echo '<link rel="stylesheet" type="text/css" href="' . URLencode_path($CSS_FILE) . '">';
  }
  Language_and_config_adjusted_styles();
}
//end Load_style_sheet() //****************************************************
//******************************************************************************
//Main logic to determine page action
//******************************************************************************
Default_Language();
System_Setup();
Session_Startup();
if (!isset ($_SESSION['admin_page'])) {
  $_SESSION['admin_page'] = false;
  $_SESSION['admin_ipath'] = '';
}
if ($_SESSION['valid']) {
  undo_magic_quotes();
  Init_ICONS();
  Get_GET();
  if ($page == "phpinfo") {
    phpinfo();
    die;
  }
  Valid_Path($ipath, true);
  Validate_params();
  Init_Macros(); //Needs to be after Get_Get()/Validate_params()/Valid_Path()
//$ACCESS_ROOT.$ACCESS_PATH == $ipath
  $ipath_len = mb_strlen($ipath);
  $ACCESS_PATH = '';
  if (($ACCESS_ROOT_len < $ipath_len)) {
    $ACCESS_PATH = trim(mb_substr($ipath, $ACCESS_ROOT_len), ' /') . '/';
  }
  Respond_to_POST();
  Verify_Page_Conditions(); //Must come after Respond_to_POST()
  Update_Recent_Pages();
//Don't show current/path/ header on some pages.
  $Show_Path = true;
  $pages_dont_show_path = array("login", "admin", "hash", "changepw", "changeun");
  if (in_array($page, $pages_dont_show_path)) {
    $Show_Path = false;
  } //
}
//end if $_SESSION[valid]
//end logic to determine page action *******************************************
//******************************************************************************
//Output page contents
//******************************************************************************
$early_output = ob_get_clean(); // Should be blank unless trouble-shooting.
ob_start();
header('Content-type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="noindex">
<?php
echo '<title>' . hsc($config_title . ' - ' . Page_Title()) . '</title>' . "\n";
Load_style_sheet();
common_scripts();
echo '</head><body>';
Error_reporting_status_and_early_output(0, 0); //0,0 will only show early output.
if ($_SESSION['valid']) {
  echo '<div id="main" >';
}
else {
  echo '<div id="login_page">';
}
Page_Header();
if ($_SESSION['valid'] && $Show_Path) {
  Current_Path_Header();
}
$TABINDEX_XBOX = $TABINDEX++; //Messages, and the [X] box, not displayed until later.
echo '<div id="message_box"></div>';
Load_Selected_Page();
//footer...
if ($_SESSION['valid']) {
//Countdown timer
  echo '<hr style="border-color: white;">';
  echo '<span id=timer0  class="timer timeout"></span>';
  echo '<span class="timeout">' . hsc($_['time_out_txt']) . '&nbsp; </span>';
//Adjust tabindex to account for [m][c][d][x] and file names in directory list.
//(Directory list created via js, so $TAB_INDEX is also passed to, and handled by, js at that point.)
  if (isset ($DIRECTORY_COUNT)) {
    $TAB_INDEX = "tabindex=" . ($TABINDEX + ($DIRECTORY_COUNT * 5));
  }
  else {
    $TAB_INDEX = "";
  }
//Admin link
  if (($_SESSION['admin_page'] === false)) {
    echo '<a id="admin" ' . $TAB_INDEX . ' href="' . $ONESCRIPT . $param1 . $param2 . '&amp;p=admin">' . hsc($_['Admin']) . '</a>';
  }
}
//end footer
echo '</div>'; //end main/login_page
echo "</body></html>\n";
if (($page == "edit") && $WYSIWYG_VALID && $EDIT_WYSIWYG) {
  include ($WYSIWYG_PLUGIN_OS);
}
//Display any $message's
echo '<script>';
echo 'var $tabindex_xbox = ' . $TABINDEX_XBOX . ';'; //Used in Display_Messages()
echo 'var $page    = "' . $page . '";';
echo 'var $message = "' . addslashes($message) . '";';
//Cause $message's $X_box to take focus on these pages only.
echo 'if (($page == "index") || ($page == "edit")) {take_focus = 1}';
echo 'else										   {take_focus = 0}';
//##### ACTUAL COUNTDOWN STARTS ON THE SERVER.
//##### DO I NEED TO ACCOUNT FOR TIME RECEIVING & LOADING PAGE CLIENT SIDE?
//The setTimeout() delay should be greater than what is set for the Sort_and_Show() "working..." message.
echo 'setTimeout("Display_Messages($message, take_focus)", ' . $DELAY_final_messages . ');';
echo '</script>';
//start any timers (Yea, they could probably be put in a window.onload function or something...)
if ($_SESSION['valid']) {
  echo Timeout_Timer($MAX_IDLE_TIME, 'timer0', 'LOGOUT');
}
if ($page == 'edit') {
  echo Timeout_Timer($MAX_IDLE_TIME, 'timer1', 'LOGOUT');
}
if ($LOGIN_DELAYED > 0) {
  echo Timeout_Timer($LOGIN_DELAYED, 'timer0', '');
}
//##### END OF FILE ############################################################
//##### Header (UTF-8) for [View Raw] incorrect or not getting sent??
//##### If file has non-ascii characters, browers display in ISO-8859-1/Windows-1252,
//##### Except IE, which asks to download the file...
//##### When browsers manually set to UTF-8, files display fine.