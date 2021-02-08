document.addEventListener('DOMContentLoaded', function () {
  console.log("loding bookings-admin");
  loadBookingData();

});

function loadBookingData() {
  if(document.getElementById('post_ID')) {
    let formData = new FormData();
    formData.append('action', 'db_get_participants');
    formData.append('post_id',document.getElementById('post_ID').value);
    AJAXCALL(formData, function (data) {
      let row = JSON.parse(data);
      if (row[0]){
        let field = document.getElementById("meta-"+row[0].meta_id+"-value");
        if (field) {
          field.value = row[0].meta_value;
          field.innerHTML = row[0].meta_value;

        }
      }
      setTimeout(function () {
        loadBookingData()
      }, 1000);
    });
  }
}
function AJAXCALL(formData, callback) {
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function () {
    if (xhttp.readyState === 4 && xhttp.status === 200) {
      if (xhttp.responseText) {
        callback(xhttp.responseText);
      }
    }
  };
  xhttp.open('POST', myAjax.ajaxurl, true);
  xhttp.send(formData);
}
