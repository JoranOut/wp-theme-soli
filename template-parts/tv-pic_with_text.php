<div class="stage">
  <div class="left" style="background-image:url('<?php echo get_soli_post_image($post,'large'); ?>')"></div>
  <div class="right">
    <div class="qrcode" style="background-image:url('<?php echo 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.urlencode($post->guid).'&choe=UTF-8'; ?>')"></div>
    <h1><?php echo $post->post_title; ?></h1>
    <p><?php echo $post->post_content; ?></p>
  </div>
</div>
