<!-- ######################     Main Navigation   ########################## -->
<nav>
    <ol>
        <?php
        // This sets a class for current page so you can style it differently

        print '<li ';
        if ($PATH_PARTS['filename'] == 'index') {
            print ' class="activePage" ';
        }
        print '><a href="index.php">Home</a></li>';

        print '<li ';
        if ($PATH_PARTS['filename'] == 'teas') {
            print ' class="activePage" ';
        }
        print '><a href="teas.php">Teas</a></li>';

        print '<li ';
        if ($PATH_PARTS['filename'] == 'cart') {
            print ' class="activePage" ';
        }
        $count = 0;
        if(isset($_SESSION['cart'])) {
            foreach($_SESSION['cart'] as $id => $value) {
                $count += $value['quantity'];
            }
        }
        print '><a href="cart.php">View Cart ('.$count.')</a></li>';

        if($isAdmin) {

            print '<li ';
            if ($PATH_PARTS['filename'] == 'manageproducts') {
                print ' class="activePage" ';
            }
            print '><a href="manageproducts.php">Product Form</a></li>';

            print '<li ';
            if ($PATH_PARTS['filename'] == 'manageorders') {
                print ' class="activePage" ';
            }
            print '><a href="manageorders.php">Manage Orders</a></li>';


            print '<li ';
            if ($PATH_PARTS['filename'] == 'tables') {
                print ' class="activePage" ';
            }
            print '><a href="tables.php">Tables</a></li>';

        }

        ?>
    </ol>
</nav>
<!-- #################### Ends Main Navigation    ########################## -->

