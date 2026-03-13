Add-Type -AssemblyName System.Drawing
$img_path = "d:\ccs\public\images\logo.png"

# Load original image
$bmp = New-Object System.Drawing.Bitmap($img_path)

# Loop over pixels to find near-white and make them transparent
for ($x = 0; $x -lt $bmp.Width; $x++) {
    for ($y = 0; $y -lt $bmp.Height; $y++) {
        $pixel = $bmp.GetPixel($x, $y)
        if ($pixel.R -gt 240 -and $pixel.G -gt 240 -and $pixel.B -gt 240) {
            $bmp.SetPixel($x, $y, [System.Drawing.Color]::Transparent)
        }
    }
}

# Save new transparent image
$out_path = "d:\ccs\public\images\logo_transparent.png"
$bmp.Save($out_path, [System.Drawing.Imaging.ImageFormat]::Png)

$bmp.Dispose()
Remove-Item -Path $img_path -Force
Rename-Item -Path $out_path -NewName "logo.png"
Write-Host "Success"
