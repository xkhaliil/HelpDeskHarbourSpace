{{-- The fields shared by the create and the edit form. $ticket is null on the create page. --}}
<x-form-textinput name="title" label="Title" placeholder="For example: Projector does not turn on" :value="$ticket?->title" required />

<x-form-select name="category_id" label="Category" placeholder="Choose a category"
               :options="$categories->map(fn ($category) => ['key' => $category->id, 'value' => $category->name])"
               :value="$ticket?->category_id" required />

{{-- Only teachers see the priority field. Students always get the normal priority. --}}
@if (auth()->user()->isTeacher())
    <x-form-select name="priority" label="Priority"
                   :options="collect($priorities)->map(fn ($priority) => ['key' => $priority->value, 'value' => $priority->label()])"
                   :value="$ticket?->priority->value ?? 'normal'" required />
@endif

<x-form-textinput name="location" label="Location" placeholder="For example: Room 2.03 or Library, 1st floor" :value="$ticket?->location" required />

<x-form-textarea name="description" label="Description" rows="6" placeholder="What happened? When did it start? What did you already try?" :value="$ticket?->description" required />
