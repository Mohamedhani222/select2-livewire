# Select2-Livewire

A seamless integration of Select2 with Laravel Livewire for dynamic, searchable dropdowns.

## Features

- AJAX-powered search with pagination for large datasets
- Direct data loading from Eloquent collections
- Compatible with Livewire's hydration cycle
- Works in loops and dynamic contexts
- Responsive and user-friendly interface

## Basic Usage

### Component Setup

First, ensure proper hydration in your Livewire component:

```php
public function hydrate()
{
    $this->dispatchBrowserEvent('refreshSelect2');
    // or dispatch('refreshSelect2') if livewire 3
}
```

### Implementation Examples

#### Using AJAX Source (Recommended for Large Datasets)

```blade
<x-select2
    id="to_account_id"
    wire:model="to_account_id"
    :url="'/select2/accounts'"
/>
```

#### Using Direct Collection

```blade
<x-select2
    id="to_account_id"
    wire:model="to_account_id"
    :options="App\Models\Patient::get()"
/>
```

### AJAX Controller Example

Create a controller method to handle AJAX requests:

```php
public function patients()
{
    if (\request()->ajax()) {
        $search = trim(\request('search'));
        
        $posts = DB::table('patients')
            ->select('id', 'first_name as text')
            ->where(function ($query) use ($search) {
                $query->where('id', $search)
                    ->orWhere('first_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', $search);
            })
            ->simplePaginate(10);
            
        $morePages = !empty($posts->nextPageUrl());
        
        $results = [
            "results" => $posts->items(),
            "pagination" => [
                "more" => $morePages
            ]
        ];
        
        return \Response::json($results);
    }
}
```

## Advanced Usage

### Using in Loops

When using inside loops, set `use_wire_model="1"` to prevent ID conflicts:

```blade
@foreach($data as $index => $item)
    <x-select2
        id="data{{$index}}"
        wire:model="data.{{$index}}.name"
        :options="App\Models\Patient::get()"
        :use_wire_model="1"
    />
@endforeach
```

## Important Notes

- The `id` attribute must match the `wire:model` name except in loops
- When using in loops, set `use_wire_model="1"` and use unique IDs
- For large datasets, use the `:url` attribute to implement paginated search
- The component automatically refreshes after Livewire hydration

## Response Format

The AJAX endpoint should return data in the following format:

```json
{
    "results": [
        {"id": 1, "text": "Option 1"},
        {"id": 2, "text": "Option 2"}
    ],
    "pagination": {
        "more": true
    }
}
```

## License

MIT License

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
