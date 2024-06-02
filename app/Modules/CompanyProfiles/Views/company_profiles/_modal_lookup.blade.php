@include('company_profiles._js_subchange')

<div class="modal fade" id="modal-lookup-company-profile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.change_company_profile') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div id="modal-status-placeholder"></div>

                <form class="form-horizontal">

                    <div class="form-group">
                        <label>{{ trans('fi.company_profile') }}</label>
                        {!! Form::select('change_company_profile_id', $companyProfiles, null, ['id' => 'change_company_profile_id', 'class' => 'form-control form-control-sm']) !!}
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">{{ trans('fi.cancel') }}</button>
                <button type="button" id="btn-submit-change-company-profile" class="btn btn-sm btn-primary">{{ trans('fi.save') }}
                </button>
            </div>
        </div>
    </div>
</div>