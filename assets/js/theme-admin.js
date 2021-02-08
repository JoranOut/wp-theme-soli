jQuery(function($){
	/*
	 * Select/Upload image(s) event
	 */
	$('body').on('click', '.default_image_button', function(e){
		e.preventDefault();

    		var button = $(this),
    		    custom_uploader = wp.media({
			title: 'Insert image',
			library : {
				type : 'image'
			},
			button: {
				text: 'Use this image'
			},
			multiple: false
		}).on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:300px;display:block;" />').next().val(attachment.id).next().show();
		})
		.open();
	});

	/*
	 * Remove image event
	 */
	$('body').on('click', '.default_remove_image_button', function(){
		$(this).hide().prev().val('').prev().addClass('button').html('Upload image');
		return false;
	});

  $('.select_event:eq(0)').prop('checked', true);

  $('body').on('change', '.select_event', function (event){
    if(event.currentTarget.dataset.image){
      var attachment = event.currentTarget.dataset.image;
      $('.default_image_button:eq(1)').removeClass('button').html('<img class="true_pre_image" src="' + attachment + '" style="max-width:300px;display:block;" />').next().val(event.currentTarget.dataset.imageid).next().show();
    } else {
      $('.default_remove_image_button:eq(1)').hide().prev().val('').prev().addClass('button').html('Upload image');
    }
    var monthNames = [
      "Januari", "Februari", "Maart",
      "April", "Mei", "Juni", "Juli",
      "Augustus", "September", "Oktober",
      "November", "December"
    ];
    if(event.currentTarget.value == 0){
      $("#frontpage_subtext").attr('value',event.currentTarget.dataset.date);
    } else {
      var date = new Date(Date.parse(event.currentTarget.dataset.date));
      $("#frontpage_subtext").attr('value',date.getDate() + " " + monthNames[date.getMonth()]);
    }
    $("#frontpage_subtitle").attr('value',event.currentTarget.dataset.subtitle);
    $("#frontpage_button_link").attr('value',event.currentTarget.dataset.url);
  });

  var saving = false;

  $(document).ready(function () {
    $('#submit_soli_images').click(function (elem) {
      if (!saving) {
        saving_settings(elem);
      }
    });
    $('.form-table tr').click(function (elem){
      if(!(elem.target === this || elem.target.parentElement === this)) return;
      elem.currentTarget.classList.toggle('expanded');
    });

    $('.button.term').click(function (elem){
      elem.currentTarget.parentNode.removeChild(elem.currentTarget);
    });

    $(".add_image_term").on('keyup', function (e) {
      if (e.keyCode == 13 || e.keyCode == 32 || e.keyCode == 59) {
        var a = document.createElement('a');
        a.className = "button term";
        a.onclick = function(elem){
          elem.currentTarget.parentNode.removeChild(elem.currentTarget);
        }
        if(e.keyCode == 59)
          a.innerHTML = e.currentTarget.value.slice(0, -1);
        else
          a.innerHTML = e.currentTarget.value;
        e.currentTarget.parentElement.insertBefore(a, e.currentTarget);
        e.currentTarget.value = "";
      }
    });
  });

  function saving_settings(elem) {
    saving = true;
    elem.currentTarget.classList.add("saving");
    var nonce = elem.currentTarget.getAttribute('data-nonce');

    //save frontpage settings
    var inputelem = elem.currentTarget.parentElement.parentElement.querySelectorAll("table.frontpage input:not(.select_event)");
    var frontpage_settings = "";
    for (i = 0; i < inputelem.length; i++){
      var input = inputelem[i];
      if(input.value !== input.getAttribute("init-value")){
        frontpage_settings += '"[\\"' + input.id + '\\", \\"' + input.value + '\\"]",';
      }
    }
    frontpage_settings = frontpage_settings.substr(0,frontpage_settings.length-1);
    frontpage_settings = "["+frontpage_settings+"]";

    //save standard images
    var inputelem = elem.currentTarget.parentElement.parentElement.querySelectorAll("table:not(.frontpage) input.image");
    var images = [];
    var previousid;
    var combinedinputs = [];
    for (i = 0; i < inputelem.length; i++){
      var input = inputelem[i];

      if (previousid && previousid!=input.id || i == 0){
        if (combinedinputs.length > 1) {
          images.push(combinedinputs);
        }
        combinedinputs = [];
        combinedinputs.push(input.id);
      }

      if(input.getAttribute("value") !== input.getAttribute("init-value")){
        if(!(combinedinputs.indexOf(input.getAttribute("value")) > -1)){
          combinedinputs.push(input.getAttribute("value"));
        }
      }

      if (i == inputelem.length - 1){
        if (combinedinputs.length > 1) {
          images.push(combinedinputs);
        }
        combinedinputs = [];
      }

      previousid = input.id;
    }
    images = JSON.stringify(images);

    var formData = new FormData();
    formData.append('action', 'save_default_images');
    formData.append('nonce', nonce);
    formData.append('info', images);
    formData.append('frontpage_settings', frontpage_settings);

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (xhttp.readyState === 4 && xhttp.status === 200) {
        setTimeout(function(){
          elem.currentTarget.classList.remove("saving");
          saving = false;
        }, 500);
      }
    };
    xhttp.open('POST', myAjax.ajaxurl, true);
    xhttp.send(formData);
  }
});
