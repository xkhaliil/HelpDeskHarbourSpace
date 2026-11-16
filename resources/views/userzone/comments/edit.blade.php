<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit comment by {{ $comment->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('userzone.comments.update', $comment->id) }}" method="post">
                        @method('PUT')
                        @csrf

                        <div class="space-y-4">
                            <x-form-textarea name="content" label="Content" :value="$comment->content" rows="8" required/>

                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="approved" value="1" @if($comment->approved_at) checked @endif>
                                Approved
                            </label>
                        </div>

                        <div class="mt-4 flex items-center gap-4">
                            <button type="submit" class="p-2 bg-blue-500 text-white">Update comment</button>

                            <a href="{{ route('userzone.comments.show', $comment->id) }}" class="text-sm text-gray-600 hover:text-gray-800">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
