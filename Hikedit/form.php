<?php
include 'top.php';
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// We print out the post array so that we can see our form is working.
// if ($debug){  // later you can uncomment the if statement
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
// }

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.

$thisURL = $domain . $phpSelf;


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form

$hiker = "";
$hikedate = "";
$trail = "";

$query = "SELECT pmkTrailsId FROM tblTrails";
$records = '';
if ($thisDatabaseReader->querySecurityOk($query, 0)) {
    $query = $thisDatabaseReader->sanitizeQuery($query);
    $records = $thisDatabaseReader->select($query, '');
}
foreach ($records as $record) {
    $trail = $record['pmkTrailsId'];
    break; //fake loop to get a default value
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
// We are keeping track of the first mistake we make so we can set the
// autofocus to it.

$hikerError = false;
$hikedateError = false;
$trailError = false;

////%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to a CSV file
// ** stored in database in this lab
$dataRecord = array();

// have we mailed the information to the user?
// ** not emaling in this lab
// $mailed=false;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2a Security
    //
    if (!securityCheck($thisURL)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported.</p>";
        die($msg);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2b Sanitize (clean) data
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.

    $hiker = htmlentities($_POST["pmkHikerId"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $hiker;

    $hikedate = htmlentities($_POST["fldDateHiked"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $hikedate;

    $trail = htmlentities($_POST["pmkTrailId"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $trail;


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2c Validation
    //
    // Validation section. Check each value for possible errors, empty or
    // not what we expect. You will need an IF block for each element you will
    // check (see above section 1c and 1d). The if blocks should also be in the
    // order that the elements appear on your form so that the error messages
    // will be in the order they appear. errorMsg will be displayed on the form
    // see section 3b. The error flag ($emailERROR) will be used in section 3c.

    if($hiker == ""){
        $errorMsg[] = "Please select a hiker";
        $hikerError = true;
    } elseif (!verifyAlphaNum($hiker)) {
        $errorMsg[] = "Please select a hiker";
        $hikerError = true;
    }

    if($hikedate == "") {
        $errorMsg[] = "Please enter a date";
        $hikedateError = true;
    } elseif (!verifyDate($hikedate)) {
        $errorMsg[] = "Please enter a valid date";
        $hikerError = true;
    }
        // hopefully this error checking is unnecessary
        // because we have a default value
    if($trail == "") {
        $errorMsg[] = "Please select a trail";
        $trailError = true;
    } elseif(!verifyAlphaNum($trail)) {
        $errorMsg[] = "Please select a trail";
        $trailError = true;
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";


        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2e Save Data
        //
        // This block saves the data to a CSV file.

        /* ** not in this lab
         // WE WILL BE SAVING INTO A DATABASE
        $fileExt = ".csv";
        $myFileName = "data/registration"; // NOTE YOU MUST MAKE THE FOLDER !!!
        $filename = $myFileName . $fileExt;

        if ($debug){
            print "\n\n<p>filename is " . $filename;
        }

        // now we just open the file for append
        $file = fopen($filename, 'a');

        // write the forms informations
        fputcsv($file, $dataRecord);

        // close the file
        fclose($file);
         */


        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2f Create message
        //
        // build a message to display on the screen in section 3a and to mail
        // to the person filling out the form (section 2g).

        $message = '<h2>Your information.</h2>';

        foreach ($_POST as $htmlName => $value) {
            $message .= "<p>";

            // breaks up the form names into words. for example
            // txtFirstName becomes First Name
            $camelCase = preg_split('/(?=[A-Z])/', substr($htmlName, 3));

            foreach ($camelCase as $oneWord) {
                $message .= $oneWord . " ";
            }

            $message .= " = " . htmlentities($value, ENT_QUOTES, "UTF-8") . "</p>";
        }

    } // end form is valid

}   // ends if form was submitted.


//#############################################################################
//
// SECTION 3 Display Form
//
?>

<article id="main">

    <?php
    //####################################
    //
    // SECTION 3a.
    //
    // If its the first time coming to the form or there are errors we are going
    // to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        print "<h2>Thank you for providing your information.</h2>";

    } else {

        print '<h2>Hiker Form</h2>';

        //####################################
        //
        // SECTION 3b Error Messages
        //
        // display any error messages before we print out the form

        if ($errorMsg) {
            print '<div id="errors">' . "\n";
            print "<h2>Your form has the following mistakes that need to be fixed.</h2>\n";
            print "<ol>\n";

            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }

            print "</ol>\n";
            print "</div>\n";
        }

		//####################################
        //
        // SECTION 3c html Form
        //
        /* Display the HTML form. note that the action is to this same page. $phpSelf
            is defined in top.php
            NOTE the line:
            value="<?php print $email; ?>
            this makes the form sticky by displaying either the initial default value (line ??)
            or the value they typed in (line ??)
            NOTE this line:
            <?php if($emailERROR) print 'class="mistake"'; ?>
            this prints out a css class so that we can highlight the background etc. to
            make it stand out that a mistake happened here.
       */
    ?>


    <form action="<?php print $phpSelf; ?>" id="frmRegister" method="post">
        <fieldset  class="listbox <?php if ($hikerError) print ' mistake'; ?>">
		<p>
            <legend>Select Hiker</legend>
	            <select id="pmkHikerId" name="pmkHikerId" tabindex="20" >
					 <?php
					 $query = "SELECT pmkHikerId, fldFirstName, fldLastName FROM tblHikers";
					 $records = '';

					 if ($thisDatabaseReader->querySecurityOk($query, 0)) {
    				 $query = $thisDatabaseReader->sanitizeQuery($query);
    				 $records = $thisDatabaseReader->select($query, '');
					 }

					 if(is_array($records)) {
					 	foreach($records as $record) {
						print '<option';
						if($hiker==$record['pmkHikerId']){ print " selected"; }
						print ' value="'.$record['pmkHikerId'].'">'.$record['fldFirstName']." ".$record['fldLastName'].'</option>'."\n\t\t\t\t\t ";
					 	}
					 }
                     print "\n";
					 ?>
			   </select>
        </p>
		</fieldset>

        <fieldset class="textarea">
        <p>
            <label class="required" for="fldDateHiked">Enter Date (yyyy-mm-dd)</label>
            <textarea <?php if ($hikedateError) print 'class="mistake"'; ?>
                id="fldDateHiked"
                name="fldDateHiked"
                onfocus="this.select()"
                tabindex="40"><?php print $hikedate; ?></textarea>
        </p>
        </fieldset>

    <fieldset class="radio <?php if ($trailError) print ' mistake'; ?>">
    <legend>Select a Trail</legend>
    <p>
    <?php
        $query = "SELECT pmkTrailsId, fldTrailName FROM tblTrails";
        $records = '';
        if ($thisDatabaseReader->querySecurityOk($query, 0)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $records = $thisDatabaseReader->select($query, '');
        }
        if(is_array($records)) {
            foreach($records as $record) {
                print '<label class="radio-field">';
                print "\n\t\t";
                print '<input type="radio"';
                if($trail==$record['pmkTrailsId']){ print ' checked="checked"'; }
                print ' value="'.$record['pmkTrailsId'].'"'.' name="pmkTrailId">';
                print "\n\t\t".$record['fldTrailName']."\n\t".'</label>'."\n\t";
            }
        }
        print "\n";
    ?>
    </p>
    </fieldset>

        <fieldset class="buttons">
            <legend></legend>
            <input class="button" id="btnSubmit" name="btnSubmit" tabindex="900" type="submit" value="Register" >
        </fieldset> <!-- ends buttons -->
    </form>

<?php
} // end body submit
?>

</article>

<?php include 'footer.php'; ?>
