//charactter only validation
function c_only() {
  if (f1.u1.value == "") {
    document.getElementById("id1").innerText = "*null";
    f1.u1.focus();
    return false;
  } else {
    var a = /[^a-zA-Z ]/g;
    {
      if (f1.u1.value.match(a)) {
        document.getElementById("id1").innerText =
          "*Please enter the CHARACTER only";
        f1.u1.focus();
        return false;
      } else {
        document.getElementById("id1").innerText = "";
        return true;
      }
    }
  }
}

//digit only validation
function d_only() {
  if (f1.m1.value == "") {
    document.getElementById("id2").innerText = "*null";
    f1.m1.focus();
    return false;
  } else {
    var letters = /[^0-9]/g;
    {
      if (f1.m1.value.match(letters)) {
        document.getElementById("id2").innerText =
          "*Please enter the DIGIT and must be 10 DIGIT only";
        f1.m1.focus();
        return false;
      } else if (f1.m1.value.length != 10) {
        document.getElementById("id2").innerText =
          "*Please enter 10 DIGIT only";
        f1.m1.focus();
        return false;
      } else {
        document.getElementById("id2").innerText = "";

        return true;
      }
    }
  }
}
//email validation

function ValidateEmail() {
  var p = f1.e1.value;
  if (p == "") {
    document.getElementById("id3").innerText = "*null";
    f1.e1.focus();
    return false;
  } else {
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    //\w =matches any alphanumeric charater and underscore
    //+ =one or more matches
    //? =zero or one matches ([\.-]?\w+)
    //* =zero or more matches
    //{2,3}  =n to m matchesrage type
    {
      if (p.match(mailformat)) {
        document.getElementById("id3").innerText = "";
        return true;
      } else {
        document.getElementById("id3").innerText =
          "*Please enter Valid EMAIL ID only";
        f1.e1.focus();
        return false;
      }
    }
  }
}

//password validation
var myInput = document.getElementById("psw");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var length = document.getElementById("length");
var special_charater = document.getElementById("special_charater");

// When the user clicks on the password field, show the message box
myInput.onfocus = function () {
  document.getElementById("message").style.display = "block";
};

// When the user clicks outside of the password field, hide the message box
myInput.onblur = function () {
  document.getElementById("message").style.display = "none";
};
myInput.onblur = function () {
  if (f1.psw.value == "") {
    document.getElementById("id4").innerText = "*null";
    f1.psw.focus();
    return false;
  } else {
    document.getElementById("id4").innerText = "";

    return true;
  }
};
// When the user starts to type something inside the password field
myInput.onkeyup = function () {
  // alert(f1.psw.value);
  // Validate lowercase letters

  var lowerCaseLetters = /[a-z]/g;
  if (myInput.value.match(lowerCaseLetters)) {
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");
  }

  // Validate capital letters
  var upperCaseLetters = /[A-Z]/g;
  if (myInput.value.match(upperCaseLetters)) {
    capital.classList.remove("invalid");
    capital.classList.add("valid");
  } else {
    capital.classList.remove("valid");
    capital.classList.add("invalid");
  }

  // Validate numbers
  var numbers = /[0-9]/g;
  if (myInput.value.match(numbers)) {
    number.classList.remove("invalid");
    number.classList.add("valid");
  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");
  }
  // validate special_charater

  var special_charaters = /[!#$%&?@"]/;
  if (myInput.value.match(special_charaters)) {
    special_charater.classList.remove("invalid");
    special_charater.classList.add("valid");
  } else {
    special_charater.classList.remove("valid");
    special_charater.classList.add("invalid");
  }

  // Validate length
  if (myInput.value.length >= 8) {
    length.classList.remove("invalid");
    length.classList.add("valid");
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
  }
};

//compare password
function confirmPassword() {
  if (f1.cpsw.value == "") {
    document.getElementById("id5").innerText = "*null";
    f1.cpsw.focus();
    return false;
  } else {
    if (f1.cpsw.value != f1.psw.value) {
      document.getElementById("id5").innerText = "*Both Password Not Match";
      f1.cpsw.focus();
      return false;
    } else {
      document.getElementById("id5").innerText = "";
      return true;
    }
  }
}

//captcha code
let captcha;
function generate() {
  // Clear old input
  document.getElementById("submit").value = "";

  // Access the element to store
  // the generated captcha
  captcha = document.getElementById("image");
  let uniquechar = "";

  const randomchar =
    "ABCDEFGHJKLMNOPQRSTUVWXYZabcdefghjkmnopqrstuvwxyz0123456789";

  // Generate captcha for length of
  // 5 with random character
  for (let i = 0; i < 5; i++) {
    uniquechar += randomchar.charAt(Math.random() * randomchar.length);
  }

  // Store generated input
  captcha.innerHTML = uniquechar;
}


function printmsg() {
    const usr_input = document
        .getElementById("submit").value;

    // Check whether the input is equal
    // to generated captcha or not
    if (usr_input == captcha.innerHTML) {
        let s = document.getElementById("captcha-message")
            .innerHTML = "Matched";
        // generate();
    }
    else {
        let s = document.getElementById("captcha-message")
            .innerHTML = "not Matched";
        generate();
    }
}


