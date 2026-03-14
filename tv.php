<?php
/**
 * The template for feeding tv information
 *
 * TEMPLATE NAME: Tv
 * @since Soli 2.0
 * @version 2.0
 */

$userid = 0;
$role_name = 0;
$myrows = get_myrows(0);

$events = tribe_get_events(array(
  'posts_per_page' => 6,
  'start_date' => date('Y-m-d H:i:s'),
  'post__not_in' => $myrows
));

function filenameToDir($name){
  return '/home/pi/Pictures/feedImg/'.explode('.',end(explode('/',$name)))[0].".jpg";
}

$chckimgs = array();

$args = array( 'post_type' => 'tv', 'posts_per_page' => -1 );
$loop = new WP_Query( $args );
$tvs = $loop->posts;


$html = "<!DOCTYPE html><html><head>
<meta charset=".get_bloginfo( 'charset' ).">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<meta name=\"googlebot\" content=\"notranslate\">
<style>
html,body {margin:0; padding:0}
html      {overflow: hidden}
h1        {color: black; font-size: 5rem}
h2        {color: black; font-size: 2rem}
p         {padding-right: 150px; color: black; font-size: 2rem}
.left     {position:relative; float:left; width: calc(50vw - 120px); height:100vh; background-size:cover; background-position:center}
.stage:nth-child(even) .left {float:right}
.left.wide  {width:100vw;background-size:contain;background-color:black;background-repeat:no-repeat;}
.left.contain {background-size:contain; background-repeat: no-repeat; background-position: center;}
.right    {width:50vw; height:100vh; padding:10px 50px; float:right; position:relative}
.stage    {width:100vw; height:100vh}
.load     {position:relative;width:100vw; height:100vh}
.load>h3  {top:50%; left:50%; position:absolute; transform: translate(-50%,-50%);}
.qrcode   {position:absolute; width:150px; height:150px; right:50px; bottom:50px; background-size:contain;}
.stage.gallery {background-position:center;background-size:cover; position: relative;}
.stage .items {background: rgba(255,255,255,1);display: flex;flex-direction: column;position: absolute;right: 0; bottom:0; top: 0;width: 30%;padding: 10px 30px;
    border-left: 2px solid rgba(204,204,204,0.8);}
.stage .item {display:flex;padding:10px 0;}
.stage .item.active {background: rgba(0,0,0,0.035)}
.stage .item div {display: flex; flex-direction: column;}
.stage .item div:nth-of-type(1) {border-right: 4px solid red;}
.stage .item div h4 {font-size:1.2rem; margin:5px 15px}
.stage .items .link {position:absolute; bottom:5%; right: 35px; font-size: 1.2rem;text-align:right;}
.stage .main_item {position: absolute; display: block; width: 50%; left: 5%; bottom: 5%; background: rgba(255,255,255,0.933); padding: 0 60px 65px 60px; box-shadow: 0px 0px 20px -5px rgba(0,0,0,1);}
.stage .items h1 {margin: 35px 5px}
.stage .item div:nth-of-type(1) h4:nth-of-type(2){color:rgba(0,0,0,0.4)}
.stage .item div:nth-of-type(1) h4:nth-of-type(1){color:rgba(0,0,0,0.666)}
.stage .item div:nth-of-type(2) h4:nth-of-type(2){color:rgba(0,0,0,0.266)}
</style><body><div id=\"load\" class=\"load\"><h3>Laden...</h3></div>";

if($events){
  foreach ($events as $post) {
    if(get_post_meta($post->ID,"invisible_on_tv", true) != 1){
      //$html .= get_template_part( 'template-parts/tv', 'event' );
      $html .= get_tv_gallery($events, get_template_directory_uri().'/assets/img/applause-audience-band-196652.jpg',true,$post);
      array_push($chckimgs, get_soli_post_image($post,"large"));
    }
  }
}

if($tvs){
  foreach ($tvs as $post){
    if(get_post_meta($post->ID,"invisible_on_tv", true) != 1){
      $html .= get_template_part( 'template-parts/tv', get_post_meta( $post->ID, 'tv_post_type', true ));
      array_push($chckimgs, get_soli_post_image($post,"large"));
    }
  }
}

$html .= "<script type=\"text/javascript\">
function checkImgs(){
  var imgs = ".json_encode($chckimgs).";
  for (var k = 0; k < imgs.length; k++){
    var image = new Image();
    image.src = imgs[k];
    if (image.width == 0) {
      //location.reload(true);
    }
  }
}

function setBlock(el,i){
  for(var j = 0; j < el.length; j++){
    if(i===j){
      el[j].style.display=\"block\"
    } else {
      el[j].style.display=\"none\"
    }
  }
}

window.addEventListener(\"load\", function(event){
  setTimeout(function(){
    if (typeof checkImgs === \"function\") {
      checkImgs();
    }
    document.getElementById(\"load\").style.display=\"none\";
    var i = 0;
    var el = document.getElementsByClassName(\"stage\");
    setBlock(el,i);
    setInterval(function(){
      setBlock(el,i);
      i=(i < el.length-1) ? i+1 : 0;
    }, 10000);
  }, 3000);
});

</script></body></html>";

echo $html;
?>
