@extends('layouts.app')

@section('title', 'Cek Presensi Siswa | SISMIK')

@push('styles')
<style>
/* HEADER */
.cek-presensi-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 20px;
    text-align: center;
    color: white;
    box-shadow: 0 10px 40px rgba(139, 92, 246, 0.3);
}
.cek-presensi-header .header-icon-large {
    width: 80px; height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 36px; color: white;
    margin: 0 auto 20px;
}
.cek-presensi-header .page-title {
    font-size: 28px; font-weight: 700; margin: 0 0 8px 0;
    text-transform: uppercase; letter-spacing: 1px;
}
.cek-presensi-header .page-subtitle {
    font-size: 14px; font-weight: 500; margin: 0;
    color: rgba(255,255,255,0.9);
}

/* SELECTOR CARDS */
.selector-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}
.selector-card {
    background: white;
    padding: 15px 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 2px solid #e5e7eb;
    display: flex; align-items: center; gap: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.selector-card:hover {
    border-color: #8b5cf6;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(139,92,246,0.15);
}
.selector-card.selected {
    border-color: #8b5cf6;
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
}
.selector-card.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.selector-card .card-icon {
    width: 45px; height: 45px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: white; flex-shrink: 0;
}
.card-icon.rombel-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.card-icon.mapel-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.card-content { flex: 1; min-width: 0; }
.card-content .card-value {
    font-size: 15px; font-weight: 700; color: #1f2937; margin: 0 0 2px 0;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.card-content .card-placeholder { font-size: 14px; color: #9ca3af; margin: 0 0 2px 0; }
.card-content .card-label {
    font-size: 11px; color: #6b7280; font-weight: 500;
    text-transform: uppercase; letter-spacing: 0.3px; margin: 0;
}

/* MODAL */
.custom-modal-overlay {
    display: none; position: fixed; top: 0; left: 0;
    width: 100%; height: 100%; background: rgba(0,0,0,0.5);
    z-index: 9999; justify-content: center; align-items: center;
}
.custom-modal-overlay.show { display: flex; }
.custom-modal {
    background: white; border-radius: 16px; width: 90%; max-width: 600px;
    max-height: 80vh; overflow-y: auto;
    animation: slideIn 0.3s ease;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
@keyframes slideIn { from { transform: translateY(-30px); opacity:0; } to { transform:translateY(0); opacity:1; } }
.custom-modal .modal-header {
    padding: 20px 24px; border-bottom: 1px solid #e5e7eb;
    display: flex; justify-content: space-between; align-items: center;
}
.custom-modal .modal-header h3 { font-size: 18px; font-weight: 700; margin: 0; }
.custom-modal .modal-header .close-btn {
    width: 32px; height: 32px; border-radius: 8px; border: none;
    background: #f3f4f6; cursor: pointer; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
}
.custom-modal .modal-header .close-btn:hover { background: #e5e7eb; }
.custom-modal .modal-body { padding: 20px 24px; }
.modal-option-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
}
.option-card {
    background: #fff; border: 2px solid #e5e7eb; border-radius: 12px;
    padding: 18px; cursor: pointer; text-align: center;
    transition: all 0.3s ease;
}
.option-card:hover {
    border-color: #8b5cf6; transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(139,92,246,0.15);
}
.option-card .option-icon {
    width: 45px; height: 45px; border-radius: 50%;
    margin: 0 auto 10px; display: flex; align-items: center;
    justify-content: center; font-size: 18px; color: white;
}
.option-card .option-icon.rombel { background: linear-gradient(135deg, #f59e0b, #d97706); }
.option-card .option-icon.mapel { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.option-card .option-name { font-size: 13px; font-weight: 600; color: #1f2937; margin: 0; }

/* DATA SECTION */
#dataSection { display: none; }
#dataSection.show { display: block; }

.section-title {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 15px;
}
.section-title h2 {
    font-size: 18px; font-weight: 700; color: #1f2937;
    display: flex; align-items: center; gap: 10px; margin: 0;
}
.section-title h2 i { color: #8b5cf6; }
.badge-count {
    background: #ede9fe; color: #7c3aed;
    padding: 6px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
}

/* DATE TABLE */
.date-table-wrapper {
    background: white; border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}
.date-table {
    width: 100%; border-collapse: collapse;
}
.date-table thead th {
    padding: 14px 16px; background: #f8fafc;
    font-size: 12px; font-weight: 600; color: #6b7280;
    text-transform: uppercase; letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb; text-align: left;
}
.date-table tbody tr {
    cursor: pointer; transition: all 0.2s ease;
    border-bottom: 1px solid #f3f4f6;
}
.date-table tbody tr:hover { background: #f5f3ff; }
.date-table tbody td {
    padding: 12px 16px; font-size: 14px; color: #374151;
}
.stat-badge {
    display: inline-block; padding: 2px 8px; border-radius: 6px;
    font-size: 11px; font-weight: 600; margin-right: 4px;
}
.stat-badge.h { background: #d1fae5; color: #059669; }
.stat-badge.s { background: #e0e7ff; color: #4338ca; }
.stat-badge.i { background: #fef3c7; color: #d97706; }
.stat-badge.a { background: #fee2e2; color: #dc2626; }
.stat-badge.d { background: #dbeafe; color: #1d4ed8; }
.stat-badge.b { background: #fce7f3; color: #be185d; }
.hari-badge {
    display: inline-block; padding: 3px 10px; border-radius: 6px;
    font-size: 12px; font-weight: 600;
    background: #ede9fe; color: #7c3aed;
}

/* DETAIL PANEL */
.detail-panel {
    display: none; background: #faf5ff; border-bottom: 2px solid #ede9fe;
}
.detail-panel.show { display: table-row; }
.detail-panel td { padding: 0 !important; }
.detail-inner { padding: 16px 20px; }
.detail-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 12px;
}
.detail-header h4 { font-size: 14px; font-weight: 700; color: #4c1d95; margin: 0; }
.btn-edit-mapel {
    padding: 6px 14px; border-radius: 8px; border: none;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white; font-size: 12px; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
}
.btn-edit-mapel:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(245,158,11,0.3); }

.detail-table {
    width: 100%; border-collapse: collapse; background: white;
    border-radius: 8px; overflow: hidden;
}
.detail-table th {
    padding: 10px 14px; background: #ede9fe; font-size: 11px;
    font-weight: 600; color: #6b7280; text-transform: uppercase;
    text-align: left;
}
.detail-table td {
    padding: 10px 14px; font-size: 13px; color: #374151;
    border-bottom: 1px solid #f3f4f6;
}
.presensi-status {
    display: inline-block; padding: 4px 12px; border-radius: 6px;
    font-size: 12px; font-weight: 700;
}
.presensi-status.H { background: #d1fae5; color: #059669; }
.presensi-status.S { background: #e0e7ff; color: #4338ca; }
.presensi-status.I { background: #fef3c7; color: #d97706; }
.presensi-status.A { background: #fee2e2; color: #dc2626; }
.presensi-status.D { background: #dbeafe; color: #1d4ed8; }
.presensi-status.B { background: #fce7f3; color: #be185d; }
.btn-edit-sm {
    padding: 4px 10px; border-radius: 6px; border: none;
    background: #8b5cf6; color: white; font-size: 11px;
    font-weight: 600; cursor: pointer; transition: all 0.2s;
}
.btn-edit-sm:hover { background: #7c3aed; }

/* EDIT MODAL */
.edit-modal-body { padding: 24px; }
.edit-form-group { margin-bottom: 16px; }
.edit-form-group label {
    display: block; font-size: 13px; font-weight: 600;
    color: #374151; margin-bottom: 6px;
}
.edit-form-group select, .edit-form-group input {
    width: 100%; padding: 10px 14px; border: 2px solid #e5e7eb;
    border-radius: 8px; font-size: 14px;
    transition: border-color 0.2s;
}
.edit-form-group select:focus, .edit-form-group input:focus {
    outline: none; border-color: #8b5cf6;
}
.edit-modal-footer {
    padding: 16px 24px; border-top: 1px solid #e5e7eb;
    display: flex; gap: 10px; justify-content: flex-end;
}
.btn-cancel {
    padding: 10px 20px; border-radius: 8px; border: 1px solid #e5e7eb;
    background: white; font-size: 13px; font-weight: 600;
    cursor: pointer;
}
.btn-save {
    padding: 10px 20px; border-radius: 8px; border: none;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all 0.2s;
}
.btn-save:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(139,92,246,0.3); }

/* CLICKABLE MAPEL CARD */
.mapel-current-card {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 18px; border: 2px solid #e5e7eb;
    border-radius: 12px; cursor: pointer;
    transition: all 0.3s ease; background: #f9fafb;
}
.mapel-current-card:hover {
    border-color: #f59e0b; background: #fffbeb;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245,158,11,0.15);
}
.mapel-current-card .mapel-card-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 16px; flex-shrink: 0;
}
.mapel-current-card .mapel-card-info { flex: 1; }
.mapel-current-card .mapel-card-name {
    font-size: 15px; font-weight: 700; color: #1f2937; margin: 0;
}
.mapel-current-card .mapel-card-hint {
    font-size: 11px; color: #9ca3af; margin: 2px 0 0 0;
}
.mapel-current-card .mapel-card-arrow {
    color: #9ca3af; font-size: 16px;
}

/* METHOD SELECTION */
.method-section {
    margin-bottom: 25px;
}
.method-section h3 {
    font-size: 16px; font-weight: 700; color: #1f2937;
    margin: 0 0 15px 0;
    display: flex; align-items: center; gap: 8px;
}
.method-section h3 i { color: #8b5cf6; }
.method-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}
@media (max-width: 768px) { .method-grid { grid-template-columns: 1fr; } }
.method-card {
    background: white;
    padding: 24px 20px;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 2px solid #e5e7eb;
    cursor: pointer;
    text-align: center;
    transition: all 0.3s ease;
}
.method-card:hover {
    border-color: #8b5cf6;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(139,92,246,0.2);
}
.method-card.active {
    border-color: #8b5cf6;
    background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
}
.method-card .method-icon {
    width: 56px; height: 56px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: white; margin: 0 auto 14px;
}
.method-card .method-icon.mapel-bg { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.method-card .method-icon.tanggal-bg { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.method-card .method-icon.minggu-bg { background: linear-gradient(135deg, #10b981, #059669); }
.method-card .method-title { font-size: 14px; font-weight: 700; color: #1f2937; margin: 0 0 4px 0; }
.method-card .method-desc { font-size: 12px; color: #6b7280; margin: 0; }
.method-card .method-badge {
    display: inline-block; padding: 3px 10px; border-radius: 6px;
    font-size: 10px; font-weight: 600; margin-top: 10px;
    background: #fef3c7; color: #92400e;
}

/* TANGGAL SELECTOR */
.tanggal-selector-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 25px;
    display: none;
}
@media (max-width: 768px) { .tanggal-selector-row { grid-template-columns: 1fr; } }
.tanggal-selector-row .selector-card { cursor: pointer; }
.tanggal-input-wrapper {
    position: relative;
}
.tanggal-input-wrapper input[type="date"] {
    width: 100%; padding: 8px 12px; border: 2px solid #e5e7eb;
    border-radius: 8px; font-size: 14px; font-weight: 600;
    color: #1f2937; background: white;
}
.tanggal-input-wrapper input[type="date"]:focus {
    outline: none; border-color: #3b82f6;
}

/* PER-TANGGAL DATA TABLE */
#dataSectionTanggal { display: none; }
#dataSectionTanggal.show { display: block; }
.jp-table-wrapper {
    background: white; border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow-x: auto;
}
.jp-table {
    width: 100%; border-collapse: collapse;
    min-width: 900px;
}
.jp-table thead th {
    padding: 10px 8px; background: #f8fafc;
    font-size: 11px; font-weight: 600; color: #6b7280;
    text-transform: uppercase; letter-spacing: 0.3px;
    border-bottom: 2px solid #e5e7eb; text-align: center;
    white-space: nowrap;
}
.jp-table thead th:nth-child(1),
.jp-table thead th:nth-child(2),
.jp-table thead th:nth-child(3) { text-align: left; }
.jp-table tbody td {
    padding: 8px; font-size: 13px; color: #374151;
    border-bottom: 1px solid #f3f4f6;
    text-align: center;
}
.jp-table tbody td:nth-child(1),
.jp-table tbody td:nth-child(2),
.jp-table tbody td:nth-child(3) { text-align: left; }
.jp-table tbody tr:hover { background: #f5f3ff; }
.jp-badge {
    display: inline-block; width: 26px; height: 26px; line-height: 26px;
    border-radius: 6px; font-size: 11px; font-weight: 700;
    text-align: center;
}
.jp-badge.H { background: #d1fae5; color: #059669; }
.jp-badge.S { background: #e0e7ff; color: #4338ca; }
.jp-badge.I { background: #fef3c7; color: #d97706; }
.jp-badge.A { background: #fee2e2; color: #dc2626; }
.jp-badge.D { background: #dbeafe; color: #1d4ed8; }
.jp-badge.B { background: #fce7f3; color: #be185d; }
.jp-badge.empty { background: #f3f4f6; color: #d1d5db; }
.persen-badge {
    display: inline-block; padding: 3px 10px; border-radius: 6px;
    font-size: 12px; font-weight: 700;
}
.persen-badge.high { background: #d1fae5; color: #059669; }
.persen-badge.mid { background: #fef3c7; color: #d97706; }
.persen-badge.low { background: #fee2e2; color: #dc2626; }
.persen-badge.none { background: #f3f4f6; color: #9ca3af; }

/* MINGGU SELECTOR */
.minggu-selector-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 25px;
    display: none;
}
@media (max-width: 768px) { .minggu-selector-row { grid-template-columns: 1fr; } }
.minggu-selector-row .selector-card { cursor: pointer; }
.week-select-wrapper {
    position: relative;
}
.week-select-wrapper select {
    width: 100%; padding: 10px 12px; border: 2px solid #e5e7eb;
    border-radius: 8px; font-size: 14px; font-weight: 600;
    color: #1f2937; background: white;
    cursor: pointer;
}
.week-select-wrapper select:focus {
    outline: none; border-color: #3b82f6;
}

/* PER-MINGGU DATA TABLE */
#dataSectionMinggu { display: none; }
#dataSectionMinggu.show { display: block; }
.minggu-table-wrapper {
    background: white; border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow-x: auto;
}
.minggu-table {
    width: 100%; border-collapse: collapse;
    min-width: 1200px;
}
.minggu-table thead th {
    padding: 10px 6px; background: #f8fafc;
    font-size: 10px; font-weight: 600; color: #6b7280;
    text-transform: uppercase; letter-spacing: 0.3px;
    border-bottom: 2px solid #e5e7eb; text-align: center;
    white-space: nowrap;
}
.minggu-table thead th.date-header {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white; font-size: 11px;
}
.minggu-table thead th:nth-child(1),
.minggu-table thead th:nth-child(2),
.minggu-table thead th:nth-child(3) { text-align: left; }
.minggu-table tbody td {
    padding: 6px; font-size: 12px; color: #374151;
    border-bottom: 1px solid #f3f4f6;
    text-align: center;
}
.minggu-table tbody td:nth-child(1),
.minggu-table tbody td:nth-child(2),
.minggu-table tbody td:nth-child(3) { text-align: left; }
.minggu-table tbody tr:hover { background: #f5f3ff; }

/* PRINT BUTTON */
.print-btn {
    padding: 8px 16px; background: linear-gradient(135deg, #10b981, #059669);
    color: white; border: none; border-radius: 8px;
    font-size: 14px; font-weight: 600; cursor: pointer;
    transition: all 0.3s ease;
}
.print-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4); }
.print-btn i { margin-right: 6px; }

/* PRINT STYLES - A4 PORTRAIT */
@media print {
    @page { size: A4 portrait; margin: 10mm 12mm; }
    body * { visibility: hidden; }
    #dataSectionTanggal, #dataSectionTanggal * { visibility: visible; }
    #dataSectionTanggal {
        position: absolute; left: 0; top: 0; width: 100%;
        background: white;
    }
    .sidebar, .navbar, .print-btn, .badge-count { display: none !important; }
    .section-title { border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 12px; }
    .section-title h2 { font-size: 16px; color: #000; }
    .jp-table { border-collapse: collapse; width: 100%; font-size: 8px; }
    .jp-table th, .jp-table td { border: 1px solid #000 !important; padding: 3px 2px !important; }
    .jp-table th { background: #f0f0f0 !important; color: #000 !important; font-weight: bold; font-size: 8px; }
    .jp-table th:nth-child(1) { width: 3%; } /* No */
    .jp-table th:nth-child(2) { width: 8%; } /* NISN */
    .jp-table th:nth-child(3) { width: 20%; } /* Nama */
    .jp-table th:nth-child(4),
    .jp-table th:nth-child(5),
    .jp-table th:nth-child(6),
    .jp-table th:nth-child(7),
    .jp-table th:nth-child(8),
    .jp-table th:nth-child(9),
    .jp-table th:nth-child(10),
    .jp-table th:nth-child(11),
    .jp-table th:nth-child(12),
    .jp-table th:nth-child(13) { width: 4%; } /* JP 1-10 */
    .jp-table th:nth-child(14) { width: 7%; } /* % Hadir */
    .jp-badge { padding: 1px 3px; border-radius: 2px; font-size: 7px; font-weight: bold; }
    .jp-badge.H { background: #d1fae5 !important; color: #065f46 !important; }
    .jp-badge.S { background: #dbeafe !important; color: #1e40af !important; }
    .jp-badge.I { background: #fef3c7 !important; color: #92400e !important; }
    .jp-badge.A { background: #fee2e2 !important; color: #991b1b !important; }
    .jp-badge.D { background: #fed7aa !important; color: #9a3412 !important; }
    .jp-badge.B { background: #e9d5ff !important; color: #6b21a8 !important; }
    .jp-badge.empty { background: #f3f4f6 !important; color: #9ca3af !important; }
    .persen-badge { padding: 2px 4px; border-radius: 3px; font-size: 7px; font-weight: bold; }
    .persen-badge.high { background: #d1fae5 !important; color: #065f46 !important; }
    .persen-badge.mid { background: #fef3c7 !important; color: #92400e !important; }
    .persen-badge.low { background: #fee2e2 !important; color: #991b1b !important; }
    .persen-badge.none { background: #f3f4f6 !important; color: #9ca3af !important; }
    #printHeader { display: block !important; }
    #printHeader h3 { font-size: 14px; margin: 0; }
    #printHeader h4 { font-size: 12px; margin: 3px 0; }
    #printHeader table { font-size: 9px; margin-top: 8px; }
}

/* STACKED MODAL (higher z-index) */
.custom-modal-overlay.stacked { z-index: 10001; }

/* LOADING */
.loading-spinner {
    display: flex; justify-content: center; align-items: center; padding: 40px;
}
.spinner {
    width: 36px; height: 36px; border: 4px solid #e5e7eb;
    border-top-color: #8b5cf6; border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* EMPTY STATE */
.empty-state {
    text-align: center; padding: 40px 20px; color: #9ca3af;
}
.empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }
.empty-state h3 { font-size: 16px; color: #6b7280; margin: 0 0 6px 0; }
.empty-state p { font-size: 13px; margin: 0; }

/* TOAST */
.toast-notification {
    position: fixed; bottom: 20px; right: 20px;
    padding: 14px 24px; border-radius: 10px; color: white;
    font-size: 14px; font-weight: 600; z-index: 99999;
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    transform: translateX(120%); transition: transform 0.3s ease;
}
.toast-notification.show { transform: translateX(0); }
.toast-notification.success { background: linear-gradient(135deg, #10b981, #059669); }
.toast-notification.error { background: linear-gradient(135deg, #ef4444, #dc2626); }

/* RESPONSIVE */
@media (max-width: 768px) {
    .cek-presensi-header { padding: 20px 15px; }
    .cek-presensi-header .header-icon-large { width: 60px; height: 60px; font-size: 28px; }
    .cek-presensi-header .page-title { font-size: 20px; }
    .selector-row { grid-template-columns: 1fr; }
    .date-table { font-size: 12px; }
    .date-table thead th, .date-table tbody td { padding: 10px 12px; }
    .stat-badge { font-size: 10px; padding: 2px 6px; }
}
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')

    <div class="main-content">
        <div class="cek-presensi-page">
            <!-- HEADER -->
            <div class="cek-presensi-header">
                <div class="header-icon-large">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h1 class="page-title">Cek Presensi Siswa</h1>
                <p class="page-subtitle">{{ $tahunPelajaran }} - Semester {{ ucfirst($semesterAktif) }}</p>
            </div>

            <!-- METHOD SELECTION -->
            <div class="method-section">
                <h3><i class="fas fa-th-large"></i> Pilih Jenis Cek Presensi</h3>
                <div class="method-grid">
                    <div class="method-card" id="methodMapel" onclick="selectMethod('mapel')">
                        <div class="method-icon mapel-bg"><i class="fas fa-book"></i></div>
                        <p class="method-title">Per Rombel per Mapel</p>
                        <p class="method-desc">Cek presensi berdasarkan rombel dan mata pelajaran</p>
                    </div>
                    <div class="method-card" id="methodTanggal" onclick="selectMethod('tanggal')">
                        <div class="method-icon tanggal-bg"><i class="fas fa-calendar-day"></i></div>
                        <p class="method-title">Per Rombel per Tanggal</p>
                        <p class="method-desc">Cek presensi berdasarkan rombel dan tanggal</p>
                    </div>
                    <div class="method-card" id="methodMinggu" onclick="selectMethod('minggu')">
                        <div class="method-icon minggu-bg"><i class="fas fa-calendar-week"></i></div>
                        <p class="method-title">Per Rombel per Minggu</p>
                        <p class="method-desc">Cek presensi berdasarkan rombel dan minggu</p>
                    </div>
                </div>
            </div>

            <!-- SELECTOR CARDS (hidden until method chosen) -->
            <div class="selector-row" id="selectorRow" style="display:none;">
                <div class="selector-card" id="rombelCard" onclick="openRombelModal()">
                    <div class="card-icon rombel-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <p class="card-value" id="rombelValue" style="display:none;"></p>
                        <p class="card-placeholder" id="rombelPlaceholder">Pilih Rombel...</p>
                        <span class="card-label">Rombel</span>
                    </div>
                </div>
                <div class="selector-card disabled" id="mapelCard" onclick="openMapelModal()">
                    <div class="card-icon mapel-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="card-content">
                        <p class="card-value" id="mapelValue" style="display:none;"></p>
                        <p class="card-placeholder" id="mapelPlaceholder">Pilih Mata Pelajaran...</p>
                        <span class="card-label">Mata Pelajaran</span>
                    </div>
                </div>
            </div>

            <!-- MINGGU SELECTOR (for per-minggu method) -->
            <div class="minggu-selector-row" id="mingguSelectorRow">
                <div class="selector-card" id="rombelCardMinggu" onclick="openRombelModalMinggu()">
                    <div class="card-icon rombel-icon"><i class="fas fa-users"></i></div>
                    <div class="card-content">
                        <p class="card-value" id="rombelValueMinggu" style="display:none;"></p>
                        <p class="card-placeholder" id="rombelPlaceholderMinggu">Pilih Rombel...</p>
                        <span class="card-label">Rombel</span>
                    </div>
                </div>
                <div class="selector-card disabled" id="weekCard">
                    <div class="card-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="card-content">
                        <p class="card-value" id="weekValue" style="display:none;"></p>
                        <p class="card-placeholder" id="weekPlaceholder">Pilih Minggu...</p>
                        <span class="card-label">Minggu</span>
                        <div class="week-select-wrapper" id="weekSelectWrapper" style="display:none; margin-top:8px;">
                            <select id="weekSelect" onchange="onWeekSelected()">
                                <option value="">-- Pilih Minggu --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TANGGAL SELECTOR (for per-tanggal method) -->
            <div class="tanggal-selector-row" id="tanggalSelectorRow">
                <div class="selector-card" id="rombelCardTanggal" onclick="openRombelModalTanggal()">
                    <div class="card-icon rombel-icon"><i class="fas fa-users"></i></div>
                    <div class="card-content">
                        <p class="card-value" id="rombelValueTanggal" style="display:none;"></p>
                        <p class="card-placeholder" id="rombelPlaceholderTanggal">Pilih Rombel...</p>
                        <span class="card-label">Rombel</span>
                    </div>
                </div>
                <div class="selector-card disabled" id="tanggalCard">
                    <div class="card-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="card-content">
                        <p class="card-value" id="tanggalValue" style="display:none;"></p>
                        <p class="card-placeholder" id="tanggalPlaceholder">Pilih Tanggal...</p>
                        <span class="card-label">Tanggal</span>
                        <div class="tanggal-input-wrapper" id="tanggalInputWrapper" style="display:none; margin-top:8px;">
                            <input type="date" id="tanggalInput" onchange="onTanggalSelected()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- DATA SECTION (per mapel) -->
            <div id="dataSection">
                <div class="section-title">
                    <h2><i class="fas fa-calendar-alt"></i> Riwayat Presensi</h2>
                    <span class="badge-count" id="dateCount">0 Tanggal</span>
                </div>
                <div class="date-table-wrapper">
                    <table class="date-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Hari</th>
                                <th>Guru</th>
                                <th>Siswa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="dateTableBody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DATA SECTION (per tanggal - JP table) -->
            <div id="dataSectionTanggal">
                <div class="section-title">
                    <h2><i class="fas fa-calendar-day"></i> Presensi per Jam Pelajaran</h2>
                    <div>
                        <span class="badge-count" id="jpSiswaCount">0 Siswa</span>
                        <button class="print-btn" onclick="printPerTanggal()" style="margin-left:10px;">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>
                
                <!-- PRINT HEADER (hidden on screen, visible in print) -->
                <div id="printHeader" style="display:none; margin-bottom:20px;">
                    <h3 style="text-align:center; margin:0; font-size:16px;">DAFTAR PRESENSI SISWA</h3>
                    <h4 style="text-align:center; margin:5px 0; font-size:14px;">SMAN 1 SEPUTIH RAMAN</h4>
                    <div style="margin-top:10px; font-size:11px;">
                        <table style="width:100%; border:none;">
                            <tr>
                                <td style="width:20%; border:none;">Rombel</td>
                                <td style="width:1%; border:none;">:</td>
                                <td style="border:none;"><strong id="printRombel"></strong></td>
                                <td style="width:20%; border:none; text-align:right;">Tanggal</td>
                                <td style="width:1%; border:none;">:</td>
                                <td style="width:25%; border:none;"><strong id="printTanggal"></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="jp-table-wrapper">
                    <table class="jp-table">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">NISN</th>
                                <th rowspan="2">Nama Siswa</th>
                                <th colspan="10">Jam Pelajaran</th>
                                <th rowspan="2">% Hadir</th>
                            </tr>
                            <tr>
                                <th>1</th><th>2</th><th>3</th><th>4</th><th>5</th>
                                <th>6</th><th>7</th><th>8</th><th>9</th><th>10</th>
                            </tr>
                        </thead>
                        <tbody id="jpTableBody">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- DATA SECTION (per minggu - wide table with multiple dates) -->
            <div id="dataSectionMinggu">
                <div class="section-title">
                    <h2><i class="fas fa-calendar-week"></i> Presensi per Minggu</h2>
                    <span class="badge-count" id="mingguSiswaCount">0 Siswa</span>
                </div>
                <div class="minggu-table-wrapper">
                    <table class="minggu-table" id="mingguTable">
                        <thead id="mingguTableHeader">
                        </thead>
                        <tbody id="mingguTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROMBEL MODAL -->
<div class="custom-modal-overlay" id="rombelModal">
    <div class="custom-modal">
        <div class="modal-header">
            <h3><i class="fas fa-users" style="color:#f59e0b;margin-right:8px;"></i> Pilih Rombel</h3>
            <button class="close-btn" onclick="closeModal('rombelModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-option-grid">
                @foreach($rombelList as $rombel)
                    <div class="option-card" onclick="selectRombel({{ $rombel->id }}, '{{ addslashes($rombel->nama_rombel) }}')">
                        <div class="option-icon rombel">
                            <i class="fas fa-users"></i>
                        </div>
                        <p class="option-name">{{ $rombel->nama_rombel }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- MAPEL MODAL -->
<div class="custom-modal-overlay" id="mapelModal">
    <div class="custom-modal">
        <div class="modal-header">
            <h3><i class="fas fa-book" style="color:#8b5cf6;margin-right:8px;"></i> Pilih Mata Pelajaran</h3>
            <button class="close-btn" onclick="closeModal('mapelModal')">&times;</button>
        </div>
        <div class="modal-body" id="mapelModalBody">
            <div class="loading-spinner"><div class="spinner"></div></div>
        </div>
    </div>
</div>

<!-- EDIT PRESENSI MODAL -->
<div class="custom-modal-overlay" id="editPresensiModal">
    <div class="custom-modal" style="max-width:400px;">
        <div class="modal-header">
            <h3><i class="fas fa-edit" style="color:#8b5cf6;margin-right:8px;"></i> Edit Presensi</h3>
            <button class="close-btn" onclick="closeModal('editPresensiModal')">&times;</button>
        </div>
        <div class="edit-modal-body">
            <input type="hidden" id="editPresensiId">
            <div class="edit-form-group">
                <label>Nama Siswa</label>
                <input type="text" id="editNamaSiswa" readonly style="background:#f9fafb;">
            </div>
            <div class="edit-form-group">
                <label>Status Presensi</label>
                <select id="editPresensiStatus">
                    <option value="H">H - Hadir</option>
                    <option value="S">S - Sakit</option>
                    <option value="I">I - Izin</option>
                    <option value="A">A - Alfa</option>
                    <option value="D">D - Dispensasi</option>
                    <option value="B">B - Bolos</option>
                </select>
            </div>
        </div>
        <div class="edit-modal-footer">
            <button class="btn-cancel" onclick="closeModal('editPresensiModal')">Batal</button>
            <button class="btn-save" onclick="savePresensi()">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </div>
</div>

<!-- EDIT MAPEL MODAL -->
<div class="custom-modal-overlay" id="editMapelModal">
    <div class="custom-modal" style="max-width:450px;">
        <div class="modal-header">
            <h3><i class="fas fa-book" style="color:#f59e0b;margin-right:8px;"></i> Edit Mata Pelajaran</h3>
            <button class="close-btn" onclick="closeModal('editMapelModal')">&times;</button>
        </div>
        <div class="edit-modal-body">
            <input type="hidden" id="editMapelTanggal">
            <input type="hidden" id="editMapelOld">
            <div class="edit-form-group">
                <label>Tanggal</label>
                <input type="text" id="editMapelDate" readonly style="background:#f9fafb;">
            </div>
            <div class="edit-form-group">
                <label>Mapel Saat Ini <small style="color:#9ca3af;">(klik untuk mengganti)</small></label>
                <div class="mapel-current-card" onclick="openPilihMapelBaru()">
                    <div class="mapel-card-icon"><i class="fas fa-book"></i></div>
                    <div class="mapel-card-info">
                        <p class="mapel-card-name" id="editMapelCurrentName">-</p>
                        <p class="mapel-card-hint">Klik untuk pilih mapel baru</p>
                    </div>
                    <i class="fas fa-chevron-right mapel-card-arrow"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PILIH MAPEL BARU MODAL (stacked on top) -->
<div class="custom-modal-overlay stacked" id="pilihMapelBaruModal">
    <div class="custom-modal" style="max-width:600px;">
        <div class="modal-header">
            <h3><i class="fas fa-exchange-alt" style="color:#f59e0b;margin-right:8px;"></i> Pilih Mapel Baru</h3>
            <button class="close-btn" onclick="closeModal('pilihMapelBaruModal')">&times;</button>
        </div>
        <div class="modal-body" id="pilihMapelBaruBody">
            <div class="loading-spinner"><div class="spinner"></div></div>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast-notification" id="toast"></div>

@endsection

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';
let selectedMethod = null;
let selectedRombel = null;
let selectedMapel = null;
let expandedDate = null;
let selectedRombelTanggal = null;
let selectedTanggal = null;
let hariLiburList = [];
let selectedRombelMinggu = null;
let selectedWeekStart = null;
let selectedWeekEnd = null;

function selectMethod(method) {
    selectedMethod = method;

    // Highlight active card
    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('active'));
    let activeMethod = 'methodMapel';
    if (method === 'tanggal') activeMethod = 'methodTanggal';
    else if (method === 'minggu') activeMethod = 'methodMinggu';
    document.getElementById(activeMethod).classList.add('active');

    // Hide all sections first
    document.getElementById('selectorRow').style.display = 'none';
    document.getElementById('tanggalSelectorRow').style.display = 'none';
    document.getElementById('mingguSelectorRow').style.display = 'none';
    document.getElementById('dataSection').classList.remove('show');
    document.getElementById('dataSectionTanggal').classList.remove('show');
    document.getElementById('dataSectionMinggu').classList.remove('show');

    if (method === 'mapel') {
        document.getElementById('selectorRow').style.display = 'grid';
        // Reset mapel selections
        selectedRombel = null;
        selectedMapel = null;
        document.getElementById('rombelCard').classList.remove('selected');
        document.getElementById('rombelPlaceholder').style.display = 'block';
        document.getElementById('rombelValue').style.display = 'none';
        document.getElementById('mapelCard').classList.add('disabled');
        document.getElementById('mapelCard').classList.remove('selected');
        document.getElementById('mapelPlaceholder').style.display = 'block';
        document.getElementById('mapelPlaceholder').textContent = 'Pilih Mata Pelajaran...';
        document.getElementById('mapelValue').style.display = 'none';
    } else if (method === 'tanggal') {
        document.getElementById('tanggalSelectorRow').style.display = 'grid';
        // Reset tanggal selections
        selectedRombelTanggal = null;
        selectedTanggal = null;
        document.getElementById('rombelCardTanggal').classList.remove('selected');
        document.getElementById('rombelPlaceholderTanggal').style.display = 'block';
        document.getElementById('rombelValueTanggal').style.display = 'none';
        document.getElementById('tanggalCard').classList.add('disabled');
        document.getElementById('tanggalPlaceholder').style.display = 'block';
        document.getElementById('tanggalValue').style.display = 'none';
        document.getElementById('tanggalInputWrapper').style.display = 'none';
        document.getElementById('tanggalInput').value = '';
    } else if (method === 'minggu') {
        document.getElementById('mingguSelectorRow').style.display = 'grid';
        // Reset minggu selections
        selectedRombelMinggu = null;
        selectedWeekStart = null;
        selectedWeekEnd = null;
        document.getElementById('rombelCardMinggu').classList.remove('selected');
        document.getElementById('rombelPlaceholderMinggu').style.display = 'block';
        document.getElementById('rombelValueMinggu').style.display = 'none';
        document.getElementById('weekCard').classList.add('disabled');
        document.getElementById('weekPlaceholder').style.display = 'block';
        document.getElementById('weekValue').style.display = 'none';
        document.getElementById('weekSelectWrapper').style.display = 'none';
        document.getElementById('weekSelect').innerHTML = '<option value="">-- Pilih Minggu --</option>';
    }
}

const hariNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

function openRombelModal() {
    document.getElementById('rombelModal').classList.add('show');
}

function openMapelModal() {
    if (!selectedRombel) {
        showToast('Pilih rombel terlebih dahulu!', 'error');
        return;
    }
    document.getElementById('mapelModal').classList.add('show');
    loadMapelOptions();
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

// Close modal on overlay click
document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});

function selectRombel(id, name) {
    selectedRombel = { id, name };
    selectedMapel = null;

    document.getElementById('rombelCard').classList.add('selected');
    document.getElementById('rombelPlaceholder').style.display = 'none';
    document.getElementById('rombelValue').textContent = name;
    document.getElementById('rombelValue').style.display = 'block';

    // Reset mapel
    document.getElementById('mapelCard').classList.remove('selected', 'disabled');
    document.getElementById('mapelPlaceholder').style.display = 'block';
    document.getElementById('mapelPlaceholder').textContent = 'Pilih Mata Pelajaran...';
    document.getElementById('mapelValue').style.display = 'none';

    // Hide data
    document.getElementById('dataSection').classList.remove('show');

    closeModal('rombelModal');
}

function loadMapelOptions() {
    const body = document.getElementById('mapelModalBody');
    body.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';

    fetch(`{{ route("admin.cek-presensi.mapel-list") }}?id_rombel=${selectedRombel.id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                let html = '<div class="modal-option-grid">';
                data.data.forEach(m => {
                    html += `
                        <div class="option-card" onclick="selectMapel('${escapeHtml(m.nama_mapel)}')">
                            <div class="option-icon mapel">
                                <i class="fas fa-book"></i>
                            </div>
                            <p class="option-name">${escapeHtml(m.nama_mapel)}</p>
                        </div>`;
                });
                html += '</div>';
                body.innerHTML = html;
            } else {
                body.innerHTML = `<div class="empty-state">
                    <i class="fas fa-book-open"></i>
                    <h3>Tidak Ada Mapel</h3>
                    <p>Tidak ditemukan mata pelajaran untuk rombel ini.</p>
                </div>`;
            }
        })
        .catch(() => {
            body.innerHTML = `<div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i><h3>Error</h3><p>Gagal memuat data.</p>
            </div>`;
        });
}

function selectMapel(name) {
    selectedMapel = { name };

    document.getElementById('mapelCard').classList.add('selected');
    document.getElementById('mapelPlaceholder').style.display = 'none';
    document.getElementById('mapelValue').textContent = name;
    document.getElementById('mapelValue').style.display = 'block';

    closeModal('mapelModal');
    loadData();
}

function loadData() {
    const section = document.getElementById('dataSection');
    const tbody = document.getElementById('dateTableBody');
    section.classList.add('show');
    tbody.innerHTML = '<tr><td colspan="5"><div class="loading-spinner"><div class="spinner"></div></div></td></tr>';

    fetch(`{{ route("admin.cek-presensi.data") }}?id_rombel=${selectedRombel.id}&mapel=${encodeURIComponent(selectedMapel.name)}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                document.getElementById('dateCount').textContent = data.data.length + ' Tanggal';
                let html = '';
                data.data.forEach(row => {
                    const d = new Date(row.tanggal_presensi);
                    const hari = hariNames[d.getDay()];
                    const tglFormatted = d.toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'});
                    const rowId = 'row-' + row.tanggal_presensi;
                    const detailId = 'detail-' + row.tanggal_presensi;

                    html += `<tr id="${rowId}" onclick="toggleDetail('${row.tanggal_presensi}')">
                        <td><strong>${tglFormatted}</strong></td>
                        <td><span class="hari-badge">${hari}</span></td>
                        <td>${escapeHtml(row.guru_pengajar || '-')}</td>
                        <td>${row.total_siswa}</td>
                        <td>
                            ${row.hadir > 0 ? `<span class="stat-badge h">H:${row.hadir}</span>` : ''}
                            ${row.sakit > 0 ? `<span class="stat-badge s">S:${row.sakit}</span>` : ''}
                            ${row.izin > 0 ? `<span class="stat-badge i">I:${row.izin}</span>` : ''}
                            ${row.alfa > 0 ? `<span class="stat-badge a">A:${row.alfa}</span>` : ''}
                            ${row.dispen > 0 ? `<span class="stat-badge d">D:${row.dispen}</span>` : ''}
                            ${row.bolos > 0 ? `<span class="stat-badge b">B:${row.bolos}</span>` : ''}
                        </td>
                    </tr>
                    <tr id="${detailId}" class="detail-panel">
                        <td colspan="5">
                            <div class="detail-inner" id="detail-inner-${row.tanggal_presensi}">
                                <div class="loading-spinner"><div class="spinner"></div></div>
                            </div>
                        </td>
                    </tr>`;
                });
                tbody.innerHTML = html;
            } else {
                tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state">
                    <i class="fas fa-clipboard-list"></i><h3>Tidak Ada Data</h3>
                    <p>Belum ada presensi untuk rombel dan mapel ini.</p>
                </div></td></tr>`;
                document.getElementById('dateCount').textContent = '0 Tanggal';
            }
        })
        .catch(() => {
            tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i><h3>Error</h3>
                <p>Gagal memuat data presensi.</p>
            </div></td></tr>`;
        });
}

function toggleDetail(tanggal) {
    const detailRow = document.getElementById('detail-' + tanggal);
    if (expandedDate === tanggal) {
        detailRow.classList.remove('show');
        expandedDate = null;
        return;
    }

    // Collapse previous
    if (expandedDate) {
        const prev = document.getElementById('detail-' + expandedDate);
        if (prev) prev.classList.remove('show');
    }

    expandedDate = tanggal;
    detailRow.classList.add('show');
    loadDetail(tanggal);
}

function loadDetail(tanggal) {
    const container = document.getElementById('detail-inner-' + tanggal);
    container.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';

    fetch(`{{ route("admin.cek-presensi.detail") }}?id_rombel=${selectedRombel.id}&mapel=${encodeURIComponent(selectedMapel.name)}&tanggal=${tanggal}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                const d = new Date(tanggal);
                const tglFormatted = d.toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'});
                let html = `
                    <div class="detail-header">
                        <h4>Detail Presensi — ${tglFormatted}</h4>
                        <button class="btn-edit-mapel" onclick="event.stopPropagation(); openEditMapelModal('${tanggal}', '${escapeAttr(selectedMapel.name)}')">
                            <i class="fas fa-edit"></i> Edit Mapel
                        </button>
                    </div>
                    <table class="detail-table">
                        <thead><tr>
                            <th>No</th><th>Nama Siswa</th><th>NISN</th><th>Status</th><th>Aksi</th>
                        </tr></thead><tbody>`;

                data.data.forEach((s, i) => {
                    html += `<tr>
                        <td>${i+1}</td>
                        <td>${escapeHtml(s.nama_siswa || s.nisn)}</td>
                        <td>${s.nisn}</td>
                        <td><span class="presensi-status ${s.presensi}">${s.presensi}</span></td>
                        <td><button class="btn-edit-sm" onclick="event.stopPropagation(); openEditPresensi(${s.id}, '${escapeAttr(s.nama_siswa || s.nisn)}', '${s.presensi}')">
                            <i class="fas fa-edit"></i> Edit
                        </button></td>
                    </tr>`;
                });

                html += '</tbody></table>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="empty-state"><i class="fas fa-info-circle"></i><h3>Tidak ada data</h3></div>';
            }
        })
        .catch(() => {
            container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Error</h3></div>';
        });
}

function openEditPresensi(id, nama, status) {
    document.getElementById('editPresensiId').value = id;
    document.getElementById('editNamaSiswa').value = nama;
    document.getElementById('editPresensiStatus').value = status;
    document.getElementById('editPresensiModal').classList.add('show');
}

function savePresensi() {
    const id = document.getElementById('editPresensiId').value;
    const presensi = document.getElementById('editPresensiStatus').value;

    fetch('{{ route("admin.cek-presensi.update") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ id, presensi })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Presensi berhasil diubah!', 'success');
            closeModal('editPresensiModal');
            // Reload detail and summary
            if (expandedDate) loadDetail(expandedDate);
            loadData();
        } else {
            showToast(data.message || 'Gagal menyimpan', 'error');
        }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
}

function openEditMapelModal(tanggal, currentMapel) {
    const d = new Date(tanggal);
    const tglFormatted = d.toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'});
    document.getElementById('editMapelTanggal').value = tanggal;
    document.getElementById('editMapelOld').value = currentMapel;
    document.getElementById('editMapelDate').value = tglFormatted;
    document.getElementById('editMapelCurrentName').textContent = currentMapel;
    document.getElementById('editMapelModal').classList.add('show');
}

function openPilihMapelBaru() {
    const body = document.getElementById('pilihMapelBaruBody');
    body.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';
    document.getElementById('pilihMapelBaruModal').classList.add('show');

    fetch(`{{ route("admin.cek-presensi.all-mapel") }}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                let html = '<div class="modal-option-grid">';
                data.data.forEach(m => {
                    html += `
                        <div class="option-card" onclick="pilihMapelBaruDanSimpan('${escapeAttr(m.nama_mapel)}')">
                            <div class="option-icon mapel">
                                <i class="fas fa-book"></i>
                            </div>
                            <p class="option-name">${escapeHtml(m.nama_mapel)}</p>
                        </div>`;
                });
                html += '</div>';
                body.innerHTML = html;
            } else {
                body.innerHTML = '<div class="empty-state"><i class="fas fa-book-open"></i><h3>Tidak ada mapel</h3></div>';
            }
        })
        .catch(() => {
            body.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Error</h3></div>';
        });
}

function pilihMapelBaruDanSimpan(newMapel) {
    const tanggal = document.getElementById('editMapelTanggal').value;
    const oldMapel = document.getElementById('editMapelOld').value;

    // Close both modals immediately
    closeModal('pilihMapelBaruModal');
    closeModal('editMapelModal');

    fetch('{{ route("admin.cek-presensi.update-mapel") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({
            id_rombel: selectedRombel.id,
            old_mapel: oldMapel,
            new_mapel: newMapel,
            tanggal: tanggal
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(`Mapel berhasil diubah untuk ${data.affected} record`, 'success');
            selectedMapel.name = newMapel;
            document.getElementById('mapelValue').textContent = newMapel;
            loadData();
        } else {
            showToast(data.message || 'Gagal menyimpan', 'error');
        }
    })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
}

function showToast(message, type) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast-notification ' + type + ' show';
    setTimeout(() => toast.classList.remove('show'), 3000);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function escapeAttr(text) {
    if (!text) return '';
    return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

// ==========================================
// PER-TANGGAL METHOD FUNCTIONS
// ==========================================

function openRombelModalTanggal() {
    // Reuse same rombel modal but with tanggal handler
    const modal = document.getElementById('rombelModal');
    // Temporarily override onclick handlers
    const options = modal.querySelectorAll('.option-card');
    options.forEach(opt => {
        const origOnclick = opt.getAttribute('onclick');
        // Extract id and name from selectRombel(id, 'name')
        const match = origOnclick.match(/selectRombel\((\d+),\s*'(.+?)'\)/);
        if (match) {
            opt.setAttribute('data-orig-onclick', origOnclick);
            opt.setAttribute('onclick', `selectRombelTanggal(${match[1]}, '${match[2]}')`);
        }
    });
    modal.classList.add('show');
}

function selectRombelTanggal(id, name) {
    selectedRombelTanggal = { id, name };
    selectedTanggal = null;

    document.getElementById('rombelCardTanggal').classList.add('selected');
    document.getElementById('rombelPlaceholderTanggal').style.display = 'none';
    document.getElementById('rombelValueTanggal').textContent = name;
    document.getElementById('rombelValueTanggal').style.display = 'block';

    // Enable tanggal card
    document.getElementById('tanggalCard').classList.remove('disabled');
    document.getElementById('tanggalPlaceholder').style.display = 'none';
    document.getElementById('tanggalInputWrapper').style.display = 'block';
    document.getElementById('tanggalInput').value = '';
    document.getElementById('tanggalValue').style.display = 'none';
    document.getElementById('dataSectionTanggal').classList.remove('show');

    // Restore original rombel modal onclick handlers
    const modal = document.getElementById('rombelModal');
    modal.querySelectorAll('.option-card[data-orig-onclick]').forEach(opt => {
        opt.setAttribute('onclick', opt.getAttribute('data-orig-onclick'));
        opt.removeAttribute('data-orig-onclick');
    });
    closeModal('rombelModal');

    // Fetch hari libur to set date restrictions
    fetch(`{{ route("admin.cek-presensi.hari-libur") }}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                hariLiburList = data.data || [];
            }
        });
}

function onTanggalSelected() {
    const input = document.getElementById('tanggalInput');
    const val = input.value;
    if (!val) return;

    // Check if it's Saturday (6) or Sunday (0)
    const d = new Date(val);
    const day = d.getDay();
    if (day === 0 || day === 6) {
        showToast('Sabtu dan Minggu bukan hari efektif KBM!', 'error');
        input.value = '';
        return;
    }

    // Check if it's a holiday/non-KBM day
    if (hariLiburList.includes(val)) {
        showToast('Tanggal ini adalah hari libur/non-KBM!', 'error');
        input.value = '';
        return;
    }

    selectedTanggal = val;

    // Update display
    const tglFormatted = d.toLocaleDateString('id-ID', {weekday:'long', day:'2-digit', month:'long', year:'numeric'});
    document.getElementById('tanggalValue').textContent = tglFormatted;
    document.getElementById('tanggalValue').style.display = 'block';
    document.getElementById('tanggalCard').classList.add('selected');

    loadDataPerTanggal();
}

function loadDataPerTanggal() {
    const section = document.getElementById('dataSectionTanggal');
    const tbody = document.getElementById('jpTableBody');
    section.classList.add('show');
    tbody.innerHTML = '<tr><td colspan="14"><div class="loading-spinner"><div class="spinner"></div></div></td></tr>';

    fetch(`{{ route("admin.cek-presensi.data-per-tanggal") }}?id_rombel=${selectedRombelTanggal.id}&tanggal=${selectedTanggal}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                document.getElementById('jpSiswaCount').textContent = data.total_siswa + ' Siswa';
                let html = '';
                data.data.forEach(row => {
                    html += `<tr>
                        <td>${row.no}</td>
                        <td>${escapeHtml(row.nisn)}</td>
                        <td><strong>${escapeHtml(row.nama)}</strong></td>`;

                    for (let jp = 1; jp <= 10; jp++) {
                        const val = row['jp_' + jp];
                        if (val && val !== '-' && val !== '') {
                            html += `<td><span class="jp-badge ${val}">${val}</span></td>`;
                        } else {
                            html += `<td><span class="jp-badge empty">-</span></td>`;
                        }
                    }

                    // Percentage
                    if (row.prosentase !== null) {
                        let pClass = 'none';
                        if (row.prosentase >= 80) pClass = 'high';
                        else if (row.prosentase >= 50) pClass = 'mid';
                        else pClass = 'low';
                        html += `<td><span class="persen-badge ${pClass}">${row.prosentase}%</span></td>`;
                    } else {
                        html += `<td><span class="persen-badge none">-</span></td>`;
                    }

                    html += '</tr>';
                });
                tbody.innerHTML = html;
            } else {
                tbody.innerHTML = `<tr><td colspan="14"><div class="empty-state">
                    <i class="fas fa-clipboard-list"></i><h3>Tidak Ada Data</h3>
                    <p>Belum ada presensi untuk rombel ini pada tanggal tersebut.</p>
                </div></td></tr>`;
                document.getElementById('jpSiswaCount').textContent = '0 Siswa';
            }
        })
        .catch(() => {
            tbody.innerHTML = `<tr><td colspan="14"><div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i><h3>Error</h3>
                <p>Gagal memuat data presensi.</p>
            </div></td></tr>`;
        });
}

// ==========================================
// PER-MINGGU METHOD FUNCTIONS
// ==========================================

function openRombelModalMinggu() {
    const modal = document.getElementById('rombelModal');
    const options = modal.querySelectorAll('.option-card');
    options.forEach(opt => {
        const origOnclick = opt.getAttribute('onclick');
        const match = origOnclick.match(/selectRombel\((\d+),\s*'(.+?)'\)/);
        if (match) {
            opt.setAttribute('data-orig-onclick', origOnclick);
            opt.setAttribute('onclick', `selectRombelMinggu(${match[1]}, '${match[2]}')`);
        }
    });
    modal.classList.add('show');
}

function selectRombelMinggu(id, name) {
    selectedRombelMinggu = { id, name };
    selectedWeekStart = null;
    selectedWeekEnd = null;

    document.getElementById('rombelCardMinggu').classList.add('selected');
    document.getElementById('rombelPlaceholderMinggu').style.display = 'none';
    document.getElementById('rombelValueMinggu').textContent = name;
    document.getElementById('rombelValueMinggu').style.display = 'block';

    // Enable week card
    document.getElementById('weekCard').classList.remove('disabled');
    document.getElementById('weekPlaceholder').style.display = 'none';
    document.getElementById('weekSelectWrapper').style.display = 'block';
    document.getElementById('weekValue').style.display = 'none';
    document.getElementById('dataSectionMinggu').classList.remove('show');

    // Restore original rombel modal onclick handlers
    const modal = document.getElementById('rombelModal');
    modal.querySelectorAll('.option-card[data-orig-onclick]').forEach(opt => {
        opt.setAttribute('onclick', opt.getAttribute('data-orig-onclick'));
        opt.removeAttribute('data-orig-onclick');
    });
    closeModal('rombelModal');

    // Fetch week ranges
    fetch(`{{ route("admin.cek-presensi.week-ranges") }}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                const select = document.getElementById('weekSelect');
                let html = '<option value="">-- Pilih Minggu --</option>';
                data.data.forEach(week => {
                    html += `<option value="${week.start}|${week.end}">${week.label}</option>`;
                });
                select.innerHTML = html;
            } else {
                showToast('Tidak ada minggu yang tersedia', 'error');
            }
        })
        .catch(() => {
            showToast('Gagal memuat daftar minggu', 'error');
        });
}

function onWeekSelected() {
    const select = document.getElementById('weekSelect');
    const val = select.value;
    if (!val) return;

    const [start, end] = val.split('|');
    selectedWeekStart = start;
    selectedWeekEnd = end;

    // Update display
    const selectedText = select.options[select.selectedIndex].text;
    document.getElementById('weekValue').textContent = selectedText;
    document.getElementById('weekValue').style.display = 'block';
    document.getElementById('weekCard').classList.add('selected');

    loadDataPerMinggu();
}

function loadDataPerMinggu() {
    const section = document.getElementById('dataSectionMinggu');
    const tbody = document.getElementById('mingguTableBody');
    const thead = document.getElementById('mingguTableHeader');
    section.classList.add('show');
    tbody.innerHTML = '<tr><td colspan="50"><div class="loading-spinner"><div class="spinner"></div></div></td></tr>';
    thead.innerHTML = '';

    fetch(`{{ route("admin.cek-presensi.data-per-minggu") }}?id_rombel=${selectedRombelMinggu.id}&week_start=${selectedWeekStart}&week_end=${selectedWeekEnd}`)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                document.getElementById('mingguSiswaCount').textContent = data.total_siswa + ' Siswa';
                
                // Build header with dates
                let headerHtml = '<tr><th rowspan="2">No</th><th rowspan="2">NISN</th><th rowspan="2">Nama Siswa</th>';
                data.dates.forEach(date => {
                    const d = new Date(date);
                    const dayName = hariNames[d.getDay()];
                    const formatted = d.toLocaleDateString('id-ID', {day:'2-digit', month:'short'});
                    headerHtml += `<th colspan="10" class="date-header">${dayName}, ${formatted}</th>`;
                });
                headerHtml += '<th rowspan="2">% Hadir</th></tr><tr>';
                data.dates.forEach(() => {
                    for (let jp = 1; jp <= 10; jp++) {
                        headerHtml += `<th>JP${jp}</th>`;
                    }
                });
                headerHtml += '</tr>';
                thead.innerHTML = headerHtml;

                // Build body
                let bodyHtml = '';
                data.data.forEach(row => {
                    bodyHtml += `<tr>
                        <td>${row.no}</td>
                        <td>${escapeHtml(row.nisn)}</td>
                        <td><strong>${escapeHtml(row.nama)}</strong></td>`;
                    
                    data.dates.forEach(date => {
                        for (let jp = 1; jp <= 10; jp++) {
                            const key = `${date}_jp_${jp}`;
                            const val = row[key];
                            if (val && val !== '-' && val !== '') {
                                bodyHtml += `<td><span class="jp-badge ${val}">${val}</span></td>`;
                            } else {
                                bodyHtml += `<td><span class="jp-badge empty">-</span></td>`;
                            }
                        }
                    });

                    // Percentage
                    if (row.prosentase !== null) {
                        let pClass = 'none';
                        if (row.prosentase >= 80) pClass = 'high';
                        else if (row.prosentase >= 50) pClass = 'mid';
                        else pClass = 'low';
                        bodyHtml += `<td><span class="persen-badge ${pClass}">${row.prosentase}%</span></td>`;
                    } else {
                        bodyHtml += `<td><span class="persen-badge none">-</span></td>`;
                    }

                    bodyHtml += '</tr>';
                });
                tbody.innerHTML = bodyHtml;
            } else {
                tbody.innerHTML = `<tr><td colspan="50"><div class="empty-state">
                    <i class="fas fa-clipboard-list"></i><h3>Tidak Ada Data</h3>
                    <p>Belum ada presensi untuk rombel ini pada minggu tersebut.</p>
                </div></td></tr>`;
                document.getElementById('mingguSiswaCount').textContent = '0 Siswa';
            }
        })
        .catch(() => {
            tbody.innerHTML = `<tr><td colspan="50"><div class="empty-state">
                <i class="fas fa-exclamation-triangle"></i><h3>Error</h3>
                <p>Gagal memuat data presensi.</p>
            </div></td></tr>`;
        });
}

// Print function for per-tanggal
function printPerTanggal() {
    if (!selectedRombelTanggal || !selectedTanggal) {
        showToast('Pilih rombel dan tanggal terlebih dahulu', 'error');
        return;
    }
    
    // Populate print header
    document.getElementById('printRombel').textContent = selectedRombelTanggal.name;
    const d = new Date(selectedTanggal);
    const dayName = hariNames[d.getDay()];
    const formatted = d.toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'});
    document.getElementById('printTanggal').textContent = `${dayName}, ${formatted}`;
    
    // Trigger print
    window.print();
}
</script>
@endpush
