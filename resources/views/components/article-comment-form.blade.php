<div id="comments">
    @auth
    <form method="POST" action="{{route('articles.comments.store', $article->id)}}" class="mt-4 max-w-xl">
        @csrf

        You are {{ auth()->user()->name }}

        <x-form-textarea name="content" label="Your comment" placeholder="Leave a comment" rows="4" required/>

        <button type="submit" class="mt-3 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Post comment</button>
    </form>
    @else
        <div class="bg-amber-100 py-1 px-4 flex justify-between items-center">
            <span>You need to be logged in to make a comment</span>
            <a class="rounded bg-amber-300 hover:bg-amber-500 px-2 py-0.5" href="{{ route('login', ['redirect' => request()->getRequestUri().'#comments']) }}">Login</a>
        </div>
    @endauth

</div>
