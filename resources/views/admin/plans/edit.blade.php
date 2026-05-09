<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            宿泊プラン編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-6">
                            {{-- プラン名 --}}
                            <div>
                                <x-input-label for="name" value="プラン名" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $plan->name)" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            {{-- 対象の部屋 --}}
                            <div>
                                <x-input-label for="room_id" value="対象の部屋タイプ" />
                                <select id="room_id" name="room_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">選択してください</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id', $plan->room_id) == $room->id ? 'selected' : '' }}>
                                            {{ $room->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('room_id')" />
                            </div>

                            {{-- 料金 --}}
                            <div>
                                <x-input-label for="price" value="基本料金（税込）" />
                                <div class="flex items-center">
                                    <x-text-input id="price" name="price" type="number" class="mt-1 block w-full" :value="old('price', $plan->price)" required />
                                    <span class="ml-2 mt-1">円</span>
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('price')" />
                            </div>

                            {{-- 説明文 --}}
                            <div>
                                <x-input-label for="description" value="プラン説明" />
                                <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $plan->description) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            {{-- 設定期間 --}}
                            <div>
                                <x-input-label value="期間設定（期間制限なしは空白）" />
                                <div class="flex items-center gap-3 mt-1">
                                    <div class="flex-1">
                                        <x-input-label for="available_from" value="開始日" class="text-xs text-gray-500" />
                                        <x-text-input id="available_from" name="available_from" type="date" class="mt-1 block w-full"
                                            :value="old('available_from', $plan->available_from?->format('Y-m-d'))" />
                                        <x-input-error class="mt-2" :messages="$errors->get('available_from')" />
                                    </div>
                                    <span class="mt-6 text-gray-500">〜</span>
                                    <div class="flex-1">
                                        <x-input-label for="available_to" value="終了日" class="text-xs text-gray-500" />
                                        <x-text-input id="available_to" name="available_to" type="date" class="mt-1 block w-full"
                                            :value="old('available_to', $plan->available_to?->format('Y-m-d'))" />
                                        <x-input-error class="mt-2" :messages="$errors->get('available_to')" />
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">※ 両方空白の場合は常時提供されるプランとして扱われます。</p>
                            </div>

                            <div class="flex items-center gap-4 pt-4">
                                <x-primary-button>更新する</x-primary-button>
                                <a href="{{ route('admin.plans.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                    キャンセル
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>