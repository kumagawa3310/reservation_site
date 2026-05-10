<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                予約枠管理
            </h2>
            <a href="{{ route('admin.slot-rules.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">
                自動生成ルールを設定
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="px-4 py-3 bg-red-100 border border-red-400 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 予約枠の一括作成フォーム --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">予約枠の一括作成</h3>
                <form action="{{ route('admin.slots.bulkStore') }}" method="POST"
                      class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">部屋タイプ</label>
                        <select name="room_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->number_of_rooms }}室)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">開始日</label>
                        <input type="date" name="start_date" min="{{ date('Y-m-d') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">終了日</label>
                        <input type="date" name="end_date" min="{{ date('Y-m-d') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">料金（空白で部屋設定値）</label>
                        <input type="number" name="price" min="0" placeholder="例: 12000"
                               class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <x-primary-button class="w-full justify-center">一括作成</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- 予約枠一覧 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-sm font-medium text-gray-700">登録済み予約枠</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">日付</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">部屋名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">料金</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ステータス</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($slots as $reservationSlot)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reservationSlot->date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $reservationSlot->room->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($reservationSlot->price) }}円</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($reservationSlot->status === 'available')
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">空き</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">予約済</span>
                                    @endif
                                </td>
<td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
    @if($reservationSlot->status === 'available')
        <!-- 編集ボタン -->
        <a href="{{ route('admin.slots.edit', $reservationSlot) }}" 
           class="text-indigo-600 hover:text-indigo-900 font-medium">
            編集
        </a>

        <!-- 既存の削除ボタン -->
        <form action="{{ route('admin.slots.destroy', $reservationSlot) }}" method="POST"
              onsubmit="return confirm('この予約枠を削除しますか？');" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">削除</button>
        </form>
    @else
        <span class="text-gray-400 cursor-not-allowed">編集・削除不可</span>
    @endif
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    予約枠が登録されていません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4">
                    {{ $slots->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
