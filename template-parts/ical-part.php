<div class="ical-flexbox">
  <div>
    <h3>Openbare agenda</h3>
    <p>Download of koppel de openbare agenda, om met iedereen te delen!</p>
    <a class="copy">Koppel de agenda</a>
    <a class="download" href="<?php echo get_home_url()."/ical/"?>">Of download de agenda</a>
    <div>
      <p>Gebruik deze link in je agenda applicatie</p>
      <input type="text" value="<?php echo get_home_url()."/ical/"?>">
    </div>
  </div>
  <div class="<?php if(!is_user_logged_in()) {echo "disabled";} ?>">
    <h3>Complete agenda</h3>
    <p>Download of koppel de openbare agenda samen met al je persoonlijke agendapunten</p>
    <?php if(is_user_logged_in()) {?>
      <a class="copy">Koppel de agenda</a>
      <a class="download" href="<?php echo get_home_url()."/ical/?i=".wp_get_current_user()->ID."&h=".get_user_ical_hash(wp_get_current_user()->ID)?>">Of download de agenda</a>
    <?php } else {?>
      <a class="copy" href="<?php echo home_url("/wp-login.php");?>">Log in</a>
      <a class="download"> om te koppelen of downloaden</a>
    <?php } ?>
    <div>
      <p>Gebruik deze link in je agenda applicatie</p>
      <input type="text" value="<?php echo get_home_url()."/ical/?i=".wp_get_current_user()->ID."&h=".get_user_ical_hash(wp_get_current_user()->ID)?>">
    </div>
  </div>
  <div class="<?php if(!is_user_logged_in()) {echo "disabled";} ?>">
    <h3>Persoonlijke agenda</h3>
    <p>Download of koppel alleen je persoonlijke agenda</p>
    <?php if(is_user_logged_in()) {?>
      <a class="copy">Koppel de agenda</a>
      <a class="download" href="<?php echo get_home_url()."/ical/?i=".wp_get_current_user()->ID."&h=".get_user_ical_hash(wp_get_current_user()->ID)."&o=1"?>">Of download de agenda</a>
    <?php } else {?>
      <a class="copy" href="<?php echo home_url("/wp-login.php");?>">Log in</a>
      <a class="download"> om te koppelen of downloaden</a>
    <?php } ?>
    <div>
      <p>Gebruik deze link in je agenda applicatie</p>
      <input type="text" value="<?php echo get_home_url()."/ical/?i=".wp_get_current_user()->ID."&h=".get_user_ical_hash(wp_get_current_user()->ID)."&o=1"?>">
    </div>
  </div>
</div>
<script>
var copyelements = document.querySelectorAll(".ical-flexbox > div:not(.disabled) > .copy");
for (var i = 0; i < copyelements.length; i++) {
    copyelements[i].addEventListener('click', function() {
      this.parentElement.getElementsByTagName("div")[0].style.display = "block";
      var copyText = this.parentElement.querySelector("div > input");
      console.log(copyText);
      copyText.select();
      copyText.setSelectionRange(0, 99999)
      document.execCommand("copy");
    }, false);
}
</script>
