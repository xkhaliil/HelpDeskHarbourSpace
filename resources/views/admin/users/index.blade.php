<x-app-layout title="Users">
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs font-bold uppercase tracking-widest text-gray-500">
                <tr>
                    <th scope="col" class="px-4 py-3">Name</th>
                    <th scope="col" class="px-4 py-3">Role</th>
                    <th scope="col" class="px-4 py-3">Tickets</th>
                    <th scope="col" class="px-4 py-3">Change role</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 py-3"><x-badge :value="$user->role" /></td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->tickets_count }}</td>
                        <td class="px-4 py-3">
                            @if ($user->is(auth()->user()))
                                <span class="text-xs text-gray-500">This is you</span>
                            @else
                                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')

                                    <label for="role-{{ $user->id }}" class="sr-only">Role of {{ $user->name }}</label>
                                    <select id="role-{{ $user->id }}" name="role" class="rounded-md border border-gray-300 py-1.5 pl-3 pr-8 text-sm shadow-xs focus:border-harbour focus:ring-harbour">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="rounded-full border border-harbour px-3 py-1.5 text-xs font-bold uppercase tracking-widest text-harbour transition hover:bg-harbour hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour">Save</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @error('role')
        <p role="alert" class="mt-4 text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</x-app-layout>
