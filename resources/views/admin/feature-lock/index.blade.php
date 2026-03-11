@extends('layouts.app')
@section('title', 'Kunci Fitur | SISMIK')

@push('styles')
<style>
    .fl-container { max-width: 1000px; margin: 0 auto; }
    .fl-header { display: flex; align-items: center; gap: 15px; margin-bottom: 24px; }
    .fl-header-icon { width: 50px; height: 50px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; }
    .fl-header h2 { margin: 0; font-size: 20px; font-weight: 700; color: #1f2937; }
    .fl-header p { margin: 4px 0 0 0; color: #6b7280; font-size: 13px; }

    .fl-section { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); margin-bottom: 20px; overflow: hidden; }
    .fl-section-header { padding: 18px 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 10px; }
    .fl-section-header h3 { margin: 0; font-size: 16px; font-weight: 600; color: #1f2937; }
    .fl-section-header .role-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .fl-section-header .role-badge.guru { background: #dbeafe; color: #1d4ed8; }
    .fl-section-header .role-badge.siswa { background: #dcfce7; color: #15803d; }

    .fl-list { padding: 0; margin: 0; list-style: none; }
    .fl-item { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px; border-bottom: 1px solid #f3f4f6; transition: background .15s; }
    .fl-item:last-child { border-bottom: none; }
    .fl-item:hover { background: #f9fafb; }
    .fl-item-info { display: flex; align-items: center; gap: 12px; }
    .fl-item-info .feature-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; }
    .fl-item-info .feature-icon.unlocked { background: #dcfce7; color: #15803d; }
    .fl-item-info .feature-icon.locked { background: #fee2e2; color: #991b1b; }
    .fl-item-name { font-size: 14px; font-weight: 600; color: #1f2937; }
    .fl-item-key { font-size: 11px; color: #9ca3af; margin-top: 2px; }

    /* Toggle switch */
    .toggle-switch { position: relative; width: 48px; height: 26px; cursor: pointer; }
    .toggle-switch input { display: none; }
    .toggle-slider { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: #10b981; border-radius: 26px; transition: .3s; }
    .toggle-slider::before { content: ''; position: absolute; width: 20px; height: 20px; border-radius: 50%; background: white; top: 3px; left: 3px; transition: .3s; box-shadow: 0 1px 4px rgba(0,0,0,0.15); }
    .toggle-switch input:checked + .toggle-slider { background: #ef4444; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(22px); }

    .fl-status { font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; min-width: 65px; text-align: center; }
    .fl-status.open { background: #dcfce7; color: #15803d; }
    .fl-status.locked { background: #fee2e2; color: #991b1b; }

    .toast-msg { position: fixed; bottom: 24px; right: 24px; padding: 14px 24px; background: #1f2937; color: white; border-radius: 12px; font-size: 13px; font-weight: 500; z-index: 9999; opacity: 0; transform: translateY(20px); transition: all .3s; box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
    .toast-msg.show { opacity: 1; transform: translateY(0); }
</style>
@endpush

@section('content')
<div class="layout">
    @include('layouts.partials.sidebar-admin')
    <div class="main-content">
        <div class="fl-container">
            <div class="fl-header">
                <div class="fl-header-icon"><i class="fas fa-lock"></i></div>
                <div>
                    <h2>Kunci Fitur</h2>
                    <p>Kelola akses fitur untuk role Guru dan Siswa</p>
                </div>
            </div>

            <!-- Guru Section -->
            <div class="fl-section">
                <div class="fl-section-header">
                    <i class="fas fa-chalkboard-teacher" style="color:#1d4ed8;"></i>
                    <h3>Fitur Guru</h3>
                    <span class="role-badge guru">{{ $guruFeatures->count() }} fitur</span>
                </div>
                <ul class="fl-list">
                    @foreach($guruFeatures as $f)
                    <li class="fl-item" id="feature-{{ $f->id }}">
                        <div class="fl-item-info">
                            <div class="feature-icon {{ $f->is_locked ? 'locked' : 'unlocked' }}" id="icon-{{ $f->id }}">
                                <i class="fas {{ $f->is_locked ? 'fa-lock' : 'fa-unlock' }}"></i>
                            </div>
                            <div>
                                <div class="fl-item-name">{{ $f->feature_name }}</div>
                                <div class="fl-item-key">{{ $f->feature_key }}</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <span class="fl-status {{ $f->is_locked ? 'locked' : 'open' }}" id="status-{{ $f->id }}">
                                {{ $f->is_locked ? 'Terkunci' : 'Terbuka' }}
                            </span>
                            <label class="toggle-switch">
                                <input type="checkbox" {{ $f->is_locked ? 'checked' : '' }} onchange="toggleFeature({{ $f->id }})">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Siswa Section -->
            <div class="fl-section">
                <div class="fl-section-header">
                    <i class="fas fa-user-graduate" style="color:#15803d;"></i>
                    <h3>Fitur Siswa</h3>
                    <span class="role-badge siswa">{{ $siswaFeatures->count() }} fitur</span>
                </div>
                <ul class="fl-list">
                    @foreach($siswaFeatures as $f)
                    <li class="fl-item" id="feature-{{ $f->id }}">
                        <div class="fl-item-info">
                            <div class="feature-icon {{ $f->is_locked ? 'locked' : 'unlocked' }}" id="icon-{{ $f->id }}">
                                <i class="fas {{ $f->is_locked ? 'fa-lock' : 'fa-unlock' }}"></i>
                            </div>
                            <div>
                                <div class="fl-item-name">{{ $f->feature_name }}</div>
                                <div class="fl-item-key">{{ $f->feature_key }}</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <span class="fl-status {{ $f->is_locked ? 'locked' : 'open' }}" id="status-{{ $f->id }}">
                                {{ $f->is_locked ? 'Terkunci' : 'Terbuka' }}
                            </span>
                            <label class="toggle-switch">
                                <input type="checkbox" {{ $f->is_locked ? 'checked' : '' }} onchange="toggleFeature({{ $f->id }})">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="toast-msg" id="toast"></div>

@push('scripts')
<script>
function toggleFeature(id) {
    fetch('{{ route("admin.feature-lock.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: id })
    })
    .then(r => r.json())
    .then(data => {
        const icon = document.getElementById('icon-' + id);
        const status = document.getElementById('status-' + id);
        if (data.is_locked) {
            icon.className = 'feature-icon locked';
            icon.innerHTML = '<i class="fas fa-lock"></i>';
            status.className = 'fl-status locked';
            status.textContent = 'Terkunci';
        } else {
            icon.className = 'feature-icon unlocked';
            icon.innerHTML = '<i class="fas fa-unlock"></i>';
            status.className = 'fl-status open';
            status.textContent = 'Terbuka';
        }
        showToast(data.message);
    });
}

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
@endpush
@endsection
