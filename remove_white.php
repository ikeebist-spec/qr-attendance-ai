<?php
$file = 'd:/ccs/public/images/logo.png';
$src = imagecreatefrompng($file);

$w = imagesx($src);
$h = imagesy($src);

$dest = imagecreatetruecolor($w, $h);
imagesavealpha($dest, true);
$trans_colour = imagecolorallocatealpha($dest, 0, 0, 0, 127);
imagefill($dest, 0, 0, $trans_colour);

for ($x = 0; $x < $w; $x++) {
    for ($y = 0; $y < $h; $y++) {
        $rgb = imagecolorat($src, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        
        // Treat near-white as transparent
        if ($r > 230 && $g > 230 && $b > 230) {
            // transparent (skip drawing, since dest is already transparent)
        } else {
            // If it's pure white outline around the text, it might still have some antialiasing
            // Simple thresholding
            imagesetpixel($dest, $x, $y, imagecolorallocatealpha($dest, $r, $g, $b, 0));
        }
    }
}

imagepng($dest, 'd:/ccs/public/images/logo.png');
echo "Done";
