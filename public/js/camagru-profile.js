/*
** Ths js file is for the profile page, did profile page seperately
** to lazy to make changes now lol
*/

// var url = 'http:\/\/10.0.0.150:80\/Camagru\/public\/';
var url = 'http:\/\/localhost:8080\/';
// var url = 'http:\/\/afullstopdot.duckdns.org\/Camagru\/public\/';

window.onload =  () => {
	const options 	= document.getElementsByClassName('options')[0];
	const gallery 	= document.getElementById('option-list').children;
	const file 		= document.getElementById('file');
	const form 		= document.getElementById('image-form');
	const button 	= document.getElementById('submit');
	const png 		= document.getElementById('image-png');
	const thumbnail = document.getElementsByClassName('thumbnails')[0];
	const modal 	= document.getElementsByClassName('img-modal')[0];
	const del_btn 	= document.getElementById('del-img');
	const likes		= document.getElementsByClassName('likes-count')[0];
	const cls_mdl 	= document.getElementById('img-close');

	/*
	** Unless an image is uploaded this value will remain false
	** and live preview will not be shown
	*/

	var allow_preview = false;

	/*
	** Objects are used instead of normal variable so value
	** can be updated inside annoymous function
	*/

	var	asset = {
		index: -1
	};

	/*
	** When a thumbnail is clicked a modal is shown
	** with a larger picture of the thumbnail
	** Keep the current instance of the modal
	*/

	var modal_img = {
		obj: '',
	}

	/*
	** Create a toggle feature between webcam and upload
	** state will keep update what state is currently used
	*/

	var state = {
	  cam: false
	};


	/*
	** Once the page is loaded remove loader and z-indexed center
	*/

	if (document.getElementById('loading-div') !== null) {
      document.getElementById('loading-div').style.display = 'none';
      document.getElementById('hash').style.height = '0px';       
    }

	/*
	** Disable button until image is selected
	*/

	if (button) 
	{
		button.disabled = true;
	}

	/*
	** Request to server for user thumbnails
	*/

	if (thumbnail && modal && form && modal_img)
	{
		const req 	= new XMLHttpRequest();

		req.open('POST', url + 'profile/thumbnails', true)
		req.onload = (e) => {
			if (req.status == 200) {
				const	resp = JSON.parse(req.responseText);

				if (resp.status == 200) {
					const	list = resp.list;

					for (var count = 0; count < list.length; count++) {
						var image = list[count];

						add_thumbnail(thumbnail, image.img_path, modal, form, modal_img, false);
					}
				}
			}
			else {
				console.log('Failed to load thumbnails');
			}
		};
		req.send();
	}

	/*
	** Display uploaded image retuned from image as base64_encoded img
	** Allow user to select an asset for live preview
	*/

	if (file)
	{
		file.onchange = () => {
			if (!state.cam) {
				const label = document.getElementById('file-label');

				if (form) {
					const data = new FormData(form);
					const req = new XMLHttpRequest();

					req.open('POST', url + 'profile/validate', true);
					req.onload = function (e) {
						if (req.status == 200) {
							const res = JSON.parse(req.responseText);
							const img = document.getElementById('image-preview');

							if (img && options && res.status == 200) {
								var img_width;

								img.src = res.image;
								allow_preview = true;
								if (screen.width <= 680) {
									img_width = '100%';
								}
								else {
									img_width = '475px';
								}
								img.style.width = img_width;
								options.style.display = 'inline-block';
								if (label) {
									const background = label.style.background;

									label.style.background = 'rgba(23, 195, 13, 0.77)';
									label.innerHTML = 'Change image here'
									window.setTimeout(function () {
						              label.style.background = background;
						              label.innerHTML = 'Upload image'
						            }, 5000);
								}
							}
							else {
								console.log('status: ' + res.status);
							}
						}
					};
					req.send(data);
				}
			}
		};
	}

	/*
	** Ajax call to upload and save new superimposed image
	** Update the thumbnails aswell with the rsponse from the server
	*/

	if (form)
	{
		form.addEventListener('submit', (e) => {
			const 	req = new XMLHttpRequest();
			var 	data;

			if (!state.cam) {
				data = new FormData(form);
			}
			else {

				const 	video 		= document.getElementById('cam-preview');
				const 	canvas 		= document.getElementById('cam-canvas');
				const 	width 		= 475;
				var 	height 		= 500;
				var 	streaming 	= false;
				
				data = new FormData();

				if (typeof video !== 'undefined' && typeof canvas !== 'undefined') {

					/*
					** insert getContext explanation
					*/

					const context = canvas.getContext('2d');

					if (typeof context !== 'undefined') {

						/*
						** insert drawImage explanation
						*/

					    context.drawImage(video, 0, 0, width, height);

					    /*
					    ** insert toDataURL explanation
					    */

					    data.append(
							'cam-image',
						    canvas.toDataURL('image/jpeg')
						);
					}
				}
			}

			/*
			** Send post request
			*/

			if (asset.index > -1) {
				data.append(
					'selection',
					asset.index
				);

				if (button) {
					button.innerHTML = 'Processing image...';
				}
				req.open('POST', url + 'profile/save', true);
				req.onload = (e) => {
					if (req.status == 200) {
						const resp = JSON.parse(req.responseText);

						if (resp.status == 200) {
							button.innerHTML = 'Image has been uploaded!';
							add_thumbnail(thumbnail, resp.path, modal, form, modal_img, true);
							reset_preview(png, button, allow_preview);
							window.setTimeout(function () {
								button.innerHTML = 'Snap!';
								button.style.background = 'rgba(28, 33, 30, 0.46)';
				            }, 2000);
						}
						else {
							button.innerHTML = 'Oops, error occured';
						}
					}
				};
				req.send(data);
			}
			e.preventDefault();
		}, false);
	}

	/*
	** Delete image permanently
	*/

	if (del_btn) {
		del_btn.onclick = () => {
			const data = new FormData();
			const req = new XMLHttpRequest();

			/*
			** Append information to request.
			** Images will be deleted by names only if the user
			** is allowed to
			*/

			data.append('request', 'delete');
			data.append('image', modal_img.obj.src);

			/*
			** Let user know whats happening, send del request
			** remove thumbnail
			*/

			del_btn.innerHTML = 'Deleting image ...';
			req.open('POST', url +'profile/remove', true);
			req.onload = (e) => {
				if (req.status == 200) {
					const resp = JSON.parse(req.responseText);

					if (resp.status == 200) {
						const child = getThumbnailBySrc(thumbnail, modal_img.obj.src);

						if (child != false) {
							removeThumbnail(thumbnail, child);
						}
						del_btn.innerHTML = 'Image deleted, I agree it was ugly';
						del_btn.style.background = 'rgba(3, 62, 15, 0.77)';
						window.setTimeout(function () {
							form.style.opacity = '1';
							modal_img.obj.src = '';
							modal.style.display = 'none';
							del_btn.innerHTML = 'Delete image';
							del_btn.style.background = 'rgb(216, 15, 15)';
							likes.innerHTML = '?';
			            }, 1000);
					}
					else {
						del_btn.innerHTML = 'Oops, an error occured';
					}
				}
			};
			req.send(data);
		};
	}

	/*
	** Select superimposable image and create live preview for user
	** Also updat the index of the asset selection to be passed as post
	** for image superimposing
	*/

	if (gallery && png) {
		for (var count = 0; count < gallery.length; count++) {
			((index, button, asset) => {
				gallery[index].addEventListener('click', () => {
					//set background of clicked img, unselect all others
					asset.index = index;
					if (allow_preview) {
						select_image(gallery, index);
						live_preview(png, gallery, index, button);
					}
				})
			})(count, button, asset);
		}
	}

	/*
	** Sened request to server, to check how many likes a image has
	*/

	if (likes) {
		likes.onclick = () => {
			const data = new FormData();
			const req = new XMLHttpRequest();

			/*
			** Append information to request.
			** Images will be selected by names only if the user
			** is allowed to
			*/

			data.append('image', modal_img.obj.src);
			likes.innerHTML = '...';
			req.open('POST', url + 'profile/likes', true);
			req.onload = (e) => {
				if (req.status == 200) {
					const resp = JSON.parse(req.responseText);

					if (resp.status == 200) {
						likes.innerHTML = resp.like_count;
						likes.style.border = '2px solid #06fff0';
					}
					else {
						likes.innerHTML = '?';
					}
				}
			};
			req.send(data);
		};
	}

	/*
	** When left or right key is pressed i will look for the next
	** or previous image to display on img_modal
	*/

	if (document && modal) {
		document.onkeydown = (e) => {
			e = e || window.event;
			var	currentSibling;
			var temp;

			if (modal_img.obj) {

				/*
				** The modal is opened when a thumbnail is clicked
				** The object is assigned to the model_img object
				** using that object we check if the sibling has other siblings
				** before or after only if left or right arrow key is pressed
				*/

				currentSibling = getThumbnailBySrc(thumbnail, modal_img.obj.src);

				if (e.keyCode == '37') {
					temp = currentSibling.previousSibling;
				}
				else if (e.keyCode == '39') {
					temp = currentSibling.nextSibling;
				}

				if (temp) {
					if (temp.src) {
						modal_img.obj.src = temp.src;
						if (likes) {
							likes.innerHTML = '?';
						}
					}
				}
			}
		};
	}

	/*
	** When span to close modal is clicked hide modal
	** show previous view
	*/

	if (cls_mdl && modal && form && likes) {
		cls_mdl.onclick = () => {
			modal.style.display = 'none';
			form.style.opacity = '1';
			likes.innerHTML = '?';
		};
	}

	/*
    ** Toggle webcam and file upload
    */

    const toggle = document.getElementsByClassName('toggle')[0];

    if (toggle) {
      toggle.onclick =  function (e) {

      	/*
      	** Check what the state is and update the object
      	*/

      	if (state.cam) {
      		state.cam = false;

      		/*
      		** Hide the video element, webcam is in use
      		*/

      		const upload = document.getElementById('image-preview');
	        const input = document.getElementById('file');
	        const label = document.getElementById('file-label');

      		if (typeof upload !== 'undefined' && 
      			typeof input !== 'undefined'  &&
      			typeof label !== 'undefined') {

      			/*
      			** Hide the img preview
      			*/

      			upload.style.display = 'inline-block';
      			input.disabled = false;
      			label.style.background = 'rgba(11, 199, 220, 0.38)';
      			label.innerHTML = 'Upload Image';

      			const video = document.getElementById('cam-preview');

      			if (typeof video !== 'undefined') {
      				video.style.display = 'none';
	      			options.style.display = 'none';
      			}
      		}
      	}
      	else {
	        state.cam = true;

	        /*
	        ** Show the video element, file upload is in use
	        */

	        const upload = document.getElementById('image-preview');
	        const input = document.getElementById('file');
	        const label = document.getElementById('file-label');

      		if (typeof upload !== 'undefined' && 
      			typeof input !== 'undefined'  &&
      			typeof label !== 'undefined') {

      			/*
      			** Hide the img preview
      			*/

      			upload.style.display = 'none';
      			input.disabled = true;
      			label.style.background = 'rgb(27, 30, 27)';
      			label.innerHTML = 'Upload Disabled';
      			allow_preview = true;
      			options.style.display = 'inline-block';

      			const video = document.getElementById('cam-preview');

      			if (typeof video !== 'undefined') {
      				video.style.display = 'inline-block';
      			}
      		}
    	}
      }; 
    }

    /*
    ** Access webcam
    */

    (function() {
		const upload 	= document.getElementById('image-preview');
		const video 	= document.getElementById('cam-preview');
		const canvas 	= document.getElementById('cam-canvas');
		const width 	= 475;
		var height 		= 500;
		var streaming 	= false;

		if (typeof upload !== 'undefined' && typeof video !== 'undefined') {

			/*
			** Constrainst for getUserMedia, request video only
			*/

			const constraints = {
		      audio: false, 
		      video: true
		    };

		    /*
		    ** Make promise to getUserMedia
		    */

		    const mediaStream = navigator.mediaDevices.getUserMedia(constraints);

		    if (typeof mediaStream !== 'undefined') {
			    mediaStream.then(function(stream) {
			    	video.srcObject = stream;

			    	/*
			    	** Play webcam, update canvas
			    	*/

			        video.onloadedmetadata = function (e) {
			        	video.play();
			            video.addEventListener('canplay', function(e) {

			              /*
			              ** If the video can be played then we set the webcam resolution
			              */

			              if (!streaming) {

			                /*
			                ** Update video element attributes, streaming is now true
			                */

			                canvas.setAttribute('width', width);
			                canvas.setAttribute('height', height);
			                streaming = true;
			              }
			            }, false);
			        };
			    }).catch(function (err) {
			    	console.log(err.name + ": " + err.message);
			    });
			}
		}
    })();
};

/*
** open and close nav bar for small screens
*/

function open_close()
{
	var x = document.getElementById("myTopnav");

	if (x.className === "topnav") {
		x.className += " responsive";
	}
	else {
		x.className = "topnav";
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
** Create illussion of selection for user
** Update selection var
*/

function select_image(gallery, index) 
{
	const color_new = 'rgba(0, 0, 0, 0.5)';
	const color_default = 'rgba(11, 199, 220, 0.38)';

	if (gallery) {
		for (var count = 0; count < gallery.length; count++) {
			if (count == index) {
				gallery[count].style.background = color_new;
			}
			else {
				gallery[count].style.background = color_default;
			}
		}
	}
}

/*
** This function will give the img tag with the z-index the src of user choice
** set the display of said image to display inline
** enable the button for form resubmission
** style the background of the button accordingly
** 
*/

function live_preview(png, gallery, index, button) 
{
	if (png && gallery && button) {
		png.style.display = 'inline-block';
		png.src = gallery[index].src;
		button.disabled = false;
		button.style.background = 'rgba(0, 128, 34, 0.46)';
	}
}

/*
** Reset live preview so another submit wont create same image just created
*/

function reset_preview(parent, submit, allow_preview)
{
	if (parent && submit) {
		allow_preview = false;
		parent.style.display = 'none';
		submit.disabled = true;
	}
	else {
		console.log('Failed to reset preview');
	}
}

/*
** Add new thumbnail, after ajax call.. response will be path to the image
** new img tag will be appended to thumbnails class with the correct src
*/

function add_thumbnail(parent, src, modal, form, modal_img, before)
{
	if (parent && src) {
		const node = document.createElement('img');

		if (node) {
			node.src = src;
			if (before) {
				var index = 0;
				/*
				** Because the first element of the childNodes is text, find
				** the first element that is an img so the next and right function (toggle pics)
				** will work
				*/
				for (var count = 0; count < parent.childNodes.length; count++) {
					if (parent.childNodes[count].localName == "img") {
						index = count;
						break;
					}
				}
				parent.insertBefore(node, parent.childNodes[index]);

			}
			else {
				parent.appendChild(node);
			}

			/*
			** Add event to img so larger size can be shown,
			** and image can be deleted
			*/

			node.onclick = () => {
				if (modal && form) {
					/*
					** Modal only has one img tag, look for it
					*/
					const children = modal.children;

					for (var count = 0; count < children.length; count++) {
						if (children[count].localName == "img") {
							/*
							** Change the src of the image for the modal
							** to the clicked thumbnail
							*/
							children[count].src = src;
							/*
							** Update the img object of the modal
							*/
							modal_img.obj = children[count];
						}
					}
					form.style.opacity = '0';
					modal.style.display = 'block';
				}
				else {
					console.log('modal is not defined');
				}
			};
		}
	}
}

/*
** get thumbnail by src
*/

function getThumbnailBySrc(parent, src) {
	if (parent && src) {
		const children = parent.children;

		for (var count = 0; count < children.length; count++) {
			if (children[count].localName == "img") {
				if (children[count].src == src) {
					return children[count];
				}
			}
		}
	}
	return false;
}

/*
** remove thumbnail
*/

function removeThumbnail(parent, child) {
	if (parent && child) {
		parent.removeChild(child);
	}
}

/*
** Close image model
*/

function close_modal(modal, form, likes)
{
	if (modal && form && likes) {
		modal.style.display = 'none';
		form.style.opacity = '1';
		likes.innerHTML = '?';
	}
}
