<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">予約枠 自動生成ルール</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 border border-green-400 text-green-800 rounded whitespace-pre-line">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ルール追加フォーム --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700">ルールを追加する</h3>
                </div>
                <form method="POST" action="{{ route('admin.slot-rules.store') }}"
                      class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">部屋タイプ</label>
                        <select name="room_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">選択してください</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">料金上書き（空白=デフォルト）</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="price_override" min="0" placeholder="例: 15000"
                                   class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <span class="text-sm text-gray-500 shrink-0">円</span>
                        </div>
                    </div>
                    <div>
                        <x-primary-button class="w-full justify-center">追加</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- ルール一覧 --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700">設定済みルール</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">部屋タイプ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">生成期間</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">料金</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状態</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($rules as $rule)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $rule->room->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $rule->start_date->format('Y/m/d') }} 〜 {{ $rule->end_date->format('Y/m/d') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if ($rule->price_override)
                                        {{ number_format($rule->price_override) }}円
                                        <span class="text-xs text-indigo-600">（上書き）</span>
                                    @else
                                        {{ number_format($rule->room->price) }}円
                                        <span class="text-xs text-gray-400">（デフォルト）</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <form method="POST" action="{{ route('admin.slot-rules.update', $rule) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="start_date" value="{{ $rule->start_date->format('Y-m-d') }}">
                                        <input type="hidden" name="end_date" value="{{ $rule->end_date->format('Y-m-d') }}">
                                        <input type="hidden" name="price_override" value="{{ $rule->price_override }}">
                                        <input type="hidden" name="is_active" value="{{ $rule->is_active ? 0 : 1 }}">
                                        <button type="submit"
                                                class="px-2 py-1 text-xs font-medium rounded-full
                                                    {{ $rule->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                            {{ $rule->is_active ? '有効' : '無効' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-right">
                                    <form method="POST" action="{{ route('admin.slot-rules.destroy', $rule) }}"
                                          onsubmit="return confirm('このルールを削除しますか？')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">削除</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    ルールがまだ設定されていません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- スケジューラー案内 --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg px-6 py-4 text-sm text-blue-800">
                <p class="font-medium mb-1">自動実行の設定（サーバー管理者向け）</p>
                <p>以下の cron を登録すると毎日深夜 0 時に自動生成されます：</p>
                <code class="block mt-2 bg-blue-100 rounded px-3 py-2 text-xs font-mono">
                    * * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1
                </code>
            </div>

        </div>
    </div>
</x-app-layout>
