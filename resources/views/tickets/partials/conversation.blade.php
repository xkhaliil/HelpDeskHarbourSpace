{{-- The conversation of a ticket. Used on the ticket page of the owner and on the admin page. --}}
<x-card>
    <h2 class="text-sm font-black uppercase tracking-widest text-gray-500">Conversation</h2>

    <ol class="mt-4 space-y-4">
        @forelse ($ticket->replies as $reply)
            <li @class([
                'rounded-lg border p-4',
                'border-harbour/20 bg-harbour-light' => $reply->user->isAdmin(),
                'border-gray-200 bg-white' => ! $reply->user->isAdmin(),
            ])>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="font-bold">{{ $reply->user->name }}</span>
                    <x-badge :value="$reply->user->role" />
                    <span class="text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                </div>

                <p class="mt-2 whitespace-pre-line text-sm">{{ $reply->message }}</p>

                {{-- Only the author of the reply sees these buttons (see App\Policies\ReplyPolicy) --}}
                @can('update', $reply)
                    <div class="mt-3 flex items-center gap-4 text-xs font-bold uppercase tracking-widest">
                        <a href="{{ route('replies.edit', $reply) }}" class="text-harbour hover:text-harbour-dark">Edit</a>

                        <form action="{{ route('replies.destroy', $reply) }}" method="POST" onsubmit="return confirm('Delete this reply?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-bold uppercase tracking-widest text-red-600 hover:text-red-700">Delete</button>
                        </form>
                    </div>
                @endcan
            </li>
        @empty
            <li class="text-sm text-gray-500">No replies yet.</li>
        @endforelse
    </ol>

    @can('reply', $ticket)
        <form action="{{ route('tickets.replies.store', $ticket) }}" method="POST" class="mt-6 space-y-4 border-t border-gray-200 pt-6">
            @csrf

            <x-form-textarea name="message" label="Your reply" rows="3" placeholder="Write a message..." required />

            <x-breeze.primary-button>Send reply</x-breeze.primary-button>
        </form>
    @else
        <p class="mt-6 rounded-md bg-gray-50 p-3 text-sm text-gray-600">This ticket is closed, so no new replies can be added.</p>
    @endcan
</x-card>
