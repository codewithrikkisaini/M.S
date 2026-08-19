<div>
    {{-- Page Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('reservations.index') }}" class="btn-icon text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors border border-slate-150 rounded-lg shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">New Reservation</h1>
            <p class="text-sm text-gray-500 mt-0.5">Create a room booking and payment record for a guest</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Form Panel --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-user-friends text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Guest & Stay Information</h3>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider mb-0">Guest Details <span class="text-red-500">*</span></label>
                            <div class="flex bg-slate-100 p-0.5 rounded-lg border border-slate-200">
                                <button type="button" wire:click="$set('is_new_guest', false)" class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-all cursor-pointer {{ !$is_new_guest ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">Existing Guest</button>
                                <button type="button" wire:click="$set('is_new_guest', true)" class="px-2.5 py-1 text-[10px] font-bold rounded-md transition-all cursor-pointer {{ $is_new_guest ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">New Guest</button>
                            </div>
                        </div>

                        @if(!$is_new_guest)
                            <select wire:model="guest_id" class="pms-select text-xs">
                                <option value="">Select guest...</option>
                                @foreach($guests as $g)
                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                @endforeach
                            </select>
                            @error('guest_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 bg-slate-50/50 p-4 rounded-xl border border-dashed border-slate-250">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Guest Name <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="new_guest_name" class="pms-input text-xs mt-1" placeholder="Enter name...">
                                    @error('new_guest_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                                    <input type="email" wire:model="new_guest_email" class="pms-input text-xs mt-1" placeholder="email@example.com">
                                    @error('new_guest_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Phone Number</label>
                                    <input type="text" wire:model="new_guest_phone" class="pms-input text-xs mt-1" placeholder="+1 (555) 000-0000">
                                    @error('new_guest_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Blacklist Warning --}}
                    @if($is_blacklisted)
                    <div class="col-span-2 p-4 bg-red-50 border-2 border-red-300 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                                <i class="fas fa-ban text-red-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black text-red-800">Guest Blacklisted</p>
                                <p class="text-xs text-red-600 mt-0.5">This guest is currently blacklisted and cannot make new reservations.</p>
                                @if($blacklist_reason)
                                <p class="text-[10px] text-red-500 mt-1 font-semibold">Reason: {{ $blacklist_reason }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50/40 p-3.5 rounded-xl border border-slate-200/80">
                        {{-- ID Type --}}
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                ID Type
                            </label>
                            <select wire:model.live="id_type" class="pms-select text-xs">
                                <option value="">Select ID Type...</option>
                                <option value="Aadhaar Card">🪪 Aadhaar Card</option>
                                <option value="Driving License">🪪 Driving License</option>
                                <option value="Passport">🛂 Passport</option>
                                <option value="Voter ID">🗳️ Voter ID</option>
                                <option value="Other">📄 Other Document</option>
                            </select>
                            @error('id_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ID Number --}}
                        <div>
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                {{ $id_type ? ($id_type . ' Number') : 'ID / Document Number' }}
                            </label>
                            <input type="text" 
                                   wire:model="guest_id_number" 
                                   class="pms-input text-xs" 
                                   placeholder="{{ $id_type === 'Aadhaar Card' ? 'e.g. 1234 5678 9012' : ($id_type === 'Driving License' ? 'e.g. DL-1420110012345' : ($id_type === 'Passport' ? 'e.g. P1234567' : ($id_type === 'Voter ID' ? 'e.g. ABC1234567' : 'Enter ID / Document No...'))) }}">
                            @error('guest_id_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Booking Type --}} 
                        <div> 
                            <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider"> Booking Type <span class="text-red-500">*</span></label> 
                            <select wire:model="booking_type" class="pms-select text-xs"> 
                                <option value="Walk in">🚶 Walk in</option> 
                                <option value="Direct website">🌐 Direct website</option> 
                                <option value="OTA">🏨 OTA (Booking.com/MMT/Agoda)</option> 
                                <option value="Phone">📞 Phone</option> 
                                <option value="Other">📌 Other</option> 
                            </select> 
                            @error('booking_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror 
                        </div>
                    </div>

                    {{-- ID & Guest Photo Attachments Section --}}
<<<<<<< HEAD
                    <div class="col-span-2 bg-slate-50/70 p-4 rounded-xl border border-slate-200 mt-2">
=======
                    <div class="col-span-2 bg-slate-50/70 p-4 rounded-xl border border-slate-200 mt-2"
                         x-data="{
                            showCamera: false,
                            targetField: '',
                            stream: null,
                            facingMode: 'environment',
                            hasMultipleCameras: true,
                            isLoading: false,
                            cameraError: '',
                            
                            async startCamera(field, preferredMode = null) {
                                this.targetField = field;
                                this.cameraError = '';
                                this.facingMode = preferredMode || (field === 'guest' ? 'user' : 'environment');
                                this.showCamera = true;
                                await this.initStream();
                            },

                            async initStream() {
                                this.isLoading = true;
                                this.cameraError = '';
                                if (this.stream) {
                                    this.stream.getTracks().forEach(t => t.stop());
                                    this.stream = null;
                                }

                                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                                    this.isLoading = false;
                                    this.cameraError = 'Live webcam not supported on this browser/insecure HTTP. Use Native Mobile Camera.';
                                    return;
                                }

                                try {
                                    const constraints = {
                                        video: {
                                            facingMode: { ideal: this.facingMode },
                                            width: { ideal: 1280 },
                                            height: { ideal: 720 }
                                        },
                                        audio: false
                                    };
                                    this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                                    if (this.$refs.videoElem) {
                                        this.$refs.videoElem.srcObject = this.stream;
                                        await this.$refs.videoElem.play();
                                    }
                                } catch (err) {
                                    console.warn('Ideal constraint failed, trying generic camera...', err);
                                    try {
                                        this.stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                                        if (this.$refs.videoElem) {
                                            this.$refs.videoElem.srcObject = this.stream;
                                            await this.$refs.videoElem.play();
                                        }
                                    } catch (fallbackErr) {
                                        console.error('All camera attempts failed:', fallbackErr);
                                        this.cameraError = 'Camera permission denied or camera not found. Please allow camera permission or use Native Camera.';
                                    }
                                } finally {
                                    this.isLoading = false;
                                }
                            },

                            async flipCamera() {
                                this.facingMode = (this.facingMode === 'environment' ? 'user' : 'environment');
                                await this.initStream();
                            },

                            openNativeCamera() {
                                if (this.targetField === 'front' && this.$refs.nativeInputFront) {
                                    this.$refs.nativeInputFront.click();
                                } else if (this.targetField === 'back' && this.$refs.nativeInputBack) {
                                    this.$refs.nativeInputBack.click();
                                } else if (this.targetField === 'guest' && this.$refs.nativeInputGuest) {
                                    this.$refs.nativeInputGuest.click();
                                }
                                this.stopCamera();
                            },

                            stopCamera() {
                                if (this.stream) {
                                    this.stream.getTracks().forEach(t => t.stop());
                                    this.stream = null;
                                }
                                this.showCamera = false;
                                this.cameraError = '';
                                this.isLoading = false;
                            },

                            capture() {
                                const video = this.$refs.videoElem;
                                if (!video || !video.videoWidth) return;

                                const canvas = document.createElement('canvas');
                                canvas.width = video.videoWidth || 1280;
                                canvas.height = video.videoHeight || 720;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                const dataUrl = canvas.toDataURL('image/jpeg', 0.88);

                                if (this.targetField === 'front') {
                                    $wire.set('id_card_front_base64', dataUrl);
                                } else if (this.targetField === 'back') {
                                    $wire.set('id_card_back_base64', dataUrl);
                                } else if (this.targetField === 'guest') {
                                    $wire.set('guest_photo_base64', dataUrl);
                                }
                                this.stopCamera();
                            }
                         }">
>>>>>>> 69db85840fcc1cae6b7e35a7e3d62d99aaafe6d4
                        
                        {{-- Hidden Native Camera File Inputs for 100% Mobile Compatibility Fallback --}}
                        <input type="file" x-ref="nativeInputFront" wire:model="id_card_front" accept="image/*" capture="environment" class="hidden">
                        <input type="file" x-ref="nativeInputBack" wire:model="id_card_back" accept="image/*" capture="environment" class="hidden">
                        <input type="file" x-ref="nativeInputGuest" wire:model="guest_photo" accept="image/*" capture="user" class="hidden">

                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-200/60">
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs">
                                    <i class="fas fa-id-card"></i>
                                </span> 
                                Guest Photo & ID Document Verification (Front & Back)
                            </h4>
                            <span class="text-[10px] font-bold text-slate-400 bg-white px-2.5 py-1 rounded-full border border-slate-200">
                                Official Identity Scans
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- 1. ID Card Front --}}
                            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs hover:shadow-md transition-all flex flex-col justify-between relative group">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-[11px] font-bold text-slate-800">ID Scan - Front</label>
                                        @if($id_card_front_base64 || $id_card_front)
                                            <span class="text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i> Ready
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-3">Front side of Aadhaar / License / Passport</p>
                                </div>
                                
                                @if($id_card_front_base64)
                                    <div class="relative mb-3 group/img">
                                        <img src="{{ $id_card_front_base64 }}" class="w-full h-28 object-cover rounded-lg border border-slate-200 shadow-inner">
                                        <button type="button" wire:click="$set('id_card_front_base64', '')" class="absolute -top-2 -right-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full w-6 h-6 text-xs shadow-md flex items-center justify-center transition-transform hover:scale-110"><i class="fas fa-times"></i></button>
                                    </div>
                                @elseif($id_card_front)
                                    <div class="relative mb-3 group/img">
                                        <img src="{{ $id_card_front->temporaryUrl() }}" class="w-full h-28 object-cover rounded-lg border border-slate-200 shadow-inner">
                                        <button type="button" wire:click="$set('id_card_front', null)" class="absolute -top-2 -right-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full w-6 h-6 text-xs shadow-md flex items-center justify-center transition-transform hover:scale-110"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2">
<<<<<<< HEAD
                                    <label class="w-full py-2 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-extrabold rounded-lg flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-2xs">
=======
                                    <button type="button" @click="startCamera('front', 'environment')" class="flex-1 py-2 px-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-extrabold rounded-lg flex items-center justify-center gap-1.5 transition-all shadow-2xs cursor-pointer" title="Scan with Camera (Front/Back)">
                                        <i class="fas fa-camera text-xs"></i> Camera
                                    </button>
                                    <label class="flex-1 py-2 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-extrabold rounded-lg flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-2xs" title="Upload from Gallery/Files">
>>>>>>> 69db85840fcc1cae6b7e35a7e3d62d99aaafe6d4
                                        <i class="fas fa-upload text-xs"></i> Upload
                                        <input type="file" wire:model="id_card_front" accept="image/*" class="hidden">
                                    </label>
                                </div>
                                @error('id_card_front') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- 2. ID Card Back --}}
                            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs hover:shadow-md transition-all flex flex-col justify-between relative group">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-[11px] font-bold text-slate-800">ID Scan - Back</label>
                                        @if($id_card_back_base64 || $id_card_back)
                                            <span class="text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i> Ready
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-3">Back side of Aadhaar / License / Passport</p>
                                </div>

                                @if($id_card_back_base64)
                                    <div class="relative mb-3 group/img">
                                        <img src="{{ $id_card_back_base64 }}" class="w-full h-28 object-cover rounded-lg border border-slate-200 shadow-inner">
                                        <button type="button" wire:click="$set('id_card_back_base64', '')" class="absolute -top-2 -right-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full w-6 h-6 text-xs shadow-md flex items-center justify-center transition-transform hover:scale-110"><i class="fas fa-times"></i></button>
                                    </div>
                                @elseif($id_card_back)
                                    <div class="relative mb-3 group/img">
                                        <img src="{{ $id_card_back->temporaryUrl() }}" class="w-full h-28 object-cover rounded-lg border border-slate-200 shadow-inner">
                                        <button type="button" wire:click="$set('id_card_back', null)" class="absolute -top-2 -right-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full w-6 h-6 text-xs shadow-md flex items-center justify-center transition-transform hover:scale-110"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2">
<<<<<<< HEAD
                                    <label class="w-full py-2 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-extrabold rounded-lg flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-2xs">
=======
                                    <button type="button" @click="startCamera('back', 'environment')" class="flex-1 py-2 px-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-extrabold rounded-lg flex items-center justify-center gap-1.5 transition-all shadow-2xs cursor-pointer" title="Scan with Camera (Front/Back)">
                                        <i class="fas fa-camera text-xs"></i> Camera
                                    </button>
                                    <label class="flex-1 py-2 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-extrabold rounded-lg flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-2xs" title="Upload from Gallery/Files">
>>>>>>> 69db85840fcc1cae6b7e35a7e3d62d99aaafe6d4
                                        <i class="fas fa-upload text-xs"></i> Upload
                                        <input type="file" wire:model="id_card_back" accept="image/*" class="hidden">
                                    </label>
                                </div>
                                @error('id_card_back') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- 3. Guest Photo --}}
                            <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs hover:shadow-md transition-all flex flex-col justify-between relative group">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-[11px] font-bold text-slate-800">Guest Live Photo</label>
                                        @if($guest_photo_base64 || $guest_photo)
                                            <span class="text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i> Ready
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-3">Guest portrait photo at check-in</p>
                                </div>

                                @if($guest_photo_base64)
                                    <div class="relative mb-3 group/img">
                                        <img src="{{ $guest_photo_base64 }}" class="w-full h-28 object-cover rounded-lg border border-slate-200 shadow-inner">
                                        <button type="button" wire:click="$set('guest_photo_base64', '')" class="absolute -top-2 -right-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full w-6 h-6 text-xs shadow-md flex items-center justify-center transition-transform hover:scale-110"><i class="fas fa-times"></i></button>
                                    </div>
                                @elseif($guest_photo)
                                    <div class="relative mb-3 group/img">
                                        <img src="{{ $guest_photo->temporaryUrl() }}" class="w-full h-28 object-cover rounded-lg border border-slate-200 shadow-inner">
                                        <button type="button" wire:click="$set('guest_photo', null)" class="absolute -top-2 -right-2 bg-rose-600 hover:bg-rose-700 text-white rounded-full w-6 h-6 text-xs shadow-md flex items-center justify-center transition-transform hover:scale-110"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2">
<<<<<<< HEAD
                                    <label class="w-full py-2 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-extrabold rounded-lg flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-2xs">
=======
                                    <button type="button" @click="startCamera('guest', 'user')" class="flex-1 py-2 px-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-extrabold rounded-lg flex items-center justify-center gap-1.5 transition-all shadow-2xs cursor-pointer" title="Take Guest Photo (Front/Back Camera)">
                                        <i class="fas fa-camera text-xs"></i> Camera
                                    </button>
                                    <label class="flex-1 py-2 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-extrabold rounded-lg flex items-center justify-center gap-1.5 cursor-pointer transition-all shadow-2xs" title="Upload from Gallery/Files">
>>>>>>> 69db85840fcc1cae6b7e35a7e3d62d99aaafe6d4
                                        <i class="fas fa-upload text-xs"></i> Upload
                                        <input type="file" wire:model="guest_photo" accept="image/*" class="hidden">
                                    </label>
                                </div>
                                @error('guest_photo') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

<<<<<<< HEAD
=======
                        {{-- WebCam Live Capture Scanner Modal with Front/Back Switch --}}
                        <div x-show="showCamera" style="display: none;" class="fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-4">
                            <div class="bg-white rounded-3xl p-5 md:p-6 max-w-lg w-full shadow-2xl border border-slate-100 text-center transition-all">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
                                            <i class="fas fa-expand"></i>
                                        </div>
                                        <div class="text-left">
                                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider" x-text="targetField === 'front' ? 'Scan ID Document - FRONT Side' : (targetField === 'back' ? 'Scan ID Document - BACK Side' : 'Capture Guest Live Photo')"></h3>
                                            <p class="text-[10px] text-slate-400">Position clearly inside the frame</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        {{-- Camera Flip Button (Front / Back) --}}
                                        <button type="button" @click="flipCamera()" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-[11px] font-bold flex items-center gap-1.5 transition-all cursor-pointer shadow-2xs" title="Switch Front / Back Camera">
                                            <i class="fas fa-sync-alt text-indigo-600"></i>
                                            <span x-text="facingMode === 'environment' ? 'Back (Rear)' : 'Front (Selfie)'"></span>
                                        </button>
                                        <button type="button" @click="stopCamera()" class="w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 text-xs flex items-center justify-center cursor-pointer">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Camera Viewfinder with ID Card Scanner Overlay --}}
                                <div class="bg-slate-950 rounded-2xl overflow-hidden aspect-4/3 relative mb-4 shadow-inner border border-slate-800 flex items-center justify-center">
                                    <video x-ref="videoElem" autoplay playsinline muted class="w-full h-full object-cover"></video>

                                    {{-- Loading indicator --}}
                                    <div x-show="isLoading" class="absolute inset-0 bg-slate-950/80 flex flex-col items-center justify-center text-white z-10">
                                        <i class="fas fa-spinner fa-spin text-2xl text-indigo-400 mb-2"></i>
                                        <p class="text-xs font-bold">Starting Camera...</p>
                                    </div>

                                    {{-- Error overlay & fallback --}}
                                    <div x-show="cameraError" style="display: none;" class="absolute inset-0 bg-slate-950/90 flex flex-col items-center justify-center p-6 text-center text-white z-20">
                                        <div class="w-12 h-12 rounded-full bg-rose-500/20 text-rose-400 flex items-center justify-center text-xl mb-2">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <p class="text-xs font-bold text-rose-300 mb-3" x-text="cameraError"></p>
                                        <button type="button" @click="openNativeCamera()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-md cursor-pointer">
                                            <i class="fas fa-camera"></i> Open Phone Camera
                                        </button>
                                    </div>
                                    
                                    {{-- ID Card Scanner Viewfinder Target Overlay --}}
                                    <div x-show="!cameraError && !isLoading" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center p-4">
                                        <template x-if="targetField === 'front' || targetField === 'back'">
                                            <div class="w-full h-44 sm:h-48 border-2 border-dashed border-indigo-400/90 rounded-2xl relative shadow-[0_0_30px_rgba(99,102,241,0.25)] bg-indigo-950/10 flex flex-col justify-between p-3">
                                                <div class="flex justify-between items-start">
                                                    <div class="w-4 h-4 border-t-4 border-l-4 border-indigo-400 rounded-tl"></div>
                                                    <span class="text-[9px] font-black uppercase tracking-widest text-indigo-200 bg-indigo-950/80 px-2 py-0.5 rounded-full border border-indigo-400/40" x-text="targetField === 'front' ? 'FRONT SIDE' : 'BACK SIDE'"></span>
                                                    <div class="w-4 h-4 border-t-4 border-r-4 border-indigo-400 rounded-tr"></div>
                                                </div>
                                                <p class="text-[10px] font-extrabold text-white bg-black/60 backdrop-blur-xs py-1 px-3 rounded-full mx-auto shadow-sm">
                                                    <i class="fas fa-crop-alt text-indigo-400 mr-1"></i> Align ID Card Inside Frame
                                                </p>
                                                <div class="flex justify-between items-end">
                                                    <div class="w-4 h-4 border-b-4 border-l-4 border-indigo-400 rounded-bl"></div>
                                                    <div class="w-4 h-4 border-b-4 border-r-4 border-indigo-400 rounded-br"></div>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="targetField === 'guest'">
                                            <div class="w-40 h-52 sm:w-44 sm:h-56 border-2 border-dashed border-emerald-400/90 rounded-full relative shadow-[0_0_30px_rgba(16,185,129,0.25)] bg-emerald-950/10 flex items-center justify-center">
                                                <p class="text-[10px] font-extrabold text-white bg-black/60 backdrop-blur-xs py-1 px-3 rounded-full shadow-sm">
                                                    <i class="fas fa-user-circle text-emerald-400 mr-1"></i> Align Face Here
                                                </p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Camera Controls & Switch Bar --}}
                                <div class="flex flex-wrap items-center justify-center gap-2.5">
                                    <button type="button" @click="capture()" :disabled="isLoading || !!cameraError" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 disabled:opacity-50 text-white font-extrabold text-xs rounded-xl shadow-lg transition-all flex items-center gap-2 cursor-pointer">
                                        <i class="fas fa-camera text-sm"></i> Capture & Save Scan
                                    </button>
                                    
                                    <button type="button" @click="flipCamera()" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl flex items-center gap-1.5 transition-all cursor-pointer" title="Flip Camera">
                                        <i class="fas fa-sync-alt"></i> <span x-text="facingMode === 'environment' ? 'Switch to Front' : 'Switch to Back'"></span>
                                    </button>

                                    <button type="button" @click="openNativeCamera()" class="px-3.5 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-bold text-xs rounded-xl flex items-center gap-1.5 transition-all cursor-pointer" title="Open Phone Camera Directly">
                                        <i class="fas fa-mobile-alt"></i> Phone App
                                    </button>

                                    <button type="button" @click="stopCamera()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl cursor-pointer">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
>>>>>>> 69db85840fcc1cae6b7e35a7e3d62d99aaafe6d4
                    </div>


                    
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Adults</label>
                        <input type="number" wire:model="adults" class="pms-input text-xs" min="1">
                        @error('adults') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Children</label>
                        <input type="number" wire:model="children" class="pms-input text-xs" min="0">
                    </div>

                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Check-In <span class="text-red-500">*</span></label>
                        <input type="date" wire:model.live="check_in_date" class="pms-input text-xs">
                        @error('check_in_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Check-Out <span class="text-red-500">*</span></label>
                        <input type="date" wire:model.live="check_out_date" class="pms-input text-xs">
                        @error('check_out_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if(!$check_in_date || !$check_out_date)
                    <div class="col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room(s) <span class="text-red-500">*</span></label>
                        <div class="p-3 text-xs bg-slate-50 border border-slate-100 text-slate-400 rounded-lg font-medium flex items-center gap-2">
                            <i class="fas fa-info-circle text-slate-400"></i> Please specify check-in and check-out dates to view available rooms.
                        </div>
                        @error('room_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @else
                    <div class="col-span-2 relative" wire:key="reservation-create-room-selector" x-data="{
                        open: false,
                        search: '',
                        rooms: @js($rooms->map(fn($r) => [
                            'id' => $r->id,
                            'room_number' => $r->room_number,
                            'type' => $r->roomType->name ?? '',
                            'price' => $r->price,
                            'image_url' => $r->image_url,
                            'hk' => optional($r->latestHousekeeping)->status ?? 'Clean',
                            'maint' => $r->activeMaintenanceTickets->count()
                        ])),
                        selectedIds: @entangle('room_ids').live,
                        toggleRoom(room) {
                            const idx = this.selectedIds.indexOf(room.id);
                            if (idx === -1) { this.selectedIds.push(room.id); } else { this.selectedIds.splice(idx, 1); }
                        },
                        isSelected(room) { return this.selectedIds.includes(room.id); },
                        get selectedRooms() { return this.rooms.filter(r => this.selectedIds.includes(r.id)); },
                        get totalPerNight() { return this.selectedRooms.reduce((sum, r) => sum + Number(r.price), 0); },
                        get filteredRooms() {
                            if (!this.search) return this.rooms;
                            return this.rooms.filter(r =>
                                r.room_number.toLowerCase().includes(this.search.toLowerCase()) ||
                                r.type.toLowerCase().includes(this.search.toLowerCase())
                            );
                        }
                    }">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Room(s) <span class="text-red-500">*</span> <span class="text-[10px] text-slate-400 font-normal lowercase">(select multiple for family bookings)</span></label>

                        <button type="button" @click.stop="open = !open"
                                class="w-full flex items-center justify-between pms-input text-left bg-white border border-slate-300 rounded-lg shadow-sm px-3.5 py-2.5 text-xs text-gray-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 cursor-pointer transition-all">
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-door-open text-slate-400"></i>
                                <template x-if="selectedRooms.length > 0">
                                    <span class="font-semibold text-slate-800" x-text="selectedRooms.length + ' room' + (selectedRooms.length > 1 ? 's' : '') + ' selected'"></span>
                                </template>
                                <template x-if="selectedRooms.length === 0">
                                    <span class="text-gray-400">Select room(s)...</span>
                                </template>
                            </div>
                            <div class="flex items-center gap-2">
                                <template x-if="selectedRooms.length > 0">
                                    <span class="text-xs font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full" x-text="'$' + totalPerNight.toFixed(2) + '/night'"></span>
                                </template>
                                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                            </div>
                        </button>

                        <template x-if="selectedRooms.length > 0">
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <template x-for="room in selectedRooms" :key="room.id">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-semibold shadow-sm animate-fade-in">
                                        <span x-text="'Room ' + room.room_number"></span>
                                        <button type="button" @click.stop="toggleRoom(room)" class="text-indigo-400 hover:text-indigo-700 transition-colors p-0.5 hover:bg-indigo-100 rounded-full inline-flex items-center justify-center">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </button>
                                    </span>
                                </template>
                            </div>
                        </template>

                        <div x-show="open"
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute z-30 w-full mt-1.5 bg-white border border-slate-200 rounded-xl shadow-2xl max-h-72 overflow-hidden flex flex-col"
                             style="display:none;">

                            <div class="p-2.5 border-b border-slate-100 sticky top-0 bg-white z-10">
                                <div class="relative">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" x-model="search" placeholder="Search by room number or type..."
                                           class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 pl-8 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 bg-slate-50 hover:bg-slate-50/50">
                                </div>
                            </div>

                            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                                <template x-for="room in filteredRooms" :key="room.id">
                                    <button type="button" @click="toggleRoom(room)"
                                            class="w-full text-left px-4 py-3 hover:bg-slate-50/60 flex items-center justify-between transition-colors cursor-pointer"
                                            :class="{ 'bg-indigo-50/40': isSelected(room) }">
                                        <div class="flex items-center gap-3">
                                            <div class="flex items-center justify-center w-5 h-5 rounded border transition-all shrink-0"
                                                 :class="isSelected(room) ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-300 bg-white hover:border-slate-400'">
                                                <template x-if="isSelected(room)">
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </template>
                                            </div>
                                            <img :src="room.image_url" class="w-12 h-9 object-cover rounded-lg border border-slate-200 shadow-sm shrink-0">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-slate-800 text-sm" x-text="'Room ' + room.room_number"></span>
                                                    <span class="text-[10px] font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded" x-text="room.type"></span>
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1">
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold border"
                                                          :class="{
                                                              'bg-emerald-50 text-emerald-700 border-emerald-100': room.hk === 'Clean',
                                                              'bg-rose-50 text-rose-700 border-rose-100': room.hk === 'Dirty',
                                                              'bg-amber-50 text-amber-700 border-amber-100': room.hk === 'Inspecting',
                                                              'bg-orange-50 text-orange-700 border-orange-100': room.hk === 'Maintenance'
                                                          }">
                                                        <span class="w-1.5 h-1.5 rounded-full"
                                                              :class="{
                                                                  'bg-emerald-500': room.hk === 'Clean',
                                                                  'bg-rose-500': room.hk === 'Dirty',
                                                                  'bg-amber-500': room.hk === 'Inspecting',
                                                                  'bg-orange-500': room.hk === 'Maintenance'
                                                              }"></span>
                                                        <span x-text="room.hk"></span>
                                                    </span>
                                                    <template x-if="room.maint > 0">
                                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100 animate-pulse">
                                                            <i class="fas fa-tools text-[8px]"></i>
                                                            <span x-text="room.maint + ' Ticket' + (room.maint > 1 ? 's' : '')"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right flex flex-col items-end shrink-0">
                                            <span class="font-bold text-slate-800 text-sm" x-text="'$' + Number(room.price).toFixed(2)"></span>
                                            <span class="text-[10px] text-slate-400 font-medium">/night</span>
                                        </div>
                                    </button>
                                </template>
                                <template x-if="filteredRooms.length === 0">
                                    <div class="p-5 text-center text-xs text-gray-400">
                                        <i class="fas fa-info-circle mb-1 block text-lg text-gray-300"></i>
                                        No rooms available for these dates
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    @error('room_ids') <p class="text-red-500 text-xs mt-1 col-span-2">{{ $message }}</p> @enderror

                    @if(!empty($room_ids))
                        @php
                            $selectedRoomModels = $rooms->whereIn('id', $room_ids);
                            $alertRooms = $selectedRoomModels->filter(function($r) {
                                $hk = optional($r->latestHousekeeping)->status ?? 'Clean';
                                return $hk !== 'Clean' || $r->activeMaintenanceTickets->count() > 0;
                            });
                        @endphp
                        @if($alertRooms->isNotEmpty())
                            <div class="mt-2.5 rounded-lg p-3 text-xs bg-amber-50 border border-amber-200 text-amber-800 space-y-1 col-span-2 shadow-sm">
                                <p class="font-semibold flex items-center gap-1.5"><i class="fas fa-exclamation-triangle"></i> Room Status Alert:</p>
                                @foreach($alertRooms as $r)
                                    @php
                                        $hkStatus = optional($r->latestHousekeeping)->status ?? 'Clean';
                                        $maintCount = $r->activeMaintenanceTickets->count();
                                    @endphp
                                    <p>• Room {{ $r->room_number }}:
                                        @if($hkStatus !== 'Clean') Housekeeping is <strong>{{ $hkStatus }}</strong>. @endif
                                        @if($maintCount > 0) <strong>{{ $maintCount }}</strong> active maintenance ticket(s). @endif
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    @endif
                    @endif
                    
                    <div class="col-span-2">
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Special Notes</label>
                        <textarea wire:model="special_notes" rows="3" class="pms-input text-xs resize-none rounded-lg border border-slate-200 focus:ring-1 focus:ring-indigo-500" placeholder="Any special requests..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Payment & Summary Panel --}}
        <div class="space-y-6">
            <div class="pms-card shadow-sm border border-slate-100/80 p-5">
                <div class="flex items-center gap-2 mb-4 border-b border-slate-50 pb-3">
                    <div class="w-7 h-7 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-dollar-sign text-xs"></i></div>
                    <h3 class="text-sm font-bold text-slate-800">Billing & Pricing</h3>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Discount Type</label>
                        <select wire:model.live="discount_type" class="pms-select text-xs">
                            <option value="Fixed">Fixed ($)</option>
                            <option value="Percentage">Percentage (%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Discount Value</label>
                        <input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="discount_value" class="pms-input text-xs" placeholder="0">
                        @error('discount_value') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Tax Rate (%)</label>
                        <input type="number" step="0.01" min="0" max="100" wire:model.live.debounce.400ms="tax_rate" class="pms-input text-xs" placeholder="18">
                        @error('tax_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($charges)
                    <div class="bg-slate-50 rounded-xl p-4 text-xs space-y-2.5 border border-slate-150 shadow-inner">
                        <h4 class="font-bold text-slate-700 border-b border-slate-200/60 pb-1.5 mb-1 text-[11px] uppercase tracking-wider">Invoice Summary</h4>
                        <div class="flex justify-between text-slate-600 font-medium">
                            <span>Subtotal</span>
                            <span>${{ number_format($charges['subtotal'], 2) }}</span>
                        </div>
                        @if($charges['discount'] > 0)
                        <div class="flex justify-between text-emerald-600 font-bold">
                            <span>Discount</span>
                            <span>-${{ number_format($charges['discount'], 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-slate-600 font-medium">
                            <span>Tax ({{ $charges['tax_rate'] }}%)</span>
                            <span>${{ number_format($charges['tax'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-900 font-black border-t border-slate-200/80 pt-2 text-sm">
                            <span>Total Amount</span>
                            <span>${{ number_format($charges['total'], 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-dashed border-slate-200/80 pt-2 font-black text-sm {{ $balanceDue > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            <span>Balance Due</span>
                            <span>${{ number_format($balanceDue, 2) }}</span>
                        </div>
                    </div>
                    @endif

                    <div class="border-t border-slate-100 pt-4 mt-2">
                        <div class="space-y-4">
                            <div>
                                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Payment Method</label>
                                <select wire:model="payment_type" class="pms-select text-xs">
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="UPI">UPI</option>
                                </select>
                                @error('payment_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="pms-label text-xs font-semibold text-slate-600 uppercase tracking-wider">Advance Payment ($)</label>
                                <input type="number" step="0.01" min="0" wire:model.live.debounce.400ms="payment_amount" class="pms-input text-xs" placeholder="0.00">
                                @error('payment_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <label class="pms-label text-[10px] font-black text-amber-700 uppercase tracking-wider mb-0">Security Check</label>
                                    <button type="button" wire:click="regenerateCaptcha" class="text-[9px] font-bold text-amber-700 hover:text-amber-900 underline-offset-2 hover:underline">
                                        Refresh
                                    </button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm font-black text-slate-800 shadow-sm">
                                        {{ $captcha_question }}
                                    </div>
                                    <input type="number" wire:model="captcha_input" class="pms-input text-xs w-24" placeholder="Answer" min="0">
                                </div>
                                @error('captcha_input') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2.5 pt-5 border-t border-slate-100 mt-4">
                    @if($is_blacklisted)
                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-center">
                        <p class="text-xs font-bold text-red-700"><i class="fas fa-ban mr-1"></i> Booking Not Allowed</p>
                        <p class="text-[10px] text-red-500 mt-0.5">This guest is blacklisted. Release the blacklist before creating a reservation.</p>
                    </div>
                    <button disabled class="btn-primary w-full justify-center rounded-lg shadow-sm text-xs py-2 font-bold opacity-50 cursor-not-allowed">
                        <i class="fas fa-ban mr-1"></i> Booking Blocked
                    </button>
                    @else
                    <button wire:click="save" wire:loading.attr="disabled" class="btn-primary w-full justify-center rounded-lg shadow-sm text-xs py-2 font-bold cursor-pointer">
                        <span wire:loading wire:target="save" class="mr-1"><i class="fas fa-spinner fa-spin"></i></span>
                        Create Booking
                    </button>
                    @endif
                    <a href="{{ route('reservations.index') }}" class="btn-secondary w-full justify-center rounded-lg text-xs py-2 font-bold text-center">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
