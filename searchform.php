<?php
/**
 * Template for displaying search forms in Twenty Seventeen
 *
 * @since Soli 2.0
 * @version 2.0
 */


 function checkcheckbox($id){
   if(ISSET($_POST[$id])){
     echo "checked";
   }
 }
?>

<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>
<article class="wrap-excerpt searchpageform">
  <form onChange="document.getElementsByClassName('search-form')[0].submit()" role="search" method="post" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <div class="top-search">
      <?php if(is_404()){?>
      <h1>Oeps...</h1>
      <h3>De pagina die je probeert te bereiken bestaat niet. Probeer de zoekfunctie hieronder of het menu aan de bovenkant om de gewenste pagina te vinden.</h3>
    <?php }else{ ?>
      <h1>Zoeken...</h1>
    <?php } ?>
    	<input type="search" id="<?php echo $unique_id; ?>"
              class="search-field"
              placeholder="Vul hier je zoekterm in..."
              value="<?php echo get_search_query(); ?>"
              name="s" />
    	<button type="submit" class="searchpage-submit"></button>
    </div>
    <div type="hidden">
      <input id="page"                name="page"                 value="true"    type="checkbox" <?php checkcheckbox("page") ?>/>
      <input id="news"                name="news"                 value="true"    type="checkbox" <?php checkcheckbox("news") ?>/>
      <input id="event"               name="event"                value="true"    type="checkbox" <?php checkcheckbox("event") ?>/>
      <input id="music"               name="music"                value="true"    type="checkbox" <?php checkcheckbox("music") ?>/>
      <input id="mededeling"          name="mededeling"           value="true"    type="checkbox" <?php checkcheckbox("mededeling") ?>/>
      <input id="Harmonie"            name="Harmonie"             value="true"    type="checkbox" <?php checkcheckbox("Harmonie") ?>/>
      <input id="KleinOrkest"         name="KleinOrkest"          value="true"    type="checkbox" <?php checkcheckbox("KleinOrkest") ?>/>
      <input id="Marsorkest"          name="Marsorkest"           value="true"    type="checkbox" <?php checkcheckbox("Marsorkest") ?>/>
      <input id="Bigband"             name="Bigband"              value="true"    type="checkbox" <?php checkcheckbox("Bigband") ?>/>
      <input id="Opleidingsorkest"    name="Opleidingsorkest"     value="true"    type="checkbox" <?php checkcheckbox("Opleidingsorkest") ?>/>
      <input id="Slagwerkgroep"       name="Slagwerkgroep"        value="true"    type="checkbox" <?php checkcheckbox("Slagwerkgroep") ?>/>
      <input id="Opstapklas"          name="Opstapklas"           value="true"    type="checkbox" <?php checkcheckbox("Opstapklas") ?>/>
      <input id="Samenspeelklas"      name="Samenspeelklas"       value="true"    type="checkbox" <?php checkcheckbox("Samenspeelklas") ?>/>
      <input id="Volwassenopstapklas" name="Volwassenopstapklas"  value="true"    type="checkbox" <?php checkcheckbox("Volwassenopstapklas") ?>/>
      <input id="Slagwerkklas"        name="Slagwerkklas"         value="true"    type="checkbox" <?php checkcheckbox("Slagwerkklas") ?>/>
      <input id="Twirlteam"           name="Twirlteam"            value="true"    type="checkbox" <?php checkcheckbox("Twirlteam") ?>/>
      <input id="StilOrkest"          name="StilOrkest"           value="true"    type="checkbox" <?php checkcheckbox("StilOrkest") ?>/>
      <input id="Dicksfive"           name="Dicksfive"            value="true"    type="checkbox" <?php checkcheckbox("Dicksfive") ?>/>
      <input id="Funband"             name="Funband"              value="true"    type="checkbox" <?php checkcheckbox("Funband") ?>/>
      <input id="Kopersextet"         name="Kopersextet"          value="true"    type="checkbox" <?php checkcheckbox("Kopersextet") ?>/>
    </div>
  </form>
</article>
