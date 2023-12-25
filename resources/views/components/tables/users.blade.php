@props(['results'])

@if ($results && $results->isNotEmpty())
    <table class="w-full text-white">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Title</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="dark:text-gray-400 dark:bg-gray-800">
            @foreach ($results as $result)
                <tr>
                    <td>
                        <img
                            class="h-8 w-8 rounded-full object-cover"
                            src="{{ $result->profile_photo_url }}"
                            alt="{{ $result->name }}"
                        />
                    </td>
                    <td>{{ $result->name }}</td>
                    <td>{{ $result->email }}</td>
                    <td>
                        {{ $result->getFormattedPhoneNumber() }}
                    </td>
                    <td>{{ $result->title }}</td>
                    <td class="w-32">
                        <button
                            class="rounded-full p-2 hover:bg-gray-900 transition duration-200 ease-in-out"
                            wire:click.prevent="edit({{ $result->id }})"
                        >
                            <x-icons.edit class="h-6 w-6" />
                        </button>
                        <x-confirms-delete wire:then="delete({{ $result->id }})">
                            <button class="rounded-full p-2 hover:bg-gray-900 transition duration-200 ease-in-out">
                                <x-icons.delete class="h-6 w-6" />
                            </button>
                        </x-confirms-delete>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <x-empty-results />
@endif
