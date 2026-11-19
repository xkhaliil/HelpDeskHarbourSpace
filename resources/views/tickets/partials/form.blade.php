{{-- The fields shared by the create and the edit form. $ticket is null on the create page. --}}
<x-form-textinput name="title" label="Title" placeholder="For example: Projector does not turn on" :value="$ticket?->title" required />

<x-form-select name="category_id" label="Category" placeholder="Choose a category"
               :options="$categories->map(fn ($category) => ['key' => $category->id, 'value' => $category->name])"
               :value="$ticket?->category_id" required />

<x-form-textinput name="location" label="Location" placeholder="For example: Room 2.03 or Library, 1st floor" :value="$ticket?->location" required />

<x-form-textarea name="description" label="Description" rows="6" placeholder="What happened? When did it start? What did you already try?" :value="$ticket?->description" required />

<div class="space-y-1">
    <label for="screenshot" class="block text-sm font-semibold text-ink">
        Screenshot <span class="font-normal text-gray-500">(optional, an image of max. 2 MB)</span>
    </label>
    <input type="file" id="screenshot" name="screenshot" accept="image/png, image/jpeg, image/webp"
           class="block w-full rounded-md border p-1 text-sm file:mr-4 file:rounded-full file:border-0 file:bg-harbour-light file:px-4 file:py-2 file:text-xs file:font-bold file:uppercase file:tracking-widest file:text-harbour hover:file:bg-harbour hover:file:text-white {{ $errors->has('screenshot') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}"
           @if($errors->has('screenshot')) aria-invalid="true" aria-describedby="screenshot-error" @endif>
    @error('screenshot')
        <p id="screenshot-error" class="text-sm font-medium text-red-600">{{ $message }}</p>
    @enderror

    @if ($ticket?->screenshotUrl())
        <p class="text-xs text-gray-500">This ticket already has a screenshot. A new image replaces it.</p>
    @endif
</div>
