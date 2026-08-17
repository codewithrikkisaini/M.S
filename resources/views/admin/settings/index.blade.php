<div class="space-y-6">
    {{-- Page Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">System Settings</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure webcam and other system features</p>
        </div>
    </div>

    {{-- Settings Card --}}
    <div class="grid grid-cols-1 max-w-2xl">
        <div class="pms-card shadow-sm border border-slate-100/80 p-6">
            <div class="space-y-6">
                {{-- Webcam Feature Toggle --}}
                <div class="border-b border-slate-100 pb-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-camera text-indigo-600"></i>
                                Webcam Feature for ID Capture
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Enable or disable webcam functionality for capturing ID documents and guest photos during reservation creation and editing.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-4">
                        @csrf
                        @method('POST')
                        
                        <div class="flex items-center gap-3 mt-4">
                            <div class="flex bg-slate-100 p-0.5 rounded-lg border border-slate-200">
                                <button type="submit" name="webcam_enabled" value="1" 
                                    class="px-4 py-2 text-xs font-bold rounded-md transition-all cursor-pointer {{ $webcam_enabled ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                                    <i class="fas fa-check-circle mr-1"></i> Enabled
                                </button>
                                <button type="submit" name="webcam_enabled" value="0"
                                    class="px-4 py-2 text-xs font-bold rounded-md transition-all cursor-pointer {{ !$webcam_enabled ? 'bg-white text-rose-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                                    <i class="fas fa-times-circle mr-1"></i> Disabled
                                </button>
                            </div>
                        </div>

                        <div class="mt-3 p-3 rounded-lg border {{ $webcam_enabled ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200' }}">
                            <p class="text-xs {{ $webcam_enabled ? 'text-emerald-700' : 'text-rose-700' }}">
                                <i class="fas {{ $webcam_enabled ? 'fa-check-circle' : 'fa-info-circle' }} mr-1"></i>
                                {{ $webcam_enabled 
                                    ? 'Webcam is currently ENABLED. Staff can use camera to capture ID documents and guest photos.' 
                                    : 'Webcam is currently DISABLED. Staff can only upload files manually.' 
                                }}
                            </p>
                        </div>
                    </form>
                </div>

                {{-- Info Section --}}
                <div class="bg-slate-50/70 border border-slate-200 rounded-lg p-4">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2 mb-3">
                        <i class="fas fa-lightbulb text-amber-500"></i> Notes
                    </h4>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li class="flex gap-2">
                            <span class="text-slate-400 mt-0.5">•</span>
                            <span>When <strong>enabled</strong>: Staff see both Camera and Upload buttons when creating/editing reservations</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="text-slate-400 mt-0.5">•</span>
                            <span>When <strong>disabled</strong>: Staff only see Upload button; camera buttons are hidden</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="text-slate-400 mt-0.5">•</span>
                            <span>This setting applies to all staff members across your hotel</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="text-slate-400 mt-0.5">•</span>
                            <span>File uploads via the Upload button work regardless of this setting</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
