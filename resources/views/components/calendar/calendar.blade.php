<div>
    <style>
        .fc { font-family: 'Inter', sans-serif; }
        .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 800 !important; color: #0f172a !important; letter-spacing: -0.02em; }
        .fc-button { font-size: 0.75rem !important; font-weight: 600 !important; border-radius: 8px !important; text-transform: capitalize !important; padding: 6px 12px !important; }
        .fc-button-primary { background-color: #4f46e5 !important; border-color: #4f46e5 !important; }
        .fc-button-primary:hover { background-color: #4338ca !important; border-color: #4338ca !important; }
        .fc-button-primary:not(.fc-button-active) { background-color: #fff !important; border-color: #e2e8f0 !important; color: #475569 !important; }
        .fc-button-primary:not(.fc-button-active):hover { background-color: #f8fafc !important; }
        .fc-button-active { background-color: #4f46e5 !important; border-color: #4f46e5 !important; color: #fff !important; }
        .fc-event { border-radius: 6px !important; border: none !important; font-size: 0.75rem !important; font-weight: 600 !important; padding: 4px 8px !important; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .fc-daygrid-event { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .fc-col-header-cell { background: #f8fafc; border-bottom: 1px solid #e2e8f0 !important; }
        .fc-col-header-cell-cushion { font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; padding: 8px 4px !important; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #f1f5f9 !important; }
        .fc .fc-daygrid-day.fc-day-today { background-color: rgba(99, 102, 241, 0.04) !important; }
        .fc-daygrid-day-number { font-size: 0.785rem !important; font-weight: 700 !important; color: #64748b !important; padding: 8px 10px !important; }
        .fc-daygrid-day-frame { min-height: 100px !important; }
        .fc-toolbar { margin-bottom: 1.5rem !important; gap: 0.5rem; flex-wrap: wrap; }
        
        .sticky-column {
            position: sticky;
            left: 0;
            z-index: 10;
            background-color: #fff;
            box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
        }
    </style>

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Booking Calendar</h1>
            <p class="text-sm text-gray-500 mt-0.5">Interactive visual timeline of all reservations and guest occupancy</p>
        </div>
        
        <div class="flex items-center gap-3">
            {{-- View Toggle Switch --}}
            <div class="flex bg-slate-100 p-0.5 rounded-lg border border-slate-200">
                <button type="button" wire:click="switchView('timeline')" 
                        class="px-3.5 py-1.5 text-xs font-bold rounded-md transition-all cursor-pointer {{ $activeView === 'timeline' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fas fa-stream mr-1 text-[10px]"></i> Timeline View
                </button>
                <button type="button" wire:click="switchView('month')" 
                        class="px-3.5 py-1.5 text-xs font-bold rounded-md transition-all cursor-pointer {{ $activeView === 'month' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fas fa-calendar-alt mr-1 text-[10px]"></i> Month View
                </button>
            </div>
            
            <a href="{{ route('reservations.create') }}" class="btn-primary btn-sm rounded-lg shadow-sm">
                <i class="fas fa-plus text-xs"></i> New Reservation
            </a>
        </div>
    </div>

    {{-- Calendar Wrapper Card --}}
    <div class="pms-card shadow-sm border border-slate-100/80">
        <div class="pms-card-header flex-wrap gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100"><i class="fas fa-calendar-alt text-sm"></i></div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">
                        {{ $activeView === 'timeline' ? 'Occupancy Timeline' : 'Schedule Planner' }}
                    </h3>
                    <p class="text-[10px] text-slate-400">
                        {{ $activeView === 'timeline' ? 'Visual room-wise calendar. Click empty cells to book.' : 'Drag, view, and organize reservations across dates' }}
                    </p>
                </div>
            </div>
            
            <div class="flex items-center flex-wrap gap-3">
                {{-- Timeline Navigator Controls --}}
                @if($activeView === 'timeline')
                    <div class="flex items-center bg-slate-100 p-0.5 rounded-lg border border-slate-200">
                        <button type="button" wire:click="navigate(-14)" class="p-1 px-2.5 text-xs font-bold text-slate-600 hover:text-slate-800 rounded transition-all cursor-pointer"><i class="fas fa-chevron-left"></i> 14d</button>
                        <button type="button" wire:click="navigate(-1)" class="p-1 px-2 text-xs font-bold text-slate-600 hover:text-slate-800 rounded transition-all cursor-pointer"><i class="fas fa-chevron-left"></i></button>
                        <button type="button" wire:click="setToday" class="p-1 px-3 text-xs font-bold text-slate-600 hover:text-slate-800 bg-white shadow-sm rounded transition-all cursor-pointer">Today</button>
                        <button type="button" wire:click="navigate(1)" class="p-1 px-2 text-xs font-bold text-slate-600 hover:text-slate-800 rounded transition-all cursor-pointer"><i class="fas fa-chevron-right"></i></button>
                        <button type="button" wire:click="navigate(14)" class="p-1 px-2.5 text-xs font-bold text-slate-600 hover:text-slate-800 rounded transition-all cursor-pointer">14d <i class="fas fa-chevron-right"></i></button>
                    </div>
                @endif

                <div class="flex items-center gap-4 text-xs font-semibold text-slate-600 bg-slate-50/80 border border-slate-100 px-3 py-1.5 rounded-xl">
                    <span class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Legend:</span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 shadow-sm shadow-indigo-200"></span>
                        Confirmed
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></span>
                        Checked-In
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-50 shadow-sm shadow-amber-200"></span>
                        Reserved
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shadow-sm shadow-slate-200"></span>
                        Checked-Out
                    </span>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($activeView === 'timeline')
                {{-- Room-wise Horizontal Timeline View --}}
                <div class="overflow-x-auto border border-slate-200 rounded-2xl shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-sm table-fixed">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="sticky-column left-0 z-20 bg-slate-50 px-4 py-3.5 text-left text-xs font-black text-slate-500 uppercase tracking-wider border-r border-slate-200 w-44">
                                    Room Info
                                </th>
                                @foreach($days as $day)
                                    <th scope="col" class="px-2 py-3 text-center text-xs font-bold border-r border-slate-200 {{ $day['is_today'] ? 'bg-indigo-50 text-indigo-600' : 'text-slate-500' }} min-w-[72px]">
                                        <span class="block text-[9px] uppercase font-bold tracking-wider opacity-75">{{ $day['day_name'] }}</span>
                                        <span class="block text-sm font-black mt-0.5">{{ $day['day_num'] }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-150">
                            @foreach($rooms as $room)
                                <tr class="hover:bg-slate-50/20 transition-colors">
                                    <td class="sticky-column left-0 z-10 bg-white font-semibold text-slate-700 text-xs px-4 py-3 border-r border-slate-200 flex flex-col justify-center h-16 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                        <span class="font-extrabold text-slate-800 text-sm">Room {{ $room->room_number }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ $room->roomType->name }}</span>
                                    </td>
                                    @foreach($days as $idx => $day)
                                        @php
                                            $dateStr = $day['date_str'];
                                            $booking = $roomBookings[$room->id][$dateStr] ?? null;
                                            
                                            $isStart = false;
                                            $isEnd = false;
                                            $classes = '';
                                            $textClass = 'text-white';
                                            
                                            if ($booking) {
                                                $prevBooking = ($idx > 0) ? ($roomBookings[$room->id][$days[$idx-1]['date_str']] ?? null) : null;
                                                $nextBooking = ($idx < 13) ? ($roomBookings[$room->id][$days[$idx+1]['date_str']] ?? null) : null;
                                                
                                                $isStart = !$prevBooking || $prevBooking['reservation_id'] !== $booking['reservation_id'];
                                                $isEnd = !$nextBooking || $nextBooking['reservation_id'] !== $booking['reservation_id'];
                                                
                                                $classes = match($booking['status']) {
                                                    'Checked-In' => 'bg-emerald-500 hover:bg-emerald-600 shadow-emerald-100',
                                                    'Confirmed' => 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-100',
                                                    'Reserved' => 'bg-amber-500 hover:bg-amber-600 shadow-amber-100',
                                                    default => 'bg-slate-400 hover:bg-slate-500 shadow-slate-100',
                                                };
                                            }
                                        @endphp
                                        <td class="p-1.5 border-r border-slate-150 text-center min-w-[72px] h-16 {{ $day['is_today'] ? 'bg-indigo-50/10' : '' }} relative align-middle">
                                            @if($booking)
                                                <a href="/reservations?search={{ urlencode($booking['guest_name']) }}" 
                                                   class="block h-10 flex items-center justify-center text-[10px] font-black tracking-tight leading-tight px-1.5 transition-all shadow-sm {{ $classes }} {{ $textClass }} {{ $isStart ? 'rounded-l-lg ml-0.5' : '' }} {{ $isEnd ? 'rounded-r-lg mr-0.5' : '' }}">
                                                    @if($isStart)
                                                        <span class="truncate block max-w-full" title="{{ $booking['guest_name'] }}">{{ $booking['guest_name'] }}</span>
                                                    @endif
                                                </a>
                                            @else
                                                <button type="button" 
                                                        wire:click="openBookingModal({{ $room->id }}, '{{ $dateStr }}')"
                                                        class="w-full h-10 rounded-lg border border-dashed border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/30 flex items-center justify-center text-slate-300 hover:text-indigo-600 transition-all cursor-pointer group">
                                                    <i class="fas fa-plus text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                                </button>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- FullCalendar Month View --}}
                <div id="calendar" x-init="
                    const initCalendar = () => {
                        const calendar = new FullCalendar.Calendar($el, {
                            initialView: 'dayGridMonth',
                            headerToolbar: {
                                left:   'prev,next today',
                                center: 'title',
                                right:  'dayGridMonth,timeGridWeek,listWeek'
                            },
                            events: {{ json_encode($events) }},
                            height: 680,
                            dateClick: function(info) {
                                @this.openBookingModal(0, info.dateStr);
                            },
                            eventClick: function(info) {
                                const p = info.event.extendedProps;
                                
                                let statusBadge = '';
                                if (p.status === 'Checked-In') {
                                    statusBadge = `<span class='inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100'><span class='w-1.5 h-1.5 rounded-full bg-emerald-500'></span>Checked-In</span>`;
                                } else if (p.status === 'Confirmed') {
                                    statusBadge = `<span class='inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100'><span class='w-1.5 h-1.5 rounded-full bg-indigo-500'></span>Confirmed</span>`;
                                } else if (p.status === 'Reserved') {
                                    statusBadge = `<span class='inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100'><span class='w-1.5 h-1.5 rounded-full bg-amber-500'></span>Reserved</span>`;
                                } else {
                                    statusBadge = `<span class='inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-50 text-slate-700 border border-slate-100'><span class='w-1.5 h-1.5 rounded-full bg-slate-400'></span>Checked-Out</span>`;
                                }

                                Swal.fire({
                                    title: `<div class='text-left font-black text-slate-800 text-lg border-b border-slate-100 pb-3 flex items-center gap-2'>
                                                <div class='w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100'><i class='fas fa-calendar-check text-sm'></i></div>
                                                Reservation Details
                                            </div>`,
                                    html: `<div class='text-left font-sans text-sm text-slate-600 space-y-3 pt-3'>
                                        <div class='flex justify-between items-center py-1 border-b border-slate-50'>
                                            <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Guest Name</span>
                                            <span class='font-bold text-slate-800'>\${p.guest || 'N/A'}</span>
                                        </div>
                                        <div class='flex justify-between items-center py-1 border-b border-slate-50'>
                                            <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Room Assigned</span>
                                            <span class='font-bold text-indigo-600 bg-indigo-50/50 px-2 py-0.5 rounded border border-indigo-100/50 text-xs'>Room \${p.room || 'N/A'}</span>
                                        </div>
                                        <div class='flex justify-between items-center py-1 border-b border-slate-50'>
                                            <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Status</span>
                                            <span>\${statusBadge}</span>
                                        </div>
                                        <div class='flex justify-between items-center py-1 border-b border-slate-50'>
                                            <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Check-In</span>
                                            <span class='font-semibold text-slate-700'>\${info.event.startStr}</span>
                                        </div>
                                        <div class='flex justify-between items-center py-1'>
                                            <span class='font-semibold text-slate-400 text-[10px] uppercase tracking-wider'>Check-Out</span>
                                            <span class='font-semibold text-slate-700'>\${info.event.endStr}</span>
                                        </div>
                                        <div class='pt-4 flex gap-2'>
                                            <a href='/reservations?search=\${encodeURIComponent(p.guest)}' class='w-full text-center inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition-colors'>
                                                <i class='fas fa-external-link-alt text-[10px]'></i> View Reservation
                                            </a>
                                        </div>
                                    </div>`,
                                    showCloseButton: true,
                                    showConfirmButton: false,
                                    width: 380,
                                    customClass: {
                                        popup: 'rounded-2xl border border-slate-100 shadow-xl p-5',
                                        closeButton: 'focus:outline-none focus:ring-0'
                                    }
                                });
                            },
                            dayCellClassNames: function(arg) {
                                return arg.isToday ? ['bg-indigo-50'] : [];
                            },
                        });
                        calendar.render();
                        window.addEventListener('refresh-fullcalendar', () => {
                            setTimeout(() => calendar.updateSize(), 50);
                        });
                        $watch('sidebarOpen', () => {
                            setTimeout(() => calendar.updateSize(), 200);
                        });
                    };
                    initCalendar();
                "></div>
            @endif
        </div>
    </div>

    {{-- Add Reservation Popup Modal --}}
    <div x-data="{ show: @entangle('showModal') }" 
         x-show="show" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Body -->
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-100" 
                 x-show="show" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.outside="show = false">
                
                {{-- Modal Header --}}
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-150 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center border border-indigo-100">
                            <i class="fas fa-calendar-plus text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800 leading-none">New Reservation</h3>
                            <p class="text-[10px] text-slate-400 mt-1 leading-none">Quickly assign room and create booking record</p>
                        </div>
                    </div>
                    <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 transition-colors p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Modal Form Content --}}
                <div class="p-6">
                    <form wire:submit.prevent="saveBooking" class="space-y-4 text-xs">
                        
                        {{-- Booking Date Inputs --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="pms-label text-[10px] font-bold text-slate-500 uppercase tracking-wider">Check-In Date <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="modalCheckInDate" class="pms-input text-xs mt-1">
                                @error('modalCheckInDate') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="pms-label text-[10px] font-bold text-slate-500 uppercase tracking-wider">Check-Out Date <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="modalCheckOutDate" class="pms-input text-xs mt-1">
                                @error('modalCheckOutDate') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Room and Capacity Info --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="pms-label text-[10px] font-bold text-slate-500 uppercase tracking-wider">Room <span class="text-red-500">*</span></label>
                                @if($modalRoomId > 0)
                                    <input type="text" class="pms-input text-xs mt-1 bg-slate-50 text-slate-500 cursor-not-allowed font-extrabold" value="Room {{ $modalRoomNumber }}" readonly>
                                    <input type="hidden" wire:model="modalRoomId">
                                @else
                                    <select wire:model="modalRoomId" class="pms-select text-xs mt-1">
                                        <option value="">Select Room...</option>
                                        @foreach($rooms as $r)
                                            <option value="{{ $r->id }}">Room {{ $r->room_number }} ({{ $r->roomType->name }})</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('modalRoomId') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="pms-label text-[10px] font-bold text-slate-500 uppercase tracking-wider">Adults <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="modalAdults" class="pms-input text-xs mt-1" min="1">
                                @error('modalAdults') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="pms-label text-[10px] font-bold text-slate-500 uppercase tracking-wider">Children</label>
                                <input type="number" wire:model="modalChildren" class="pms-input text-xs mt-1" min="0">
                                @error('modalChildren') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Guest Details Section --}}
                        <div class="border-t border-slate-100 pt-4">
                            <div class="flex items-center justify-between mb-2">
                                <label class="pms-label text-[10px] font-black text-slate-500 uppercase tracking-wider mb-0">Guest Information <span class="text-red-500">*</span></label>
                                <div class="flex bg-slate-100 p-0.5 rounded-lg border border-slate-200">
                                    <button type="button" wire:click="$set('modalIsNewGuest', false)" 
                                            class="px-2.5 py-1 text-[9px] font-bold rounded transition-all cursor-pointer {{ !$modalIsNewGuest ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                                        Existing Guest
                                    </button>
                                    <button type="button" wire:click="$set('modalIsNewGuest', true)" 
                                            class="px-2.5 py-1 text-[9px] font-bold rounded transition-all cursor-pointer {{ $modalIsNewGuest ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                                        New Guest
                                    </button>
                                </div>
                            </div>

                            @if(!$modalIsNewGuest)
                                <select wire:model="modalGuestId" class="pms-select text-xs mt-1">
                                    <option value="">Select guest...</option>
                                    @foreach($guests as $g)
                                        <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->email ?? 'no email' }})</option>
                                    @endforeach
                                </select>
                                @error('modalGuestId') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                            @else
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-slate-50/50 p-4 rounded-xl border border-dashed border-slate-250 mt-1">
                                    <div>
                                        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Guest Name <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="modalNewGuestName" class="pms-input text-xs mt-1" placeholder="Full Name">
                                        @error('modalNewGuestName') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                                        <input type="email" wire:model="modalNewGuestEmail" class="pms-input text-xs mt-1" placeholder="email@example.com">
                                        @error('modalNewGuestEmail') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Phone Number</label>
                                        <input type="text" wire:model="modalNewGuestPhone" class="pms-input text-xs mt-1" placeholder="Phone Number">
                                        @error('modalNewGuestPhone') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Special Notes --}}
                        <div>
                            <label class="pms-label text-[10px] font-bold text-slate-500 uppercase tracking-wider">Special Notes</label>
                            <textarea wire:model="modalSpecialNotes" rows="2" class="pms-input text-xs mt-1 placeholder:opacity-50" placeholder="Dietary requests, room preferences, extra bed..."></textarea>
                            @error('modalSpecialNotes') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                        </div>

                        {{-- Payment details --}}
                        <div class="border-t border-slate-100 pt-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="pms-label text-[10px] font-bold text-slate-500 uppercase tracking-wider">Advance Payment Amount ($)</label>
                                <input type="number" step="0.01" wire:model="modalPaymentAmount" class="pms-input text-xs mt-1" placeholder="0.00">
                                @error('modalPaymentAmount') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="pms-label text-[10px] font-bold text-slate-500 uppercase tracking-wider">Payment Method</label>
                                <select wire:model="modalPaymentType" class="pms-select text-xs mt-1">
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="UPI">UPI</option>
                                </select>
                                @error('modalPaymentType') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="border-t border-slate-100 pt-4 flex justify-end gap-3">
                            <button type="button" @click="show = false" class="btn-secondary rounded-lg px-4 py-2 text-xs font-bold transition-all">Cancel</button>
                            <button type="submit" class="btn-primary rounded-lg px-5 py-2 text-xs font-bold transition-all shadow-sm">Save Reservation</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

</div>