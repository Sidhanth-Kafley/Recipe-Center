<?php
include "top.php";
?>



<?php
$records = '';



$query = 'SELECT pmkRecipeId, fldFirstName, fldLastName, fldRecipeName, fldRegion, fldPrepTime, fldCookTime, fldFlavor, fldRecipe ';
$query .= 'FROM tblRecipe';


// NOTE: The full method call would be:
//           $thisDatabaseReader->querySecurityOk($query, 0, 0, 0, 0, 0)
if ($thisDatabaseReader->querySecurityOk($query, 0)) {
$query = $thisDatabaseReader->sanitizeQuery($query);
$records = $thisDatabaseReader->select($query, '');

}




if (DEBUG) {
print '<p>Contents of the array<pre>';
print_r($records);
print '</pre></p>';
}



print '<article><h2 align="center" class="alternateRows">Recipes</h2>';
print '<table id = "tabletrails"><tbody>';

if ($IS_ADMIN == true){
print '<tr><th>Delete</th><th>Edit</th><th>Name</th><th>Name of Recipe</th><th>Origin</th><th>Preparation Time</th><th>Cook Time</th><th>Flavor</th><th>Recipe</th></tr>';
} else {
print '<tr><th>Delete</th><th>Edit</th><th>Name</th><th>Name of Recipe</th><th>Origin</th><th>Preparation Time</th><th>Cook Time</th><th>Flavor</th><th>Recipe</th></tr>';
}

if (is_array($records)) {

foreach ($records as $record) {


print '<tr><td><a style="color:red;" href="DeleteRecipeForm.php?id=' . $record['pmkRecipeId'] . '">[X]</a>' . '</td><td><a href="newRecipeForm.php?id=' . $record['pmkRecipeId'] . '">Edit</a>' . '</td><td>'. $record['fldFirstName'] . ' '.  $record['fldLastName'] . '</td><td>' . $record['fldRecipeName'] . '</td><td>' . $record['fldRegion'] . '</td><td>' . $record['fldPrepTime'] . '</td><td>' . $record['fldCookTime'] . '</td><td>' . $record['fldFlavor'] . '</td><td>' . $record['fldRecipe'] . '</td></tr>';


}
}

print '</tbody></table></article>';

include "footer.php";

?>







