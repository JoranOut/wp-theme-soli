jQuery(function($){
  $(window).ready(function(){
    var publish = document.getElementById('publish');
    var title = document.getElementById('title');
    if(title){
      check_title(title.value);
      title.addEventListener('change',function(el){
        check_title(el.target.value);
      });
    }
    var add_image_button = document.getElementById('set-post-thumbnail');
    if(add_image_button){
      add_image_button.addEventListener('click',function (el) {
        check_image();
      });
    }

    document.getElementById('uam_post_access').style.display = "none";

    setTimeout(function () {
      setInterval(function () {
        switch ($('input[name=tv_post_type]:checked').val()){
          case "pic_with_text":
            check_required_existence("pic","text");
            break;
          case "pic_with_text_contain":
            check_required_existence("pic","text");
            break;
          case "pic_only":
            check_required_existence("pic");
            break;
          default:
            console.log("not found");
            break;
        }
      }, 500);
    }, 1000);

    function check_required_existence(...args){
      if(args.includes("pic")){
        document.getElementById("postimagediv").style.display = "block";
        if(document.querySelectorAll("#postimagediv .inside > * ").length === 2){
          disablePublish("pic","Stel een afbeelding in");
        } else {
          enablePublish("pic");
        }
      } else {
        document.getElementById("postimagediv").style.display = "none";
        enablePublish("pic");
      }
      if(args.includes("text")){
        document.getElementsByClassName("postarea")[0].style.display = "block";
      } else {
        document.getElementsByClassName("postarea")[0].style.display = "none";
      }
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
              if(parseInt(dims[0])<800||parseInt(dims[1])<800){
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

    function enablePublish(id){
      var warning = document.getElementById('title_warning'+id);
      if(warning)warning.parentNode.removeChild(warning);
      publish.removeAttribute('disabled');
    }

    function disablePublish(id,text){
      publish.setAttribute('disabled',true);
      var warning = document.getElementById('title_warning'+id)
      if(!warning){
        var warning = document.createElement('div');
        warning.className = 'update-nag';
        warning.id = 'title_warning'+id;
        warning.innerText = text;
        document.getElementById('submitpost').insertBefore(warning,document.getElementById('major-publishing-actions'));
      }
    }


    function check_title(t) {
      var perma = document.getElementById('editable-post-name');
      if(perma)perma.innerText = t;
      if(t.length > 200){
        disablePublish("title","Tekst mag maximaal 200 tekens zijn.")
      } else {
        enablePublish("title");
      }
    };
  });
});
