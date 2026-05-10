<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">予約管理</h1>

            {{-- 検索フォーム --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <form method="GET" action="{{ route('admin.reservations.index') }}" id="search-form">
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <div class="flex-1 w-full">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="名前、メールアドレスで検索" 
                                class="w-full bg-gray-50 border-none rounded-xl py-3 focus:ring-2 focus:ring-indigo-100">
                        </div>
                        <div class="w-full sm:w-48">
                            <select name="status" class="w-full bg-gray-50 border-none rounded-xl py-3 focus:ring-2 focus:ring-indigo-100">
                                <option value="">全てのステータス</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>予約確定</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>チェックイン済</option>
                                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>キャンセル</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button type="submit" class="flex-1 sm:flex-none px-8 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition">
                                検索
                            </button>
                            @if(request('keyword') || request('status') || request('date_range'))
                                <a href="{{ route('admin.reservations.index') }}" class="text-sm text-gray-400 hover:text-gray-600 font-bold whitespace-nowrap transition">
                                    クリア
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- 日付ショートカット --}}
                    <div class="flex flex-wrap gap-2 mt-4">
                        <span class="text-xs text-gray-400 font-bold self-center mr-2 uppercase tracking-widest">フィルター検索:</span>
                        
                        <button type="button" onclick="setQuickFilter('today')" 
                            class="px-4 py-1.5 text-xs font-bold rounded-full border {{ request('date_range') === 'today' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-400' }} transition">
                            本日
                        </button>
                        
                        <button type="button" onclick="setQuickFilter('tomorrow')" 
                            class="px-4 py-1.5 text-xs font-bold rounded-full border {{ request('date_range') === 'tomorrow' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-400' }} transition">
                            明日
                        </button>
                        
                        <button type="button" onclick="setQuickFilter('week')" 
                            class="px-4 py-1.5 text-xs font-bold rounded-full border {{ request('date_range') === 'week' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-400' }} transition">
                            今後7日間
                        </button>

                        <input type="hidden" name="date_range" id="date_range_input" value="{{ request('date_range') }}">
                    </div>
                </form>
            </div>

            {{-- 予約一覧テーブル --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-900 font-bold">
                        <tr>
                            <th class="p-4">予約番号</th>
                            <th class="p-4">宿泊者</th>
                            <th class="p-4">チェックイン</th>
                            <th class="p-4">ステータス</th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($reservations as $res)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">#{{ str_pad($res->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="p-4">{{ $res->guest_name }}</td>
                            <td class="p-4">{{ \Carbon\Carbon::parse($res->check_in_date)->format('Y/m/d') }}</td>
                            <td class="p-4">
                                @if($res->status == 1) <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">確定</span>
                                @elseif($res->status == 2) <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">チェックイン済</span>
                                @elseif($res->status == 3) <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">キャンセル</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.reservations.show', $res) }}" class="text-indigo-600 font-bold hover:underline">詳細を見る</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $reservations->links() }}</div>
        </div>
    </div>

    <script>
    function setQuickFilter(range) {
        document.getElementById('date_range_input').value = range;
        document.getElementById('search-form').submit();
    }
    </script>

</x-app-layout>