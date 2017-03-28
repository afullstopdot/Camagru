<?php

class home extends Controller
{
  /*
  ** renders view for home, should display every image posted to date
  */

  public function index($params = [])
  {
  	$uploads = $this->model('gallery')->getUploads();
    $comments = $this->model('gallery')->getComments();
    $likes = $this->model('gallery')->getLikes();

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
    if ($this->valid() === true) {
      
      /*
      ** To add a like, an id of the image must be passed as a param
      */
      
      $image_id = isset($params[0]) ? trim($params[0]) : NULL;
      if (isset($image_id)) {
        
        /*
        ** Image must be a valid upload in the db
        */

        if ($this->model('gallery')->validImage($image_id) === true)
        {

          /*
          ** Like picture if the user hasnt already only
          */
          
          $result = $this->model('gallery')->addLike(
            $this->user()['user_id'],
            $image_id
          );

          if ($result === true) {
            echo json_encode([
              'like_status' => 200,
              'like_count' => $this->model('gallery')->getLikes($image_id)]
            );

            $image_owner = $this->model('gallery')->imageOwner($image_id);
            if (is_array($image) === true) {
              //send user email
            }
            else {
              //send admin error log
            }
          }
          else {
            echo json_encode(['like_status' => 'Oops, can only like a picture once!']);
          }

        }
        else {
          echo json_encode(['like_error' => 'Invalid image has been specified']);
        }
      }
      else {
        echo json_encode(['like_error' => 'No image has been specified']);
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
    if ($this->valid() === true) {
      
      /*
      ** To add a comment, a id of the image must be passed as a param
      */
      
      $image_id = isset($params[0]) ? trim($params[0]) : NULL;
      $comment = isset($_POST['data']) ? trim($_POST['data']) : NULL;

      if (isset($image_id) && $this->model('gallery')->validImage($image_id) === true) {
        if (count($comment) > 0) {
          $comment = strip_tags($comment);
          if ($this->model('gallery')->addComment($this->user()['username'], $image_id, $comment) === true) {
            echo json_encode([
              'success' => true,
              'status' => 5,
              'comment' => $comment
            ]);

            //send image owner notification
            $image_owner = $this->model('gallery')->imageOwner($image_id);
            if (is_array($image) === true) {
              //send user email
            }
            else {
              //send admin error log
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

}