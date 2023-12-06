<!-- Modal -->
<div id="{{ $id }}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header {{ isset($hideHeader)?'d-none':'' }}">
                <h4 class="modal-title">{!! $header ?? ''  !!}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body" id="modal-body-{{ $id }}">
                {!! $slot??'' !!}
            </div>

            <div class="modal-footer {{ !empty($footer)?'':'d-none' }}">
                {!! $footer ?? '' !!}
            </div>
        </div>
    </div>
</div>
