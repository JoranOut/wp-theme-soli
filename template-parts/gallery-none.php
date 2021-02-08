<div id="post-<?php the_ID(); ?>" <?php post_class("personal_gallery_item"); ?>>
	 <div class="personal_gallery_img" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .1) 0%, rgba(75, 33, 191, .1) 100%),
     url(<?php echo get_soli_post_image($post,'medium'); ?>)">
   </div>
   <div class="personal_gallery_sub">
     <p class="hovergroup">@Ontbrekend</p>
     <p>
       <?php
           echo current_time('d F, y');
         ?>
       </p>
     <h2 class="entry-title">Niks gevonden</h2>
     <p class="hoversub">Het lijkt erop alsof we hier niks gevonden hebben... </p>
  </div>
</div>
