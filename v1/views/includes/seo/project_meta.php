<?php
  $uro = 'https://'.$_SERVER['HTTP_HOST']."/";
 $bd = previewBody($project_details[0]['text_content'], 22);
 $rf = strip_tags($bd);

 $cut = explode(' ',$project_details[0]['input_name']);
 $blogMeta = implode(',',$cut).",";
 echo '<meta name="description" content="'.$rf.'" />
 <meta name="keywords" content="'.$blogMeta.'blog">';
 echo '<meta property="og:title" content="'.$site_name." - ".ucwords($project_details[0]['input_name'] ).'" />
 <meta property="og:type" content="article" />
 <meta property="og:image" content="'.$project_details[0]['image_1'].'" />
 <meta property="og:image:width" content="450"/>
 <meta property="og:image:height" content="298"/>
 <meta property="og:description" content="'.$rf.'" />';
 echo '<meta name="twitter:card" content="summary_large_image">
 <meta name="twitter:title" content="'.$site_name." - ".ucwords($project_details[0]['input_name'] ).'">
 <meta name="twitter:description" content="'.$rf.'">
 <meta name="twitter:image" content="'.$project_details[0]['image_1'].'">
 <meta name="twitter:image:width" content="280">
 <meta name="twitter:image:height" content="150">';
  ?>
