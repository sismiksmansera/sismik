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
            $filename = 'guru_' . $guru->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Store file
            $tempPath = $file->storeAs('guru', $filename, 'public');
            
            // Compress image if needed
            $fullPath = storage_path('app/public/guru/' . $filename);
            $this->compressImage($fullPath, $fullPath, 250);
            
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
        if (!file_exists($source)) {
            return false;
        }

        $info = getimagesize($source);
        if ($info === false) {
            return false;
        }

        $mime = $info['mime'];
        
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        if (!$image) {
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
            
            // Preserve transparency for PNG
            if ($mime === 'image/png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        // Compress with decreasing quality until target size
        $quality = 90;
        do {
            ob_start();
            if ($mime === 'image/png') {
                $pngQuality = intval(9 - ($quality / 10));
                imagepng($image, null, max(0, min(9, $pngQuality)));
            } else {
                imagejpeg($image, null, $quality);
            }
            $imageData = ob_get_clean();
            $size = strlen($imageData) / 1024;
            $quality -= 10;
        } while ($size > $maxSizeKB && $quality > 10);

        // Save final image
        file_put_contents($destination, $imageData);
        imagedestroy($image);

        return true;
    }
}
