<div class="modal fade" id="modal-search-config" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('fi.task-search-config') }}</h5>
                <button type="button" class="close close-search-config-modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" id="filter_title" class="form-check-input search-config-chk"
                                           name="filterBy[title]"
                                           value="1" {{ !Session::has('filter_by_title') && Session::get('filter_by_title') == 0 ? 'checked' : (Session::get('filter_by_title') == 1 ? 'checked' : '' )}}>
                                    <label for="filter_title" class="form-check-label">{{ trans('fi.title') }}</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="filter_description"
                                           class="form-check-input search-config-chk" name="filterBy[description]"
                                           value="1" {{ !Session::has('filter_by_task_description') ? 'checked' : (Session::get('filter_by_task_description') == 1 ? 'checked' : '')}}>
                                    <label for="filter_description"
                                           class="form-check-label">{{ trans('fi.description') }}</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="filter_client" class="form-check-input search-config-chk"
                                           name="filterBy[client]"
                                           value="1" {{ !Session::has('filter_by_client') ? 'checked' : (Session::get('filter_by_client') == 1 ? 'checked' : '')}}>
                                    <label for="filter_client" class="form-check-label">{{ trans('fi.client') }}</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="filter_assignee"
                                           class="form-check-input search-config-chk" name="filterBy[assignee]"
                                           value="1" {{ !Session::has('filter_by_assignee') ? 'checked' : (Session::get('filter_by_assignee') == 1 ? 'checked' : '')}}>
                                    <label for="filter_assignee"
                                           class="form-check-label">{{ trans('fi.assignee') }}</label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary custom-search" data-target="modal-placeholder">{{ trans('fi.save') }}</button>
            </div>
        </div>
    </div>
</div>