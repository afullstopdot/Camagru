/*
** Because most of the communication on this website is asynchronous
** the communication will be done using Ajax
*/

var url = 'http:\/\/localhost:80\/Camagru\/public\/';

window.onload = function () {
    //Ajax registration
    var form = document.forms.namedItem('signup');

    /*
    ** The btn the user submits the form will be used to show progress
    */

    var btn = document.getElementById('signup-button');

    if (form) {
        form.addEventListener('submit', function (e) {

          /*
          ** validate_xxx functions check if input is valid and make changes to DOM
          */

          var username_v = validate_username(form.username.value);
          var email_v = validate_email(form.email.value);
          var password_v = validate_password(form.password.value);

          /*
          ** if the form has been validated, we will communicate with the server, the server response will be
          ** if the username and/or email are in use already.
          */

          if (username_v && email_v && password_v) {

            /*
            ** Update the btn text so the user knows whats happening, send the form via post and depending on the response
            ** update the button, the form input to show errors or show success and redirect user to home
            */

            var data = new FormData(form);
            var req = new XMLHttpRequest();

            btn.innerHTML = 'Creating account ...';

            req.open('POST', url + 'auth/signup', true);
            req.onload = function (event) {
                if (req.status == 200) {

                  /*
                  ** successful communication with server, inform user of success or error (if email is taken etc)
                  ** every communication with the server will return a json string
                  */

                  var result = JSON.parse(req.responseText);

                  console.log(result); // if mailer not working i use this to get the link
                  
                  if (typeof result['email'] !== 'undefined' &&
                      typeof result['username'] !== 'undefined')
                  {
                    username_email_taken(result);
                    if (result['email'] === 'OK' && result['username'] === 'OK')
                      btn.innerHTML = 'Account created!';
                    else
                      btn.innerHTML = 'Unsuccessful!';
                  }
                  else {
                    btn.innerHTML = 'Oops error!!';
                  }

                } else {
                  btn.innerHTML = 'Oops error!';
                  console.log('error communicating with camagru server.');
                }
            };
            req.send(data);//send form
          }
          e.preventDefault();//prevent the form from redirecting after response
        }, false);
    }

};

/*
** open and close nav bar for small screens
*/

function open_close()
{
  var x = document.getElementById("myTopnav");

  if (x.className === "topnav")
    x.className += " responsive";
  else
    x.className = "topnav";
}

/*
** validate username, the lenght must be atleast two characters
** only alphanumeric and underscore allowed, else make changes to dom
*/

function validate_username(username)
{
  var errors = [];
  var regexp = /^[a-zA-Z0-9_]+$/;

  // length must be, > 2 & < 11 letters
  if (username.length < 2 || username.length > 16)
    errors.push('Must be between 3 - 15 characters.');
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

/*
** Validate email address, because the html one might not be what we want
** and forms can be edited
*/

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

/*
** Validate the password, it must be atleast 8 characters.
*/

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

/*
** This function will be used to check an array to see if the server response
** regarding signing up was successful or if the email or username has been taken
** already
*/

function username_email_taken(response)
{
  if (response['email'] !== 'OK')
  {
    document.getElementById('b-email').style.color = "red";
    document.getElementById('email').style.border = "2px solid red";
    document.getElementById('email').value = "";
    document.getElementById('err-email').innerHTML = response['email'];
    document.getElementById('err-email').style.display = 'inline';
  }
  else
  {
    document.getElementById('b-email').style.color = "#14D385";
    document.getElementById('email').style.border = "2px solid green";
    document.getElementById('err-email').style.display = 'none';
  }

  if (response['username'] !== 'OK')
  {
    document.getElementById('b-username').style.color = "red";
    document.getElementById('username').style.border = "2px solid red";
    document.getElementById('username').value = "";
    document.getElementById('err-username').innerHTML = response['username'];
    document.getElementById('err-username').style.display = 'inline';
  }
  else
  {
    document.getElementById('b-username').style.color = "#14D385";
    document.getElementById('username').style.border = "2px solid green";
    document.getElementById('err-username').style.display = 'none';
  }
}
