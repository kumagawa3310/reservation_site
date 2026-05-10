<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                お問い合わせ詳細 <span class="text-gray-400 font-normal text-base">#{{ $contact->id }}</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="px-4 py-3 bg-green-100 border border-green-400 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- お問い合わせ内容 --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <dl class="divide-y divide-gray-200">
                    <div class="px-6 py-4 flex gap-4">
                        <dt class="w-32 text-sm font-medium text-gray-500 shrink-0">受信日時</dt>
                        <dd class="text-sm text-gray-900">{{ $contact->created_at->format('Y年m月d日 H:i') }}</dd>
                    </div>
                    <div class="px-6 py-4 flex gap-4">
                        <dt class="w-32 text-sm font-medium text-gray-500 shrink-0">お名前</dt>
                        <dd class="text-sm text-gray-900">{{ $contact->name }} 様</dd>
                    </div>
                    <div class="px-6 py-4 flex gap-4">
                        <dt class="w-32 text-sm font-medium text-gray-500 shrink-0">メールアドレス</dt>
                        <dd class="text-sm">
                            <a href="mailto:{{ $contact->email }}" class="text-indigo-600 hover:underline">
                                {{ $contact->email }}
                            </a>
                        </dd>
                    </div>
                    <div class="px-6 py-4 flex gap-4">
                        <dt class="w-32 text-sm font-medium text-gray-500 shrink-0">内容</dt>
                        <dd class="text-sm text-gray-900 whitespace-pre-wrap">{{ $contact->body }}</dd>
                    </div>
                    <div class="px-6 py-4 flex gap-4">
                        <dt class="w-32 text-sm font-medium text-gray-500 shrink-0">ステータス</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                {{ $contact->status_label }}
                            </span>                                
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- ステータス更新 --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700">ステータスを更新する</h3>
                </div>
                <div class="px-6 py-4">
                    <form method="POST" action="{{ route('admin.contacts.updateStatus', $contact) }}"
                          class="flex items-center gap-4">
                        @csrf
                        @method('PATCH')
                        <select name="status"
                                class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="0" @selected($contact->status === 0)>未対応</option>
                            <option value="1" @selected($contact->status === 1)>対応中</option>
                            <option value="2" @selected($contact->status === 2)>完了</option>
                        </select>
                        <x-primary-button>更新する</x-primary-button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>