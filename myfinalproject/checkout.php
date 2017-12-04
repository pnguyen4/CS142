<?php
include 'top.php';

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// We print out the post array so that we can see our form is working.
 if ($debug){  // later you can uncomment the if statement
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
 }

$update = false;

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

$shipName = "";
$shipAddress = "";
$shipCity = "";
$shipZip = "";
$billName = "";
$billAddress = "";
$billCity = "";
$billZip = "";
$email = "";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
// We are keeping track of the first mistake we make so we can set the
// autofocus to it.


$shipNameERROR = "";
$shipAddressERROR = "";
$shipCityERROR = "";
$shipZipERROR = "";
$billNameERROR = "";
$billAddressERROR = "";
$billCityERROR = "";
$billZipERROR = "";
$emailERROR = "";

////%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to a CSV file
// ** stored in database in this lab
$dataRecord = array();
$dataEntered = false;

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

    $shipName = htmlentities($_POST["fldShipName"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $shipName;

    $shipAddress = htmlentities($_POST["fldShipAddress"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $shipAddress;

    $shipCity = htmlentities($_POST["fldShipCity"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $shipCity;

    $shipZip = htmlentities($_POST["fldShipZip"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $shipZip;

    $billName = htmlentities($_POST["fldBillName"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $billName;

    $billAddress = htmlentities($_POST["fldBillAddress"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $billAddress;

    $billCity = htmlentities($_POST["fldBillCity"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $billCity;

    $billZip = htmlentities($_POST["fldBillZip"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $billZip;

    $email = htmlentities($_POST["fldEmail"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $email;

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

        // hopefully this error checking is unnecessary
        // because we have a default value
    if($trailName == "") {
        $errorMsg[] = "Please enter trail name";
        $trailError = true;
    } elseif(!verifyAlphaNum($trailName)) {
        $errorMsg[] = "Your trail name has an extra character";
        $trailNameERROR = true;
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
        // This block saves the data to a database (formerly csv file).

		$dataEntered = false;
        try {
            $thisDatabaseWriter->db->beginTransaction();

            $query = 'INSERT INTO tblTrails SET ';
            $query .= 'fldTrailName = ?, ';
            $query .= 'fldTotalDistance = ?, ';
            $query .= 'fldHikingTime = ?, ';
            $query .= 'fldVerticalRise = ?, ';
            $query .= 'fldRating = ? ';

            if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                $query = $thisDatabaseWriter->sanitizeQuery($query);
                $results = $thisDatabaseWriter->insert($query, $dataRecord);
                $primaryKey = $thisDatabaseWriter->lastInsert();

                if (DEBUG) {
                    print "<p>pmk= " . $primaryKey;
                }
            }

            foreach($tagRecord as $tags) {
                if(isset($_POST[str_replace(' ', '_', $tags['pmkTag'])])) {
                    $query = 'INSERT INTO tblTrailsTags SET ';
                    $query .= 'pfkTrailsId = ?,';
                    $query .= 'pfkTag = ?';
                    $thisquery = array($primaryKey, $tags['pmkTag']);

                    if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                        $query = $thisDatabaseWriter->sanitizeQuery($query);
                        $results = $thisDatabaseWriter->insert($query, $thisquery);
                        print_r($results);
                    }
                }
            }

            // all sql statements are done so lets commit to our changes

            $dataEntered = $thisDatabaseWriter->db->commit();

            if (DEBUG)
                print "<p>submission complete</p> ";
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if (DEBUG)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
        }

    } // end form is valid

}   // ends if form was submitted.
?>

<div id="wrapper">
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

        print '<h2>Checkout Form</h2>';

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

        <h1>Shipping Information: </h1>
        <fieldset>
            <label class="required" for="fldShipName">Full Name</label>
            <p>
                <input type="text" <?php if (false) print 'class="mistake"';?>
                    id="fldShipName"
                    name="fldShipName"
                    value="<?php print $shipName;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldShipAddress">Address Line</label>
            <p>
                <input type="text" <?php if (false) print 'class="mistake"';?>
                    id="fldShipAddress"
                    name="fldShipAddress"
                    value="<?php print $shipAddress;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldShipCity">City</label>
            <p>
                <input type="text" <?php if (false) print 'class="mistake"';?>
                    id="fldShipCity"
                    name="fldShipCity"
                    value="<?php print $shipCity;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldShipZip">Zip Code</label>
            <p>
                <input type="text" <?php if (false) print 'class="mistake"';?>
                    id="fldShipZip"
                    name="fldShipZip"
                    value="<?php print $shipZip;?>">
            </p>
        </fieldset>

        <h1>Billing Information: </h1>
        <fieldset>
            <label class="required" for="fldBillName">Full Name</label>
            <p>
                <input type="text" <?php if (false) print 'class="mistake"';?>
                    id="fldBillName"
                    name="fldBillName"
                    value="<?php print $billName;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldBillAddress">Address Line</label>
            <p>
                <input type="text" <?php if (false) print 'class="mistake"';?>
                    id="fldBillAddress"
                    name="fldBillAddress"
                    value="<?php print $billAddress;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldBillCity">City</label>
            <p>
                <input type="text" <?php if (false) print 'class="mistake"';?>
                    id="fldBillCity"
                    name="fldBillCity"
                    value="<?php print $billCity;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldBillZip">Zip Code</label>
            <p>
                <input type="text" <?php if (false) print 'class="mistake"';?>
                    id="fldBillZip"
                    name="fldBillZip"
                    value="<?php print $billZip;?>">
            </p>
        </fieldset>



        <!-- make this into a list box with valid countries being in a table
        <fieldset>
            <label class="required" for="fldShipCountry">Zip Code</label>
            <p>
                <input type="text" <?php if (false) print 'class="mistake"';?>
                    id="fldShipZip"
                    name="fldShipZip"
                    value="<?php print $shipZip;?>">
            </p>
        </fieldset>
        -->

        <fieldset>
        <legend>Select Tags (choose all that apply)</legend>
        <p>
        <?php
        if($update) {
            foreach($tagRecord as $tags) {
                $output = '<label><input ';
                if(isset($_POST[str_replace(' ', '_', $tags['pmkTag'])]) || in_array($tags['pmkTag'], $tagList)) {
                    $output .= ' checked ';
                }
                $output .= 'id="'.$tags['pmkTag'].'" name="';
                $output .= $tags['pmkTag'].'" type="checkbox" value="';
                $output .= $tags['pmkTag'].'">'.$tags['pmkTag'].'</label>';
                print $output;
                print "\n";
            }
        } else {
            foreach($tagRecord as $tags) {
                $output = '<label><input ';
                if(isset($_POST[str_replace(' ', '_', $tags['pmkTag'])])){
                    $output .= ' checked ';
                }
                $output .= 'id="'.$tags['pmkTag'].'" name="';
                $output .= $tags['pmkTag'].'" type="checkbox" value="';
                $output .= $tags['pmkTag'].'">'.$tags['pmkTag'].'</label>';
                print $output;
                print "\n";
            }
        }
        ?>

        </p>
        </fieldset>

        <fieldset class="buttons">
            <legend></legend>
            <input class="button" id="btnSubmit" name="btnSubmit" tabindex="900" type="submit" value="Submit" >
        </fieldset> <!-- ends buttons -->
    </form>

</div>

<?
    }
include 'footer.php';
?>
