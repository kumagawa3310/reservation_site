<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('plans.index') }}" class="hover:text-indigo-600 transition">プラン一覧</a>
            <span>/</span>
            <span class="text-gray-900 font-medium truncate">{{ $plan->name }}</span>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- 横2カラム: 画像左・情報右 --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden lg:flex">

                {{-- 左: 画像 --}}
                <div class="lg:w-1/2 shrink-0 bg-gray-100 h-64 lg:h-auto">
                    @if($plan->room->image)
                        <img src="{{ asset('storage/' . $plan->room->image) }}"
                             alt="{{ $plan->room->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full text-gray-300 min-h-64">
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- 右: 情報 --}}
                <div class="lg:w-1/2 flex flex-col p-8">

                    {{-- バッジ群 --}}
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-md">
                            {{ $plan->room->name }}
                        </span>
                        <span class="text-gray-300">|</span>
                        <span class="text-gray-500 text-xs">最大定員：{{ $plan->room->capacity }}名様</span>
                        @if($plan->available_from || $plan->available_to)
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-md border border-amber-200">
                                期間限定
                            </span>
                        @endif
                    </div>

                    {{-- タイトル --}}
                    <h1 class="text-2xl font-extrabold text-gray-900 mb-4">{{ $plan->name }}</h1>

                    {{-- 提供期間 --}}
                    @if($plan->available_from || $plan->available_to)
                        <div class="flex items-center gap-2 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5 mb-4">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>
                                {{ $plan->available_from?->format('Y年m月d日') ?? '〜' }}
                                〜
                                {{ $plan->available_to?->format('Y年m月d日') ?? '' }}
                            </span>
                        </div>
                    @endif

                    {{-- 説明 --}}
                    <p class="text-gray-600 text-sm leading-relaxed flex-grow whitespace-pre-wrap mb-6">
                        {{ $plan->description ?? 'ご不明な点はお問い合わせください。' }}
                    </p>

                    {{-- 料金 --}}
                    <div class="border-t border-gray-100 pt-5 mb-6">
                        <p class="text-xs text-gray-400 mb-1">基本料金（税込 / 1泊）</p>
                        <p class="text-3xl font-black text-indigo-600">
                            ¥{{ number_format($plan->price) }}
                            <span class="text-sm font-normal text-gray-400">〜</span>
                        </p>
                    </div>

                    <div class="mb-12 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                        
                        {{-- 月移動ナビゲーション --}}
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-xl font-bold text-gray-900">
                                {{ $date->format('Y年n月') }}
                            </h3>
                            
                            <div class="inline-flex rounded-md shadow-sm" role="group">
                                <a href="{{ route('plans.show', ['plan' => $plan, 'month' => $prevMonth]) }}" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-l-lg hover:bg-gray-100 transition">
                                    &lt; 前月
                                </a>
                                <a href="{{ route('plans.show', ['plan' => $plan, 'month' => now()->format('Y-m')]) }}" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-t border-b border-gray-200 hover:bg-gray-100 transition">
                                    今月
                                </a>
                                <a href="{{ route('plans.show', ['plan' => $plan, 'month' => $nextMonth]) }}" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-r-lg hover:bg-gray-100 transition">
                                    次月 &gt;
                                </a>
                            </div>
                        </div>

                        {{-- 曜日・日付部分はそのまま --}}
                        <div class="grid grid-cols-7 gap-1 text-center">
                            @foreach(['日', '月', '火', '水', '木', '金', '土'] as $index => $dayOfWeek)
                                <div class="py-2 text-xs font-bold {{ $index === 0 ? 'text-red-500' : ($index === 6 ? 'text-blue-500' : 'text-gray-500') }}">
                                    {{ $dayOfWeek }}
                                </div>
                            @endforeach

                            @foreach($calendar as $day)
                                <div class="relative py-4 border border-gray-50 rounded-lg flex flex-col items-center justify-center 
                                    {{ $day['is_current_month'] ? 'bg-white' : 'bg-gray-50/50 text-gray-300' }}
                                    {{ $day['is_past'] || !$day['is_available'] ? 'opacity-60' : 'hover:bg-indigo-50 cursor-pointer transition' }}">
                                    
                                    <span class="text-sm mb-1 {{ $day['date']->isToday() ? 'text-indigo-600 font-bold underline' : '' }}">
                                        {{ $day['date']->day }}
                                    </span>

                                    @if($day['is_available'])
                                        <span class="text-green-600 font-bold text-lg leading-none">○</span>
                                    @else
                                        <span class="text-gray-400 font-bold text-lg leading-none">{{ $day['status'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- 予約ボタン --}}
                    <a href="#"
                    class="block w-full text-center px-8 py-3.5 bg-gray-800 text-white font-bold rounded-xl hover:bg-gray-900 shadow-md shadow-gray-200 transition">
                        空室確認・予約へ進む
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
