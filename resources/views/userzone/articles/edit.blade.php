<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit an existing article {{ $article->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('userzone.articles.update', $article->id) }}" method="post" enctype="multipart/form-data">

                        @method('PUT')
                        @csrf

                        <div class="space-y-4">
                            <x-form-textinput name="title" label="Title" placeholder="Your article title" :value="$article->title" required/>

                            <x-form-textarea name="content" label="Content" placeholder="Your article content" :value="$article->content" rows="8" required/>

                            <x-form-select name="author_id" label="Author" :options="$authors" :value="$article->author_id" placeholder="Choose an author" required/>

                            <x-form-datetimeinput name="publish_date" label="Publish date" :value="$article->publish_date" required/>

                            <x-form-multiselect name="categories" label="Categories" :options="$categories" :value="$article->categories->pluck('id')->all()"/>

                            Image for article:<br/>
                            <input name="image" type="file">
                        </div>

                        <hr class="my-4"/>

                        <button type="submit" class="p-2 bg-blue-500 text-white">Update article</button>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
