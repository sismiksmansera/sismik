<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;

class ProfileController extends Controller
{
    /**
     * Display siswa profile page
     */
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        
        return view('siswa.profil', compact('siswa'));
    }

    /**
     * Update siswa profile
     */
    public function update(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();

        $request->validate([
            'email' => 'nullable|email|max:255',
            'nohp_siswa' => 'nullable|string|max:20',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = [
            'email' => $request->email,
            'nohp_siswa' => $request->nohp_siswa,
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        Siswa::where('id', $siswa->id)->update($data);

        return redirect()->route('siswa.profil')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            
            // Delete old photo if exists
            if ($siswa->foto && Storage::disk('public')->exists('siswa/' . $siswa->foto)) {
                Storage::disk('public')->delete('siswa/' . $siswa->foto);
            }

            // Generate filename
            $filename = 'siswa_' . $siswa->id . '_' . time() . '.jpg';
            $uploadPath = storage_path('app/public/siswa/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/public/siswa'))) {
                mkdir(storage_path('app/public/siswa'), 0755, true);
            }
            
            // Compress image directly from temp upload to max 250KB
            $this->compressImage($file->getPathname(), $uploadPath, 250);
            
            // Update database
            Siswa::where('id', $siswa->id)->update(['foto' => $filename]);
        }

        return redirect()->route('siswa.profil')
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
