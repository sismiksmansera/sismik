
            // === CREATE HISTORY RECORD ===
            $uniqueMapels = array_unique(array_column($resultData, 'mapel'));
            $uniqueSiswa = array_unique(array_column($resultData, 'nisn'));
            
            $historyId = DB::table('katrol_nilai_history')->insertGetId([
                'rombel_id' => $id,
                'tahun_pelajaran' => $tahunAktif,
                'semester' => $semesterAktif,
                'nilai_min_baru' => $minBaru,
                'nilai_max_baru' => $maxBaru,
                'total_siswa' => count($uniqueSiswa),
                'total_mapel' => count($uniqueMapels),
                'total_records' => count($resultData),
                'generated_by' => auth()->user()->name ?? 'System',
                'generated_at' => now()
            ]);

            // === SAVE DETAILS TO HISTORY ===
            foreach ($resultData as $row) {
                DB::table('katrol_nilai_details')->insert([
                    'history_id' => $historyId,
                    'nisn' => $row['nisn'],
                    'mapel' => $row['mapel'],
                    'nilai_asli' => $row['nilai_lama'],
                    'nilai_katrol' => $row['nilai_baru'],
                    'created_at' => now()
                ]);
            }
