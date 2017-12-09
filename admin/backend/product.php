<?php
	require_once("../../assets/required/config.php");
	require_once ('functions.php');
	require('vendor/autoload.php');

	use MarcL\AmazonAPI;
	use MarcL\AmazonUrlBuilder;

	session_start();
	
	checkDatabaseConn();
	checkAPI();
	if (!userLoggedIn())
		redirectLogin();

	if ($_SERVER['REQUEST_METHOD'] != 'POST')
		header("Location: " . SITE_URL . PATH . "dashboard.php");

	$error = false;

	switch ($_GET['action']) {

		case "new":
			// Adding a new product from Amazon to the database

			// Check value of 'only amazon' checkbox
			if($_POST['onlyAmazon'] == "true") {
				$onlyAmazon = true;
			} else if($_POST['onlyAmazon'] == "false") {
				$onlyAmazon = false;
			} else {
				$_SESSION['onlyAmazonErr'] = "Not sure if you wanted all products or only products sold by Amazon.";
				$error = true;
			}

			// Check the ASIN number provided is in the proper format (10 character alpha-numeric)
			if (!preg_match("/^[A-Za-z0-9]{10}$/", $_POST['itemid']) && strlen($_POST['itemid']) != 10) {
				$_SESSION['asinErr'] = "Invalid item ASIN. Make sure the ASIN entered is correct and try again, it is a 10 character alaphanumeric string. If the ASIN you entered is correct and you think this is in error, please contact the <a href='mailto:" . ADMIN_EMAIL . "?subject=" . SITE_TITLE . ": Invalid ASIN when adding product'>website administrators</a>.";
				$error = true;
			}

			// If errors are found, return to previous page and display them
			if ($error)
				header("Location: " . SITE_URL . PATH . "dashboard.php?errors=true");
				
			$urlBuilder = new AmazonUrlBuilder (
				ACCESSKEY,
				SECRET,
				ASSOCIATEID,
				MARKET
			);

			$itemid = $_POST['itemid'];
			
			// $idtype = $_POST['idtype'];  // Commented out because it doesn't do anything yet
			
			$amazonAPI = new AmazonAPI($urlBuilder, 'xml');
			$items = $amazonAPI->ItemLookup($itemid, $onlyAmazon);

			// Setting a namespace prefix. Amazon uses their own namespace, and thus this is needed to allow 'xpath' to work.
			foreach($items->getDocNamespaces() as $strPrefix => $strNamespace) {
			    if(strlen($strPrefix)==0) {
			        $strPrefix="a"; //Assign an arbitrary namespace prefix.
			    }
			    $items->registerXPathNamespace($strPrefix,$strNamespace);
			}

			foreach ($items->Items->Item as $Item) {
				// Assemble all the images into a single string space deliminated
				// Add the primary image first
				$productImages = $Item->LargeImage->URL . " ";
				
				// Add any extra images
				$imagesets = $items->xpath("//a:ImageSet[@Category='variant']");
				if ($imagesets != false && count($imagesets) != 0) {
					for ($i=0; $i < count($imagesets); $i++) { 
						$productImages .= $imagesets[$i]->LargeImage->URL . " ";
					}

				}

				// Since it seems '->' means something in sql, we need to put our long ass '->' sprees into a regular variable. IDK
				$productTitle = $Item->ItemAttributes->Title;
				$productURL = $Item->DetailPageURL;
				$productReviews = $Item->ItemLinks->ItemLink[5]->URL;
				$productPrice = $Item->Offers->Offer->OfferListing->Price->FormattedPrice;
				$productCategory = $Item->ItemAttributes->ProductGroup;

				// SQL statement to add the data to the database, not using any sanitiztion because the data is coming from a trusted enough source that we should not expect any sql injections.
				$addProduct = "INSERT INTO products (asin, title, url, reviewLink, images, price, category) VALUES ('$itemid', '$productTitle', '$productURL', '$productReviews', '$productImages', '$productPrice', '$productCategory')";
				if ($conn->query($addProduct)) {
					
					// To give confirmation as to what all was added to the database
					$_SESSION['product']['title'] = $productTitle;
					$_SESSION['product']['category'] = $productCategory;
					header("Location: " . SITE_URL . PATH . "dashboard.php?success=true");
				} else {
//!!!!!! Remove mysql error message at release
					$_SESSION['prodErrTxt'] = "<p>Error adding product to database: " . mysqli_errno($conn) . ": " . mysqli_error($conn) . " : " . $productTitle . "</p>";
					header("Location: " . SITE_URL . PATH . "dashboard.php?success=false");
				}
			}
			break;

		case "amazon":
			// Update the product from amazon

			$id = $_POST['id'];

			// Get ASIN number from the database for the product ID given
			$getASIN = "SELECT asin FROM products WHERE id='$id'";
			$result = $conn->query($getASIN);
			if ($result) {
				$asin = $result->fetch_object()->asin;
				echo $asin;
			} else
				echo "Error";
			break;

		case "update":
			// Manual product update

			$id = $_POST['id'];
			$title = $_POST['title'];
			$category = $_POST['category'];
			$prepareUpdate = $conn->prepare("UPDATE products SET title=?, category=? WHERE id='$id'");
			$prepareUpdate->bind_param('ss', $title, $category);

			// Check the update succeeded
			if ($prepareUpdate->execute()) {
				$_SESSION['product']['title'] = $title;
				$_SESSION['product']['category'] = $category;
				header("Location: " . SITE_URL . PATH . "dashboard.php?success=true");
			} else {
//!!!!!! Remove mysql error message at release
				$_SESSION['prodErrTxt'] = "<p>Error adding product to database: " . mysqli_errno($prepareUpdate) . ": " . mysqli_error($prepareUpdate) . "</p>";
				header("Location: " . SITE_URL . PATH . "dashboard.php?success=false");
			}
			break;

		case "delete":
			// Remove product from database

			$id = $_POST['id'];
			$removeProd = $conn->query("UPDATE products SET deleted='y' WHERE id='$id'");
			if ($removeProd) {
				$_SESSION['prodErrTxt'] = "Sucessfully removed the product.";
				header("Location: " . SITE_URL . PATH . "dashboard.php?success=false");
			} else {
				$_SESSION['prodErrTxt'] = "Error removing the product! " . mysqli_errno($conn) . " : " . mysqli_error($conn);
				header("Location: " . SITE_URL . PATH . "dashboard.php?success=false");
			}
			break;

		default:
			header("Location: " . SITE_URL . PATH . "dashboard.php");
			break;
	}
?>