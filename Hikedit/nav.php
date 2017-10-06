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
        if ($PATH_PARTS['filename'] == 'sampleQuery') {
            print ' class="activePage" ';
        }
        print '><a href="sampleQuery.php">Sample SELECT</a></li>';

        print '<li ';
        if ($PATH_PARTS['filename'] == 'tables') {
            print ' class="activePage" ';
        }
        print '><a href="tables.php">Tables</a></li>';

        ?>
    </ol>
</nav>
<!-- #################### Ends Main Navigation    ########################## -->

