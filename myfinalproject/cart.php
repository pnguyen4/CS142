<?php
include 'top.php';

if (DEBUG) {
    print '<p>Contents of the array<pre>';
    print_r($records);
    print '</pre></p>';
}

if(isset($_POST['update'])){
    foreach($_POST['quantity'] as $key => $val) {
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

<div id="wrapper">
<h1>My Cart</h1>
<form method="post" action="cart.php">
    <table>

    <?php
        if(isset($_SESSION['cart'])) {
        print('
        <tr>
            <th>Icon</th>
            <th>Item</th>
            <th>Price</th>
            <th>Quantity</th>
        </tr>');
        foreach($records as $record) {
            $totalprice += $_SESSION['cart'][$record['pmkProductId']]['quantity']*$record['fldPrice'];
    ?>
        <tr>
            <td width="110" class="cartitem"><img class="cartimg" src="<?php print $record['fldImgUrl']?>"</td>
            <td class="cartitem"><?php print $record['fldProductName'] ?></td>
            <td class="cartitem"><?php print $record['fldPrice'] ?></td>
            <td class="cartitem"><input type="text" name="quantity[<?php print $record['pmkProductId'] ?>]" size="5"
            value="<?php print $_SESSION['cart'][$record['pmkProductId']]['quantity'] ?>"/></td>
        </tr>
        <?php }} else { print '<td colspan="4">Sorry, Your Shopping Cart is as empty as this cup.</td>'; } ?>
    <?php
    if(isset($_SESSION['cart'])) {
        print '<tr><td colspan="4">Subtotal: <?php print $totalprice ?></td></tr>';
    }?>
    </table>
    <?php
    if(isset($_SESSION['cart'])) {
    print '<button type="submit" name="update">Update Cart</button>';
    } else { print '<img src="images/cup.jpg">'; }
    ?>
</div>

<?
include 'footer.php';
?>
