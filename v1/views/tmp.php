<?php

$page_title = "Home";

include 'includes/header.php';

$insertPage = false;

if ($getPages != null) {
	if(is_array($getPages)){
		if(($getPages['input_link'] === $pageUrl)  && $getPages['bool_method'] === "1" ){
			$insertPage = true;
		}
	}
}

?>

<?php if ($insertPage){ ?>
	<?php echo $getPages['input_iframe_link'] ?>
<?php }else{ ?>
  <!-- #region Your Custom Content -->
  
  <!-- #endregion -->
<?php } ?>
<?php include 'includes/footer.php'; ?>
