<form id="doctors_filters">
    <div class="card search-filter">
        <div class="card-header">
            <h4 class="card-title mb-0">@lang('reservation-doctors::labels.doctors.search_filter')</h4>
        </div>
        <div class="card-body">

            <div class="filter-widget">

                <input type="text" name="search_term" class="form-control"
                       placeholder="@lang('reservation-doctors::labels.doctors.search_term_placeholder')"
                       value="{{request('search_term')}}">
                <hr>

                <input type="text" name="address" class="form-control"
                       id="_autocomplete"
                       placeholder="@lang('reservation-doctors::labels.doctors.address')"
                       value="{{request('address')}}">

                <input type="range" class="form-control" min="1" max="100" value="{{request('radius',50)}}" name="radius"  id="distance">
                <p>@lang('reservation-doctors::labels.doctors.radius'): <span id="distance_value"></span></p>

                <input type="hidden" id="lat" name="lat" value="{{request('lat')}}">
                <input type="hidden" id="long" name="long" value="{{request('long')}}">
                <hr>

                <h4>
                    @lang('reservation-doctors::labels.doctors.select_specialist')

                </h4>
                @foreach(\Category::getCategoriesByParent('services-categories','active',true) as $category)

                    <div>
                        <label class="custom_check">
                            <input type="checkbox" name="categories[]"
                                   {{in_array($category->slug,request('categories',[])) ? 'checked':''}} value="{{$category->slug}}">
                            <span class="checkmark"></span> {{$category->name}}
                        </label>
                    </div>
                @endforeach

                <hr>

                <div>
                    <label class="custom_check">
                        <input type="checkbox" name="open_now"
                               {{request('open_now',[]) ? 'checked':''}} value="1">
                        <span class="checkmark"></span> @lang('reservation-doctors::labels.doctors.open_now')
                    </label>
                </div>

                <hr>

                <div>
                    <label class="custom_check">
                        <input type="checkbox" name="my_favourites"
                               {{request('my_favourites',[]) ? 'checked':''}} value="1">
                        <span class="checkmark"></span> @lang('reservation-doctors::labels.doctors.my_favourites')
                    </label>
                </div>

            </div>
            <div class="btn-search">
                <button type="submit" class="btn btn-block">
                    @lang('reservation-doctors::labels.doctors.search')
                </button>
            </div>

            <div class="btn-search mt-1">
                <a href="#" class="btn btn-block btn-secondary restFilter"
                   style="background-color: #5a6268; border: #5a6268; color:#fff ">
                    @lang('reservation-doctors::labels.doctors.rest_filters')
                </a>
            </div>

        </div>
    </div>
</form>

@push('partial_js')

    <script>
        $(document).on('click', '.restFilter', function (e) {
            e.preventDefault();

            let form = $('#doctors_filters');
            clearForm({}, form);
            form.find(':input').val(null);
        });

        var slider = document.getElementById("distance");
        var output = document.getElementById("distance_value");
        output.innerHTML = slider.value; // Display the default slider value

        // Update the current slider value (each time you drag the slider handle)
        slider.oninput = function() {
            output.innerHTML = this.value;
        }
        $('#_autocomplete').on('change', function (e) {
            $('#lat').val('');
            $('#long').val('');
        });

    </script>

    {!! Html::script(asset('assets/corals/js/auto_complete_google_address.js')) !!}

@endpush