<?php
/**
 * The front page template file
 *
 * @since Soli 2.0
 * @version 2.0
 */

get_header();

$fp_info = get_soli_fp_info();
$image = wp_get_attachment_image_src($fp_info['frontpage_background'],'large');
$header_image = wp_get_attachment_image_src($fp_info['frontpage_header_image'],'large');
global $myrows;
?>

<header style="<?php
  if($image[0]!==null){
    echo "background-image:linear-gradient(-135deg, rgba(170, 42, 42, .7) 0,rgba(75, 33, 191, .7) 100%),url('".$image[0]."')";
  }?>; cursor: pointer" onclick="window.location.href='<?php echo $fp_info['frontpage_button_link']; ?>'">
  <img src="<?php
    if($header_image[0]){
      echo $header_image[0];
    } else {
      bloginfo('template_url');
      echo "/assets/img/logo_white.svg";
    }?>" alt="logo">
  <?php if($fp_info['frontpage_subtitle']&&$fp_info['frontpage_button_link']){?>
  <div class="to_event">
    <a href="<?php echo $fp_info['frontpage_button_link']; ?>">
      <p class="frontpage_date"><?php echo $fp_info['frontpage_subtitle'];
      if ($fp_info['frontpage_subtext']){
      ?>, <?php echo $fp_info['frontpage_subtext'];} ?> →</p>
    </a>
  </div>
  <?php } ?>
  <img id="arrow-down" src="<?php bloginfo('template_url'); ?>/assets/img/arrow-down.svg" alt="arrow-down">
</header>
<div id="primary" class="content-area">
  <div id="scroll-target"></div>
	<main id="main" class="site-main" role="main">
    <section>
      <article class="wrap-excerpt">
        <section class="title"><h1>Nieuws</h1></section>
        <style> @import url("https://use.typekit.net/gkv6gpa.css");</style>
        <?php
        global $post;
        $posts = get_posts(array(
          'posts_per_page' => 4,
          'order' => 'DESC',
          'exclude' => $myrows));
        if($posts){
          foreach ($posts as $post) {
            get_template_part( 'template-parts/post', 'excerpt' );
          }
        } else {
          get_template_part( 'template-parts/content', 'none' );
        }
        ?>
        <section class="button"><button onclick="location.href='./archief/';">Meer Nieuws</button></section>
      </article>
    </section>
    <section class="white">
      <article class="wrap-excerpt">
        <section class="title"><h1>Onze vereniging</h1></section>
        <script src="https://www.youtube.com/iframe_api"></script>
        <div class="iframe-container">
          <div id="video" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"width="1200"
          height="400" type="text/html"></div>
          <div id="video-overlay">
            <div id="video-sound-on">Bekijk deze video met geluid!</div>
            <div id="video-play"></div>
            <div id="video-mute"></div>
          </div>
        </div>
        <script>
        var player, playing = false;
        function onYouTubeIframeAPIReady() {
          player = new YT.Player('video', {
            height: '400',
            width: '1200',
            videoId: '4VkbbB6X_lg',
            events: {
              'onReady': onPlayerReady,
              'onStateChange': onPlayerStateChange
            },
            playerVars: {
              'autoplay': 0,
              'controls': 0,
              'enablejsapi': 1,
              'disablekb': 1,
              'fs': 1,
              'loop': 1,
              'modestbranding': 1,
              'rel': 0,
              'showinfo': 0,
              'mute': 1,
              'playlist': '4VkbbB6X_lg',
              'autohide': 1,
              'cc_load_policy': 1,
              'cc_lang_pref': 'nl',
            }
          });
        }

        function onPlayerReady(event) {
          var firstplay = 0;
          document.getElementById("video-play").addEventListener('click',function(el){
            if(document.getElementById("video-overlay").classList.contains('playing')||document.getElementById("video-overlay").classList.contains('playingfirst')){
              document.getElementById("video-overlay").classList.remove('playingfirst');
              document.getElementById("video-overlay").classList.remove('playing');
              player.pauseVideo();
            } else {
              player.playVideo();
              if (firstplay == 0) {
                document.getElementById("video-overlay").classList.add('playingfirst');
                firstplay++;
              } else {
                document.getElementById("video-overlay").classList.add('playing');
              }
            }
          });
          document.getElementById("video-mute").addEventListener('click',function(el){
            if(document.getElementById("video-mute").classList.contains('sound')){
              player.mute();
              document.getElementById("video-mute").classList.remove('sound');
            } else {
              player.unMute();
              document.getElementById("video-mute").classList.add('sound');
            }
          });
        }

        function onPlayerStateChange(event) {
          if (event.data == YT.PlayerState.PLAYING) {
            playing = true;
          }

          else if(event.data == YT.PlayerState.PAUSED){
            playing = false;
          }
        }

      </script>
        <div class="flex-info" onclick="window.location.href='<?php echo esc_url( get_permalink( get_page_by_title('Lidmaatschap') ) )?>'">
          <div class="flex-text">
            <h2>Lid worden?</h2>
            <p>Al sinds 1909 is Soli een van de grootste en gezelligste verenigingen uit de omgeving. Denk je erover ook lid te worden van Soli? Als lid heb je geen hoge aanschafkosten voor een instrument. Ook wordt je een muziekopleiding via Soli met officieel erkende HaFaBra-diploma's aangeboden. Maar bovenal heeft Soli het verenigingsleven hoog in het vaandel staan. Benieuwd geworden? Neem hier een kijkje! </p>
          </div>
          <div style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .35) 0%, rgba(75, 33, 191, .35) 100%),
            url(<?php echo get_soli_post_image(get_page_by_title('Lidmaatschap'),'large'); ?>);background-size: cover;background-position: center;">
          </div>
        </div>
        <div class="flex-info" onclick="window.location.href='<?php echo esc_url( get_permalink( get_page_by_title('Muzieklessen') ) )?>'">
          <div style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .35) 0%, rgba(75, 33, 191, .35) 100%),
            url(<?php echo get_soli_post_image(get_page_by_title('Muzieklessen'),'large'); ?>);background-size: cover;background-position: center;">
          </div>
          <div class="flex-text">
            <h2>Muzieklessen nemen?</h2>
            <p>Soli houdt vast aan een goede kwaliteit HaFaBra opleiding door professionele docenten tegen een redelijk tarief (vergelijkbaar met de bedragen die hier genoemd worden). Zodra er over de organisatie van de muzieklessen meer bekend is dan zal dit worden vermeld. Indien u muzieklessen wilt volgen, neem dan contact op met opleidingen@soli.nl voor meer informatie.</p>
          </div>
        </div>
        <div class="flex-info" onclick="window.location.href='<?php echo esc_url( get_permalink( get_page_by_title('Orkesten en groepen') ) )?>'">
          <div class="flex-text">
            <h2>Geen feest als Soli niet is geweest!</h2>
            <p>Gezelligheid is er altijd binnen de vereniging te vinden. Maar die nemen wij graag ook mee naar uw evenement! Een orkest, pietenband of iets totaal anders? Voor elke gelegenheid kunnen wij een muzikale omlijsting verzorgen! </p>
          </div>
          <div style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .35) 0%, rgba(75, 33, 191, .35) 100%),
            url(<?php echo get_soli_post_image(get_page_by_title('Funband'),'large'); ?>);background-size: cover;background-position: center;">
          </div>
        </div>
      </article>
    </section>
    <section>
      <?php
        get_template_part( 'template-parts/sponsorkliks', 'frontpage' );
      ?>
      <article class="wrap-excerpt">
        <section class="title"><h1>Agenda</h1></section>
        <?php
        if(function_exists('tribe_get_events')){
          $events = tribe_get_events(array(
            'posts_per_page' => 6,
            'start_date' => date('Y-m-d 00:00:00'),
            'post__not_in' => $myrows
          ));
          if($events){
            foreach ($events as $post) {
              get_template_part( 'template-parts/post', 'excerpt' );
            }
          } else{
            get_template_part( 'template-parts/agenda-content', 'none');
          }
        } else {
          echo "Tribe Event Calendar plugin is not installed.";
        }
        ?>
        <section class="button"><button onclick="location.href='./agenda/';">Agenda</button></section>
      </article>
    </section>
	</main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
