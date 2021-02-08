jQuery(document).ready(function($) {
  var total_ads = parseInt(jQuery('.nav-dots').attr('data-size'));
  var current_ad = 0;
  var rotate_time = parseInt(jQuery('.recla').attr('data-time'));
  setAdvert(current_ad,total_ads);
  jQuery('.nav-dots div').click(function(event){
    current_ad = parseInt($(this).attr('data-ad'));
    setAdvert($(this).attr('data-ad'),total_ads);
  });
  jQuery('.nav.right').click(function(event){
    current_ad = (current_ad == total_ads - 1) ? 0 : current_ad + 1;
    setAdvert(current_ad,total_ads);
  });
  jQuery('.nav.left').click(function(event){
    current_ad = (current_ad == 0) ? total_ads - 1 : current_ad - 1;
    setAdvert(current_ad,total_ads);
  });
  setInterval(function() {
    current_ad = (current_ad == total_ads - 1) ? 0 : current_ad + 1;
    setAdvert(current_ad,total_ads);
  }, rotate_time);

  function setAdvert(first_ad,total_ads) {
    var second_ad = (first_ad == total_ads -1) ? 0 : parseInt(first_ad) + 1;
    jQuery('.recla .container').children()
        .removeClass('active')
        .removeClass('second');
    jQuery('.recla .container').children().eq(first_ad).addClass('active');
    jQuery('.recla .container').children().eq(second_ad)
        .addClass('active')
        .addClass('second');
    jQuery('.nav-dots').children()
      .removeClass('active')
      .eq(first_ad).addClass('active');
  }
});
