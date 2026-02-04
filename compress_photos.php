<?php
/**
 * Script to compress existing photos larger than 250KB
 * Run: php compress_photos.php
 */

$folders = ['guru', 'siswa', 'guru_bk'];
$maxSizeBytes = 250 * 1024; // 250 KB
$count = 0;

echo "=== SISMIK Photo Compression Script ===\n\n";

foreach ($folders as $folder) {
    $path = __DIR__ . '/storage/app/public/' . $folder;
    
    if (!is_dir($path)) {
        echo "Folder not found: $folder\n";
        continue;
    }
    
    echo "Processing folder: $folder\n";
    
    $files = glob($path . '/*.{jpg,jpeg,png,JPG,JPEG,PNG}', GLOB_BRACE);
    
    foreach ($files as $file) {
        $size = filesize($file);
        
        if ($size > $maxSizeBytes) {
            $info = getimagesize($file);
            
            if (!$info) {
                echo "  - Skip (invalid): " . basename($file) . "\n";
                continue;
            }
            
            $mime = $info['mime'];
            $image = null;
            
            // Create image from source
            switch ($mime) {
                case 'image/jpeg':
                    $image = @imagecreatefromjpeg($file);
                    break;
                case 'image/png':
                    $image = @imagecreatefrompng($file);
                    break;
                default:
                    echo "  - Skip (unsupported): " . basename($file) . "\n";
                    continue 2;
            }
            
            if (!$image) {
                echo "  - Skip (read error): " . basename($file) . "\n";
                continue;
            }
            
            // Get dimensions
            $width = imagesx($image);
            $height = imagesy($image);
            
            // Resize if larger than 800px
            if ($width > 800 || $height > 800) {
                if ($width > $height) {
                    $newWidth = 800;
                    $newHeight = (int)($height * 800 / $width);
                } else {
                    $newHeight = 800;
                    $newWidth = (int)($width * 800 / $height);
                }
                
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }
            
            // Compress with decreasing quality until target size
            $quality = 85;
            do {
                ob_start();
                imagejpeg($image, null, $quality);
                $data = ob_get_clean();
                $quality -= 10;
            } while (strlen($data) > $maxSizeBytes && $quality > 20);
            
            // Save compressed image
            file_put_contents($file, $data);
            imagedestroy($image);
            
            $newSize = strlen($data);
            $count++;
            
            echo "  + Compressed: " . basename($file) . " (" . round($size/1024) . "KB -> " . round($newSize/1024) . "KB)\n";
        }
    }
}

echo "\n=== Done! Total compressed: $count files ===\n";
