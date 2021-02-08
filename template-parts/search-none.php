<?php
$fp_info = get_soli_fp_info();
$image = wp_get_attachment_image_src($fp_info['frontpage_background'],'full');
 ?>

<div class="main_underlay" style="<?php
  if($image[0]!==null){
    echo "background-image:linear-gradient(-135deg, rgba(170, 42, 42, .7) 0,rgba(75, 33, 191, .7) 100%),url('".$image[0]."')";
  }?>;">
    <?php get_search_form()?>
  <div class="to_event">
    <a href="<?php echo $fp_info['frontpage_button_link']; ?>">
      <p class="frontpage_date"><?php echo $fp_info['frontpage_subtitle'];
      if ($fp_info['frontpage_subtext']){
      ?>, <?php echo $fp_info['frontpage_subtext'];} ?> →</p>
    </a>
  </div>
</div>
