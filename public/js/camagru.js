/*
** Because most of the communication on this website is asynchronous
** the communication will be done using Ajax
*/

var url = 'http:\/\/localhost:8080\/';

var color_red = '#710909';
var color_blue = '#119261';
var color_grey = '#333';
var color_green = '#33a203';

window.onload = function () {

    /*
    ** Window has loaded,. hide loader
    */

    var active_acc; //rezise comments when new one added

    if (document.getElementById('loading-div') !== null) {
      document.getElementById('loading-div').style.display = 'none';
      document.getElementById('hash').style.height = '0px';
      create_cookie('page', 1, 5);
    }

    //Ajax registration
    var form = document.forms.namedItem('signup');
    var login = document.forms.namedItem('signin');
    var reset = document.forms.namedItem('reset');

    /*
    ** The btn the user submits the form will be used to show progress
    */

    var btn = document.getElementById('signup-button');
    var login_btn = document.getElementById('signin-button');
    var reset_btn = document.getElementById('reset-button');

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
            btn.style.backgroundColor = color_blue;

            req.open('POST', url + 'auth/signup', true);
            req.onload = function (event) {
                if (req.status == 200) {

                  /*
                  ** successful communication with server, inform user of success or error (if email is taken etc)
                  ** every communication with the server will return a json string
                  */

                  var result = JSON.parse(req.responseText);

                  if (typeof result['error'] !== 'undefined')
                  {
                      btn.innerHTML = 'Oops, Invalid details!';
                      btn.style.backgroundColor = color_red;
                  }
                  else {
                    if (typeof result['email'] !== 'undefined' &&
                        typeof result['username'] !== 'undefined')
                    {
                      username_email_taken(result);
                      if (result['email'] === 'OK' && result['username'] === 'OK') {
                        btn.innerHTML = 'Account created!';
                        btn.style.backgroundColor = color_green;
                      }
                      else {
                        btn.innerHTML = 'Unsuccessful!';
                        btn.style.backgroundColor = color_red;
                      }
                    }
                    else {
                      btn.innerHTML = 'Oops error!!';
                      btn.style.backgroundColor = color_red;
                    }
                  }
                }
                else {
                  btn.innerHTML = 'Oops error!';
                  btn.style.backgroundColor = color_red;
                }
            };
            req.send(data);//send form
          }
          else {
            btn.style.backgroundColor = color_red;
          }
          e.preventDefault();//prevent the form from redirecting after response
        }, false);
    }

    //log in registration
    if (login)
    {
      login.addEventListener('submit', function (e) {

        /*
        ** validate_xxx functions check if input is valid and make changes to DOM
        */

        var email_v = validate_email(login.email.value);

        /*
        ** if the form has been validated, we will communicate with the server, the server response will be
        ** if the username and/or email are in use already.
        */

        if (email_v) {

          /*
          ** Update the btn text so the user knows whats happening, send the form via post and depending on the response
          ** update the button, the form input to show errors or show success and redirect user to home
          */

          var data = new FormData(login);
          var req = new XMLHttpRequest();

          login_btn.innerHTML = 'Authenticating ...';
          login_btn.style.backgroundColor = color_blue;

          req.open('POST', url + 'auth/login', true);
          req.onload = function (event) {
              if (req.status == 200)
              {

                var result = JSON.parse(req.responseText);

                /*
                ** The response from camagru will be either error with a message
                ** or success with a message
                */

                if (typeof result['error'] !== 'undefined')
                {
                  login_btn.innerHTML = 'Invalid email/password';
                  login_btn.style.backgroundColor = color_red;
                }
                else if (typeof result['success'] !== 'undefined')
                {
                  if (get_cookie('username') == '') {
                    create_cookie('username', result['username'], 5);
                  }
                  login_btn.style.backgroundColor = color_green;
                  login_btn.innerHTML = 'Success, redirecting ...!';
                  window.location = url + 'home';
                }

              }
              else
              {

                /*
                ** The response from camagru was not okay
                */

                login_btn.innerHTML = 'Oops camagru error!';
                login_btn.style.backgroundColor = color_red;
              }
          };

          window.setTimeout(function () {
            login_btn.innerHTML = 'Log me in !';
            login_btn.style.backgroundColor = color_grey;
          }, 5000);

          req.send(data);//send form
        }
        e.preventDefault();//prevent the form from redirecting after response
      }, false);
    }

    //reset account
    if (reset)
    {
      reset.addEventListener('submit', function (e) {

        /*
        ** Reset form will be used to send a verification email
        ** and also to request the new passwords, as such different
        ** actions are required for each
        */

        var index = window.location.href.indexOf("uid");

        if (index != -1) {
          var uid = window.location.href.slice(index);

          index = uid.indexOf("/token");
          uid = uid.slice(0, index);
        }

        if (reset.password1 !== undefined && reset.password2 !== undefined) {
            
          /*
          ** Verification email has a user id, here we get it and pass it to 
          ** the same reset controller to update the password
          */

          var index = window.location.href.indexOf("uid");

          if (index != -1) {
            var uid = window.location.href.slice(index);

            index = uid.indexOf("/token");
            uid = uid.slice(0, index);

            var data = new FormData(reset);
            var req = new XMLHttpRequest();

            reset_btn.innerHTML = 'Changing Password ...';
            reset_btn.style.backgroundColor = color_blue;

            req.open('POST', url + 'auth/reset/' + uid, true);
            req.onload = function (event) {
                if (req.status == 200)
                {
                  var result = JSON.parse(req.responseText);

                  /*
                  ** The response from camagru will be either error with a message
                  ** or success with a message
                  */

                  if (typeof result['error'] !== 'undefined')
                  {
                    reset_btn.innerHTML = result['error'];
                    reset_btn.style.backgroundColor = color_red;
                  }
                  else if (typeof result['success'] !== 'undefined')
                  {
                    reset_btn.innerHTML = result['success'];
                    reset_btn.style.backgroundColor = color_green;
                    window.location = url + 'auth/login'
                  }
                }
                else
                {
                  reset_btn.innerHTML = 'Oops camagru error!';
                  reset_btn.style.backgroundColor = color_red;
                }
            };
            window.setTimeout(function () {
              reset_btn.innerHTML = 'Reset Account !';
              reset_btn.style.backgroundColor = color_grey;
            }, 5000);
            req.send(data);
          }
        }

        if (reset.email !== undefined) {
          var email_v = validate_email(reset.email.value);

          if (email_v === true) {

            var data = new FormData(reset);
            var req = new XMLHttpRequest();

            reset_btn.innerHTML = 'Authenticating ...';
            reset_btn.style.backgroundColor = color_blue;

            req.open('POST', url + 'auth/reset', true);
            req.onload = function (event) {
                if (req.status == 200)
                {
                  var result = JSON.parse(req.responseText);

                  /*
                  ** The response from camagru will be either error with a message
                  ** or success with a message
                  */

                  if (typeof result['error'] !== 'undefined')
                  {
                    reset_btn.style.backgroundColor = color_red;
                    reset_btn.innerHTML = result['error'];
                  }
                  else if (typeof result['success'] !== 'undefined')
                  {
                    reset_btn.style.backgroundColor = color_green;
                    reset_btn.innerHTML = result['success'];
                  }
                }
                else
                {
                  reset_btn.style.backgroundColor = color_red;
                  reset_btn.innerHTML = 'Oops camagru error!';
                }
            };
            window.setTimeout(function () {
              reset_btn.style.backgroundColor = color_grey;
            }, 5000);
            req.send(data);//send form
          }
        }
        e.preventDefault();//prevent the form from redirecting after response
      }, false);
    }

    // comment
    var buttons = document.getElementsByName('comment-submit');
    var count;

    if (buttons) {
      for (count = 0; count < buttons.length; count++) {
        buttons[count].onclick = function (e) {
            var id = this.id;
            var comment = document.forms[id + '-form'];
            var button = this;

            if (comment) {
              if (comment.data.value != '') {
                var data = new FormData(comment);
                var req = new XMLHttpRequest();

                button.innerHTML = 'Posting ...';
                button.style.backgroundColor = color_blue;
                req.open('POST', comment.action, true);
                req.onload = function (event) {
                  if (req.status == 200)
                  {
                    var result = JSON.parse(req.responseText);

                    if (result.success === true) {
                      button.style.backgroundColor = color_green;
                      button.innerHTML = 'Comment posted!';
                      add_comment(id + '-comment-panel', get_cookie('username'), result.comment);
                      comment.data.value = '';
                    }
                    else {
                      button.style.backgroundColor = color_red;
                      if (result.status === 1) {
                        button.innerHTML = 'Log in to comment';
                      }
                      if (result.status === 2) {
                        button.innerHTML = 'Image not specified/invalid';
                      }
                    }
                  }
                  else
                  {
                    button.style.backgroundColor = color_red;
                    button.innerHTML = 'Oops Camagru error!';
                  }
                };
                req.send(data);
              }
              else {
                button.innerHTML = 'Write something!';
              }
              window.setTimeout(function () {
                button.innerHTML = 'Comment';
                button.style.backgroundColor = color_grey;
              }, 5000);
            }
            //end
            e.preventDefault();
        }
      }
    }

    //accordion
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
      acc[i].onclick = function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight){
          panel.style.maxHeight = null;
        } else {
          panel.style.maxHeight = panel.scrollHeight + "px";
        } 
      }
    }

    /*
    ** Open and close the alert divs
    */

    var close = document.getElementsByClassName("alertclosebtn");
    var i;

    for (i = 0; i < close.length; i++) {
      close[i].onclick = function(){
        var div = this.parentElement;
        div.style.opacity = "0";
        setTimeout(function(){ div.style.display = "none"; }, 600);
      }
    }

    var modal = document.getElementById('dialog-modal');
    var modal_close = document.getElementById('close-modal');

    if (modal && modal_close) {
      modal_close.onclick = function () {
        modal.style.display = 'none';
      }

      window.onclick = function (event) {
        modal.style.display = 'none';
      }
    }

    /*
    ** Create pagination
    */

    var load_more = document.getElementsByClassName('load-more')[0];

    if (load_more) {
      load_more.onclick = function () {
        var req = new XMLHttpRequest();

        this.innerHTML = 'Loading...';
        req.open('POST', url + 'home/more/' + get_cookie('page'), true);
        req.onload = function (event) {
          if (req.status == 200) {

            var resp = JSON.parse(req.responseText);

            /*
            ** The response from camagru will be either error with a message
            ** or success with a message
            */

            if (resp.status == 200) {
              load_more.innerHTML = 'Load More';

              /*
              ** Update cookie page value
              */
              create_cookie('page', resp.page, 5);
              
              /*
              ** Append uploads to home gallery
              */

              document.getElementById('loading-div').style.display = 'inline';
              document.getElementById('hash').style.height = '100%';

              //append here

              for (var i = 0; i < resp.data.length; i++) {
                add_upload(resp.data[i], resp.comment, resp.likes);
              }

              /*
              ** After appending uploads to body remove loader
              */

              document.getElementById('loading-div').style.display = 'none';
              document.getElementById('hash').style.height = '0px';

            }
            if (resp.status == 202) {
              load_more.innerHTML = 'No more images to load';
            }

          }
          else {
            console.log('fail');
          }
        };
        req.send();
      }
    }
};

/*
** Like picture
*/

function like(id) {
  var XHR = new XMLHttpRequest();

  if (id) {
    var req = new XMLHttpRequest();

    req.open('POST', url + 'home/add_like/' + id, true);
    req.onload = function (event) {
      if (req.status == 200)
      {
        var result = JSON.parse(req.responseText); 

        /*
        ** If like_status that means a valid request has been answered
        */

        if (result['like_status'] !== undefined) {
          if (result['like_status'] === 200) {
            document.getElementById(id + ' like-count').innerHTML = result['like_count'];
            document.getElementById(id + ' like-count').style.color = 'red';
          }
        }

        /*
        ** If like_error that means a invalid request has been answered
        */

        if (result['like_error'] !== undefined) {
          document.getElementById('dialog-modal').style.display = 'block';
          document.getElementById('header-modal').innerHTML = 'Oops, slight problemo';
          document.getElementById('content-modal').innerHTML = '<span id="close-modal" class="modal-close">&times;</span>' + result['like_error'];
        }
      }
    };
    req.send();
  }
}

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

/*
** Cookies. store user info in browser
*/

function create_cookie(name, value, exp)
{
  var date = new Date();
  var expires;

  date.setTime(date.getTime() + (exp * 24 * 60 * 60 * 1000));
  expires = "expires=" + date.toUTCString();
  if (date && expires) {
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
  }
}

/*
** Get the value of a cookie
*/

function get_cookie(cname)
{
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var arr = decodedCookie.split(';');
  var cookie;

  for (var count = 0; count < arr.length; count++) {
    cookie = arr[count];
    while (cookie.charAt(0) == ' ') {
      cookie = cookie.substring(1);
    }

    if (cookie.indexOf(name) == 0) {
      return cookie.substring(name.length, cookie.length);
    }
  }
  return "";
}

/*
** Delete cookie
*/

function delete_cookie(name)
{
  if (name) {
    document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
  }
}

/*
** Create new comment element
*/

function add_comment(panel, username, text)
{
  if (panel && username && text) {
    var parent = document.getElementById(panel);
    var comment = document.createElement('p');
    var username_e = document.createElement('strong');

    username_e.style.color = "white";

    comment.appendChild(document.createTextNode(' ' + text));
    parent.insertBefore(comment, parent.childNodes[0]);

    username_e.appendChild(document.createTextNode(username));
    comment.insertBefore(username_e, comment.childNodes[0]);
  }
}

/*
** Create new image upload, this is reallly long bleh
*/

function add_upload(data, comment, img_likes)
{
  if (data && comment && img_likes) {
    var heart_tag;
    const parent = document.getElementsByClassName('container-fluid')[0];

    /*
    ** Create div image card, img , comment etc will be children
    ** to this div
    */

    const upload = document.createElement("div");

    if (upload) {
      upload.className = "image-card";
      upload.setAttribute("id", data.image_id);

      /*
      ** Add img tag to parent image card
      */

      const img = document.createElement('img');

      if (img) {
        img.setAttribute('id', data.image_id);

        /*
        ** Add dblclick event for like feature
        */

        img.ondblclick = function () {
          like(img.id);
        };

        /*
        ** set src path for image
        */

        img.src = data.img_path;

        /*
        ** Set img width
        */

        img.style.width = '100%';

        /*
        ** Append img to parent image-card
        */

        upload.appendChild(img);
      }

      /*
      ** Add accordion button for panel etc
      */

      const accordion = document.createElement('button');

      if (accordion) {
        accordion.className = "accordion";

        /*
        ** accordion is the parent, its children will consist
        ** of a div profile-picture aswell as div heart
        */

        const profile = document.createElement("div");

        if (profile) {

          profile.className = "profile-picture";

          /*
          ** img tag has user profile picture
          */

          const profile_img = document.createElement('img');

          if (profile_img) {
            profile_img.src = data.picture;
            profile_img.style.height = '50';
            profile_img.style.width = '50';

            /*
            ** Append profile img to profile
            */

            profile.appendChild(profile_img);
          }

          /*
          ** Add username to profile div
          */

          profile.append(document.createTextNode(data.username));

          /*
          ** Append profile to accordion
          */

          accordion.appendChild(profile);
        }

        /*
        ** Create heart div (like + comment coount)
        */

        const heart = document.createElement("div");

        if (heart) {
          heart.className = "heart";

          /*
          ** create p element with info about # of likes + comments
          */

          const p = document.createElement("p");
          heart_tag = p;

          if (p) {
            //arbitary values for likes and comments
            p.innerHTML = 0 + " likes " + 0 + " comments";
            heart.appendChild(p);
          }
          accordion.appendChild(heart);
        }

        /*
        ** Append accordion to image card
        */

        upload.appendChild(accordion);
      }

      /*
      ** Add panel with form for commenting and liking
      */

      const panel = document.createElement('div');

      if (panel) {
        panel.className = "panel";

        /*
        ** add like text to panel
        */

        const panel_b =  document.createElement('b');

        if (panel_b) {
          const panel_span = document.createElement('span');

          if (panel_span) {
            panel_span.className = "like-button";
            panel_span.setAttribute('id', data.image_id);
            panel_span.onclick = function () {
              like(data.image_id);
            };
            panel_span.innerHTML = '&hearts; Like';
            panel_b.appendChild(panel_span);
          }
          panel.appendChild(panel_b);
        }

        /*
        ** When the accordion is clicked the panel will show
        */

        accordion.onclick = function() {
          this.classList.toggle("active");

          if (panel.style.maxHeight){
            panel.style.maxHeight = null;
          } else {
            panel.style.maxHeight = panel.scrollHeight + "px";
          } 
        }

        /*
        ** Add form to panel with text area
        */

        const form = document.createElement('form');

        if (form) {

          /*
          ** Form attributes
          */

          form.setAttribute('id', 'comment-form');
          form.setAttribute('role', 'form');
          form.action = 'home/comment/' + data.image_id;
          form.name = data.image_id + '-form';
          form.method = 'post';

          /*
          ** Add textarea to form
          */

          const textarea = document.createElement('textarea');

          if (textarea) {
            textarea.name = 'data';
            textarea.className = 'comment-area';
            textarea.placeholder = 'LOL great pic!';
            textarea.rows = '3';

            /*
            ** Append to form
            */

            form.appendChild(textarea);
          }

          /*
          ** Create form submit button
          */

          const button = document.createElement('button');

          if (button) {
            button.type = 'submit';
            button.setAttribute('id', data.image_id);
            button.name = 'comment-submit';
            button.className = 'comment-submit-button';
            button.innerHTML = 'Comment';

            /*
            ** Add on click event for button
            */

            button.onclick = function (e) {
              var id = this.id;
              var comment = document.forms[id + '-form'];
              var button = this;

              if (comment) {
                if (comment.data.value != '') {
                  var data = new FormData(comment);
                  var req = new XMLHttpRequest();

                  button.innerHTML = 'Posting ...';
                  button.style.backgroundColor = color_blue;
                  req.open('POST', comment.action, true);
                  req.onload = function (event) {
                    if (req.status == 200)
                    {
                      var result = JSON.parse(req.responseText);

                      if (result.success === true) {
                        button.style.backgroundColor = color_green;
                        button.innerHTML = 'Comment posted!';
                        add_comment(id + '-comment-panel', get_cookie('username'), result.comment);
                        comment.data.value = '';
                      }
                      else {
                        button.style.backgroundColor = color_red;
                        if (result.status === 1) {
                          button.innerHTML = 'Log in to comment';
                        }
                        if (result.status === 2) {
                          button.innerHTML = 'Image not specified/invalid';
                        }
                      }
                    }
                    else
                    {
                      button.style.backgroundColor = color_red;
                      button.innerHTML = 'Oops Camagru error!';
                    }
                  };
                  req.send(data);
                }
                else {
                  button.innerHTML = 'Write something!';
                }
                window.setTimeout(function () {
                  button.innerHTML = 'Comment';
                  button.style.backgroundColor = color_grey;
                }, 5000);
              }
              //end
              e.preventDefault();
            }

            /*
            ** Append to form
            */

            form.appendChild(button);
          }
          panel.appendChild(form);

          /*
          ** Create comment panel with list of previous comments of a picture
          */

          const comment_panel = document.createElement('div');

          if (comment_panel) {
            var comment_count = 0;
            var like_count = 0;

            comment_panel.setAttribute('id', data.image_id + '-comment-panel');

            /*
            ** Update like count
            */

            for (var a = 0; a < img_likes.length; a++) {
              if (img_likes[a].image_id === data.image_id) {
                  like_count++;
                  heart_tag.innerHTML = like_count + ' likes ' + comment_count + ' comments';
              }
            }

            for (var i = 0; i < comment.length; i++) {
              if (comment[i].image_id === data.image_id) {
                var p_tag = document.createElement('p');
                var strong_tag = document.createElement('strong');

                /*
                ** Update comment count
                */

                comment_count++;
                heart_tag.innerHTML = like_count + ' likes ' + comment_count + ' comments';
                if (p_tag && strong_tag) {
                  strong_tag.innerHTML = comment[i].username + ' ';
                  strong_tag.style.color = 'white';
                  p_tag.appendChild(strong_tag);
                  p_tag.appendChild(document.createTextNode(comment[i].comment));
                  comment_panel.appendChild(p_tag);
                }
              }
            }
          }

          panel.appendChild(comment_panel);
        }
        upload.appendChild(panel);
      }



      /*
      ** Append parent to body
      */

      if (parent) {
        parent.appendChild(upload);
      }
      else {
        document.body.appendChild(upload);
      }
    }
  }
}