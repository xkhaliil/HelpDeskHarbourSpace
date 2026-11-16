<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit an existing category {{$category->name}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{route('userzone.categories.update', $category->id)}}" method="post">
                        @method('PUT')
                        @csrf
                        <x-form-textinput name="name" label="Name" placeholder="Your category name" value="{{$category->name}}" required/>

                        <hr class="my-4"/>

                        <button type="submit" class="p-2 bg-blue-500 text-white">Update category</button>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>


