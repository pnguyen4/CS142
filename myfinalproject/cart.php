<?php
include 'top.php';
?>
<div id="wrapper">
<div id="spacedcontent">
<?php
if (DEBUG) {
    print '<p>Contents of the array<pre>';
    print_r($records);
    print '</pre></p>';
}
$isUpdated = false;
$hasError = false;

if(isset($_POST['update'])){
    $hasError = false;

    foreach($_POST['quantity'] as $key => $val) {
        $thisquery = array($key);
        $query = "SELECT fldProductName, fldInStock from tblProducts WHERE pmkProductId = ?";
        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $thisitem = $thisDatabaseReader->select($query, $thisquery);
        }
        if($val > $thisitem[0]['fldInStock']) {
            print '<p><b>Error in updating quantity of '.
                $thisitem[0]['fldProductName'].': quantity exceeds stock.</b></p>';
            $hasError = true;
        }
    }
    if($hasError) {
        print '<p><b>Please correct values and try again.</b></p>';
    }

    if(!$hasError) {
        foreach($_POST['quantity'] as $key => $val) {
        $thisquery = array($key);
        $query = "SELECT fldProductName, fldInStock from tblProducts WHERE pmkProductId = ?";
        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $thisitem = $thisDatabaseReader->select($query, $thisquery);
        }

            if($val == 0) {
                unset($_SESSION['cart'][$key]);
                if(empty($_SESSION['cart'])) {
                    session_destroy(); //resets session so that empty cart message shows up
                }
            } else {
                $_SESSION['cart'][$key]['quantity']=$val;
            }
        }
        header("Location: http://pnguyen4.w3.uvm.edu/cs148/dev/myfinalproject/cart.php");
        die();
    }
}

$totalprice=0;
if(isset($_SESSION['cart'])) {
    $query = "SELECT * FROM tblProducts WHERE pmkProductId IN (";

    foreach($_SESSION['cart'] as $id => $value) {
        $query .= $id.",";
    }
    $query=substr($query, 0, -1).")";

    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $records = $thisDatabaseReader->select($query, '');
    }
}
?>

<h1>My Cart</h1>
<form class="cartbuttons" method="post" action="cart.php">
    <table id="mycart">
    <?php
        if(isset($_SESSION['cart'])) {
        print('
        <tr>
            <th><u>Icon</u></th>
            <th><u>Item</u></th>
            <th><u>Price</u></th>
            <th><u>Quantity</u></th>
        </tr>');
        foreach($records as $record) {
            $totalprice += $_SESSION['cart'][$record['pmkProductId']]['quantity']*$record['fldPrice'];
    ?>
        <tr>
            <td width="80" class="cartitem"><img class="cartimg" src="<?php print $record['fldImgUrl']?>"></td>
            <td class="cartitem"><?php print $record['fldProductName'] ?></td>
            <td class="cartitem"><?php print '$'.$record['fldPrice'] ?></td>
            <td class="cartitem"><input type="text" name="quantity[<?php print $record['pmkProductId'] ?>]" size="5"
            value="<?php print $_SESSION['cart'][$record['pmkProductId']]['quantity'] ?>"/></td>
        </tr>
        <?php }} else { print '<tr><td colspan="4"><b>Sorry, your shopping cart is empty.'; } ?>
    <?php
    if(isset($_SESSION['cart'])) {
        print '<tr><td colspan="4"><b>Subtotal: $'.$totalprice; }?></b></td></tr>
    </table>
    <?php
    if(isset($_SESSION['cart'])) {
    print '<button type="submit" name="update">Update Cart</button>';
    } //else { print '<img src="images/cup.jpg">'; }
    ?>
</form>
<?php
 if(isset($_SESSION['cart'])) {
print '
<form class="cartbuttons" action="checkout.php">
    <input type="submit" value="checkout" />
</form>';
 }
?>
</div>
</div>

<?php
include 'footer.php';
?>
