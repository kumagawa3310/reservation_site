<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">お問い合わせ</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                
                @if (session('success'))
                    <div class="mb-4 text-green-600 font-bold">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf <!-- これがないとエラー -->

                    <div class="mb-4">
                        <label>お名前</label>
                        <input type="text" name="name" class="w-full border-gray-300 rounded" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-4">
                        <label>メールアドレス</label>
                        <input type="email" name="email" class="w-full border-gray-300 rounded" required>
                    </div>

                    <div class="mb-4">
                        <label>お問い合わせ内容</label>
                        <textarea name="body" class="w-full border-gray-300 rounded" rows="5" required></textarea>
                    </div>

                    <x-primary-button>送信する</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>