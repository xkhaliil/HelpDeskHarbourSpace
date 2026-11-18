{{-- The fields shared by the create and the edit form. $ticket is null on the create page. --}}
<x-form-textinput name="title" label="Title" placeholder="For example: Projector does not turn on" :value="$ticket?->title" required />

<x-form-select name="category_id" label="Category" placeholder="Choose a category"
               :options="$categories->map(fn ($category) => ['key' => $category->id, 'value' => $category->name])"
               :value="$ticket?->category_id" required />

<x-form-textinput name="location" label="Location" placeholder="For example: Room 2.03 or Library, 1st floor" :value="$ticket?->location" required />

<x-form-textarea name="description" label="Description" rows="6" placeholder="What happened? When did it start? What did you already try?" :value="$ticket?->description" required />
