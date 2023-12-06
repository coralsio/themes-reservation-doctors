<div class="card search-widget">
    <div class="card-body">
        <form class="search-form" action="{{ url('blog') }}" method="GET">
            <div class="input-group">
                <input type="text" name="query" placeholder="@lang('reservation-doctors::labels.doctors.search')" class="form-control">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>