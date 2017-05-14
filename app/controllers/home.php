<?php

class home extends Controller
{
  /*
  ** renders view for home, should display every image posted to date
  */

  public function index($params = [])
  {

    /*
    ** Get upload data
    */

  	$uploads = $this->model('gallery')->getUploads();
    $comments = $this->model('gallery')->getComments();
    $likes = $this->model('gallery')->getLikes();

    /*
    ** Run through comments and use htmlentities
    ** to present XSS attacks if the comments
    ** have tags or usernames
    */

    $count = 0;
    foreach ($comments as $comment) {
      foreach ($comment as $key => $value) {
        if ($key === 'comment' || $key === 'username') {
          $comments[$count][$key] = htmlentities($value);
          $comments[$count][$key] = strip_tags($comments[$count][$key], ENT_QUOTES);
        }
      }
      $count++;
    }

    $data = [
      'uploads' => $uploads,
      'comments' => $comments,
      'likes' => $likes
    ];
    $this->view('home/index', $data);
  }

  /*
  ** Like feature, notify user image was liked
  */

  public function add_like($params = [])
  {
    if ($this->valid()) {
      
      /*
      ** To add a like, an id of the image must be passed as a param
      */
      
      $image_id = isset($params[0]) ? trim($params[0]) : NULL;
      if (isset($image_id)) {
        
        /*
        ** Image must be a valid upload in the db
        */

        if ($this->model('gallery')->validImage($image_id))
        {

          /*
          ** Like picture if the user hasnt already only
          */
          
          $result = $this->model('gallery')->addLike(
            $this->user()->user_id,
            $image_id
          );

          if ($result) {
            echo json_encode([
              'like_status' => 200,
              'like_count' => $this->model('gallery')->getLikes($image_id)]
            );
          }
          else {
            echo json_encode([
              'like_status' => 'Oops, can only like a picture once!'
            ]);
          }

        }
        else {
          echo json_encode([
            'like_error' => 'Invalid image has been specified'
          ]);
        }
      }
      else {
        echo json_encode([
          'like_error' => 'No image has been specified'
        ]);
      }
    }
    else {

      /*
      ** User must be logged on
      */

      echo json_encode(['like_error' => 'You must be logged on to like a picture']);
    }
  }

  /*
  ** Comment feature, notify user comment was recieved
  */

  public function comment($params = [])
  {
    if ($this->valid()) {
      
      /*
      ** To add a comment, a id of the image must be passed as a param
      */
      
      $image_id = isset($params[0]) ? trim($params[0]) : NULL;
      $comment = isset($_POST['data']) ? trim($_POST['data']) : NULL;

      if (isset($image_id) && $this->model('gallery')->validImage($image_id)) {
        if (count($comment) > 0) {
          $comment = strip_tags($comment);
          if ($this->model('gallery')->addComment($this->user()->username, $image_id, $comment)) {
            echo json_encode([
              'success' => true,
              'status' => 5,
              'comment' => $comment
            ]);

            /*
            ** send image owner notification, first
            ** find owner of image
            */

            $image_owner = $this->model('gallery')->imageOwner($image_id);
            if (is_array($image_owner)) {
              /*
              ** Get base64_encode image for mailer
              */

              $image = ROOT_DIR . $image_owner['img_path'];

              /*
              ** Check if file exists before trying emailing link to picture
              */

              if (file_exists($image)) {
                if (is_string($image_owner['img_path']) && !empty($image_owner['img_path'])) {
                  $params = [
                    'to' => $image_owner['email'],
                    'name' => $image_owner['name'],
                    'commenter' => $this->user()->username,
                    'comment' => $comment,
                    'image' => SITE_HOST . $image_owner['img_path']
                  ];
                  $this->helper('Mailer')->comment_mail($params);
                }
              }
            }
          }
          else {
            echo json_encode([
              'success' => false,
              'status' => 4
            ]);
          }
        }
        else {
          echo json_encode([
            'success' => false, 
            'status' => 3
          ]);
        }
      }
      else {
        echo json_encode([
          'success' => false, 
          'status' => 2
        ]);
      }
    }
    else {
      echo json_encode([
        'success' => false, 
        'status' => 1
      ]);
    }
  }

  /*
  ** handle ajax request for more uploads
  */

  public function more($params = [])
  {

    /*
    ** gallery offset passed as param from cookie
    */

    $param = isset($params[0]) ? trim($params[0]) : NULL;

    if (isset($param)) {
      $uploads  = $this->model('gallery')->getUploads((int) $param * 10);
      $comments = $this->model('gallery')->getComments();
      $likes    = $this->model('gallery')->getLikes();

      /*
      ** Run through comments and use htmlentities/strip_tags
      ** to present XSS attacks if the comments
      ** have tags or usernames
      */

      $count = 0;
      foreach ($comments as $comment) {
        foreach ($comment as $key => $value) {
          if ($key === 'comment' || $key === 'username') {
            $comments[$count][$key] = htmlentities($value);
            $comments[$count][$key] = strip_tags($comments[$count][$key], ENT_QUOTES);
          }
        }
        $count++;
      }

      if (is_array($uploads) && !empty($uploads)) {
        echo json_encode([
          'status' => 200,
          'data' => $uploads,
          'comment' => $comments,
          'likes' => $likes,
          'page' => (int) $param + 1
        ]);
      }
      else {
        echo json_encode([
          'status' => 202,
          'msg' => 'Nothing to return'
        ]);
      }
    }
    else {
      echo json_encode([
        'status' => 203,
        'msg' => 'cookie not set'
      ]);
    }
  }
}