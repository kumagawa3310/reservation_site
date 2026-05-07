<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                予約枠の編集
            </h2>
            <a href="{{ route('admin.slots.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">
                一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('admin.slots.update', $slot) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="room_id" value="部屋タイプ" />
                        <select id="room_id" name="room_id" required
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" @selected($slot->room_id === $room->id)>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('room_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="date" value="日付" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 w-full"
                                      :value="$slot->date" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="price" value="料金（円）" />
                        <x-text-input id="price" name="price" type="number" min="0" class="mt-1 w-full"
                                      :value="$slot->price" required />
                        <x-input-error :messages="$errors->get('price')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="status" value="ステータス" />
                        <select id="status" name="status" required
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="available" @selected($slot->status === 'available')>空き</option>
                            <option value="reserved"  @selected($slot->status === 'reserved')>予約済</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-1" />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>更新する</x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
