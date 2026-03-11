<?php

namespace App\Http\Controllers\GuruBK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\GuruBK;

class ProfileController extends Controller
{
    /**
     * Display guru BK profile page
     */
    public function index()
    {
        $guruBk = Auth::guard('guru_bk')->user();
        
        return view('guru_bk.profil', compact('guruBk'));
    }

    /**
     * Update guru BK profile
     */
    public function update(Request $request)
    {
        $guruBk = Auth::guard('guru_bk')->user();

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

        GuruBK::where('id', $guruBk->id)->update($data);

        return redirect()->route('guru_bk.profil')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto(Request $request)
    {
        $guruBk = Auth::guard('guru_bk')->user();

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            
            // Delete old photo if exists
            if ($guruBk->foto && Storage::disk('public')->exists('guru_bk/' . $guruBk->foto)) {
                Storage::disk('public')->delete('guru_bk/' . $guruBk->foto);
            }

            // Generate filename
            $filename = 'guru_bk_' . $guruBk->id . '_' . time() . '.jpg';
            $uploadPath = storage_path('app/public/guru_bk/' . $filename);
            
            // Ensure directory exists
            if (!file_exists(storage_path('app/public/guru_bk'))) {
                mkdir(storage_path('app/public/guru_bk'), 0755, true);
            }
            
            // Compress image directly from temp upload to max 250KB
            $this->compressImage($file->getPathname(), $uploadPath, 250);
            
            // Update database
            GuruBK::where('id', $guruBk->id)->update(['foto' => $filename]);
        }

        return redirect()->route('guru_bk.profil')
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
