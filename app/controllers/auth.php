<?php

class auth extends Controller
{
  /*
  ** Render view for google+ oauth
  */

  public function googlesignup($params = [])
  {
    $this->view('home/index', $params);
  }

  /*
  ** Render view for 42 oauth
  */

  public function fourtytwosignup($params = [])
  {
    $this->view('home/index', $params);
  }

  /*
  ** Render view for reset
  */

  public function reset($params = [])
  {
    $this->view('auth/reset', $params);
  }

  /*
  ** Render view for login
  */

  public function login($params = [])
  {
    $this->view('auth/signin', $params);
  }

  /*
  ** This function will check the db to see if the email or username is taken
  ** if they are available, the users will be temporarily added as a user until
  ** email validation.
  */

  private function register_user($email, $username, $password)
  {

    /*
    ** function validate_details will return an array which will dictate below
    ** whether to create a new account or not. regardless the response will
    ** be returned so the clien can be updated with how the registration
    ** went
    */

    $response = $this->model('user_signup')->validate_details($email, $username);
   
    if ($response['username'] === 'OK' && $response['email'] === 'OK')
    {
      /*
      ** By this point its confirmed that the user can create an account
      ** since the username and email are available, we will tempoaraily add them
      ** and once they verify via email their account will be active.
      ** create_temp_account will return false if the a pdo exception is thrown
      */

      $verification = hash('whirlpool', mt_rand(50, 100));
      $link = SITE_URL . '/auth/verify/uid=' . base64_encode($username) . '/code=' . $verification;
      
      $subject = 'Camagru Account Verification';
      $h3 = 'please verify your account';
      $button = 'Verify!';
      
      $body = $this->get_html_str($h3, $button, $link, $username);
      
      $result = $this->model('user_signup')
      ->create_temp_account($email, $username, $password, $verification);

      if ($result === true)
      {
        if ($this->send_mail($username, $subject, $body, true))//this is for debugging if a mailer isnt working and u need the veri link
        {
          $response['link'] = $link;
        }
      }
      else
      {
        $response['error'] = 'Failed to create account';
      }
    }
    
    echo json_encode ($response);
  }

  /*
  ** The verification email sent will be dealt with here
  ** This funcion will be given an array of params containing
  ** the uid and verification
  */

  public function verify($params = [])
  {
    /*
    ** clean up the params so uid and ver have hash values only, this is done by
    ** exploding the strs by delim = because the format of the params is uid=???
    ** verification=??? (thats why after the explode i assign the 2nd element to
    ** uid and ver respectively).
    */

    $uid = isset($params[0]) ? explode('=', trim($params[0]))[1] : NULL;
    $ver = isset($params[1]) ? explode('=', trim($params[1]))[1] : NULL;

    if (isset($uid) && isset($ver))
    {
      if (($result = $this->model('user')
      ->check_verify(base64_decode($uid), $ver)) !== false)
      {
        if (($this->model('user')
        ->create_perm_account(
          $result['email'],
          $result['username'],
          $result['password'])) == false)
        {

          /*
          ** For some reason the account wasnt able to be created, redierect the
          ** user home. If any arg is null it will fail, if an pdo excepion is
          ** thrown etc.
          */

          $this->home();
        }
        else
        {

          /*
          ** By this point the user has successdfully completed registration.
          ** render the home page wih info about how registration went. Then unset
          ** it.
          */

          $_SESSION['flash'] = ['message' =>'Yayyy, account verification successful! log in.'];
          $this->view('home/index');
          if (isset($_SESSION['flash']))
            unset($_SESSION['flash']);
        }
      }
    }
  }

  /*
  ** This function will either render a view or it will register a user
  ** from the ajax request
  */

  public function signup($params = [])
  {
    if (filter_has_var(INPUT_POST, 'email')
     && filter_has_var(INPUT_POST, 'username')
     && filter_has_var(INPUT_POST, 'password'))
    {
      $this->register_user(
        trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)),
        trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING)),
        trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING))
      );
    }
    else
      $this->view('auth/signup', $params);
  }

  /*
  ** These are functions that a url should not call.
  **
  ** This function will return an html formatted string for use by send_mail
  ** the html code will be the same format the variables however will change
  ** the header (verificaton or reset) the button (verification or reset) and
  * the url (verification or reset)
  */

  private function get_html_str($h3, $button, $link, $username)
  {
    return '
      <html>
      <head>
      <link href="https://fonts.googleapis.com/css?family=Josefin+Slab" rel="stylesheet">
      <style>
        body
        {
          font-family: "Josefin Slab", serif;;
        }
        .button
            {
                background-color: #4CAF50; /* Green */
                width: 100%;
                margin-left: auto;
                margin-right: auto;
                border: none;
                color: white;
                padding: 16px 32px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin: 4px 2px;
                -webkit-transition-duration: 0.4s; /* Safari */
                transition-duration: 0.4s;
                cursor: pointer;
        }

        .button1
            {
            background-color: white;
            color: black;
            border: 2px solid #4CAF50;
        }

            .button1:hover
            {
                background-color: #4CAF50;
                color: white;
            }
      </style>
      </head>
      <body>

      <h3 style="color: green; text-align: center;">Hello ' . $username . ', ' . $h3 . '</h3>
      <p style="color: #333; font-style: bold; text-align: center;">This e-mail was sent automatically by Camagru, if you did not allow this, ignore this email.</p>
      <a href="' . $link . '"><button class="button button1">' . $button . '</button></a>
      <p style="color: red; font-style: bold; text-align: center;">If this button doesnt work, click this <a href="' . $link .'">link</a> or paste it in your browser</p>

      </body>
      </html>
    ';
  }
}
