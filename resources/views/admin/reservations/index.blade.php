<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                予約管理
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 検索フォーム --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
                <form method="GET" action="{{ route('admin.reservations.index') }}" id="search-form">
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <!-- キーワード入力 -->
                        <div class="flex-1 w-full">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="名前、メールアドレスで検索" 
                                class="w-full bg-gray-50 border-none rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-indigo-100 placeholder-gray-400">
                        </div>
                        
                        <!-- ステータス選択 -->
                        <div class="w-full sm:w-40">
                            <select name="status" class="w-full bg-gray-50 border-none rounded-lg py-2 px-3 text-sm focus:ring-2 focus:ring-indigo-100">
                                <option value="">全てのステータス</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>確定</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>チェックイン済</option>
                                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>キャンセル</option>
                            </select>
                        </div>
                        
                        <!-- ボタンエリア -->
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button type="submit" class="flex-1 sm:flex-none px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                検索
                            </button>
                            @if(request('keyword') || request('status') || request('date_range'))
                                <a href="{{ route('admin.reservations.index') }}" class="text-xs text-gray-400 hover:text-gray-600 font-medium whitespace-nowrap transition">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">予約番号</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">宿泊者</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">チェックイン</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ステータス</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reservations as $res)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ str_pad($res->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $res->guest_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ \Carbon\Carbon::parse($res->check_in_date)->format('Y/m/d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($res->status == 1) <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">確定</span>
                                @elseif($res->status == 2) <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">チェックイン済</span>
                                @elseif($res->status == 3) <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">キャンセル</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.reservations.show', $res) }}" class="text-indigo-600 hover:text-indigo-900">詳細を見る</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4">
                    {{ $reservations->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
    function setQuickFilter(range) {
        document.getElementById('date_range_input').value = range;
        document.getElementById('search-form').submit();
    }
    </script>

</x-app-layout>