let bookingsInputHold = false;

document.addEventListener('DOMContentLoaded', function () {
  loadBookingData();
});

function loadBookingData() {
  let formData = new FormData();
  formData.append('action', 'get_booking_data');
  AJAXCALL(formData, function (data) {
    if (!bookingsInputHold) {
      importBookingData(data);
    }
    setTimeout(function () {
      loadBookingData()
    }, 2000);
  });
}

function removeError(elem) {
  elem.classList.remove("error");
}

function setChecked(id) {
  let checkbox = document.getElementById("booking-" + id);
  let instrument = document.getElementById("booking-instrument-" + id);
  if (checkbox.checked && instrument.value === "") {
    checkbox.checked = false;
    instrument.parentElement.classList.add("error");
    instrument.addEventListener('click', function () {
      removeError(instrument.parentElement)
    });
  } else {
    bookingsInput(id);
  }
}


function bookingsInput(id, userid, del) {
  console.log("checked");

  bookingsInputHold = true;
  let formData = new FormData();
  if (document.getElementById("booking-" + id).checked && !(userid && del)) {
    document.getElementById("booking-instrument-" + id).disabled = document.getElementById("booking-" + id).checked;
    formData.append('action', 'set_booking_participant');
    formData.append('instrument', document.getElementById("booking-instrument-" + id).value);
  } else {
    formData.append('action', 'delete_booking_participant');
    if (userid && del) {
      formData.append('user_id', userid);
      let remel = document.getElementById(del);
      remel.parentElement.removeChild(remel);
    } else {
      document.getElementById("booking-instrument-" + id).disabled = document.getElementById("booking-" + id).checked;
    }
  }
  formData.append('post_id', id);

  AJAXCALL(formData, function (data) {
    bookingsInputHold = false;
    importBookingData(data);
  });
}

function AJAXCALL(formData, callback) {
  let nonce = document.getElementById('booking').getAttribute('data-nonce');
  formData.append('nonce', nonce);

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

function importBookingData(imp) {
  let events = JSON.parse(imp);
  let user_id = document.getElementById('user_id').getAttribute('data-id');
  let user_role = document.getElementById('user_role').getAttribute('data-role');

  if (events) {
    for (let i = 0; i < events.length; i++) {
      if(document.getElementById("booking-" + events[i].post_id)) {
        if (events[i].max_participants) {
          document.getElementById("booking-max-participants-" + events[i].post_id).innerHTML = events[i].max_participants;
        } else {
          document.getElementById("booking-max-participants-" + events[i].post_id).innerHTML = "30";
        }

        if (events[i].closed) {
          document.getElementById("booking-" + events[i].post_id).disabled = (events[i].closed === "true");
        } else {
          document.getElementById("booking-" + events[i].post_id).disabled = false;
        }

        let participants;
        if (events[i].participants) participants = JSON.parse(events[i].participants);
        if (participants) {
          document.getElementById("booking-participants-" + events[i].post_id).innerHTML = participants.length;
          let ischecked = false;
          for (let j = 0; j < participants.length; j++) {
            if (participants[j].id == user_id) {
              ischecked = true;
            }
          }
          if (document.getElementById("booking-" + events[i].post_id).checked !== ischecked) {
            document.getElementById("booking-" + events[i].post_id).checked = ischecked;
          }
          if (events[i].max_participants <= participants.length) {
            document.getElementById("booking-" + events[i].post_id).disabled = true;
          }
          document.getElementById("booking-instrument-" + events[i].post_id).disabled = ischecked || (events[i].closed === "true" || events[i].max_participants <= participants.length);
          if (user_role !== "lid" && !bookingsInputHold) {
            importAdminBookingData(participants, events[i].post_id);
          }
        } else {
          document.getElementById("booking-participants-" + events[i].post_id).innerHTML = "0";
        }
      }
    }
  }
}

function importAdminBookingData(participants, post_id) {
  let formData = new FormData();
  formData.append('action', 'get_booking_users');
  formData.append('participants', JSON.stringify(participants));

  AJAXCALL(formData, function (data) {
    let partcont = document.getElementById("booking-" + post_id + "-participants");
    let usernames = JSON.parse(data);
    if (usernames && usernames.length > 0) {
      partcont.innerHTML = '<div class="fparticipant">Deelnemers:</div>';
      document.getElementById("booking-participants-" + post_id).innerHTML = participants.length;
      for (let j = 0; j < usernames.length; j++) {
        partcont.innerHTML += '<div id="participant-' + post_id + '-' + participants[j].id + '" class="participant">' + usernames[j].name + '<div onclick="bookingsInput(' + post_id + ',' + participants[j].id + ',\'participant-' + post_id + '-' + participants[j].id + '\')"></div></div>';
      }
      partcont.appendChild(createDownloadButton(usernames, post_id));
    } else {
      partcont.innerHTML = "";
      document.getElementById("booking-participants-" + post_id).innerHTML = "0";
    }
  });
}

function createDownloadButton(usernames, post_id) {
  let post = document.getElementById("post-" + post_id);
  let download = document.createElement('a');
  download.className = "fparticipant csv";
  download.innerHTML = "Download";
  let username_string = "";
  for (let i = 0; i < usernames.length; i++) {
    if (usernames[i].name) username_string += usernames[i].name + ",";
    if (usernames[i].email) username_string += usernames[i].email + ",";
    if (usernames[i].instrument) username_string += usernames[i].instrument + ",";
    username_string += "\n";
  }
  let csvContent = "data:text/csv;charset=utf-8," +
    "Evenement:," + post.getAttribute("data-titel") + "\n" +
    "Datum:," + post.getAttribute("data-datum") + "\n" +
    "Url:," + post.getAttribute("data-url") + "\n" +
    "Naam,Email,Instrument\n" + username_string;
  let encodedUri = encodeURI(csvContent);
  download.setAttribute("href", encodedUri);
  download.setAttribute("download", post.getAttribute("data-titel") + ".csv");

  return download;
}
