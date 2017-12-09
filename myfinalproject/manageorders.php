<?php
include 'top.php';

if($isAdmin) {

if ($debug) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}

$tableName = "";
$thisURL = $domain . $phpSelf;

print '
<div id="wrapper">
<div id="spacedcontent">';

if (isset($_GET['getRecordsFor'])) {
    print '<h1>Order Details</h1>';
    $query = 'SHOW COLUMNS FROM tblOrders';

    $info = '';

    if ($thisDatabaseReader->querySecurityOk($query, 0)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $info = $thisDatabaseReader->select($query);
    }

    $span = count($info);
    //print out the table name and how many records there are
    print '<table>';


    $orderName = (int) htmlentities($_GET['getRecordsFor'], ENT_QUOTES, "UTF-8");
    $thisquery = array($orderName);
    $query = 'SELECT * FROM tblOrders WHERE pmkOrderId = ?';
    $allRecords = '';

    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $allRecords = $thisDatabaseReader->select($query, $thisquery);
    }


    // print out the column headings, note i always use a 3 letter prefix
    // and camel case like pmkCustomerId and fldFirstName
    print '<tr>';
    $columns = 0;

    // loop through all the tables in the database, display fields and properties
    if (is_array($info)) {
        foreach ($info as $field) {
            print '<td>';
            $camelCase = preg_split('/(?=[A-Z])/', substr($field[0], 3));

            foreach ($camelCase as $one) {
                print $one . " ";
            }

            print '</td>';
            $columns++;
        }
    }
    print '</tr>';

    $highlight = 0; // used to highlight alternate rows
    foreach ($allRecords as $rec) {
        $highlight++;
        if ($highlight % 2 != 0) {
            $style = ' odd ';
        } else {
            $style = ' even ';
        }
        print '<tr class="' . $style . '">';
        for ($i = 0; $i < $columns; $i++) {
            print '<td>' . $rec[$i] . '</td>';
        }
        print '</tr>';
    }

    // all done
    print '</table>';


?>
    <form action="<?php print $phpSelf; ?>" method="post">
        <input type="hidden" name="orderId" value="<?php print $orderName; ?>">
        <input type="submit" id="Delete" name="Delete" value="Delete"/>
    </form>
<?php

    } elseif (isset($_POST['Delete'])) {
        $dataEntered = '';
        $orderName = (int) htmlentities($_POST['orderId'], ENT_QUOTES, "UTF-8");
        try {
            $thisDatabaseWriter->db->beginTransaction();

            $query = "DELETE FROM tblOrderItems WHERE fnkOrderId = ?";
            $thisquery = array($orderName);

            if ($thisDatabaseWriter->querySecurityOk($query, 1)) {
                $query = $thisDatabaseWriter->sanitizeQuery($query);
                $results = $thisDatabaseWriter->delete($query, $thisquery);
            }

            $query = "DELETE FROM tblOrders WHERE pmkOrderId = ?";
            $thisquery = array($orderName);

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
    }



    $query = 'SHOW COLUMNS FROM tblOrders';

    $info = '';

    if ($thisDatabaseReader->querySecurityOk($query, 0)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $info = $thisDatabaseReader->select($query);
    }

    $span = count($info);

    $query = 'SELECT * FROM tblOrders';

    $allRecords = '';

    if ($thisDatabaseReader->querySecurityOk($query, 0)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $allRecords = $thisDatabaseReader->select($query);
    }

    print "<h1>Pending Orders</h1>";

    foreach ($allRecords as $rec) {
        for ($i = 0; $i < 1; $i++) {
            print '<a href="'.$thisURL.'?getRecordsFor='.$rec[$i].'">' . $rec[$i] . '</a><br><br>';
        }
    }


?>



<?php
    }
print '
    </div>
    </div><!--end wrapper-->';
include 'footer.php';
?>
