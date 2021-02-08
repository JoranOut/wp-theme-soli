<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @since Soli 2.0
 * @version 2.0
 */

?>

</main>
<footer>
  <a href="https://www.facebook.com/Muziekvereniging-Soli-464559090251091/" target="_blank">
    <img src="<?php bloginfo('template_url'); ?>/assets/img/facebook-icon.svg" alt="facebook">
  </a>
  <a href="https://www.twitter.com/mzvsoli" target="_blank">
    <img src="<?php bloginfo('template_url'); ?>/assets/img/twitter-icon.svg" alt="twitter">
  </a>
  <a href="<?php echo get_permalink(get_page_by_title('muziekcentrum'))?>">
    <img src="<?php bloginfo('template_url'); ?>/assets/img/location-icon.svg" alt="location">
  </a>
  <a href="<?php echo get_permalink(get_page_by_title('contact'));?>">
    <img src="<?php bloginfo('template_url'); ?>/assets/img/contact-icon.svg" alt="contact">
  </a>
</footer>
<?php wp_footer(); ?>

</body>
</html>
