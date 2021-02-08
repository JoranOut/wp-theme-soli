var pageContent = document.getElementsByClassName("page-content")[0];
if (pageContent){
  var p = pageContent.getElementsByTagName("p");
  for (var i = 0; i < p.length; i++){
    var img = p[i].getElementsByTagName("img")[0];
    if(img){
      p[i].classList.add("image-wrapper");
      p[i].addEventListener('click', function(){openNewtag(img.src);});
    }
  }
}

function openNewtag(url){
  window.open(url);
}
