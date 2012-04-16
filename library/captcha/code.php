<?php
$pass_length=5;
$img_width = isset($_GET['width']) ? $_GET['width'] : 200;
$img_height = isset($_GET['height']) ? $_GET['height'] : 50;

if($img_width > 500) $img_width = 500;
if($img_height > 300) $img_height = 300;

$fnt_path = dirname(__FILE__);
$fnt_path.="/fonts";

$passwd = "";

for($i = 0; $i < $pass_length; $i++)
{
	$passwd.= chr(rand(65, 90));
}

session_start();
$_SESSION['code'] = $passwd;

$fonts = array();

if($handle = opendir($fnt_path))
{
	while(false !== ($file = readdir($handle)))
	{
		if(substr(strtolower($file), -4, 4) == '.ttf')
		{
			$fonts[] = $fnt_path.'/'.$file;
		}
	}
}

if(count($fonts) < 1) die('Нет шрифтов');


header("Content-type: image/jpeg");
header("Expires: Wen, 09 JUL 2010 19:18:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", true);
header("Pragma: no-cache");

$img = imagecreatetruecolor($img_width, $img_height);
$bg = imagecolorallocate($img, 255, 210, 258);
imagefilledrectangle($img, 0, 0, $img_width, $img_height, $bg);


$col_min = rand(80, 95);
$col_max = rand(100, 200);

for($top = 0; $top < $img_height; $top+= 20)
{
	$bg = imagecolorallocate($img, 0, 0, 0);
	$bottom = 10;
	$points = array(0, 
                    $top,
                    0,
					$top+$bottom, 
                    $img_width,
					$bottom+$top, 
                    $img_width,
					$top                    
                    );
	imagefilledpolygon($img, $points, 4, $bg);
}

$spacing = $img_width / (strlen($passwd)+2);
$x = $spacing;
         
for($i = 0; $i < strlen($passwd); $i++)
{
	$letter = $passwd[$i];
	$size = rand($img_height/3, $img_height/2);
	$rotation = rand(-30, 30);

	$y = rand($img_height * 0.9, $img_height - $size - 4);

	$font = $fonts[array_rand($fonts)];
	$r = 255;
	$g = 255;
	$b = 255;

	$color = imagecolorallocate($img, $r, $g, $b);
	$shadow = imagecolorallocate($img, 0, 0, 0);

	imagettftext($img, $size, $rotation, $x, $y, $shadow, $font, $letter);
	imagettftext($img, $size, $rotation, $x-1, $y-3, $color, $font, $letter);

	$x+= rand($spacing, $spacing*1.5);
}

imagejpeg($img,null,100);
imagedestroy($img);
exit;