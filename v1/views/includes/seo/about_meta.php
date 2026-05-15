<?php
$page_url = 'https://'.$_SERVER['HTTP_HOST']."/";
$bd = previewBody($about[0]['input_content'], 50);
// $image = $aboutContent['image_1'];
$content = strip_tags($bd);
$tn = $site_name." - ".ucwords($page_title);


echo '<meta name="description" content="'.$content.'" />
<meta name="keywords" content="'.$metakeys.'">';
echo '<meta property="og:title" content="'.$tn.'" />
<meta property="og:type" content="article" />
<meta property="og:image" content="'.$logo_directory.'" />
<meta property="og:image:width" content="450"/>
<meta property="og:image:height" content="298"/>
<meta property="og:description" content="'.$about[0]['input_content'].'" />';
echo '<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="'.$tn.'">
<meta name="twitter:description" content="'.$about[0]['input_content'].'">
<meta name="twitter:image" content="'.$logo_directory.'">
<meta name="twitter:image:width" content="280">
<meta name="twitter:image:height" content="150">';
 ?>
