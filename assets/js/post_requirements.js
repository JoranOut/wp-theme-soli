window.onload = function(){
  var publish = document.getElementById('publish');
  var title = document.getElementById('title');
  check_title(title.value);
  title.addEventListener('change',function(el){
    check_title(el.target.value);
  });
  var add_image_button = document.getElementById('set-post-thumbnail');
  if(add_image_button){
    add_image_button.addEventListener('click',function (el) {
      check_image();
    });
  }

  function check_image() {
    setTimeout(function () {
      var popup = document.getElementsByClassName('media-sidebar')[0];
      var fullpopup = document.getElementById('__wp-uploader-id-2');
      var i = setInterval(function(){
        if(fullpopup.style.display!='none'){
          var dimension_text = popup.getElementsByClassName('dimensions')[0];
          if(dimension_text){
            var dims = dimension_text.innerText.split('×');
            if(parseInt(dims[0])<300||parseInt(dims[1])<300){
              var imgbutton = document.getElementsByClassName('media-button-select')[0];
              dimension_text.className = "dimensions delete-attachment";
              imgbutton.setAttribute('disabled',false);
            } else {
              imgbutton.removeAttribute('disabled');
            }
          }
        } else {
          clearInterval(i);
        }
      }, 500);
    }, 1000);
  }

  function check_title(t) {
    var perma = document.getElementById('editable-post-name');
    if(perma)perma.innerText = t;
    if(t.length > 200){
      publish.setAttribute('disabled',true);
      var warning = document.getElementById('title_warning')
      if(!warning){
        var warning = document.createElement('div');
        warning.className = 'update-nag';
        warning.id = 'title_warning';
        warning.innerText = "Titel mag maximaal 200 karakters zijn.";
        document.getElementById('wpbody-content').insertBefore(warning,document.getElementsByClassName('wrap')[0]);
      }
    } else {
      var warning = document.getElementById('title_warning')
      if(warning)warning.parentNode.removeChild(warning);
      publish.removeAttribute('disabled');
    }
  };
};
