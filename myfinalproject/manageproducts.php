<?php
include 'top.php';

if($isAdmin) {
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// We print out the post array so that we can see our form is working.
//

if ($debug) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}
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

if (isset($_GET["Id"])) {
    ///////////////////////////////////////////////////////////////////
    $productId = (int) htmlentities($_GET["Id"], ENT_QUOTES, "UTF-8");
    $query = "SELECT * FROM tblProducts WHERE pmkProductId = ?";

    $data = array($productId);
    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $record = $thisDatabaseReader->select($query, $data);
    }

    $productName = $record[0]["fldProductName"];
    $supplier = $record[0]["fnkSupplierId"];
    $category = $record[0]["fnkCategoryId"];
    $price = $record[0]["fldPrice"];
    $instock = $record[0]["fldInStock"];
    $inorder = $record[0]["fldInOrder"];
    $description = $record[0]["fldDescription"];
    $image = $record[0]["fldImgUrl"];

} else {
    $productId = -1;
    $productName = "";
    $supplier =  "";
    $category =  "";
    $price =  "";
    $instock =  "";
    $inorder = "";
    $description = "";
    $image = "";
}
$update = false;
if($productId > 0) {
    $update = true;
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
// We are keeping track of the first mistake we make so we can set the
// autofocus to it.

$productIdERROR = false;
$productNameERROR = false;
$supplierERROR = false;
$categoryERROR = false;
$priceERROR = false;
$instockERROR = false;
$inorderERROR = false;
$descriptionERROR = false;
$imageERROR = false;

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
    $productId = (int) htmlentities($_POST["pmkProductId"], ENT_QUOTES, "UTF-8");
    if($productId > 0) {
        $update = true;
    }

    $productName = htmlentities($_POST["fldProductName"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $productName;

    $supplier =  htmlentities($_POST["fnkSupplierId"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $supplier;

    $category =  htmlentities($_POST["fnkCategoryId"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $category;

    $price = htmlentities($_POST["fldPrice"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $price;

    $instock = htmlentities($_POST["fldInStock"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $instock;

    $inorder = htmlentities($_POST["fldInOrder"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $inorder;

    $description = htmlentities($_POST["fldDescription"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $description;

    $image = htmlentities($_POST["fldImgUrl"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $image;

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

    if($productName == "") {
        $errorMsg[] = "Please enter a product name.";
        $productNameERROR = true;
    } elseif(!verifyAlphaNum($productName)) {
        $errorMsg[] = "Your product name has an extra character.";
        $productNameERROR = true;
    }

    if($supplier == "") {
        $errorMsg[] = "Please select a supplier.";
        $supplierERROR = true;
    } elseif(!verifyNumeric($supplier)) {
        $errorMsg[] = "Your supplier has an extra character.";
        $supplierERROR = true;
    }

    if($category == "") {
        $errorMsg[] = "Please select a category.";
        $categoryERROR = true;
    } elseif(!verifyNumeric($category)) {
        $errorMsg[] = "Your category has an extra character.";
        $categoryERROR = true;
    }

    if($price == "") {
        $errorMsg[] = "Please enter a price.";
        $priceERROR = true;
    } elseif(!verifyNumeric($price)) {
        $errorMsg[] = "Your price has an extra character.";
        $priceERROR = true;
    }

    if($instock == "") {
        $errorMsg[] = "Please enter the number of items in stock.";
        $instockERROR = true;
    } elseif(!verifyNumeric($instock)) {
        $errorMsg[] = "Your # in stock has an extra character.";
        $instockERROR = true;
    }

    if($inorder == "") {
        $errorMsg[] = "Please enter the number of items in order.";
        $inorderERROR = true;
    } elseif(!verifyNumeric($inorder)) {
        $errorMsg[] = "Your # in order has an extra character.";
        $inorderERROR = true;
    }

    if(!verifyAlphaNum($image /*can be blank*/)) {
        $errorMsg[] = "Image url has an extra character";
        $imageERROR = true;
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

        try {
            $thisDatabaseWriter->db->beginTransaction();

            if($update) {
                $query = 'UPDATE tblProducts SET ';
            } else {
            $query = 'INSERT INTO tblProducts SET ';
            }

            $query .= 'fldProductName = ?, ';
            $query .= 'fnkSupplierId = ?, ';
            $query .= 'fnkCategoryId = ?, ';
            $query .= 'fldPrice = ?, ';
            $query .= 'fldInStock = ?, ';
            $query .= 'fldInOrder = ?, ';
            $query .= 'fldDescription = ?, ';
            $query .= 'fldImgUrl = ? ';

            if($update) {
                $query .= 'WHERE pmkProductId = ?';
                $dataRecord[] = $productId;

                if ($thisDatabaseWriter->querySecurityOk($query, 1)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->update($query, $dataRecord);
                }

            } else {
                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query, 0);
                    print_r($data);
                }

                if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->insert($query, $dataRecord);
                    $primaryKey = $thisDatabaseWriter->lastInsert();

                    if (DEBUG) {
                        print "<p>pmk= " . $primaryKey;
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

} elseif(isset($_POST["btnDelete"])) {
    $productId = $_POST["pmkProductId"];
    try {
        $thisDatabaseWriter->db->beginTransaction();

        $query = "DELETE FROM tblProducts WHERE pmkProductId = ?";
        $thisquery = array($productId);

        if ($thisDatabaseWriter->querySecurityOk($query, 1)) {
            $query = $thisDatabaseWriter->sanitizeQuery($query);
            $results = $thisDatabaseWriter->delete($query, $thisquery);
        }
            $dataEntered = $thisDatabaseWriter->db->commit();
    } catch (PDOExecption $e) {
        $thisDatabase->db->rollback();
        if (DEBUG)
            print "Error!: " . $e->getMessage() . "</br>";
        $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
    }
} // ends if form was submitted.
//#############################################################################
//
// SECTION 3 Display Form
//
$query = "SELECT * FROM tblSupplier";
if ($thisDatabaseReader->querySecurityOk($query, 0)) {
    $query = $thisDatabaseReader->sanitizeQuery($query);
    $supplierlist = $thisDatabaseReader->select($query, '');
}

$query = "SELECT * FROM tblCategory";
if ($thisDatabaseReader->querySecurityOk($query, 0)) {
    $query = $thisDatabaseReader->sanitizeQuery($query);
    $categorylist = $thisDatabaseReader->select($query, '');
}
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
    if (isset($_POST["btnDelete"]) || isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        print "<h2>Your form has been submitted.</h2>";

    } else {

        print '<h2>Product Management Form</h2>';

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

    <?php if(isset($_GET["Id"])) {
        print '
        <fieldset class="buttons">
            <legend></legend>
            <input class="button" id="btnDelete" name="btnDelete" tabindex="900" type="submit" value="Delete" >
            <label for="btnDelete ">WARNING: are you sure you want to delete?</label>
        </fieldset> <!-- ends buttons -->';
        }
    ?>

        <input type="hidden" name="pmkProductId" value="<?php print $productId; ?>">

        <fieldset>
            <label class="required" for="fldProductName">Item Name</label>
            <p>
                <input type="text" <?php if ($productNameERROR) print 'class="mistake"';?>
                    id="fldProductName"
                    name="fldProductName"
                    value="<?php print $productName;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fnkSupplierId">Supplier</label>
            <p>
		    <select name="fnkSupplierId" id="fnkSupplierId">
                <?php
                foreach($supplierlist as $supply) {
                    print "<option";
                    if((isset($_POST["fnkSupplierId"]) && $_POST["fnkSupplierId"] == $supply)
                        || $supply['pmkSupplierId'] == $supplier ) {
                        print " selected";
                    }
                    print ' label="'.$supply['fldSupplierName'].'" value="';
                    print $supply['pmkSupplierId'].'">'.$supply['fldSupplierName'];
                    print '</option>';
                }
                ?>
            </select>
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fnkCategoryId">Category</label>
            <p>
		    <select name="fnkCategoryId" id="fnkCategoryId">
                <?php
                foreach($categorylist as $cat) {
                    print "<option";
                    if((isset($_POST["fnkCategoryId"]) && $_POST["fnkCategoryId"] == $category)
                        || $category == $cat['pmkCategoryId']) {
                        print " selected";
                    }
                    print ' label="'.$cat['fldCategoryName'].'" value="';
                    print $cat['pmkCategoryId'].'">'.$cat['fldCategoryName'];
                    print "</option>\n";
                }
                ?>
            </select>
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldPrice">Price</label>
            <p>
                <input type="text" <?php if ($priceERROR) print 'class="mistake"';?>
                    id="fldPrice"
                    name="fldPrice"
                    value="<?php print $price;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldInStock"># of items in stock</label>
            <p>
                <input type="text" <?php if ($instockERROR) print 'class="mistake"';?>
                    id="fldInStock"
                    name="fldInStock"
                    value="<?php print $instock;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldInOrder"># of items in order</label>
            <p>
                <input type="text" <?php if ($inorderERROR) print 'class="mistake"';?>
                    id="fldInOrder"
                    name="fldInOrder"
                    value="<?php print $inorder;?>">
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldDescription">Item Description</label>
            <p>
                <textarea rows="10" cols="80" <?php if ($descriptionERROR) print 'class="mistake"';?>
                    id="fldDescription"
                    name="fldDescription"><?php print $description;?></textarea>
            </p>
        </fieldset>

        <fieldset>
            <label class="required" for="fldImgUrl">Relative path to image</label>
            <p>
                <input type="text" <?php if ($imageERROR) print 'class="mistake"';?>
                    id="fldImgUrl"
                    name="fldImgUrl"
                    value="<?php print $image;?>">
            </p>
        </fieldset>

        <fieldset class="buttons">
            <legend></legend>
            <input class="button" id="btnSubmit" name="btnSubmit" tabindex="900" type="submit" value="Submit" >
        </fieldset> <!-- ends buttons -->

    </form>


<?php
    }
    }
print '
    </div>
    </div><!--end wrapper-->';
include 'footer.php';
?>
