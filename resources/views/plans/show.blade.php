<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('plans.index') }}" class="hover:text-indigo-600 transition">プラン一覧</a>
            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium truncate">{{ $plan->name }}</span>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 1. プラン基本情報カード --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="flex flex-col lg:flex-row">
                    {{-- 左: ビジュアル画像 --}}
                    <div class="lg:w-3/5 relative group">
                        <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                            @if($plan->room->image)
                                <img src="{{ asset('storage/' . $plan->room->image) }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 右: 概要テキスト --}}
                    <div class="lg:w-2/5 p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded tracking-wider uppercase">{{ $plan->room->name }}</span>
                                <span class="text-gray-400 text-xs">定員: {{ $plan->room->capacity }}名</span>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900 leading-tight mb-4">{{ $plan->name }}</h1>
                            <p class="text-gray-500 text-sm leading-relaxed line-clamp-4">{{ $plan->description }}</p>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-50">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Price per night</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-xs text-indigo-600 font-bold">¥</span>
                                <span class="text-4xl font-black text-indigo-600 tracking-tight">{{ number_format($plan->price) }}</span>
                                <span class="text-gray-400 text-xs">〜</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. カレンダー・予約セクション --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 lg:p-12">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">宿泊日を選択</h2>
                        <p class="text-sm text-gray-400 mt-1">カレンダーからチェックインとチェックアウト日を選んでください</p>
                    </div>
                    
                    {{-- 月移動ナビ --}}
                    <div class="flex items-center bg-gray-50 rounded-xl p-1 border border-gray-100">
                        <a href="{{ route('plans.show', ['plan' => $plan, 'month' => $prevMonth]) }}" class="p-2 hover:bg-white hover:shadow-sm rounded-lg transition text-gray-600">&lt; 前月</a>
                        <span class="px-6 font-bold text-gray-800 tabular-nums">{{ $date->format('Y年n月') }}</span>
                        <a href="{{ route('plans.show', ['plan' => $plan, 'month' => $nextMonth]) }}" class="p-2 hover:bg-white hover:shadow-sm rounded-lg transition text-gray-600">次月 &gt;</a>
                    </div>
                </div>

                {{-- カレンダー本体 --}}
                <div class="grid grid-cols-7 mb-10">
                    @foreach(['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'] as $dayOfWeek)
                        <div class="py-4 text-[10px] font-bold text-gray-300 text-center tracking-widest">{{ $dayOfWeek }}</div>
                    @endforeach

                    @foreach($calendar as $day)
                        @php
                            $dateStr = $day['date']->format('Y-m-d');
                            $isSelectable = !$day['is_past'] && $day['is_available'] && $day['is_current_month'];
                        @endphp
                        <div class="calendar-day group relative aspect-square flex flex-col items-center justify-center border-t border-gray-50 transition-all duration-200
                            {{ $isSelectable ? 'cursor-pointer hover:bg-indigo-50' : 'opacity-20' }}"
                            data-date="{{ $dateStr }}"
                            data-available="{{ $day['is_available'] ? '1' : '0' }}">
                            
                            {{-- 本日のマーカー --}}
                            @if($day['date']->isToday())
                                <div class="absolute top-2 w-1 h-1 bg-indigo-600 rounded-full"></div>
                            @endif

                            <span class="text-sm font-medium {{ $day['is_current_month'] ? 'text-gray-700' : 'text-gray-300' }} z-10">
                                {{ $day['date']->day }}
                            </span>

                            @if($day['is_available'] && !$day['is_past'])
                                <span class="text-[10px] mt-1 font-bold text-green-500 group-hover:scale-110 transition">○</span>
                            @else
                                <span class="text-[10px] mt-1 font-bold text-gray-300">×</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- 予約フォームエリア --}}
                <form action="{{ route('reservations.create', $plan) }}" method="GET" id="reservation-form" class="relative">
                    {{-- form-container クラスを付与し、スタイルを CSS で制御 --}}
                    <div class="form-container rounded-3xl p-6 lg:p-8 transition-all duration-300">
                        <div class="flex flex-col lg:flex-row items-center gap-6">
                            
                            <div class="flex flex-1 gap-4 w-full">
                                {{-- チェックイン --}}
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 text-center lg:text-left">Check-in</label>
                                    <input type="date" name="check_in" id="input-checkin" readonly required
                                        class="w-full bg-gray-50 border-none rounded-xl text-gray-800 font-bold focus:ring-2 focus:ring-indigo-100 py-3 text-center">
                                </div>
                                
                                {{-- 矢印アイコン --}}
                                <div class="flex items-end pb-3">
                                    <div class="p-2 bg-indigo-50 rounded-full">
                                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- チェックアウト --}}
                                <div class="flex-1">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 text-center lg:text-left">Check-out</label>
                                    <input type="date" name="check_out" id="input-checkout" readonly required
                                        class="w-full bg-gray-50 border-none rounded-xl text-gray-800 font-bold focus:ring-2 focus:ring-indigo-100 py-3 text-center">
                                </div>
                            </div>

                            {{-- 予約ボタン --}}
                            <button type="submit" id="submit-btn" disabled
                                    class="w-full lg:w-auto px-12 py-4 bg-gray-100 text-gray-400 font-bold rounded-xl transition-all cursor-not-allowed whitespace-nowrap">
                                日程を選択
                            </button>
                        </div>
                        
                        <p id="form-helper-text" class="mt-4 text-xs text-gray-400 text-center lg:text-left">
                            ※カレンダーをクリックして宿泊日を選んでください
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* 1. フォームコンテナのベーススタイル（白基調） */
        .form-container {
            background-color: #ffffff;
            border: 2px solid #f8fafc; /* 非常に薄いグレー */
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
        }

        /* 2. カレンダー：選択された日（明るいインディゴ） */
        .day-selected { 
            background-color: #6366f1 !important; 
            color: white !important; 
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        .day-selected span { color: white !important; font-weight: 800; }
        
        /* 3. カレンダー：期間中のハイライト（さらに淡いブルー） */
        .day-in-range { 
            background-color: #f5f7ff !important; 
            border-radius: 0;
        }
        .day-in-range:hover { background-color: #eef2ff !important; }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let checkin = null;
        let checkout = null;
        const days = document.querySelectorAll('.calendar-day');

        days.forEach(day => {
            day.addEventListener('click', function() {
                const selectedDate = this.dataset.date;
                if (this.dataset.available !== '1') return;

                // --- 2回クリックで解除ロジック ---
                if (selectedDate === checkin) {
                    // チェックイン日を再クリック -> 全リセット
                    checkin = null;
                    checkout = null;
                } else if (selectedDate === checkout) {
                    // チェックアウト日を再クリック -> チェックアウトのみリセット
                    checkout = null;
                } else if (!checkin || (checkin && checkout)) {
                    // 初回選択 or 選択し直し
                    checkin = selectedDate;
                    checkout = null;
                } else if (checkin && !checkout) {
                    // チェックイン済みでチェックアウトを選択
                    if (selectedDate < checkin) {
                        // チェックインより前を選んだらチェックインを上書き
                        checkin = selectedDate;
                    } else if (isRangeAvailable(checkin, selectedDate)) {
                        checkout = selectedDate;
                    } else {
                        alert('ご選択の期間内に満室の日が含まれています。');
                        checkin = selectedDate;
                        checkout = null;
                    }
                }

                updateCalendarUI();
                updateForm();
            });
        });

        function updateCalendarUI() {
            days.forEach(day => {
                const date = day.dataset.date;
                day.classList.remove('day-selected', 'day-in-range');

                if (date === checkin || date === checkout) {
                    day.classList.add('day-selected');
                } else if (checkin && checkout && date > checkin && date < checkout) {
                    day.classList.add('day-in-range');
                }
            });
        }

        function updateForm() {
            const ciInput = document.getElementById('input-checkin');
            const coInput = document.getElementById('input-checkout');
            const btn = document.getElementById('submit-btn');
            const helper = document.getElementById('form-helper-text');
            
            ciInput.value = checkin || '';
            coInput.value = checkout || '';

            if (checkin && checkout) {
                btn.disabled = false;
                btn.innerText = '予約手続きへ進む';
                // 明るいメインカラー
                btn.className = "w-full lg:w-auto px-12 py-4 bg-indigo-600 text-white font-bold rounded-xl transition-all hover:bg-indigo-500 shadow-lg shadow-indigo-100 hover:-translate-y-0.5 active:translate-y-0";
                helper.innerText = "日程が決まりました！";
                helper.className = "mt-4 text-xs text-indigo-500 text-center lg:text-left font-bold";
            } else {
                btn.disabled = true;
                btn.innerText = checkin ? 'チェックアウト日を選択' : '日程を選択';
                // シンプルなグレーアウト
                btn.className = "w-full lg:w-auto px-12 py-4 bg-gray-100 text-gray-400 font-bold rounded-xl cursor-not-allowed transition-all";
                helper.innerText = "※カレンダーをクリックして宿泊日を選んでください";
                helper.className = "mt-4 text-xs text-gray-400 text-center lg:text-left";
            }
        }

        function isRangeAvailable(start, end) {
            let available = true;
            days.forEach(day => {
                const d = day.dataset.date;
                // 宿泊期間内の空きを確認
                if (d >= start && d < end && day.dataset.available !== '1') {
                    available = false;
                }
            });
            return available;
        }
    });
    </script>
</x-app-layout>