@echo off
echo Uploading files to server...
echo.
echo Server: 76.13.192.177
echo User: zainul
echo Password: Zchsyl75
echo.
echo Please run these commands manually in your terminal:
echo.
echo === Option 1: Using SCP (you will need to enter password) ===
echo scp app\Http\Controllers\Admin\RiwayatAkademikController.php zainul@76.13.192.177:/sismik/app/Http/Controllers/Admin/
echo scp resources\views\admin\riwayat-akademik\show.blade.php zainul@76.13.192.177:/sismik/resources/views/admin/riwayat-akademik/
echo scp resources\views\admin\riwayat-akademik\print.blade.php zainul@76.13.192.177:/sismik/resources/views/admin/riwayat-akademik/
echo scp resources\views\admin\riwayat-akademik\print-all.blade.php zainul@76.13.192.177:/sismik/resources/views/admin/riwayat-akademik/
echo scp routes\web.php zainul@76.13.192.177:/sismik/routes/
echo scp resources\views\admin\rombel\members.blade.php zainul@76.13.192.177:/sismik/resources/views/admin/rombel/
echo.
echo === Option 2: Using SSH and git pull ===
echo ssh zainul@76.13.192.177
echo cd /sismik
echo git pull origin main
echo php artisan route:cache
echo php artisan view:clear
echo.
pause
