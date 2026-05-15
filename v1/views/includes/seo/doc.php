<?php

switch ($page_title) {
  case ' Home':
    include 'seo/home_meta.php';
    break;
  case ' About-Us':
    include 'seo/about_meta.php';
    break;

  case 'Blog Details: '.@$blog['input_title']:
    include 'seo/blog_meta.php';
    break;

  case 'View Services: '.@$single_service[0]['input_title']:
    include 'seo/services_meta.php';
    break;

  case 'View Project: '.@$single_portfolio[0]['input_title']:
    include 'seo/portfolio_meta.php';
    break;

  default:
    include 'seo/others_meta.php';
    break;
}































 ?>
