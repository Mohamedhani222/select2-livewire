
@php
    $optionNameCol = $attributes->get('option_col') ?? 'name';
    $selectId = $attributes->get('id') ?? 'select2_' . uniqid();
    $use_wire_model = $attributes->get('use_wire_model');
    $selectedObj =$attributes->get('selectedObj');
    $selectedObjValue =$attributes->get('selectedObjValue');
@endphp


<div {{ $attributes->has('wire:model') ? 'wire:ignore' : '' }}>

    <select class="{{$attributes->get('class')}}" id="{{$selectId}}" {{ $attributes->except(['option_col', 'url']) }}>
        <option>اختر</option>
        @if($selectedObj)
            <option value="{{$selectedObj->id}}">{{$selectedObjValue ?? $selectedObj->name}}</option>

        @endif
        @if(isset($options))
            @foreach($options as $option)
                <option value="{{$option->id}}">{{$option->$optionNameCol}}</option>
            @endforeach
        @endif


    </select>
</div>

@once
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
    <script src="https://code.jquery.com/jquery-3.6.1.js"
            integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
            integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endonce


<script>

    $(document).ready(function (){
        initSelect2_{{$selectId}}();
    });

    document.addEventListener('refreshSelect2', () => {
        initSelect2_{{$selectId}}();
    });

    function initSelect2_{{$selectId}}() {
        const select2Instance = $('#{{$selectId}}');
        var $p = select2Instance.parent();

        if (select2Instance.data('select2')) {
            select2Instance.select2('destroy');
        }

        select2Instance.select2({

            allowClear: true,
            width: '100%',
            dropdownParent: $p,
            @if(isset($url))
            ajax: {
                url: `{{$url}}`,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || '',
                        page: params.page || 1,
                    }
                },
                cache: true,
                pagination: {
                    more: true
                }
            }
            @endif
        });
        select2Instance.on('select2:open', function() {
            document.querySelector('.select2-search__field').focus()
        });

        select2Instance.on('change', function () {
            const data = $(this).val();
            const name = $(this).attr("{{$use_wire_model ? 'wire:model' : 'id' }}");
            @this.set(name, data);
        });
    }
</script>
