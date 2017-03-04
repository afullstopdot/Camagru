<?php

class profile extends Controller
{

	/*
	** Render edit home, where you can snap pics, upload pics, delete pics
	*/

	public function home()
	{

		/*
		** Only logged on users allowed here
		*/

		if ($this->valid() === true)
		{
			$this->view('profile/home');
		}
		else
		{
			$this->flash_message(
				'You must br logged in to access this section',
				'danger',
				SITE_URL . '/auth/login'
			);
		}
	}
}