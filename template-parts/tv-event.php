<?php
$arraydag = array(
  "Zondag",
  "Maandag",
  "Dinsdag",
  "Woensdag",
  "Donderdag",
  "Vrijdag",
  "Zaterdag"
);
$arraymaand = array(
  "Januari",
  "Februari",
  "Maart",
  "April",
  "Mei",
  "Juni",
  "Juli",
  "Augustus",
  "September",
  "Oktober",
  "November",
  "December"
);
  ?>
<div class="main_item">
  <div class="qrcode" style="background-image:url('<?php echo 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.urlencode($post->guid).'&choe=UTF-8'; ?>')"></div>
  <h1><?php echo $post->post_title; ?></h1>
  <?php
    $date = DateTime::createFromFormat('Y-m-d H:i:s',$post->event_date);
    $dag = $arraydag[$date->format('w')];
    $maand = $arraymaand[$date->format('n')-1];
  ?>
  <h2><?php echo $dag." ".$date->format('j')." ".$maand." ".$date->format('Y')." ".$date->format('H:i').", ".tribe_get_venue($post) ?></h2>
  <p><?php echo limit_excerpt(wp_strip_all_tags($post->post_content),200);?></p>
</div>
