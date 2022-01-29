console.log("ical loaded");
document.addEventListener('DOMContentLoaded', function () {
  let copyelements = document.getElementsByClassName("ical-flexbox-copy");
  console.log(copyelements);
  for (let i = 0; i < copyelements.length; i++) {
    console.log(copyelements[i]);
    copyelements[i].addEventListener('click', function() {
      copyelements[i].parentElement.getElementsByTagName("div")[0].style.display = "block";
      let copyText = this.parentElement.querySelector("div > input");
      console.log(copyText);
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");
    }, false);
  }
});
