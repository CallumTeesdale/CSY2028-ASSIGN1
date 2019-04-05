
<?php
require 'dbconnect.php';
?>
<!doctype html>
<html>
	<head>

		<!--Set the title of the page and metatags-->
		<title><?php echo $Title; ?></title>
		<?php
		 echo $metaurl;
		 echo $metatype;
		 echo $metatitle;
		 echo $metadescription;
		 echo $metaimage;

		 ?>
		<meta charset="utf-8" />
    <link rel="stylesheet" href="electronics.css" />
	</head>

  <body>
		<!--Javascript for the facebook share button-->
		<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

    <header>
      <h1>Ed's Electronics</h1>

			<!--Navigation bar-->
      <ul>
        <li><a href="index.php">Home</a></li>
				<li><a href="faqs.php">FAQs</a></li>
        <li><a href="productlist.php">Products</a>
          <ul>
            <?php
						//populates the drop down with the categories in the database
            $category = $pdo->prepare('SELECT * FROM categories');
            $category->execute();
            foreach ($category as $cat) {
            	echo '<li><a href="productlist.php?id='. $cat['idcategories'] . '">' . $cat['category_name'] . '</a></li>';
            	}
             ?>
          </ul>
        </li>
      </ul>

      <address>
        <p>We are open 9-5, 7 days a week. Call us on
          <strong>01604 11111</strong>
        </p>
      </address>



    </header>
    <section></section>
    <main>
      <?php
      echo $content;
       ?>


       		</main>

       		<aside>
						<?php
						//populates the sidebar with the featured products
            $featured = $pdo->prepare('SELECT * FROM products WHERE featured = 1');
            $featured->execute();
            foreach ($featured as $feature) {
            	echo '<h1><a href="productpage.php?id='.$feature['idproducts'].'">Featured Product</a></h1>
	       			<p><strong>'.$feature['product_name'].'</strong></p>
	       			<p>'.$feature['product_description'].'</p>';
            	}
             ?>



       		</aside>

       		<footer>
       			&copy; Ed's Electronics 2018
       		</footer>

       	</body>

       </html>
