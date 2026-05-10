<x-app-layout>
    <div class="py-16 bg-gray-50 min-h-screen">
        <div class="max-w-xl mx-auto px-4">

            {{-- 完了アイコン --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">ご予約が完了しました</h1>
                <p class="mt-2 text-sm text-gray-500">確認メールをお送りしましたのでご確認ください。</p>
            </div>

            {{-- 予約内容サマリー --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">予約番号</p>
                    <p class="text-lg font-bold text-gray-900">#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>

                <dl class="divide-y divide-gray-50">
                    <div class="flex justify-between px-6 py-4 text-sm">
                        <dt class="text-gray-400 font-medium">宿泊プラン</dt>
                        <dd class="text-gray-900 font-bold">{{ $reservation->stayPlan->name }}</dd>
                    </div>
                    <div class="flex justify-between px-6 py-4 text-sm">
                        <dt class="text-gray-400 font-medium">お部屋</dt>
                        <dd class="text-gray-900 font-bold">{{ $reservation->stayPlan->room->name }}</dd>
                    </div>
                    <div class="flex justify-between px-6 py-4 text-sm">
                        <dt class="text-gray-400 font-medium">チェックイン</dt>
                        <dd class="text-gray-900 font-bold">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('Y年m月d日') }}</dd>
                    </div>
                    <div class="flex justify-between px-6 py-4 text-sm">
                        <dt class="text-gray-400 font-medium">チェックアウト</dt>
                        <dd class="text-gray-900 font-bold">{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('Y年m月d日') }}</dd>
                    </div>
                    <div class="flex justify-between px-6 py-4 text-sm">
                        <dt class="text-gray-400 font-medium">代表者氏名</dt>
                        <dd class="text-gray-900 font-bold">{{ $reservation->guest_name }} 様</dd>
                    </div>
                    <div class="flex justify-between px-6 py-4 text-sm">
                        <dt class="text-gray-400 font-medium">宿泊人数</dt>
                        <dd class="text-gray-900 font-bold">{{ $reservation->number_of_guests }} 名</dd>
                    </div>
                    <div class="flex justify-between px-6 py-4 text-sm bg-indigo-50">
                        <dt class="text-indigo-700 font-bold">合計金額</dt>
                        <dd class="text-indigo-700 font-extrabold text-base">¥{{ number_format($reservation->total_price) }}</dd>
                    </div>
                </dl>
            </div>

            {{-- ボタン --}}
            <div class="mt-8 text-center">
                <a href="{{ route('plans.index') }}"
                   class="inline-block px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                    プラン一覧に戻る
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
