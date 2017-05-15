<?php

class profile extends Controller
{
	private $asset =  [
	  's-1.png',
	  's-2.png',
	  's-3.png',
	  's-4.png',
	  's-5.png',
	  's-6.png'
	];

	/*
	** Render edit home, where you can snap pics, upload pics, delete pics
	*/

	public function home()
	{

		/*
		** Only logged on users allowed here
		*/

		if ($this->valid())
		{

			$this->view('profile/home', [
				'username' => $this->user()->username
			]);
		}
		else
		{
			$this->flash_message(
				'You must be logged in to access this section',
				'danger',
				SITE_URL . '/auth/login'
			);
		}
	}

	/*
	** live preview image uploaded
	** There will be a live preview of the image, so when a image is uploaded
	** and akax request uploads the form, instead of savig the image on the
	** server, using ImageUpload the response if the image is valid will be
	** base64_encoded image
	*/

	public function validate()
	{
		/*
		** Check if user is allowed to make request here
		*/

		if ($this->valid()) {

			/*
			** Check if uploaded form has $_FILES[image]
			*/

			if (isset($_FILES['image'])) 
			{
				/*
				** ImageUpload handles file uploads
				** functions will help check if the file is allowed
				** according to type , size etc
				*/

				$upload = $this->helper('ImageUpload');
				if (is_object($upload)) {
					$upload->set_file($_FILES['image']);
					/*
					** Check if extension is valid
					*/
					if ($upload->valid_ext() && $upload->valid_size()) {
						/*
						** Return base64_encoded image for live preview
						*/
						echo json_encode([
							'status' => 200,
							'image' => $upload->content()
						]);
					}
					else {
						echo json_encode([
							'status' => 201,
							'message' => 'Image extension/size not allowed'
						]);
					}
				}
				else {
					echo json_encode([
						'status' => 500,
						'message' => 'Internal error'
					]);
				}
			}
		}
		else {
			echo json_encode([
				'status' => 401,
				'message' => 'unauthorized access'
			]);
		}
	}

	/*
	** Superimpose two images and save to db
	*/

	public function save()
	{
		/*
		** Check if request by user is allowed
		*/

		if ($this->valid()) {
			/*
			** When ajax called is made, selection is appended to form
			** selection is the asset user wants to superimpose
			*/
			if (isset($_POST['selection']) && isset($_FILES['image'])) {
				/*
				** Create new instance of ImageUpload
				** ImageUpload will get the contents of the file uploaded
				** That way I dont have to save the image to use it
				** Therefore only image saved will be the final product
				*/

				$upload = $this->helper('ImageUpload');
				if (is_object($upload)) {
					$upload->set_file($_FILES['image']);

					/*
					** Check if image type is supported png and jpeg
					** Also that a valid asset has been requested (selection key in $_POST)
					*/

					if ($upload->valid_ext() && file_exists(ROOT_DIR . ASSET_PATH . $this->asset[$_POST['selection']])) {

						/*
						** ImageSuperimpose will merge two images
						*/

						$image = $this->helper('ImageSuperimpose');

						/*
						** ImageSuperimpose function merge creates a new file
						** of the two images passed to constructor
						** arguments passed is the path where the image will be saved
						** and if the first image should be treated as raw file contents
						** on success it will return the path of the merged image
						*/

						if (is_object($image)) {

							/*
							** image_1 can be passed raw (because we dont wanna save the image uploaded by client)
							** image_2 will be the path to asset (since its already on the server)
							*/

							$image->set_images(
								$upload->content(true), 
								ROOT_DIR . ASSET_PATH . $this->asset[$_POST['selection']]
							);

							$res = $image->merge(ROOT_DIR . UPLOAD_DIR, true);

							/*
							** If file is merged and saved, we return its path
							** to the ajax call so thumbnails could be updated
							** also insert update in db
							*/

							if (!$res) {
								echo json_encode([
									'status' => 400,
									'message' => 'Error while superimposing'
								]);
							}
							else {
								/*
								** Convert the path returned (local path) to
								** url path for use
								*/

								$url = $upload->absolute_to_url($res);
								$this->model('gallery')->addUpload(
									$this->user()->user_id,
									$url
								);
								echo json_encode([
									'status' => 200,
									'path' => $url,
								]);
							}
						}
						else {
							echo json_encode([
								'status' => 500,
								'message' => 'Something internally went wrong'
							]);
						}
					}
					else {
						/*
						** The preview would tell the user if the image type was shit
						** so that means something went wrong with asset selection
						*/
						echo json_encode([
							'status' => 501,
							'message' => 'Invalid asset requested',
						]);
					}
				}
				else {
					echo json_encode([
						'status' => 500,
						'message' => 'Something went horribly fucking wrong LOL'
					]);
				}
			}
			else if (isset($_POST['selection']) && isset($_POST['cam-image'])) {
				/*
				** Check if image type is supported png and jpeg
				** Also that a valid asset has been requested (selection key in $_POST)
				*/

				if (file_exists(ROOT_DIR . ASSET_PATH . $this->asset[$_POST['selection']])) {

					/*
					** ImageSuperimpose will merge two images
					*/

					$image = $this->helper('ImageSuperimpose');
					$upload = $this->helper('ImageUpload');

					/*
					** ImageSuperimpose function merge creates a new file
					** of the two images passed to constructor
					** arguments passed is the path where the image will be saved
					** and if the first image should be treated as raw file contents
					** on success it will return the path of the merged image
					*/

					if (is_object($image) && is_object($upload)) {

						/*
						** image_1 can be passed raw (because we dont wanna save the image uploaded by client)
						** image_2 will be the path to asset (since its already on the server)
						*/

						$cam_img = str_replace('data:image/jpeg;base64,', '', $_POST['cam-image']);

						$image->set_images(
							base64_decode($cam_img), 
							ROOT_DIR . ASSET_PATH . $this->asset[$_POST['selection']]
						);

						$res = $image->merge(ROOT_DIR . UPLOAD_DIR, true);

						/*
						** If file is merged and saved, we return its path
						** to the ajax call so thumbnails could be updated
						** also insert update in db
						*/

						if (!$res) {
							echo json_encode([
								'status' => 400,
								'message' => 'Error while superimposing'
							]);
						}
						else {
							/*
							** Convert the path returned (local path) to
							** url path for use
							*/

							$url = $upload->absolute_to_url($res);
							$this->model('gallery')->addUpload(
								$this->user()->user_id,
								$url
							);

							echo json_encode([
								'status' => 200,
								'path' => $url,
							]);
						}
					}
					else {
						echo json_encode([
							'status' => 500,
							'message' => 'Something internally went wrong'
						]);
					}
				}
				else {
					
					/*
					** The preview would tell the user if the image type was shit
					** so that means something went wrong with asset selection
					*/

					echo json_encode([
						'status' => 501,
						'message' => 'Invalid asset requested',
					]);
				}
			}
			else {
				echo json_encode([
					'status' => 202,
					'message' => 'Upload info missing'
				]);
			}
		}
		else {
			echo json_encode([
				'status' => 401,
				'message' => 'unauthorized access'
			]);
		}
	}

	/*
	** Delete uploaded images, comments, likes 
	*/

	public function remove()
	{
		/*
		** Only logged on users are allowed to delete images they own
		*/

		if ($this->valid() && isset($_POST['image'])) {

			/*
			** Change the url path of the image to the root of htdocs
			** Use this path to find and remove an image
			*/

			$path = $this->helper('ImageUpload')->url_to_webroot($_POST['image']);

			/*
			** remove upload deletes an upload, and comments associated with it
			** any likes aswell if the image belongs to the user
			*/

			$res = $this->model('gallery')->removeUpload(
				$this->user()->user_id,
				$path
			);

			/*
			** After the records are removed, delete actual file
			*/

			if ($res) {

				/*
				** Check if file exists before deleting it
				*/

				if (file_exists(ROOT_DIR . $path)) {

					/*
					** Delete file, if unlink fails write to log file
					*/

					if (!unlink(ROOT_DIR . $path)) {
						file_put_contents(
							ROOT_DIR . '/app/logs/profile.txt',
							'Unable to delete: ' . $path
						);
					}
				}
				echo json_encode([
					'status' => 200,
					'image' => 'image deleted'
				]);
			}
			else {
				echo json_encode([
					'status' => 401,
					'message' => 'Unable to remove image'
				]);
			}
		}
		else {
			$this->flash_message(
			  'Invalid request #respawn',
			  'warning',
			  SITE_URL . '/home'
			);
		}
	}

	/*
	** Determine how many likes an image has got by its file name
	*/

	public function likes()
	{
		if (isset($_POST['image']) && $this->valid())
		{
			$path = $this->helper('ImageUpload')->url_to_webroot($_POST['image']);
			if (is_string($path)) {
				$id = $this->model('gallery')->findImageByName(
					$this->user()->user_id, 
					$path
				);

				if (is_array($id)) {
					$likes = $this->model('gallery')->getLikes($id['image_id']);

					echo json_encode([
						'status' => 200,
						'like_count' => $likes
					]);
				}
				else {
					echo json_encode([
						'status' => 200,
						'like_count' => 'N/A'
					]);
				}
			}
			else {
				echo json_encode([
					'status' => 200,
					'like_count' => 'N/A'
				]);
			}
		}
		else {
			echo json_encode([
				'status' => 401,
				'message' => 'unauthorized request'
			]);
		}
	}

	/*
	** Get thumbnails
	*/

	public function thumbnails()
	{
		if ($this->valid())
		{
			$uploads = $this->model('gallery')->getThumbnails(
				$this->user()->user_id
			);

			if (is_array($uploads)) {
				echo json_encode([
					'status' => 200,
					'list' => $uploads
				]);
			}
			else {
				echo json_encode([
					'status' => 500,
					'message' => 'oops, somethings fucked'
				]);
			}
		}
		else {
			echo json_encode([
				'status' => 401,
				'message' => 'unauthorized access'
			]);
		}
	}

	/*
	** This index function is for when users try to access urls that dont
	** exists. this is temporar until i create my own 404 page
	*/


	public function index($params = [])
	{
		$this->flash_message(
		  '404 page not found',
		  'danger',
		  SITE_URL . '/profile/home'
		);
	}
}