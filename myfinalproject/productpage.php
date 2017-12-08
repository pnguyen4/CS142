<?php
include 'top.php';

if ($debug) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}
//print_r($_SESSION);

//session_destroy();

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
    $price = $record[0]["fldPrice"];
    $instock = $record[0]["fldInStock"];
    $description = $record[0]["fldDescription"];
    $image = $record[0]["fldImgUrl"];

    if(isset($_GET['action']) && $_GET['action']=="add"){
        if(isset($_SESSION['cart'][$productId])){
            $_SESSION['cart'][$productId]['quantity']++;
        } else{
            $_SESSION['cart'][$productId] = array("quantity" => 1, "price" => $price);
        }
        //redirect to eliminate the issue of adding items to cart when refreshing and having the cart # update
        // in a more straightforward manner. This is done by returning to productpage with some gets removed
        // from url.
        header("Location: http://pnguyen4.w3.uvm.edu/cs148/dev/myfinalproject/productpage.php?Id=".$productId);
        die();
    }

    print('
    <div id="wrapper">
        <div id="index3">
        <img id="productimg" src="'.$image.'">
        <h1 id="productname">'.$productName.'</h1>
        <h2>Price: $'.$price.'</h2>
        <h2>In Stock: '.$instock.'</h2>
        </div>
        <div id="index2">
        <h2 style="padding: 1em;">Description:</h2>
        <p id="description">'.$description.'</p>
        <a id="description" href="productpage.php?page=products&action=add&Id='.$productId.'">Add to cart</a>
        </div>
    <div><!--end wrapper-->
    ');
} else {
    print '<p>This is not the page you are looking for.</p>';
}
?>


<?php
include 'footer.php';
?>
