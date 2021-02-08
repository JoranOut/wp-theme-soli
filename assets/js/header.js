function isOverflown(element) {
    try {
      return element.scrollHeight + 10 > element.clientHeight || element.scrollWidth + 10 > element.clientWidth;
    } catch (e) {
      return false;
    }
}

function escapeRegExp(str) {
    return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function replaceAll(str, find, replace) {
    return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}

window.onscroll = function () {
  var top = document.documentElement.scrollTop || document.body.scrollTop;
  var final = document.location.pathname.substr(document.location.pathname.lastIndexOf('/') + 1);

  if (top > 70 || (final === 'mijn-pagina' && top > 10)) {
    document.querySelector('body > header').className = 'scrolled';
  } else {
    document.querySelector('body > header').className = '';
  }
};
/*duplicate code remove later*/
document.addEventListener('DOMContentLoaded',function(){
  var top = document.documentElement.scrollTop || document.body.scrollTop;
  var final = document.location.pathname.substr(document.location.pathname.lastIndexOf('/') + 1);

  if (top > 150 || (final === 'mijn-pagina' && top > 10)) {
    document.querySelector('body > header').className = 'scrolled';
  } else {
    document.querySelector('body > header').className = '';
  }
});
window.addEventListener('load', function () {
  document.getElementById('main-nav').checked = false;
  document.getElementById('main-nav').onchange = function (event) {
    var id = event.target;
    if (id.checked) {
      document.querySelector('header .logo').classList.add('open');
      document.querySelector('header .login').classList.add('open');
      document.querySelector('header .hamburger').classList.add('open');
      document.getElementById('menu-custom-menu').parentElement.classList.add('open');
    } else {
      document.querySelector('header .logo').classList.remove('open');
      document.querySelector('header .login').classList.remove('open');
      document.querySelector('header .hamburger').classList.remove('open');
      document.getElementById('menu-custom-menu').parentElement.classList.remove('open');
    }
  };

  var titles = document.getElementsByClassName("font-resizer");
  for(u = 0; u < titles.length; u++){
    if(titles[u] || getComputedStyle(titles[u], null).display != "none"){
      titles[u] = titles[u].childNodes[1];
      var tries = 10;
      while(isOverflown(titles[u]) && tries > 0){
        titles[u].style.fontSize = parseFloat(window.getComputedStyle(titles[u]).fontSize) - 1 + "px";
        tries--;
      }
    }
  }

  var texts = document.getElementsByClassName("page-content")[0];
  if(texts){
    texts = texts.getElementsByTagName("div");
    for(var i=0; i < texts.length; i++){
      if(isOverflown(texts[i])){
        texts[i].innerHTML = replaceAll(texts[i].innerHTML, "&nbsp;"," ");
      }
    }
  }

  var nonce = document.getElementById('notification').getAttribute('data-nonce');
  var formData = new FormData();
  formData.append('action', 'any_message');
  formData.append('nonce', nonce);
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (xhttp.readyState === 4 && xhttp.status === 200) {
      if (xhttp.responseText) {
        if(parseInt(xhttp.responseText)>0){
          document.getElementById('notification').style.display = "block";
        }
      }
    }
  };
  xhttp.open('POST', myAjax.ajaxurl, true);
  xhttp.send(formData);
});

var button = document.getElementById('arrow-down');
var targetElemet = document.getElementById('scroll-target');

if (button) {
  button.addEventListener('click', function (event) {
    targetElemet.scrollIntoView({behavior: 'smooth', block: 'start'});
    event.preventDefault();
  });
}
