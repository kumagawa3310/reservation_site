<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">宿泊プラン一覧</h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <form action="{{ route('plans.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- キーワード検索 --}}
                    <div class="md:col-span-2">
                        <x-input-label for="keyword" value="キーワード" class="sr-only" />
                        <x-text-input id="keyword" name="keyword" type="text" class="w-full" 
                            placeholder="プラン名や説明文から検索" :value="request('keyword')" />
                    </div>

                    {{-- 上限予算 --}}
                    <div>
                        <select name="max_price" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">上限なし</option>
                            @foreach([10000, 20000, 30000, 50000] as $price)
                                <option value="{{ $price }}" {{ request('max_price') == $price ? 'selected' : '' }}>
                                    {{ number_format($price) }}円以下
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 検索ボタン --}}
                    <div>
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            検索する
                        </button>
                    </div>
                </form>
                
                {{-- 検索結果のクリア --}}
                @if(request()->anyFilled(['keyword', 'max_price']))
                    <div class="mt-3">
                        <a href="{{ route('plans.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">
                            検索条件をクリア
                        </a>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($plans as $plan)
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 overflow-hidden flex flex-col">

                        {{-- サムネイル --}}
                        <div class="relative h-48 bg-gray-100 shrink-0">
                            @if($plan->room->image)
                                <img src="{{ asset('storage/' . $plan->room->image) }}"
                                     alt="{{ $plan->room->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-300">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-white/90 backdrop-blur text-indigo-700 text-xs font-bold rounded-full shadow-sm">
                                {{ $plan->room->name }}
                            </span>

                            @if($plan->available_from || $plan->available_to)
                                <span class="absolute top-3 right-3 px-2.5 py-1 bg-amber-400 text-white text-xs font-bold rounded-full shadow-sm">
                                    期間限定
                                </span>
                            @endif
                        </div>

                        {{-- テキスト --}}
                        <div class="p-5 flex flex-col flex-grow">
                            <h2 class="text-base font-bold text-gray-900 mb-1 line-clamp-1">{{ $plan->name }}</h2>
                            <p class="text-gray-500 text-sm leading-relaxed line-clamp-2 flex-grow">
                                {{ $plan->description ?? '詳細は下記よりご確認ください。' }}
                            </p>

                            @if($plan->available_from || $plan->available_to)
                                <p class="mt-2 text-xs text-amber-600 font-medium">
                                    {{ $plan->available_from?->format('Y/m/d') ?? '〜' }} 〜 {{ $plan->available_to?->format('Y/m/d') ?? '' }}
                                </p>
                            @endif
                        </div>

                        {{-- フッター --}}
                        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-400">1泊 / 1名様</p>
                                <p class="text-xl font-extrabold text-indigo-600">
                                    ¥{{ number_format($plan->price) }}<span class="text-xs font-normal text-gray-400">〜</span>
                                </p>
                            </div>
                            <a href="{{ route('plans.show', $plan) }}"
                                class="px-4 py-2 bg-gray-600 text-white text-sm font-bold rounded-lg hover:bg-gray-700 transition shadow-sm">
                                詳細を見る
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 text-center text-gray-400">
                        現在、ご案内可能なプランがございません。
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
