$filePath = "c:\laragon\www\simas\sismik\app\Http\Controllers\Admin\RombelController.php"
$content = Get-Content $filePath -Raw

# The code to insert after line 475 (after the closing brace of the foreach loop)
$historyCode = @"

            // === CREATE HISTORY RECORD ===
            `$uniqueMapels = array_unique(array_column(`$resultData, 'mapel'));
            `$uniqueSiswa = array_unique(array_column(`$resultData, 'nisn'));
            
            `$historyId = DB::table('katrol_nilai_history')->insertGetId([
                'rombel_id' => `$id,
                'tahun_pelajaran' => `$tahunAktif,
                'semester' => `$semesterAktif,
                'nilai_min_baru' => `$minBaru,
                'nilai_max_baru' => `$maxBaru,
                'total_siswa' => count(`$uniqueSiswa),
                'total_mapel' => count(`$uniqueMapels),
                'total_records' => count(`$resultData),
                'generated_by' => auth()->user()->name ?? 'System',
                'generated_at' => now()
            ]);

            // === SAVE DETAILS TO HISTORY ===
            foreach (`$resultData as `$row) {
                DB::table('katrol_nilai_details')->insert([
                    'history_id' => `$historyId,
                    'nisn' => `$row['nisn'],
                    'mapel' => `$row['mapel'],
                    'nilai_asli' => `$row['nilai_lama'],
                    'nilai_katrol' => `$row['nilai_baru'],
                    'created_at' => now()
                ]);
            }
"@

# Find the position to insert (after "            }" that closes the foreach loop around line 475)
$pattern = "(\s+\], \[\`$id, \`$tahunAktif, \`$semesterAktif, \`$row\['nisn'\], \`$mapel, \`$nilaiLama, round\(\`$nilaiBaru, 1\)\]\);\r?\n\s+\})"
$replacement = "`$1$historyCode"

# Replace
$content = $content -replace $pattern, $replacement

# Save
Set-Content $filePath -Value $content -NoNewline

Write-Host "History tracking code injected successfully"
"@
$tempScript = "c:\laragon\www\simas\sismik\inject_history.ps1"
Set-Content $tempScript -Value $historyCode
