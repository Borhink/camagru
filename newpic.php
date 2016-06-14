<?php
include "header.php";
include "config/conn.php";
?>
<div id="center">
<?php

if ($_SESSION["user"])
{
	echo "<table id='table1'><tr>";
	if ($_POST['img1'] && $_POST['add'])
	{
		$data = explode(';', $_POST['img1']);
		$data = explode(',', $data[1]);
		$data = base64_decode($data[1]);

		$img = imagecreatefromstring($data);
		if (file_exists("img") && file_exists("img/".$_POST['add'].".png"))
		{
			$add = imagecreatefrompng("img/".$_POST['add'].".png");
			imagecopy($img, $add, 0, 0, 0, 0, imagesx($add), imagesy($add));
		}
		$author = $_SESSION['user'];
		$name = "gallery/".$author."_".getdate()[0].".png";
		try {
			if (!file_exists("gallery"))
				mkdir("gallery");
			imagepng($img, $name);
			$date = time();
			$req = $conn->prepare("INSERT INTO image (name, author, date) VALUES (:name, :author, $date)");
			$req->execute(array(':name' => $name, ':author' => $author));
		}
		catch(PDOException $e) {
			echo "Error: ".$e->getMessage()."<br>";
			die();
		}
	}
	else if ($_FILES['uploaded'] && $_POST['add'])
	{
		$file = $_FILES['uploaded'];
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		if ($ext == "png")
		{
			$img = imagecreatefrompng($file["tmp_name"]);
			imagealphablending($img, true);
			imagesavealpha($img, true);
			if (imagesx($img) <= 220 && imagesy($img) <= 165)
			{
				if (file_exists("img") && file_exists("img/".$_POST['add'].".png"))
				{
					$add = imagecreatefrompng("img/".$_POST['add'].".png");
					imagecopy($img, $add, 0, 0, 0, 0, imagesx($add), imagesy($add));
				}
				$author = $_SESSION['user'];
				$name = "gallery/".$author."_".getdate()[0].".png";
				try {
					if (!file_exists("gallery"))
						mkdir("gallery");
					imagepng($img, $name);
					$date = time();
					$req = $conn->prepare("INSERT INTO image (name, author, date) VALUES (:name, :author, $date)");
					$req->execute(array(':name' => $name, ':author' => $author));
				}
				catch(PDOException $e) {
					echo "Error: ".$e->getMessage()."<br>";
					die();
				}
			}
		}
	}
	?>
	<td id="main">
		<div id="vid">
			<video id="video" class="superpose"></video>
			<img id="filter1" class="superpose">
		</div>
		<canvas id="canvas" style="display: none"></canvas>
	</br><button id="startbutton" disabled>Take picture</button>
		<form action="#" method="post" enctype="multipart/form-data">
			or <input id="uploadimg" type="file" name="uploaded" accept=".png" disabled>
			<ul class="selection" id="filter">
				<label><img src="img/border.png" style="width:30%;max-width:120px"><input type="radio" name="add" value="border"></label>
				<label><img src="img/darkmask.png" style="width:30%;max-width:120px"><input type="radio" name="add" value="darkmask"></label>
				<label><img src="img/play.png" style="width:30%;max-width:120px"><input type="radio" name="add" value="play"></label>
				<label><img src="img/vache.png" style="width:30%;max-width:120px"><input type="radio" name="add" value="vache"></label>
			</ul>
			<input id="img1" type="hidden" name="img1">
			<div id="preview">
				<img id="picture" class="superpose" height="165">
				<img id="filter2" class="superpose">
			</div>
		</br><button id="send" disabled>Send</button>
	</form>
	<script langage="javascript">
		(function() {
			var streaming = false,
			video		= document.querySelector('#video'),
			cover		= document.querySelector('#cover'),
			canvas		= document.querySelector('#canvas'),
			picture		= document.querySelector('#picture'),
			startbutton	= document.querySelector('#startbutton'),
			filter		= document.querySelector('#filter'),
			filter1		= document.querySelector('#filter1'),
			filter2		= document.querySelector('#filter2'),
			send		= document.querySelector('#send'),
			uploadimg	= document.querySelector('#uploadimg'),
			img1		= document.querySelector('#img1'),
			width = 220,
			height = 0;

			navigator.getMedia = ( navigator.getUserMedia ||
								 navigator.webkitGetUserMedia ||
								 navigator.mozGetUserMedia ||
								 navigator.msGetUserMedia);

			navigator.getMedia(
			{
				video: true,
				audio: false
				},
				function(stream) {
					if (navigator.mozGetUserMedia) {
					video.mozSrcObject = stream;
					} else {
					var vendorURL = window.URL || window.webkitURL;
					video.src = vendorURL.createObjectURL(stream);
					}
					video.play();
					if (getCheckedFilter('add'))
					{
						startbutton.removeAttribute('disabled');
						filter1.setAttribute('src', getCheckedFilter('add'));
					}
				},
				function(err) {
					console.log("An error occured! " + err);
				}
			);

			video.addEventListener('canplay', function(ev){
			if (!streaming) {
				height = video.videoHeight / (video.videoWidth/width);
				video.setAttribute('width', width);
				video.setAttribute('height', height);
				canvas.setAttribute('width', width);
				canvas.setAttribute('height', height);
				streaming = true;
			}
			}, false);

			function takepicture() {
			canvas.width = width;
			canvas.height = height;
			canvas.getContext('2d').drawImage(video, 0, 0, width, height);
			var data = canvas.toDataURL('image/png');
			picture.setAttribute('src', data);
			img1.setAttribute('value', data);
			filter2.setAttribute('src', getCheckedFilter('add'));
			}

    		function readURL(input) {
        		if (input.files && input.files[0]) {
            		var reader = new FileReader();
            		reader.onload = function (e) {
                		picture.setAttribute('src', e.target.result);
						img1.removeAttribute('value');
            		}
            	reader.readAsDataURL(input.files[0]);
        		}
	    	}

			function getCheckedFilter(name) {
    			var elements = document.getElementsByName(name);
				for (var i = 0, len = elements.length; i < len; ++i)
					if (elements[i].checked)
						return ("img/" + elements[i].value + ".png");
				return (0);
			}

	    	uploadimg.addEventListener('change',function() {
	        	readURL(this);
				if (getCheckedFilter('add'))
					filter2.setAttribute('src', getCheckedFilter('add'));
				send.removeAttribute('disabled');
	    	});

			filter.addEventListener('change',function() {
				uploadimg.removeAttribute('disabled');
				if (video.hasAttribute('width'))
					filter1.setAttribute('src', getCheckedFilter('add'));
				if (picture.hasAttribute('src'))
					filter2.setAttribute('src', getCheckedFilter('add'));
				if (video.hasAttribute('width') && startbutton.hasAttribute('disabled'))
					startbutton.removeAttribute('disabled');
	    	});

			startbutton.addEventListener('click', function(ev){
				takepicture();
				send.removeAttribute('disabled');
			ev.preventDefault();
			}, false);

		})();
	</script>
	<?php
}
else
	print("</br></br>Login in to make your own pics ;)");

?>
</td>
<td id ="aside">
<?php

try {
	$author = $_SESSION['user'];
	$req = $conn->prepare("SELECT name FROM image WHERE author = '$author' ORDER BY date DESC");
	$req->execute();
	for ($i = 0; $i < 5 && $i < $req->rowCount(); $i++)
	{
		$res = $req->fetch(PDO::FETCH_ASSOC);
		if ($i)
			echo "</br>";
		echo "<CENTER><img class='mini' src='".$res["name"]."'></CENTER>";
	}
}
catch(PDOException $e) {
	echo "Error: ".$e->getMessage()."<br>";
	die();
}

?>
</td>
</tr></table>
</div>
<?php
include "footer.php";
?>
