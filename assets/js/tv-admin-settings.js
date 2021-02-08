jQuery(function($){
  $(window).ready(function(){
    var saving = false;

    if (document.getElementById('submit_soli_tv_settings_sa')){
      document.getElementById('submit_soli_tv_settings_sa').addEventListener('click', function (elem) {
        if (!saving) {
          saving_tv_settings(elem);
        }
      });
    }

    function saving_tv_settings(elem) {
      saving = true;
      elem.currentTarget.classList.add("saving");
      var nonce = elem.currentTarget.getAttribute('data-nonce');

      var myVals = $.map($('.form-table.stand-alone input'),function(el){
        return {name: el.name, value: !$(el).prop('checked')};
      });

      myVals = JSON.stringify(myVals);
      var formData = new FormData();
      formData.append('action', 'save_tv_settings');
      formData.append('nonce', nonce);
      formData.append('info', myVals);
      formData.append('type', "invisible_on_tv");

      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status === 200) {
          setTimeout(function(){
            document.getElementById('submit_soli_tv_settings_sa').classList.remove("saving");
            saving = false;
          }, 500);
        }
      };
      xhttp.open('POST', myAjax.ajaxurl, true);
      xhttp.send(formData);
    }

    if (document.getElementById('submit_soli_tv_settings_gallery')){
      document.getElementById('submit_soli_tv_settings_gallery').addEventListener('click', function (elem) {
        if (!saving) {
          saving_tv_gallery_settings(elem);
        }
      });
    }

    function saving_tv_gallery_settings(elem) {
      saving = true;
      elem.currentTarget.classList.add("saving");
      var nonce = elem.currentTarget.getAttribute('data-nonce');

      var myVals = $.map($('.form-table.gallery input'),function(el){
        return {name: el.name, value: !$(el).prop('checked')};
      });

      myVals = JSON.stringify(myVals);
      var formData = new FormData();
      formData.append('action', 'save_tv_settings');
      formData.append('nonce', nonce);
      formData.append('info', myVals);
      formData.append('type', "invisible_on_gallery");

      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status === 200) {
          setTimeout(function(){
            document.getElementById('submit_soli_tv_settings_gallery').classList.remove("saving");
            saving = false;
          }, 500);
        }
      };
      xhttp.open('POST', myAjax.ajaxurl, true);
      xhttp.send(formData);
    }
  });
});
