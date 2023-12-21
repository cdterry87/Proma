@props(['results'])

@if ($results && $results->isNotEmpty())
    <table class="w-full text-white">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Manager</th>
                <th>Members</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="dark:text-gray-400 dark:bg-gray-800">
            @foreach ($results as $result)
                <tr>
                    <td>{{ $result->name }}</td>
                    <td>{{ $result->description }}</td>
                    <td>{{ $result->getManagerName() }}</td>
                    <td>0</td>
                    <td>
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
