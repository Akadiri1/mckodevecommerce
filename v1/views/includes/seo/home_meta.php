<?php
echo '<meta name="description" content="'.$description.'" />
<meta name="keywords" content="'.$metakeys.'">';
if($websiteStyle[0]['status'] === "live"){
    echo '<meta property="og:title" content="'.ucwords($site_name).' - Home" />
    <meta property="og:type" content="article" />
    <meta property="og:image" content="'.$logo_directory.'" />
    <meta property="og:image:width" content="450"/>
    <meta property="og:image:height" content="298"/>
    <meta property="og:description" content="'.$description.'" />';
}
if($websiteStyle[0]['status'] === "demo"){
    echo '<meta property="og:title" content="'.ucwords($site_name).' - Home" />
    <meta property="og:type" content="article" />
    <meta property="og:image" content="https://'.$_SERVER['HTTP_HOST'].'/seo-image.jpg" />
    <meta property="og:image:width" content="450"/>
    <meta property="og:image:height" content="298"/>
    <meta property="og:description" content="'.$description.'" />';
}
if($websiteStyle[0]['status'] === "live"){
    echo '<meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="'.ucwords($site_name).'- Home">
    <meta name="twitter:description" content="'.$description.'">
    <meta name="twitter:image:src" content="'.$logo_directory.'">
    <meta name="twitter:image:width" content="280">
    <meta name="twitter:image:height" content="150">';
  }
  if($websiteStyle[0]['status'] === "demo"){
    echo '<meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="'.ucwords($site_name).'- Home">
    <meta name="twitter:description" content="'.$description.'">
    <meta name="twitter:image" content="https://'.$_SERVER['HTTP_HOST'].'/seo-image.jpg">
    <meta name="twitter:image:width" content="280">
    <meta name="twitter:image:height" content="150">';
  }
  
 ?>
