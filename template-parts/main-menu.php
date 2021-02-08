<?php
/*
 * template for header menu
 */

 foreach (wp_get_nav_menu_items('main_menu','ARRAY_A') as $menu_title) {
   $children = get_pages(
     array(
       'parent' => $menu_title->object_id,
       'sort_column' => 'menu_order'
     ));
   $extended_class = ($children)?'extended':'';?>
   <div class="menu_item <?php echo $extended_class;?>">
     <h3
       <?php
        if(!$children) { ?>
          onclick="window.location.href='<?php echo $menu_title->url; ?>'"
       <?php } ?>
     >
       <?php echo $menu_title->title; ?></h3>
     <?php
      if($children){
        ?>
        <div class="sub_menu_item">
          <div>
            <div><?php
              menu_sub_sub_menu($children[0]);
              menu_sub_sub_menu($children[1]);
            ?></div>
            <div><?php
              menu_sub_sub_menu($children[2]);
              menu_sub_sub_menu($children[3]);
            ?></div>
            <div>
              <?php
                $rand_page = getrandomelement($children);
                array_shift($children);array_shift($children);
                array_shift($children);array_shift($children);
                ?>

              <div class="featured_menu_item"
              onclick="window.location.href='<?php echo get_permalink($rand_page); ?>'"
              style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
                url(<?php echo get_soli_post_image($rand_page,'medium'); ?>)">
                <p><?php echo $rand_page->post_title; ?></p>
              </div>
              <?php foreach ($children as $child_menu):
                get_menu_item($child_menu,'a');
              endforeach;?>
              <a onclick="window.location.href='<?php echo get_home_url(); ?>/vereniging/leden/lidmaatschap/'">Lid worden?</a>
              <a onclick="window.location.href='<?php echo get_home_url(); ?>/contact/'">Contact</a>
              <a onclick="window.location.href='<?php echo get_home_url(); ?>/?s'">Zoeken</a>

            </div>
          </div>
        </div>
        <?php
      }
     ?>
   </div>
 <?php
 }

  function getrandomelement($array) {
    $pos=rand(0,sizeof($array)-1);
    $res=$array[$pos];
    if (get_pages( array('parent' => $res->ID) )) return getrandomelement(get_pages( array('parent' => $res->ID) ));
    else return $res;
  }

 function menu_sub_sub_menu($child_menu){
   get_menu_item($child_menu,'h3');
   foreach (get_pages( array('parent' => $child_menu->ID, 'sort_column' => 'menu_order') ) as $page_menu){
     get_menu_item($page_menu,'a');
   }
 }

 function get_menu_item($post,$tag){?>
   <<?php echo $tag; ?> onclick="window.location.href='<?php echo get_permalink($post); ?>'"><?php echo $post->post_title; ?></<?php echo $tag; ?>>
<?php }
?>
