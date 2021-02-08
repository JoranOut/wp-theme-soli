<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in Search Results and for Recent Posts in Front Page panels.
 *
 * @since Soli 2.0
 * @version 2.0
 */
 setup_postdata($post);

/*?>

<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/nl_NL/sdk.js#xfbml=1&version=v2.12';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Your share button code -->
<?php
  global $wp;
  $url = home_url( $wp->request );
  $url = "http://dev.soli.nl/archief/";
  $rawurl = rawurlencode($url);
?>

<div class="sharebox">
	<span>Deel op Social Media:</span>
	<div></div>
  <div class="fb-share-button" data-href="<?php echo $url; ?>"
  data-layout="button"
  data-size="large" data-mobile-iframe="true">
    <a target="_blank"
    href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $rawurl; ?>&amp;src=sdkpreparse"
    class="fb-xfbml-parse-ignore">
    Delen
    </a>
  </div>
<a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-size="large" data-show-count="false">Tweet</a>
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
</div>
*/
?>
<div class="sharebox a2a_kit a2a_kit_size_32 a2a_default_style" data-a2a-url="<?php echo esc_url( get_permalink() ) ?>" data-a2a-title="<?php echo $post->post_title ?>">
  <p>Deel op Social Media:</p>
  <a class="a2a_button_facebook"></a>
  <a class="a2a_button_twitter"></a>
  <a class="a2a_button_email"></a>
  <a class="a2a_button_telegram"></a>
  <a class="a2a_button_linkedin"></a>
  <a class="a2a_button_whatsapp"></a>
  <a class="a2a_button_copy_link"></a>
</div>
<script async src="https://static.addtoany.com/menu/page.js"></script>