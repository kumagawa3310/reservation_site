<x-app-layout>
    <div x-data="{ showCancelModal: false }" class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4">
            
            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">予約詳細 #{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</h1>
                <a href="{{ route('admin.reservations.index') }}" class="text-gray-500 hover:text-gray-900 font-bold text-sm">← 一覧に戻る</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- 左側：予約情報 --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h2 class="text-lg font-bold text-gray-900 mb-6 border-b pb-4">宿泊情報</h2>
                        <dl class="space-y-4 text-sm">
                            <div class="flex justify-between"><dt class="text-gray-500">宿泊者名</dt><dd class="font-bold text-gray-900">{{ $reservation->guest_name }} 様</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">メールアドレス</dt><dd class="font-bold text-gray-900">{{ $reservation->guest_email }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">プラン</dt><dd class="font-bold text-gray-900">{{ $reservation->stayPlan->name }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">日程</dt><dd class="font-bold text-gray-900">{{ $reservation->check_in_date->format('Y/m/d') }} 〜 {{ $reservation->check_out_date->format('Y/m/d') }}</dd></div>
                            <div class="flex justify-between"><dt class="text-gray-500">人数</dt><dd class="font-bold text-gray-900">{{ $reservation->number_of_guests }} 名</dd></div>
                            <div class="flex justify-between pt-4 border-t"><dt class="text-indigo-600 font-bold">合計金額</dt><dd class="font-black text-indigo-700 text-lg">¥{{ number_format($reservation->total_price) }}</dd></div>
                        </dl>
                    </div>

                    {{-- キャンセルボタン（確定時のみ表示） --}}
                    @if($reservation->status == 1)
                    <div class="text-right">
                        <button @click="showCancelModal = true" type="button" class="px-6 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition">
                            この予約をキャンセルする
                        </button>
                    </div>
                    @endif
                </div>

                {{-- 右側：メモ機能 --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sticky top-6">
                        <h2 class="text-md font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            管理者用メモ
                        </h2>
                        <form action="{{ route('admin.reservations.memo', $reservation) }}" method="POST">
                            @csrf @method('PUT')
                            <textarea name="admin_memo" rows="6" class="w-full bg-gray-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-indigo-100 text-sm resize-none mb-4" placeholder="対応履歴や特記事項を入力...">{{ old('admin_memo', $reservation->admin_memo) }}</textarea>
                            <button type="submit" class="w-full py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition">保存する</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- キャンセル確認モーダル (Alpine.js) --}}
        <div x-show="showCancelModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
            <div @click.away="showCancelModal = false" class="bg-white rounded-3xl w-full max-w-md shadow-2xl p-8">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-xl font-black text-center text-gray-900 mb-2">本当にキャンセルしますか？</h3>
                <p class="text-sm text-gray-500 text-center mb-8">この操作を実行すると、予約枠が解放され、お客様にキャンセル完了メールが自動送信されます。この操作は取り消せません。</p>
                
                <form action="{{ route('admin.reservations.cancel', $reservation) }}" method="POST" class="space-y-3">
                    @csrf
                    <button type="submit" class="w-full py-4 bg-red-600 text-white font-black rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-100">キャンセルを確定する</button>
                    <button type="button" @click="showCancelModal = false" class="w-full py-3 text-sm text-gray-500 font-bold hover:text-gray-800 transition">閉じる</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>