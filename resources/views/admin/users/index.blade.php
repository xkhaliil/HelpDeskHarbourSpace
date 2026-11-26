<x-app-layout title="Users">
    <x-slot name="actions">
        <x-button-link :href="route('admin.users.create')">New user</x-button-link>
    </x-slot>

    <!-- Accounts waiting for approval -->
    <h2 class="mb-4 text-xl font-black uppercase tracking-tight">Waiting for approval ({{ $pendingUsers->count() }})</h2>

    @if ($pendingUsers->isEmpty())
        <p class="mb-10 rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">No accounts are waiting for approval.</p>
    @else
        <p class="mb-4 text-sm text-gray-600">
            These people registered without a &#64;{{ config('helpdesk.student_email_domain') }} or &#64;{{ config('helpdesk.teacher_email_domain') }} address.
            Choose their role and approve them, so they can log in.
        </p>

        <div class="mb-10 overflow-x-auto rounded-xl border border-amber-300">
            <table class="w-full text-left text-sm">
                <thead class="bg-amber-50 text-xs font-bold uppercase tracking-widest text-amber-900">
                    <tr>
                        <th scope="col" class="px-4 py-3">Name</th>
                        <th scope="col" class="px-4 py-3">Registered</th>
                        <th scope="col" class="px-4 py-3">Approve as</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach ($pendingUsers as $pendingUser)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold">{{ $pendingUser->name }}</div>
                                <div class="text-xs text-gray-500">{{ $pendingUser->email }}</div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $pendingUser->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                <form action="{{ route('admin.users.approve', $pendingUser) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')

                                    <label for="approve-role-{{ $pendingUser->id }}" class="sr-only">Role of {{ $pendingUser->name }}</label>
                                    <select id="approve-role-{{ $pendingUser->id }}" name="role" class="rounded-md border border-gray-300 py-1.5 pl-3 pr-8 text-sm shadow-xs focus:border-harbour focus:ring-harbour">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->value }}" @selected($pendingUser->role === $role)>{{ $role->label() }}</option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="rounded-full bg-harbour px-3 py-1.5 text-xs font-bold uppercase tracking-widest text-white transition hover:bg-harbour-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-harbour">Approve</button>
                                </form>

                                {{-- Somebody who registered by mistake can be removed instead --}}
                                <form action="{{ route('admin.users.destroy', $pendingUser) }}" method="POST" class="mt-2" onsubmit="return confirm('Delete this account?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold uppercase tracking-widest text-red-600 hover:text-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Approved users -->
    <h2 class="mb-4 text-xl font-black uppercase tracking-tight">All users</h2>

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs font-bold uppercase tracking-widest text-gray-500">
                <tr>
                    <th scope="col" class="px-4 py-3">Name</th>
                    <th scope="col" class="px-4 py-3">Role</th>
                    <th scope="col" class="px-4 py-3">Tickets</th>
                    <th scope="col" class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $user) }}" class="font-semibold hover:text-harbour hover:underline">{{ $user->name }}</a>
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 py-3"><x-badge :value="$user->role" /></td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->tickets_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-4 text-xs font-bold uppercase tracking-widest">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-ink">View</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-harbour hover:text-harbour-dark">Edit</a>

                                {{-- Admins cannot delete their own account here (see Admin\UserController@destroy) --}}
                                @if ($user->is(auth()->user()))
                                    <span class="font-normal normal-case tracking-normal text-gray-500">This is you</span>
                                @else
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this account?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold uppercase tracking-widest text-red-600 hover:text-red-700">Delete</button>
                                    </form>
                                @endif
                            </div>
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