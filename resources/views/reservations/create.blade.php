<x-app-layout>
    {{-- Alpine.jsで入力を管理 --}}
    <div x-data="{
        showModal: false,
        name: '{{ old('guest_name', auth()->user()->name ?? '') }}',
        email: '{{ old('guest_email', auth()->user()->email ?? '') }}',
        guests: {{ old('number_of_guests', 1) }},
        planPrice: {{ $plan->price }},
        nights: {{ $nights }},
        get totalPrice() {
            // 人数が1以上の時だけ計算
            let g = parseInt(this.guests) || 1;
            return this.planPrice * this.nights * g;
        },
        formatPrice(price) {
            return new Intl.NumberFormat('ja-JP').format(price);
        }
    }" class="py-12 bg-gray-50 min-h-screen">

        <div class="max-w-2xl mx-auto px-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-8 text-center">ご予約内容の入力</h1>

            @if (session('error'))
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 lg:p-10">
                <form id="reservation-form" action="{{ route('reservations.store', $plan) }}" method="POST">
                    @csrf
                    {{-- 隠しフィールド --}}
                    <input type="hidden" name="check_in" value="{{ $check_in }}">
                    <input type="hidden" name="check_out" value="{{ $check_out }}">

                    <div class="space-y-8">
                        {{-- 選択した日程の表示（読み取り専用） --}}
                        <div class="grid grid-cols-2 gap-4 pb-6 border-b border-gray-50">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Check-in</label>
                                <p class="text-lg font-bold text-gray-800">{{ $check_in }}</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Check-out</label>
                                <p class="text-lg font-bold text-gray-800">{{ $check_out }}</p>
                            </div>
                        </div>

                        {{-- お客様情報 --}}
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">代表者氏名</label>
                                <input type="text" name="guest_name" x-model="name" required 
                                    class="w-full border-none bg-gray-50 rounded-xl py-4 focus:ring-2 focus:ring-indigo-100 transition-all"
                                    placeholder="山田 太郎">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">メールアドレス</label>
                                <input type="email" name="guest_email" x-model="email" required 
                                    class="w-full border-none bg-gray-50 rounded-xl py-4 focus:ring-2 focus:ring-indigo-100 transition-all"
                                    placeholder="example@mail.com">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">宿泊人数</label>
                                <div class="flex items-center gap-3">
                                    <input type="number" name="number_of_guests" x-model="guests" min="1" required
                                        class="w-32 border-none bg-gray-50 rounded-xl py-4 focus:ring-2 focus:ring-indigo-100 transition-all text-center">
                                    <span class="text-xs text-gray-400 font-medium">名</span>
                                </div>
                            </div>
                        </div>

                        {{-- 合計金額のリアルタイム表示 --}}
                        <div class="flex items-center justify-between p-5 bg-indigo-50 rounded-2xl border border-indigo-100">
                            <div>
                                <span class="block text-sm font-bold text-indigo-900">合計金額</span>
                                <span class="block text-xs text-indigo-500">¥<span x-text="formatPrice(planPrice)"></span> × <span x-text="nights"></span>泊 × <span x-text="guests || 1"></span>名</span>
                            </div>
                            <div class="text-2xl font-black text-indigo-700">
                                ¥<span x-text="formatPrice(totalPrice)"></span>
                            </div>
                        </div>

                        {{-- 確認モーダルを開くボタン --}}
                        <button type="button" @click="showModal = true"
                            class="mt-10 w-full py-5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                            <span>予約内容を確認する</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- モーダル: 予約確認 --}}
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
             style="display: none;">
            
            <div @click.away="showModal = false" class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden">
                <div class="p-8">
                    <h3 class="text-xl font-black text-gray-900 mb-6 text-center">予約内容の確認</h3>
                    
                    <div class="space-y-4 bg-gray-50 rounded-2xl p-6 mb-8">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400 font-bold">宿泊プラン</span>
                            <span class="text-gray-900 font-bold">{{ $plan->name }}</span>
                        </div>
                        <div class="flex justify-between text-sm border-t border-gray-200 pt-4">
                            <span class="text-gray-400 font-bold">チェックイン</span>
                            <span class="text-gray-900 font-bold">{{ $check_in }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400 font-bold">チェックアウト</span>
                            <span class="text-gray-900 font-bold">{{ $check_out }}</span>
                        </div>
                        <div class="flex justify-between text-sm border-t border-gray-200 pt-4">
                            <span class="text-gray-400 font-bold">お名前</span>
                            <span class="text-gray-900 font-bold" x-text="name"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400 font-bold">人数</span>
                            <span class="text-gray-900 font-bold" x-text="guests + ' 名'"></span>
                        </div>
                        <div class="flex justify-between text-base border-t border-indigo-200 pt-4 mt-2">
                            <span class="text-indigo-600 font-bold">合計金額(税込)</span>
                            <span class="text-indigo-700 font-black" x-text="'¥' + formatPrice(totalPrice)"></span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="button" onclick="document.getElementById('reservation-form').submit();"
                            class="w-full py-4 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                            予約を確定する
                        </button>
                        <button type="button" @click="showModal = false"
                            class="w-full py-3 text-sm text-gray-400 font-bold hover:text-gray-600 transition-all">
                            入力を修正する
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>