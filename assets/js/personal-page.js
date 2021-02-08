jQuery(document).ready(function($) {
  jQuery('.nav').on( 'click', function( event ) {
		event.preventDefault();
    target = event.currentTarget;
    movediv = target.parentElement.getElementsByClassName('move')[0];
    divwidth = movediv.scrollWidth;
    $screenwidth = $(window).width();
    $left = $(movediv).position().left;
    if(target.classList.contains('right')){
      console.log('next');
      movediv.style.left = nextLeft(1,divwidth,$screenwidth,$left,movediv);
    } else {
      console.log('previous');
      movediv.style.left = nextLeft(-1,divwidth,$screenwidth,$left,movediv);
    }
	});

  function nextLeft(next, divwidth, $screenwidth, $left,movediv){
    $step = Math.floor($screenwidth/240) * 240;
    if ((next < 0)&&(($left + $step)<1)) {
      return ($left + $step)+'px';
    } else if (next < 0) {
      return '0px';
    } else if((next > 0)&&(($step - $left + 120)<divwidth)){
      return ($left - $step)+'px';
    } else {
      return movediv.style.left;
    }
  }
});
