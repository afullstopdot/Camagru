<?php

/*
** Clean up notice, for thew oauth create a function to add the users
** right now repition is used for each oauth strategy, fix before submition
*/

class auth extends Controller
{

  /*
  ** This function will authenticate a user , with use of githubs oauth
  ** i do the oauth flow myself, therefore there will be many lines of code,
  ** prepare yourself LOLOLOL
  */

  public function github($params = [])
  {
    $param = isset($params[0]) ? trim($params[0]) : NULL;
    if ($param === 'signup' || $param === 'signin')
    {

      /*
      ** After we make a request to github for an authorization token
      ** we should recieve GET params code which we can than use to make an
      ** access token request
      */

      if (isset($_GET['code']) && isset($_GET['state']))
      {

        /*
        ** Use this authorization code to make an access token request
        ** but first we must compare the state github returned with the redirect
        ** and see if this is the same state we have. this is to protect againts
        ** csrf
        */

        if ($_SESSION['github_state'] !== $_GET['state'])
        {

          /*
          ** CRSF is happening, we must protect againts this now
          */

          $this->flash_message(
            'Action not allowed',
            'danger',
            SITE_URL . '/public/auth/signup'
          );

        }
        else
        {

          /*
          ** All is well no csrf, proceed with access token request here.
          */

          $post = 'client_id=1d711f901dab52485f87&' .
          'client_secret=58716090a044daacc7734761ddf997478978cbe4&' .
          'code=' . $_GET['code'] . '&' .
          'redirect_uri=' . SITE_URL . '/auth/github/signup&' .
          'state=' . $_SESSION['github_state'];

          $curl = curl_init(GITHUB_ACCESS);
          curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => [
              'Content-Type: application/x-www-form-urlencoded',
              'Accept: application/json'
            ],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1
          ));
          $response = curl_exec($curl);
          curl_close($curl);



          /*
          ** The response by explicit request will return a json str with the
          **access token, bearer and the scope
          */

          if (isset($response) && !empty($response))
          {
            $_SESSION['github_access'] = json_decode($response, true);
          }
        }

      }

      /*
      ** If we have an access_token token we can make request on behalf of the
      ** user and ultimately get information we can use to register them, if
      ** the access token is not set we must request the authorization code
      */

      if (isset($_SESSION['github_access']))
      {

        /*
        ** Use this access token to make request to github about the user profile
        ** then create an account for the user.
        */
        $curl = curl_init(GITHUB_PROFILE);
        curl_setopt_array($curl, array(
          CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'Authorization: token ' . $_SESSION['github_access']['access_token'],
            'User-Agent: Camagru'
          ],
          CURLOPT_FOLLOWLOCATION => 1,
          CURLOPT_HEADER => 0,
          CURLOPT_RETURNTRANSFER => 1
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        /*
        ** to get the users email we have to call a different endpoint
        ** sigh ...
        */

        $curl = curl_init(GITHUB_EMAIL);
        curl_setopt_array($curl, array(
          CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'Authorization: token ' . $_SESSION['github_access']['access_token'],
            'User-Agent: Camagru'
          ],
          CURLOPT_FOLLOWLOCATION => 1,
          CURLOPT_HEADER => 0,
          CURLOPT_RETURNTRANSFER => 1
        ));
        $response2 = curl_exec($curl);
        curl_close($curl);
        $response2 = json_decode($response2, true);

        if (isset($response) && isset($response['login']))
        {
          /*
          ** By this point a json string with the user info has been returned
          ** lets use it now.
          */

          $email = isset($response2[0]['email']) ?
            $response2[0]['email'] :
            'N/A';

          $username = isset($response['login']) ?
            $response['login'] :
            substr(time(), 0, 14);

          $picture = isset($response['avatar_url']) ?
            $response['avatar_url'] :
            'N/A';

          /*
          ** If by any chance the username we created specifically for this oauth
          ** user is already taken, we will continue to try and make one.
          */

          while ($this->model('user_signup')->perm_username_exists($username) === true)
          {
            $username = substr(isset($response['login']) ?
              $response['login'] . time() :
              time(), 0, 14);
          }

          /*
          ** check that the username and he email are not already registered
          */

          $validate = $this->model('user_signup')->validate_details(
            $email,
            $username
          );

          /*
          ** By this point, if the user is signing in check if the slack account
          ** is valid and redirect home. Otherwise if its signup create the account.
          */

          if ($param === 'signin')
          {
            if ($validate['email'] !== 'OK' && $validate['email'] !== 'OK')
            {
              $this->flash_message(
                'Yayy, you logged in',
                'success',
                SITE_URL . '/home'
              );
            }
            else
            {
              $this->flash_message(
                'Oops, Not an account. Create one <a href="' . SITE_URL . '/auth/signup">here</a>',
                'warning',
                SITE_URL . '/home'
              );
            }
          }

          /*
          ** if the user is signing up, create account here
          */

          if ($validate['email'] !== 'OK')
          {
            $this->flash_message(
              'Oops, this account is already registered!',
              'warning',
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

            if ($this->model('user_signup')->create_perm_account($email, $username, 'N/A', $picture) === false)
            {

              /*
              ** If for some reason, a permanent account could not be created
              ** update the user with information.
              */

              $this->flash_message(
                'Oops, unsuccessfull registration.',
                'danger',
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
                'success',
                SITE_URL . '/auth/signup'
              );

              /*
              ** If for some reason you want to unset the access token, do it
              ** here.
              */
            }

          }

        }
        else
        {
          $this->flash_message(
            'Oops, github #err3.',
            'danger',
            SITE_URL . '/auth/signup'
          );
        }
      }
      else
      {

        /*
        ** If there is no access token we request one here, upon user verificaton
        ** with github they will redirect the user here, the authorization code
        ** will be caught right at the top, then the access token will be set
        ** and this process will be complete.
        */

        $_SESSION['github_state'] = $this->get_state(time() . rand(0, 121));

        $auth_url = GITHUB_AUTH .
              'client_id=1d711f901dab52485f87&' .
              'redirect_uri=' . SITE_URL . '/auth/github/signup&' .
              'scope=user&' .
              'state=' . $_SESSION['github_state'];

        /*
        ** redirect the user to github authorization page
        */

        $this->redirect($auth_url);
      }

    }
    else
    {

      /*
      ** This method was called without an inteneded action, redirect to
      ** signup and show error.
      */

      $this->flash_message(
        '404 action not found',
        'danger',
        SITE_URL . '/public/auth/signup'
      );

    }
  }

  /*
  ** This function will authenticate a user, with use of slacks oauth
  ** I do the oauth flow myself, therefore this function contains many lines of
  ** code
  */

  public function slack($params = [])
  {
    $param = isset($params[0]) ? trim($params[0]) : NULL;
    if ($param === 'signup' || $param === 'signin')
    {

      if (isset($_GET['error']))
      {
        $this->flash_message(
          'Oops slack error.',
          SITE_URL . '/auth/signup'
        );
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

          if (isset($response) && !empty($response))
          {
            $_SESSION['slack_access'] = json_decode($response, true);
          }

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

          $picture = $profile['image_24'];

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

          /*
          ** By this point, if the user is signing in check if the slack account
          ** is valid and redirect home. Otherwise if its signup create the account.
          */

          if ($param === 'signin')
          {
            if ($validate['email'] !== 'OK' && $validate['email'] !== 'OK')
            {
              $this->flash_message(
                'Yayy, you logged in',
                'success',
                SITE_URL . '/home'
              );
            }
            else
            {
              $this->flash_message(
                'Oops, Not an account. Create one <a href="' . SITE_URL . '/auth/signup">here</a>',
                'warning',
                SITE_URL . '/home'
              );
            }
          }

          /*
          ** check valid details and create acc
          */

          if ($validate['email'] !== 'OK')
          {
            $this->flash_message(
              'Oops, this account is already registered!',
              'warning',
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

            if ($this->model('user_signup')->create_perm_account($email, $username, 'N/A', $picture) === false)
            {

              /*
              ** If for some reason, a permanent account could not be created
              ** update the user with information.
              */

              $this->flash_message(
                'Oops, unsuccessfull registration.',
                'danger',
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
                'success',
                SITE_URL . '/auth/signup'
              );

              /*
              ** If for some reason you want to unset the access token, do it
              ** here.
              */
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

        $_SESSION['slack_state'] = $this->get_state(time() . rand(0, 121));

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
  ** This function will authenticate a user. with the use of googles oauth
  ** i do the oauth web flow myself, tyherefore this function contains many
  ** lines of code.
  */

  public function google($params = [])
  {
    $param = isset($params[0]) ? trim($params[0]) : NULL;
    if ($param === 'signup' || $param === 'signin')
    {

      /*
      ** After the user authenticates with google an authorization code is
      ** passed with the redirect url and we can then use the auth code to
      ** request an access token. if code hasnt been passed, and an access
      ** token is not set then we make an request for an authoprization code
      */

      if (isset($_GET['code']) && isset($_GET['state']))
      {

        /*
        ** use this authorization code to request aqn access token via a post
        ** check for csrf aswell
        */

        if ($_SESSION['google_state'] === $_GET['state'])
        {
          $post = 'grant_type=authorization_code' .
            '&client_id=942119260837-sacf9b6rnm7lfnjdd66a413qbtbo7l5q.apps.' .
              'googleusercontent.com' .
            '&client_secret=rsLWr-UfyzlIHAliK-z_cQeq' .
            '&code=' . $_GET['code'] .
            '&redirect_uri=' . SITE_URL . '/auth/google/signup';

          $curl = curl_init(GOOGLE_ACCESS);
          curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => [
              'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1
          ));
          $response = curl_exec($curl);
          curl_close($curl);

          $response = json_decode($response, true);

          /*
          ** check if an access token was recieved, then set the session.
          */

          if (isset($response) && !empty($response))
          {
            $_SESSION['google_access'] = $response['access_token'];
          }
        }
        else
        {
          $this->flash_message(
            'ILLEGAL Request',
            'warning',
            SITE_URL . '/auth/signup'
          );
        }

      }

      /*
      ** if we recieved an authorization code already, it would have been used
      ** to request an access token, therefore is there is no token set we must
      ** now request an auth code. if an access token is set we make an api
      ** request to google for basic user info
      */

      if (isset($_SESSION['google_access']))
      {

        /*
        ** make api request for basic user info
        */

        $curl = curl_init(GOOGLE_PROFILE);
        curl_setopt_array($curl, array(
          CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $_SESSION['google_access']
          ],
          CURLOPT_FOLLOWLOCATION => 1,
          CURLOPT_HEADER => 0,
          CURLOPT_RETURNTRANSFER => 1
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        /*
        ** I unset the access token so when it expires , our session isnt set
        ** and therefore our requests fail cause an expired token is being used
        */

        unset($_SESSION['google_access']);

        if (isset($response) && !empty($response))
        {

          $username = isset($response['name']['givenName']) ?
            $response['name']['givenName'] :
            'N/A';
          $username .= '-' . isset($response['id']) ?
            $response['id'] :
            'N/A';

          $email = isset($response['emails'][0]['value']) ?
            $response['emails'][0]['value'] :
            'N/A';

          /*
          ** check that the username and he email are not already registered
          */

          $validate = $this->model('user_signup')->validate_details(
            $email,
            $username
          );

          /*
          ** Use the information returned to either create or log a user in
          */

          if ($param === 'signin')
          {
            if ($validate['email'] !== 'OK' && $validate['email'] !== 'OK')
            {
              $this->flash_message(
                'Yayy, you logged in',
                'success',
                SITE_URL . '/home'
              );
            }
            else
            {
              $this->flash_message(
                'Oops, Not an account. Create one <a href="' . SITE_URL . '/auth/signup">here</a>',
                'warning',
                SITE_URL . '/home'
              );
            }
          }

          /*
          ** Create an account using the details. only if one doesnt exist
          ** already
          */

          if ($validate['email'] !== 'OK')
          {
            $this->flash_message(
              'Oops, this account is already registered!',
              'warning',
              SITE_URL . '/auth/signup'
            );
          }
          else
          {

            if ($this->model('user_signup')->create_perm_account($email,
              $username, 'N/A', $picture) === false)
            {

              /*
              ** If for some reason, a permanent account could not be created
              ** update the user with information.
              */

              $this->flash_message(
                'Oops, unsuccessfull registration.',
                'danger',
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
                'Yayy, registered with google.',
                'success',
                SITE_URL . '/auth/signup'
              );
            }
          }

        }
        else
        {
          $this->flash_message(
            'Oops, Error reading your google profile',
            'warning',
            SITE_URL . '/auth/signup'
          );
        }
      }
      else
      {

        /*
        ** request authentification code from gopogle
        */

        $_SESSION['google_state'] = $this->get_state(time() . rand(0, 121));
        $auth_url = GOOGLE_AUTH .
          'response_type=code' .
          '&client_id=942119260837-sacf9b6rnm7lfnjdd66a413qbtbo7l5q.apps.googleusercontent.com' .
          '&redirect_uri=' . SITE_URL . '/auth/google/signup' .
          '&scope=profile email' .
          '&state=' . $_SESSION['google_state'] .
          '&prompt=consent  select_account';

        $this->redirect($auth_url);
      }
    }
    else
    {
      $this->flash_message(
        '404 Action not found',
        'danger',
        SITE_URL . '/auth/signup'
      );
    }
  }

  /*
  ** This function will authenticate a user, with the use of Ecole42s oauth
  ** I do the oauth web flow myself, therefore this function contains many lines
  ** of code.
  **
  */

  public function fourtytwo($params = [])
  {
    $param = isset($params[0]) ? trim($params[0]) : NULL;
    if ($param === 'signup' || $param === 'signin')
    {

      /*
      ** check if auth code has been returned by 42, otherwise request one if
      ** no access token has been set in the session.
      */

      if (isset($_GET['code']) && isset($_GET['state']))
      {

        /*
        ** request for an access token using the auth code, also check for csrf
        */


        if ($_SESSION['42_state'] === $_GET['state'])
        {

          /*
          ** No csrf happening, we can now request an access token from 42
          */

          $post = 'grant_type=authorization_code&' .
          'client_id=cab518e7dc16f4b40d2d49277e6fe1f0d633e5f4b65d3a82f3' .
            '081b36fd7a1e3c&' .
          'client_secret=748e1f88fb4c4ccd0623d21f80f11b6f9d771463847529' .
            'e7718c06c604b26e93&' .
          'code=' . $_GET['code'] . '&' .
          'redirect_uri=' . SITE_URL . '/auth/fourtytwo/signup&' .
          'state=' . $_SESSION['42_state'];
          $curl = curl_init(E42_ACCESS);
          curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => [
              'Content-Type: application/x-www-form-urlencoded',
              'Accept: application/json'
            ],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1
          ));
          $response = curl_exec($curl);
          curl_close($curl);

          $response = json_decode($response, true);

          if (isset($response['error']) && !empty($response['error']))
          {
            $this->flash_message(
              'Ouath failed #3',
              'danger',
              SITE_URL . '/auth/signup'
            );
          }
          else
          {
            $_SESSION['42_access'] = $response['access_token'];
          }

        }
        else
        {
          $this->flash_message(
            'ILLEGAL Request',
            'warning',
            SITE_URL . '/auth/signup'
          );
        }

      }

      /*
      ** if an access token exists, use it to make a api request, if it doesnt
      ** then request5 an auth code to be used above for an access token request
      */

      if (isset($_SESSION['42_access']))
      {

        /*
        ** use access token to make request for basic user info
        */

        $curl = curl_init(E42_PROFILE);
        curl_setopt_array($curl, array(
          CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'Authorization: Bearer ' . $_SESSION['42_access']
          ],
          CURLOPT_FOLLOWLOCATION => 1,
          CURLOPT_HEADER => 0,
          CURLOPT_RETURNTRANSFER => 1
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, true);

        if (isset($response['id']) && !empty($response['id']))
        {

          /*
          ** use the response to create an account or log the user in
          */

          $email = isset($response['email']) ?
              $response['email'] :
              'N/A';
          $username = isset($response['login']) ?
              $response['login'] :
              'N/A';
          $picture = isset($response['image_url']) ?
              $response['image_url'] :
              'N/A';

          /*
          ** Check if these details exist in the db then proceed to create or
          ** log the user in.
          */

          $validate = $this->model('user_signup')->validate_details(
            $email,
            $username
          );

          /*
          ** By this point, if the user is signing in check if the slack account
          ** is valid and redirect home. Otherwise if its signup create the account.
          */
          if ($param === 'signin')
          {
            if ($validate['email'] !== 'OK' && $validate['email'] !== 'OK')
            {
              $this->flash_message(
                'Yayy, you logged in',
                'success',
                SITE_URL . '/home'
              );
            }
            else
            {
              $this->flash_message(
                'Oops, Not an account. Create one <a href="' . SITE_URL . '/auth/signup">here</a>',
                'warning',
                SITE_URL . '/home'
              );
            }
          }
          /*
          ** if the user is signing up, create account here
          */
          if ($validate['email'] !== 'OK')
          {
            $this->flash_message(
              'Oops, this account is already registered!',
              'warning',
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
            if ($this->model('user_signup')->create_perm_account($email, $username, 'N/A', $picture) === false)
            {
              /*
              ** If for some reason, a permanent account could not be created
              ** update the user with information.
              */
              $this->flash_message(
                'Oops, unsuccessfull registration.',
                'danger',
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
                'Yayy, registered with 42.',
                'success',
                SITE_URL . '/auth/signup'
              );
            }
          }
        }

      }
      else
      {

        /*
        ** request authorization code
        */

        $_SESSION['42_state'] = $this->get_state(time() . rand(0, 121));
        $auth_url = E42_AUTH .
          'client_id=cab518e7dc16f4b40d2d49277e6fe1f0d633e5f4b65d3a82f3081b36fd7a1e3c' .
          '&redirect_uri=' . SITE_URL . '/auth/fourtytwo/signup' .
          '&response_type=code&scope=public' .
          '&state=' . $_SESSION['42_state'];

        $this->redirect($auth_url);
      }

    }
    else
    {
      $this->flash_message(
        '404 Action not found',
        'danger',
        SITE_URL . '/auth/signup'
      );
    }
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
            'success',
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
    $this->flash_message('404 page not found', 'danger', SITE_URL . '/home');
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

  private function get_state($randomm_int)
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
