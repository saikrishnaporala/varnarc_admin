@extends('layouts.master')
@section('title') @lang('translation.contacts') @endsection
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<!--datatable css-->
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<!--datatable responsive css-->
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Data @endslot
        @slot('title') Google Drive Dump @endslot
    @endcomponent
    <div class="row">
        <div class="col-xxl-9">
            <div class="card" id="filesTableWrapper">
                <div class="card-body">
                    <div>
                        <table class="table table-bordered dt-responsive nowrap table-striped align-middle" id="driveFilesTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 10px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="name" scope="col">Name</th>
                                    <th class="sort" data-sort="mime" scope="col">mime</th>
                                    <th class="sort" data-sort="status" scope="col">Import Status</th>
                                    <th class="sort" data-sort="table_name" scope="col">Table Name</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            {{-- <tbody class="list form-check-all">
                                
                            </tbody> --}}
                        </table>
                        <div class="noresult" style="display: none">
                            <div class="text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                    trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                    style="width:75px;height:75px">
                                </lord-icon>
                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                <p class="text-muted mb-0">We've searched more than 150+ contacts We
                                    did not find any
                                    contacts for you search.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close" id="btn-close deleteRecord-close"></button>
                                </div>
                                <div class="modal-body p-5 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                        style="width:90px;height:90px"></lord-icon>
                                    <div class="mt-4 text-center">
                                        <h4 class="fs-semibold">You are about to delete a contact ?</h4>
                                        <p class="text-muted fs-14 mb-4 pt-1">Deleting your contact will
                                            remove all of your information from our database.</p>
                                        <div class="hstack gap-2 justify-content-center remove">
                                            <button
                                                class="btn btn-link link-success fw-medium text-decoration-none"
                                                data-bs-dismiss="modal" id="deleteRecord-close"><i
                                                    class="ri-close-line me-1 align-middle"></i>
                                                Close</button>
                                            <button class="btn btn-danger" id="delete-record">Yes,
                                                Delete It!!</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" id="recordsWrapper" style="display:none;">
                <div class="card-body">
                    <button id="backBtn" class="btn btn-secondary mb-2">⬅ Back</button>
                    <h4 id="recordsTitle"></h4>
                    <form id="mappingForm">
                        <table class="table table-bordered" id="mappingTable">
                            <thead>
                                <tr>
                                    <th>From Column</th>
                                    <th>To Column</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <button type="submit" class="btn btn-primary">Save Mapping</button>
                    </form>
                    <table id="recordsTable" class="table table-bordered" style="width: 100%;"></table>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-xxl-3">
            <div id="importMessage" class="mt-0"></div>
            {{-- Hidden Import Section (First Screenshot UI) --}}
            <div class="card" id="importSection">
                <div class="card-body">

                    {{-- Upload CSV/Excel --}}
                    <h5>Upload CSV / Excel</h5>
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" name="table_name" class="form-control" placeholder="Table name (optional)">
                            </div>
                            <div class="col-md-6 mb-3">
                                <select name="if_exists" class="form-control">
                                    <option value="append">Append</option>
                                    <option value="replace">Replace</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Import</button>
                    </form>

                    <hr>

                    {{-- Import from URL --}}
                    <h5>Import from URL</h5>
                    <form method="POST" id="importurl">
                        @csrf
                        <div class="mb-3">
                            <label for="file_url" class="form-label">File URL (CSV/XLSX/XLS)</label>
                            <input type="url" name="file_url" class="form-control" placeholder="https://..." required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <input type="text" name="table_name" class="form-control" placeholder="Table name (optional)">
                            </div>
                            <div class="col-md-4 mb-3">
                                <select name="if_exists" class="form-control">
                                    <option value="append">Append</option>
                                    <option value="replace">Replace</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select name="file_type" class="form-control">
                                    <option value="auto">Auto</option>
                                    <option value="csv">CSV</option>
                                    <option value="xlsx">XLSX</option>
                                    <option value="xls">XLS</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Import</button>
                    </form>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="{{ URL::asset('build/js/pages/datatables.init.js') }}"></script>

    <script src="{{ URL::asset('build/js/pages/data-dump.init.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
