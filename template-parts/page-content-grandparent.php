<?php
/**
 * Template part for displaying page content in page.php
 *
 * @since Soli 2.0
 * @version 2.0
 */

function searchForId($pages) {
	$i = 0;
	foreach ($pages as $page) {
		if ($page->post_title == "Vereniging" || $page->post_title == "Orkesten") {
			return $i;
		}
		$i++;
	}
	return null;
}

function moveElement(&$array, $a, $b) {
  $out = array_splice($array, $a, 1);
  array_splice($array, $b, 0, $out);
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="page-header" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
    url(<?php echo get_soli_post_image($post,'large'); ?>)">
		<?php the_title(
      '<h1 class="font-resizer">',
      '</h1>'
    );
		$main_title = $post->post_title;
		 ?>
	</header>
		<?php
	    $children = get_pages(array(
	      'parent' => $post->ID
	    ));
			$i = searchForId($children);
			moveElement($children,$i,0);
			global $post;
			$i = 0;
			$none_parent_pages = array();
			foreach ($children as $post) {
			  setup_postdata($post);
				if($children = get_pages(array(
					      'parent' => $post->ID
					    ))){
					if(($i+1)%2===0) {?>
						<div style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
				    	url(<?php echo get_soli_post_image($post,'large'); ?>)">
					<?php } else { ?>
						<div>
					<?php
					}
					?><div class="page-content"><?php
								if($i !== 0){
									?><h1 class="font-resizer"><?php echo $post->post_title; ?></h1><?php
								} else {
									?><h1 class="title font-resizer"><?php echo $main_title; ?></h1><?php
								}
								the_content();
							    
                            ?>
							<div class="related">
								<h3>Bekijk de onderliggende pagina's</h3>
								<?php get_child_pages($post->ID); ?>
							</div>
						</div>
					</div>
					<?php $i++;
				} else {
					array_push($none_parent_pages,$post);
				}
			}
			?><div class="page-content"><?php
				foreach ($none_parent_pages as $post) {
					setup_postdata($post);
					?><div class="related"><?php
							get_template_part( 'template-parts/item', 'flat-excerpt' );
					?></div><?php
				}
				wp_reset_postdata();
				get_template_part( 'template-parts/socialmedia', 'bar' );
			?></div>

</article>
