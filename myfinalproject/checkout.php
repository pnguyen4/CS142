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
$shipState = "";
$shipZip = "";
$billName = "";
$billAddress = "";
$billCity = "";
$billState = "";
$billZip = "";
$email = "";
$cardNumber = "";
$expDate = "";
$cvv2 = "";
$shipRate = "";
$isGift = false;

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
$shipStateERROR = "";
$shipZipERROR = "";
$billNameERROR = "";
$billAddressERROR = "";
$billCityERROR = "";
$billStateERROR = "";
$billZipERROR = "";
$emailERROR = "";
$cardNumberERROR = "";
$expDateERROR = "";
$cvv2ERROR = "";
$shipRateERROR = "";

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

	$shipState = htmlentities($_POST["fldShipState"], ENT_QUOTES, "UTF-8");
	$dataRecord[] = $shipState;

    $shipZip = htmlentities($_POST["fldShipZip"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $shipZip;

    if(isset($_POST["fldbillequalship"])) {
        $billName = $shipName;
        $dataRecord[] = $billName;

        $billAddress = $shipAddress;
        $dataRecord[] = $billAddress;

        $billCity = $shipCity;
        $dataRecord[] = $billCity;

		$billState = $shipState;
		$dataRecord[] = $billState;
		$_POST["fldBillState"] = $shipState;
		//this won't show up in post array correctly because the post array gets
		//printed before this line is executed, but all the data is correct.

        $billZip = $shipZip;
        $dataRecord[] = $billZip;
    } else {
    	$billName = htmlentities($_POST["fldBillName"], ENT_QUOTES, "UTF-8");
    	$dataRecord[] = $billName;

    	$billAddress = htmlentities($_POST["fldBillAddress"], ENT_QUOTES, "UTF-8");
    	$dataRecord[] = $billAddress;

    	$billCity = htmlentities($_POST["fldBillCity"], ENT_QUOTES, "UTF-8");
    	$dataRecord[] = $billCity;

		$billState = htmlentities($_POST["fldBillState"], ENT_QUOTES, "UTF-8");
		$dataRecord[] = $billState;

    	$billZip = htmlentities($_POST["fldBillZip"], ENT_QUOTES, "UTF-8");
    	$dataRecord[] = $billZip;
    }

    $email = htmlentities($_POST["fldEmail"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $email;

    $cardNumber = htmlentities($_POST["fldCardNum"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $cardNumber;

    $expDate = htmlentities($_POST["fldExpDate"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $expDate;

    $cvv2 = htmlentities($_POST["fldCvv2"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $cvv2;

    if(isset($_POST["fldShipRate"])) {
        $shipRate = htmlentities($_POST["fldShipRate"], ENT_QUOTES, "UTF-8");
    } else { $shipRate = "economy"; }
    $dataRecord[] = $shipRate;

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

    if($shipName == "") {
        $errorMsg[] = "Please enter a Name for this shipping address.";
        $shipNameERROR = true;
    } elseif(!verifyAlphaNum($shipName)) {
        $errorMsg[] = "Error in processing shipping name.";
        $shipNameERROR = true;
    }

    if($shipAddress == "") {
        $errorMsg[] = "Please enter an address line for this shipping address.";
        $shipAddressERROR= true;
    } elseif(!verifyAlphaNum($shipAddress)) {
        $errorMsg[] = "Error in processing shipping address.";
        $shipAddressERROR= true;
    }

    if($shipCity == "") {
        $errorMsg[] = "Please enter a city name for this shipping address.";
        $shipCityERROR= true;
    } elseif(!verifyAlphaNum($shipCity)) {
        $errorMsg[] = "Error in processing shipping city.";
        $shipCityERROR= true;
    }

    if($shipState == "" || $shipState == "-") {
        $errorMsg[] = "Please select a state for this shipping address.";
        $shipStateERROR= true;
    } elseif(!verifyState($shipState)) {
        $errorMsg[] = "Error in processing State.";
        $shipStateERROR= true;
    }

    if($shipZip == "") {
        $errorMsg[] = "Please enter a zip code for this shipping address.";
        $shipZipERROR = true;
    } elseif(!verifyNumeric($shipZip)) {
        $errorMsg[] = "Error in processing shipping Zip Code.";
        $shipZipERROR = true;
    }

    if($billName == "") {
        $errorMsg[] = "Please enter a Name for this billing address.";
        $billNameERROR = true;
    } elseif(!verifyAlphaNum($billName)) {
        $errorMsg[] = "Error in processing billing name.";
        $billNameERROR = true;
    }

    if($billAddress == "") {
        $errorMsg[] = "Please enter an address line for this billing address.";
        $billAddressERROR= true;
    } elseif(!verifyAlphaNum($billAddress)) {
        $errorMsg[] = "Error in processing billing address.";
        $billAddressERROR= true;
    }

    if($billCity == "") {
        $errorMsg[] = "Please enter a city name for this billing address.";
        $billCityERROR = true;
    } elseif(!verifyAlphaNum($billCity)) {
        $errorMsg[] = "Error in processing billing city.";
        $billCityERROR = true;
    }

    if($billState == "" || $billState == "-") {
        $errorMsg[] = "Please select a state for this billing address.";
        $shipStateERROR= true;
    } elseif(!verifyState($billState)) {
        $errorMsg[] = "Error in processing State.";
        $billStateERROR= true;
    }

    if($billZip == "") {
        $errorMsg[] = "Please enter a zip code for this billing address.";
        $billZipERROR = true;
    } elseif(!verifyNumeric($billZip)) {
        $errorMsg[] = "Error in processing billing Zip Code.";
        $shipZipERROR = true;
    }

    if($email == "") {
        $errorMsg[] = "Please enter an email.";
        $emailERROR = true;
    } elseif(!verifyEmail($email)) {
        $errorMsg[] = "Your email is invalid.";
        $emailERROR = true;
    }

    if($cardNumber == "") {
        $errorMsg[] = "Please enter a credit card number.";
        $cardNumberERROR = true;
    } elseif(!verifyAlphaNum($cardNumber)) {
        $errorMsg[] = "Error in processing credit card number.";
        $cardNumberERROR = true;
    }

    if($expDate == "") {
        $errorMsg[] = "Please enter a expiration date for your card.";
        $expDateERROR = true;
    } elseif(!verifyAlphaNum($expDate)) {
        $errorMsg[] = "Error in processing expiration date for your card.";
        $expDateERROR = true;
    }

    if($cvv2 == "") {
        $errorMsg[] = "Please enter a CVV code for your card.";
        $cvv2ERROR = true;
    } elseif(!verifyNumeric($cvv2)) {
        $errorMsg[] = "Error in processing cvv code.";
        $cvv2ERROR = true;
    }

    if($shipRate == "") {
        $errorMsg[] = "Please select a shipping rate.";
        $shipRateERROR = true;
    } elseif ($shipRate != "economy" && $shipRate != "standard" && $shipRate != "express") {
        $errorMsg[] = "Invalid shipping rate, please contact administrator.";
        $shipRateERROR = true;

    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg && isset($_SESSION['cart'])) {
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

            if(isset($_POST["fldGift"])) { $isGift = true; }
            $dataRecord[] = $isGift; //better now than never

            $query = 'INSERT INTO tblOrders SET ';
            $query .= 'fldShipName = ?, ';
            $query .= 'fldShipAddress = ?, ';
            $query .= 'fldShipCity = ?, ';
            $query .= 'fldShipState = ?, ';
            $query .= 'fldShipZip = ?, ';
            $query .= 'fldBillName = ?, ';
            $query .= 'fldBillAddress = ?, ';
            $query .= 'fldBillCity = ?, ';
            $query .= 'fldBillState = ?, ';
            $query .= 'fldBillZip = ?, ';
            $query .= 'fldEmail = ?, ';
            $query .= 'fldCardNumber = ?, ';
            $query .= 'fldExpDate = ?, ';
            $query .= 'fldCvv = ?, ';
            $query .= 'fldShippingRate = ?, ';
            $query .= 'fldGift = ?';


            $primaryKey = "";
            if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                $query = $thisDatabaseWriter->sanitizeQuery($query);
                $results = $thisDatabaseWriter->insert($query, $dataRecord);
                $primaryKey = $thisDatabaseWriter->lastInsert();

                if (DEBUG) {
                    print "<p>pmk= " . $primaryKey;
                }
            }

            $reciept = "Your Receipt:<br>";
            $totalprice = 0;
            foreach($_SESSION['cart'] as $id => $value) {
                $query = 'INSERT INTO tblOrderItems SET ';
                $query .= 'fnkOrderId = ?, ';
                $query .= 'fnkProductId = ?, ';
                $query .= 'fldQuantity = ?';
                $thisquery = array($primaryKey, $id, $_SESSION['cart'][$id]['quantity']);

                if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->insert($query, $thisquery);
                }

                $thisInStock = "";
                $thisInOrder = "";
                $thisquery = array($id);
                $query = "SELECT fldProductName, fldPrice, fldInStock, fldInOrder from tblProducts WHERE pmkProductId = ?";
                if ($thisDatabaseReader->querySecurityOk($query, 1)) {
                    $query = $thisDatabaseReader->sanitizeQuery($query);
                    $thisitem = $thisDatabaseReader->select($query, $thisquery);
                }
                $reciept .= $_SESSION['cart'][$id]['quantity']." x ".
                    $thisitem[0]['fldProductName']." @ $".$thisitem[0]['fldPrice']."<br>";
                $totalprice += $thisitem[0]['fldPrice'];

                $thisInStock = $thisitem[0]['fldInStock'] - $_SESSION['cart'][$id]['quantity'];
                $thisInOrder = $thisitem[0]['fldInOrder'] + $_SESSION['cart'][$id]['quantity'];

                $thisquery = array($thisInStock, $thisInOrder, $id);
                $query = "UPDATE tblProducts SET ";
                $query .= "fldInStock = ?, ";
                $query .= "fldInOrder = ? ";
                $query .= "WHERE pmkProductId = ?";
                if ($thisDatabaseWriter->querySecurityOk($query, 1)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $thisitem = $thisDatabaseWriter->update($query, $thisquery);
                }
            }

            $shipcost = 0;
            //this is just a really bad bandaid to provide basic functionality.
            switch($shipRate) {
                case "economy":
                    break;
                case "standard":
                    $shipcost = 5;
                    break;
                case "express":
                    $shipcost = 10;
                    break;
                default:
                    break;
            }
            $reciept .= "<br>Shipping: ".$shipRate." ($".$shipcost.")<br>";
            $subtotal = $totalprice + $shipcost;
            $reciept .= "Subtotal: $".$subtotal."<br>";

            if(isset($_POST["fldNotify"])) {
                // fldEmail column is set to be UNIQUE, so no checking necessary
                $query = 'INSERT INTO tblMailingList SET fldEmail = ?';
                $thisquery = array($email);
                if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->insert($query, $thisquery);
                }
            }

            // all sql statements are done so lets commit to our changes
            $dataEntered = $thisDatabaseWriter->db->commit();

            $message = "Dear valued customer:<br><br>We have recieved your information and are
                        currently processing your order.<br><br>Order number: ".$primaryKey."<br>";
            $message .= $reciept;
            $to = $email;
            $cc = "";
            $bcc = "";
            $from = "White Lotus Support<service@pnguyen4.w3.uvm.edu>";
            $subject = "Your order has been recieved";
            $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);

            if (DEBUG)
                print "<p>submission complete</p> ";
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if (DEBUG)
                print "Error!: " . $e->getMessage() . "</br>";
            $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
        }
        session_destroy(); //cart should be empty after purchasing
    } else { $message = "Your cart is empty."; }// end form is valid
}   // ends if form was submitted.
?>

<div id="wrapper">
<div id="spacedcontent">
    <?php
    //####################################
    //
    // SECTION 3a.
    //
    // If its the first time coming to the form or there are errors we are going
    // to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        if (isset($_SESSION["cart"])) {
            print "<h2>We have sent a copy of this message to your email: <br>".$message."<h2>";
        } else {
            print "<h2>".$message."<br>You cannot checkout an empty cart.<h2>";
        }

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
                <input type="text" <?php if ($shipNameERROR) print 'class="mistake"';?>
                    id="fldShipName"
                    name="fldShipName"
                    value="<?php print $shipName;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldShipAddress">Address Line</label>
            <p>
                <input type="text" <?php if ($shipAddressERROR) print 'class="mistake"';?>
                    id="fldShipAddress"
                    name="fldShipAddress"
                    value="<?php print $shipAddress;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldShipCity">City</label>
            <p>
                <input type="text" <?php if ($shipCityERROR) print 'class="mistake"';?>
                    id="fldShipCity"
                    name="fldShipCity"
                    value="<?php print $shipCity;?>">
            </p>
        </fieldset>

		<fieldset>
		<label class="required" for="fldShipState">State</label>
		<p>
		<select name="fldShipState" id="fldShipState">
			<option label="State" value="-">- - - - - - - - - - - - - -</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "AL"){print "selected";}?> label="Alabama" value="AL">Alabama</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "AK"){print "selected";}?> label="Alaska" value="AK">Alaska</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "AZ"){print "selected";}?> label="Arizona" value="AZ">Arizona</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "AR"){print "selected";}?> label="Arkansas" value="AR">Arkansas</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "CA"){print "selected";}?> label="California" value="CA">California</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "CO"){print "selected";}?> label="Colorado" value="CO">Colorado</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "CT"){print "selected";}?> label="Connecticut" value="CT">Connecticut</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "DE"){print "selected";}?> label="Delaware" value="DE">Delaware</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "DC"){print "selected";}?> label="District Of Columbia" value="DC">District Of Columbia</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "FL"){print "selected";}?> label="Florida" value="FL">Florida</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "GA"){print "selected";}?> label="Georgia" value="GA">Georgia</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "HI"){print "selected";}?> label="Hawaii" value="HI">Hawaii</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "ID"){print "selected";}?> label="Idaho" value="ID">Idaho</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "IL"){print "selected";}?> label="Illinois" value="IL">Illinois</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "IN"){print "selected";}?> label="Indiana" value="IN">Indiana</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "IA"){print "selected";}?> label="Iowa" value="IA">Iowa</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "KS"){print "selected";}?> label="Kansas" value="KS">Kansas</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "KY"){print "selected";}?> label="Kentucky" value="KY">Kentucky</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "LA"){print "selected";}?> label="Louisiana" value="LA">Louisiana</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "ME"){print "selected";}?> label="Maine" value="ME">Maine</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "MD"){print "selected";}?> label="Maryland" value="MD">Maryland</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "MA"){print "selected";}?> label="Massachusetts" value="MA">Massachusetts</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "MI"){print "selected";}?> label="Michigan" value="MI">Michigan</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "MN"){print "selected";}?> label="Minnesota" value="MN">Minnesota</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "MS"){print "selected";}?> label="Mississippi" value="MS">Mississippi</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "MO"){print "selected";}?> label="Missouri" value="MO">Missouri</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "MT"){print "selected";}?> label="Montana" value="MT">Montana</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "NE"){print "selected";}?> label="Nebraska" value="NE">Nebraska</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "NV"){print "selected";}?> label="Nevada" value="NV">Nevada</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "NH"){print "selected";}?> label="New Hampshire" value="NH">New Hampshire</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "NJ"){print "selected";}?> label="New Jersey" value="NJ">New Jersey</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "NM"){print "selected";}?> label="New Mexico" value="NM">New Mexico</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "NY"){print "selected";}?> label="New York" value="NY">New York</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "NC"){print "selected";}?> label="North Carolina" value="NC">North Carolina</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "ND"){print "selected";}?> label="North Dakota" value="ND">North Dakota</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "OH"){print "selected";}?> label="Ohio" value="OH">Ohio</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "OK"){print "selected";}?> label="Oklahoma" value="OK">Oklahoma</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "OR"){print "selected";}?> label="Oregon" value="OR">Oregon</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "PA"){print "selected";}?> label="Pennsylvania" value="PA">Pennsylvania</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "RI"){print "selected";}?> label="Rhode Island" value="RI">Rhode Island</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "SC"){print "selected";}?> label="South Carolina" value="SC">South Carolina</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "SD"){print "selected";}?> label="South Dakota" value="SD">South Dakota</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "TN"){print "selected";}?> label="Tennessee" value="TN">Tennessee</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "TX"){print "selected";}?> label="Texas" value="TX">Texas</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "UT"){print "selected";}?> label="Utah" value="UT">Utah</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "VT"){print "selected";}?> label="Vermont" value="VT">Vermont</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "VA"){print "selected";}?> label="Virginia" value="VA">Virginia</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "WA"){print "selected";}?> label="Washington" value="WA">Washington</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "WV"){print "selected";}?> label="West Virginia" value="WV">West Virginia</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "WI"){print "selected";}?> label="Wisconsin" value="WI">Wisconsin</option>
			<option <?php if(isset($_POST["fldShipState"]) && $_POST["fldShipState"] == "WY"){print "selected";}?> label="Wyoming" value="WY">Wyoming</option>
		</select>
		</p>
		</fieldset>

        <fieldset>
            <label class="required" for="fldShipZip">Zip Code</label>
            <p>
                <input type="text" <?php if ($shipZipERROR) print 'class="mistake"';?>
                    id="fldShipZip"
                    name="fldShipZip"
                    value="<?php print $shipZip;?>">
            </p>
        </fieldset>

        <h1>Billing Information: </h1>
        <fieldset>
            <p>
            <input type="checkbox" <?php if(isset($_POST["fldbillequalship"])) {print ' checked="checked"';}?>
                   value="fldbillequalship"
                   id="fldbillequalship"
                   name="fldbillequalship">
            <label for="fldbillequalship">My Billing Information is the same as Shipping</label>
            </p>
        </fieldset>
        <fieldset>
            <label class="required" for="fldBillName">Full Name</label>
            <p>
                <input type="text" <?php if ($billNameERROR) print 'class="mistake"';?>
                    id="fldBillName"
                    name="fldBillName"
                    value="<?php print $billName;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldBillAddress">Address Line</label>
            <p>
                <input type="text" <?php if ($billAddressERROR) print 'class="mistake"';?>
                    id="fldBillAddress"
                    name="fldBillAddress"
                    value="<?php print $billAddress;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldBillCity">City</label>
            <p>
                <input type="text" <?php if ($billCityERROR) print 'class="mistake"';?>
                    id="fldBillCity"
                    name="fldBillCity"
                    value="<?php print $billCity;?>">
            </p>
        </fieldset>

		<fieldset>
		<label class="required" for="fldBillState">State</label>
		<p>
		<select name="fldBillState" id="fldBillState">
			<option label="State" value="-"> - - - - - - - - - - - - - -</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "AL"){print "selected";}?> label="Alabama" value="AL">Alabama</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "AK"){print "selected";}?> label="Alaska" value="AK">Alaska</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "AZ"){print "selected";}?> label="Arizona" value="AZ">Arizona</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "AR"){print "selected";}?> label="Arkansas" value="AR">Arkansas</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "CA"){print "selected";}?> label="California" value="CA">California</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "CO"){print "selected";}?> label="Colorado" value="CO">Colorado</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "CT"){print "selected";}?> label="Connecticut" value="CT">Connecticut</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "DE"){print "selected";}?> label="Delaware" value="DE">Delaware</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "DC"){print "selected";}?> label="District Of Columbia" value="DC">District Of Columbia</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "FL"){print "selected";}?> label="Florida" value="FL">Florida</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "GA"){print "selected";}?> label="Georgia" value="GA">Georgia</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "HI"){print "selected";}?> label="Hawaii" value="HI">Hawaii</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "ID"){print "selected";}?> label="Idaho" value="ID">Idaho</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "IL"){print "selected";}?> label="Illinois" value="IL">Illinois</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "IN"){print "selected";}?> label="Indiana" value="IN">Indiana</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "IA"){print "selected";}?> label="Iowa" value="IA">Iowa</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "KS"){print "selected";}?> label="Kansas" value="KS">Kansas</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "KY"){print "selected";}?> label="Kentucky" value="KY">Kentucky</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "LA"){print "selected";}?> label="Louisiana" value="LA">Louisiana</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "ME"){print "selected";}?> label="Maine" value="ME">Maine</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "MD"){print "selected";}?> label="Maryland" value="MD">Maryland</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "MA"){print "selected";}?> label="Massachusetts" value="MA">Massachusetts</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "MI"){print "selected";}?> label="Michigan" value="MI">Michigan</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "MN"){print "selected";}?> label="Minnesota" value="MN">Minnesota</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "MS"){print "selected";}?> label="Mississippi" value="MS">Mississippi</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "MO"){print "selected";}?> label="Missouri" value="MO">Missouri</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "MT"){print "selected";}?> label="Montana" value="MT">Montana</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "NE"){print "selected";}?> label="Nebraska" value="NE">Nebraska</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "NV"){print "selected";}?> label="Nevada" value="NV">Nevada</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "NH"){print "selected";}?> label="New Hampshire" value="NH">New Hampshire</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "NJ"){print "selected";}?> label="New Jersey" value="NJ">New Jersey</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "NM"){print "selected";}?> label="New Mexico" value="NM">New Mexico</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "NY"){print "selected";}?> label="New York" value="NY">New York</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "NC"){print "selected";}?> label="North Carolina" value="NC">North Carolina</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "ND"){print "selected";}?> label="North Dakota" value="ND">North Dakota</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "OH"){print "selected";}?> label="Ohio" value="OH">Ohio</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "OK"){print "selected";}?> label="Oklahoma" value="OK">Oklahoma</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "OR"){print "selected";}?> label="Oregon" value="OR">Oregon</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "PA"){print "selected";}?> label="Pennsylvania" value="PA">Pennsylvania</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "RI"){print "selected";}?> label="Rhode Island" value="RI">Rhode Island</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "SC"){print "selected";}?> label="South Carolina" value="SC">South Carolina</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "SD"){print "selected";}?> label="South Dakota" value="SD">South Dakota</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "TN"){print "selected";}?> label="Tennessee" value="TN">Tennessee</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "TX"){print "selected";}?> label="Texas" value="TX">Texas</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "UT"){print "selected";}?> label="Utah" value="UT">Utah</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "VT"){print "selected";}?> label="Vermont" value="VT">Vermont</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "VA"){print "selected";}?> label="Virginia" value="VA">Virginia</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "WA"){print "selected";}?> label="Washington" value="WA">Washington</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "WV"){print "selected";}?> label="West Virginia" value="WV">West Virginia</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "WI"){print "selected";}?> label="Wisconsin" value="WI">Wisconsin</option>
			<option <?php if(isset($_POST["fldBillState"]) && $_POST["fldBillState"] == "WY"){print "selected";}?> label="Wyoming" value="WY">Wyoming</option>
		</select>
		</p>
		</fieldset>

        <fieldset>
            <label class="required" for="fldBillZip">Zip Code</label>
            <p>
                <input type="text" <?php if ($billZipERROR) print 'class="mistake"';?>
                    id="fldBillZip"
                    name="fldBillZip"
                    value="<?php print $billZip;?>">
            </p>
        </fieldset>

        <h1>Payment Information: </h1>
        <fieldset>
            <label class="required" for="fldCardNum">Card Number</label>
            <p>
                <input type="text" <?php if ($cardNumberERROR) print 'class="mistake"';?>
                    id="fldCardNum"
                    name="fldCardNum"
                    value="<?php print $cardNumber;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldExpDate">Expiration Date</label>
            <p>
                <input type="text" <?php if ($expDateERROR) print 'class="mistake"';?>
                    id="fldExpDate"
                    name="fldExpDate"
                    value="<?php print $expDate?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldCvv2">CVV2 Code</label>
            <p>
                <input type="text" <?php if ($cvv2ERROR) print 'class="mistake"';?>
                    id="fldCvv2"
                    name="fldCvv2"
                    value="<?php print $cvv2?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldEmail">Email</label>
            <p>
                <input type="text" <?php if ($emailERROR) print 'class="mistake"';?>
                    id="fldEmail"
                    name="fldEmail"
                    value="<?php print $email;?>">
            </p>
        </fieldset>

        <fieldset>
            <p>
            <input type="checkbox" <?php if(isset($_POST["fldGift"])) {print ' checked="checked"';}?>
                   value="fldGift"
                   id="fldGift"
                   name="fldGift">
            <label for="fldGift">Is this a gift? (free wrapping service)</label>
            </p>
        </fieldset>

        <fieldset>
            <p>
            <input type="checkbox" <?php if(isset($_POST["fldNotify"])) {print ' checked="checked"';}?>
                   value="fldNotify"
                   id="fldNotify"
                   name="fldNotify">
            <label for="fldNotify">Would you like to recieve email updates for new products?</label>
            </p>
        </fieldset>

    <fieldset class="radio <?php if ($trailError) print ' mistake'; ?>">
        <legend>Select a Shipping Rate</legend>
        <input type="radio" value="economy" <?php if($shipRate == "economy"){print ' checked="checked"';}?> name="fldShipRate">
        <label class="radio-field">Economy (free)</label>
        <input type="radio" value="standard" <?php if($shipRate == "standard"){print ' checked="checked"';}?> name="fldShipRate">
        <label class="radio-field">Standard ($5)</label>
        <input type="radio" value="express" <?php if($shipRate == "express"){print ' checked="checked"';}?> name="fldShipRate">
        <label class="radio-field">Express ($10)</label>
    </fieldset>

        <fieldset class="buttons">
            <legend></legend>
            <input class="button" id="btnSubmit" name="btnSubmit" tabindex="900" type="submit" value="Submit" >
        </fieldset> <!-- ends buttons -->
    </form>

</div>
</div>

<?php
    }
include 'footer.php';
?>
