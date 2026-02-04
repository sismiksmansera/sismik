<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Guru;

class ProfileController extends Controller
{
    /**
     * Display guru profile page
     */
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        
        return view('guru.profil', compact('guru'));
    }

    /**
     * Update guru profile
     */
    public function update(Request $request)
    {
        $guru = Auth::guard('guru')->user();

        $request->validate([
            'email' => 'nullable|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        Guru::where('id', $guru->id)->update($data);

        return redirect()->route('guru.profil')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $guru = Auth::guard('guru')->user();

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            
            // Delete old photo if exists
            if ($guru->foto && Storage::disk('public')->exists('guru/' . $guru->foto)) {
                Storage::disk('public')->delete('guru/' . $guru->foto);
            }

            // Generate filename
            $filename = 'guru_' . $guru->id . '_' . time() . '.jpg';
            $uploadPath = storage_path('app/public/guru/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/public/guru'))) {
                mkdir(storage_path('app/public/guru'), 0755, true);
            }
            
            // Compress image directly from temp upload to max 250KB
            $this->compressImage($file->getPathname(), $uploadPath, 250);
            
            // Update database
            Guru::where('id', $guru->id)->update(['foto' => $filename]);
        }

        return redirect()->route('guru.profil')
            ->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Compress image to target size (max KB)
     */
    private function compressImage($source, $destination, $maxSizeKB = 250)
    {
        $maxSizeBytes = $maxSizeKB * 1024;
        
        if (!file_exists($source)) {
            return false;
        }

        $info = getimagesize($source);
        if ($info === false) {
            copy($source, $destination);
            return false;
        }

        $mime = $info['mime'];
        $image = null;
        
        switch ($mime) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($source);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($source);
                break;
            default:
                copy($source, $destination);
                return false;
        }

        if (!$image) {
            copy($source, $destination);
            return false;
        }

        // Resize if too large
        $width = imagesx($image);
        $height = imagesy($image);
        $maxDimension = 800;

        if ($width > $maxDimension || $height > $maxDimension) {
            if ($width > $height) {
                $newWidth = $maxDimension;
                $newHeight = intval($height * ($maxDimension / $width));
            } else {
                $newHeight = $maxDimension;
                $newWidth = intval($width * ($maxDimension / $height));
            }

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            
            if ($mime === 'image/png' || $mime === 'image/gif') {
                imagecolortransparent($resized, imagecolorallocatealpha($resized, 0, 0, 0, 127));
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        // Compress with decreasing quality using temp file
        $quality = 85;
        $tempPath = sys_get_temp_dir() . '/compress_' . uniqid() . '.jpg';
        
        do {
            imagejpeg($image, $tempPath, $quality);
            $currentSize = filesize($tempPath);
            
            if ($currentSize <= $maxSizeBytes || $quality <= 20) {
                break;
            }
            
            $quality -= 10;
        } while ($quality > 20);

        // Move to destination
        $result = copy($tempPath, $destination);
        imagedestroy($image);
        @unlink($tempPath);
        
        return $result;
    }
}
