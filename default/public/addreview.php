<?php require 'dbconnect.php';
session_start();
$Title='REVIEW ';
?>
<?php

//handles the logic to add the review to the database ready for moderation
if(isset($_POST['add_review'])){
  try {
    $date = date("Y-m-d");
    $stmt = $pdo->prepare('INSERT INTO reviews (review_customer, review_description, review_date, review_moderated, review_stars, review_product)
    VALUES (:review_customer, :review_description, :review_date, :review_moderated, :review_stars, :review_product)');
    $criteria = [
    'review_customer' => $_SESSION['username'],
    'review_description' => $_POST['review_description'],
    'review_date' => $date,
    'review_moderated' => $_POST['review_moderated'],
    'review_stars' => $_POST['star'],
    'review_product' => $_POST['review_product']
    ];
    $stmt->execute($criteria);
    $content='Reviews sent for moderation.';
    header('refresh:5, url=productpage.php?id='.$_POST['review_product'].'');


  } catch (\Exception $e) {
  $content='Error adding review.';
  header('refresh:5, url=productpage.php?id='.$_POST['review_product'].'');
  }


}
require 'layout.php';
 ?>
