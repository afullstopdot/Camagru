var url = 'http:\/\/localhost:80\/Camagru\/public\/';

window.onload = function () {
    //Ajax registration
    var form = document.forms.namedItem('signup');
    var btn = document.getElementById('signup-button');
    if (form) {
        form.addEventListener('submit', function (e) {
          if (validate_username(form.username.value) && validate_email(form.email.value) && validate_password(form.password.value)) {
            btn.innerHTML = 'Creating account ...';
            var data = new FormData(form);
            var req = new XMLHttpRequest();

            req.open('POST', url + 'auth/signup', true);
            req.onload = function (event) {
                if (req.status == 200) {
                  var result = JSON.parse(req.responseText);
                  console.log(result);
                  btn.innerHTML = 'Account created!';
                } else {
                  btn.innerHTML = 'Oops error!';
                  console.log('error communicating with camagru server.');
                }
            };
            req.send(data);
          }
          e.preventDefault();
        }, false);
    }

};

// open and close nav bar for small screens
function open_close()
{
  var x = document.getElementById("myTopnav");

  if (x.className === "topnav")
    x.className += " responsive";
  else
    x.className = "topnav";
}
// validate username
function validate_username(username)
{
  var errors = [];
  var regexp = /^[a-zA-Z0-9_]+$/;

  // length must be, 3+ letters
  if (username.length < 2)
    errors.push('Must be at least 2 characters.');
  // check if username only consists of a-zA-Z or underscore
  if (username.search(regexp) == -1)
    errors.push('Only alphanumeric characters and underscore allowed.');
  //change label color to red, show errors
  if (errors.length > 0)
  {
    document.getElementById('b-username').style.color = "red";
    document.getElementById('username').style.border = "2px solid red";
    document.getElementById('username').value = "";
    document.getElementById('err-username').innerHTML = errors.join(', ');
    document.getElementById('err-username').style.display = 'inline';
    return false;
  }
  else
  {
    document.getElementById('b-username').style.color = "#14D385";
    document.getElementById('username').style.border = "2px solid green";
    document.getElementById('err-username').style.display = 'none';
    return true;
  }
}
// validate email
function validate_email(email)
{
  var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

  if (reg.test(email) == false) {
    document.getElementById('b-email').style.color = "red";
    document.getElementById('email').style.border = "2px solid red";
    document.getElementById('email').value = "";
    document.getElementById('err-email').innerHTML = 'Invalid e-mail address';
    document.getElementById('err-email').style.display = 'inline';
    return false;
  }
  else {
    document.getElementById('b-email').style.color = "#14D385";
    document.getElementById('email').style.border = "2px solid green";
    document.getElementById('err-email').style.display = 'none';
    return true;
  }
}
// validate password
function validate_password(password)
{
  if (password.length < 8)
  {
    document.getElementById('b-password').style.color = "red";
    document.getElementById('password').style.border = "2px solid red";
    document.getElementById('password').value = "";
    document.getElementById('err-password').innerHTML = 'Minimum of 8 characters required.';
    document.getElementById('err-password').style.display = 'inline';
    return false;
  }
  else {
    document.getElementById('b-password').style.color = "#14D385";
    document.getElementById('password').style.border = "2px solid green";
    document.getElementById('err-password').style.display = 'none';
    return true;
  }
}
