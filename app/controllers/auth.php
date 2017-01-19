<?php

class auth extends Controller
{

  /*
  ** This function will authenticate a user, with use of slacks oauth
  ** I do the oauth flow myself, therefore this function contains many lines of
  ** code
  */

  public function slack($params = [])
  {
    $param = isset($params[0]) ? trim($params[0]) : NULL;
    if ($param === 'signup')
    {

      if (isset($_GET['error']))
      {
        $this->flash_message('Oops slack error.', SITE_URL . '/auth/signup');
      }

      /*
      ** If the user has authenticated with slack and granted the app permissions
      ** than slack will return (via redirect_url) an authorization code in the
      ** form of $_GET['code'] along with state, if state doesnt match the unique
      ** str we passed in the url than a 3rd party created it , csrf is happening
      ** in which case we abort..
      */

      if (isset($_GET['code']) && isset($_GET['state']))
      {
        /*
        ** As stated, if the state passed by slack and the state in the session
        ** dont match CSRF is happening and we must subsequently end our access
        ** token request
        */

        $post = 'client_id=30219036481.128455242609&' .
        'client_secret=3f1dc2dd6db5ba0113dfea80100f6b36&' .
        'code=' . $_GET['code'] . '&' .
        'redirect_uri=' . SITE_URL . '/auth/slack/signup&';

        if ($_GET['state'] !== $_SESSION['slack_state'])
        {

          /*
          ** redirect home.
          */

          $this->redirect();
        }
        else
        {

          /*
          ** No CSRF detected, procede with access token request.
          ** oauth 2.0 requuires that the access token request should be done
          ** via post metthod, the use of curl is therefore required
          */

          $headers = array();
          $headers[] = 'Content-Type: application/x-www-form-urlencoded;';

          $curl = curl_init(SLACK_ACCESS);
          curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1
          ));
          $response = curl_exec($curl);
          curl_close($curl);


          /*
          ** Slacks response will be a json string, containing the access token
          ** and the scope otherwise slack will redirect us using the redirect_url
          ** with a get param error.
          */

          $_SESSION['slack_access'] = json_decode($response, true);

        }

      }

      /*
      ** Once an authorization code is returned by slack, we use that code to
      ** for an access token which we will us o make future requests on the
      ** users behalf, if access token is not set that means either we have not
      ** requested an authorization code which we then do with the else statement
      ** or once we recieved the authorization code and requested an access token
      ** from slack it failed for some reason.
      */

      if (isset($_SESSION['slack_access']))
      {

        /*
        ** Use access token to make a basic user profile request, create the user
        ** an account.
        */

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => SLACK_PROFILE . '?token=' . $_SESSION['slack_access']['access_token']
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);

        /*
        ** slack response will container a ok field, true or false
        */

        if ($response['ok'] === true)
        {
          $profile = $response['profile'];

          /*
          ** Request was successful, pass thhe information to the user_signup
          ** model, to create a permanent account.
          */

          $email = isset($profile['email']) ?
            trim($profile['email']) : 'N/A';

          $first_name = isset($profile['first_name']) ?
            trim($profile['first_name']) : time();

          $username = substr($first_name . '-' . time(), 0, 14);

          /*
          ** If by any chance the username we created specifically for this oauth
          ** user is already taken, we will continue to try and make one.
          */

          while ($this->model('user_signup')->perm_username_exists($username) === true)
          {
            $username = substr($first_name . '-' . time(), 0, 14);
          }

          /*
          ** check that the username and he email are not already registered
          */

          $validate = $this->model('user_signup')->validate_details(
            $email,
            $username
          );

          if ($validate['email'] !== 'OK')
          {
            $this->flash_message(
              'Oops, this account is already registered!',
              SITE_URL . '/auth/signup'
            );
          }
          else
          {

            /*
            ** Since slack response will not contain a unique username
            ** above we generate a username for the user.
            ** Passwords are not required for oauth
            */

            if ($this->model('user_signup')->create_perm_account($email, $username, 'N/A') === false)
            {

              /*
              ** If for some reason, a permanent account could not be created
              ** update the user with information.
              */

              $this->flash_message(
                'Oops, unsuccessfull registration.',
                SITE_URL . '/auth/signup'
              );
            }
            else
            {

              /*
              ** At this point oauth is complete the user has been addeded to
              ** our database.
              */

              $this->flash_message(
                'Yayy, registered with slack.',
                SITE_URL . '/auth/signup'
              );

            }
          }

        }
        else
        {
          $this->flash_message(
            'Oops, slack error 3-#~#a.',
            SITE_URL . '/auth/signup'
          );
        }

      }
      else
      {

        /*
        ** URL will be used to request an authorization code from slack if one has not been
        ** made requested yet.
        **
        ** client_id - issued when you creat your app with slack(required)
        ** scope - permissions to request from user (required)
        ** redirect_uri - URL to redirect back to after auth (optional)
        ** state - unique string to be passed back upon completion (optional)
        */

        $_SESSION['slack_state'] = $this->get_scope(time() . rand(0, 121));

        $auth_url = SLACK_AUTH .
        'client_id=' . '30219036481.128455242609&' .
        'scope=' . 'users.profile:read&' .
        'redirect_uri=' . SITE_URL . '/auth/slack/signup&' .
        'state=' . $_SESSION['slack_state'];

        /*
        ** This is where we redirect the user to slack auth page, and once they
        ** authorize the permissions, slack redirects back to the site with the
        ** GET params code which we then use to request an access token
        */

        $this->redirect($auth_url);
      }

    }

    $this->view('auth/signup');//disable when debugging
  }

  /*
  ** Render view for google+ oauth
  */

  public function google($params = [])
  {
    $this->view('home/index', $params);
  }

  /*
  ** Render view for 42 oauth
  */

  public function fourtytwo($params = [])
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

          $this->redirect();
        }
        else
        {

          /*
          ** By this point the user has successdfully completed registration.
          ** render the home page wih info about how registration went. Then unset
          ** it.
          */

          $this->flash_message(
            'Yayyy, account verification successful! log in.',
            SITE_URL . '/home'
          );
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
  ** This index function is for when users try to access urls that dont
  ** exists. this is temporar until i create my own 404 page
  */


  public function index($params = [])
  {
    $this->flash_message('404 page not found', SITE_URL . '/home');
  }

  /*
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

  /*
  ** This function will generate a random hash to be used as scope for oauth
  ** to avoid forgery attacks by passing in a value that's unique to the user
  ** currently authenticating and checking it when oauth completes
  */

  private function get_scope($randomm_int)
  {
    $arr = str_split(base64_encode($randomm_int));

    foreach ($arr as $key) {
      $final .= md5($key);
    }
    return hash('whirlpool', $final);
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
        if ($this->send_mail($email, $subject, $body, true) == false)//this is for debugging if a mailer isnt working and u need the veri link
        {
          //onl for dev, remove when in production
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
  ** This index function is for when users try to access urls that dont
  ** exists. this is temporar until i create my own 404 page
  */

}
