<!-- ######################     Start of nav  ############################## -->
<nav id="nav">
    <ol>
        <?php 
       
        print'<li class="';
        if($path_parts['filename'] == 'index'){
            print'activePage';
        }
        print'">';
        print'<a href="index.php">Home</a>';
        print'</li>';
        
        print'<li class="';
        if($path_parts['filename'] == 'newRecipeForm'){
            print'activePage';
        }
        print'">';
        print'<a href="newRecipeForm.php">Add Recipe</a>';
        print'</li>';

        
        print'<li class="';
        if($path_parts['filename'] == 'aboutus'){
            print'activePage';
        }
        print'">';
        print'<a href="aboutus.php">About us</a>';
        print'</li>';
        
        print'<li class="';
        if($path_parts['filename'] == 'form1'){
            print'activePage';
        }
        print'">';
        print'<a href="form1.php">Sign up</a>';
        print'</li>';
        
        
        print'<li class="';
        if($path_parts['filename'] == 'form2'){
            print'activePage';
        }
        print'">';
        print'<a href="form2.php">Sign In</a>';
        print'</li>';
        
        
        print'<li class="';
        if($path_parts['filename'] == 'Contact'){
            print'activePage';
        }
        print'">';
        print'<a href="Contact.php">Contact Us</a>';
        print'</li>';
        
        /*print'<li class="';
        if($path_parts['filename'] == 'Table'){
            print'activePage';
        }
        print'">';
        print'<a href="tables.php">Tables</a>';
        print'</li>';
        
                print'<li class="';
        if($path_parts['filename'] == 'Welcome'){
            print'activePage';
        }
        print'">';
        print'<a href="welcome.php">Welcome</a>';
        print'</li>';*/
        ?>
    </ol>
</nav>
<!-- ######################     end of nav     ############################# -->
