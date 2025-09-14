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
            <div class="card">
                <div class="card-body">
                    <div>
                        <table id="example" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 10px;">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th data-ordering="false">SR No.</th>
                                    <th data-ordering="false">ID</th>
                                    <th data-ordering="false">Purchase ID</th>
                                    <th data-ordering="false">Title</th>
                                    <th data-ordering="false">User</th>
                                    <th>Assigned To</th>
                                    <th>Created By</th>
                                    <th>Create Date</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>01</td>
                                    <td>VLZ-452</td>
                                    <td>VLZ1400087402</td>
                                    <td><a href="#!">Post launch reminder/ post list</a></td>
                                    <td>Joseph Parker</td>
                                    <td>Alexis Clarke</td>
                                    <td>Joseph Parker</td>
                                    <td>03 Oct, 2021</td>
                                    <td><span class="badge bg-info-subtle text-info">Re-open</span></td>
                                    <td><span class="badge bg-danger">High</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>02</td>
                                    <td>VLZ-453</td>
                                    <td>VLZ1400087425</td>
                                    <td><a href="#!">Additional Calendar</a></td>
                                    <td>Diana Kohler</td>
                                    <td>Admin</td>
                                    <td>Mary Rucker</td>
                                    <td>05 Oct, 2021</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">On-Hold</span></td>
                                    <td><span class="badge bg-info">Medium</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>03</td>
                                    <td>VLZ-454</td>
                                    <td>VLZ1400087438</td>
                                    <td><a href="#!">Make a creating an account profile</a></td>
                                    <td>Tonya Noble</td>
                                    <td>Admin</td>
                                    <td>Tonya Noble</td>
                                    <td>27 April, 2022</td>
                                    <td><span class="badge bg-danger-subtle text-danger">Closed</span></td>
                                    <td><span class="badge bg-success">Low</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>04</td>
                                    <td>VLZ-455</td>
                                    <td>VLZ1400087748</td>
                                    <td><a href="#!">Apologize for shopping Error!</a></td>
                                    <td>Joseph Parker</td>
                                    <td>Alexis Clarke</td>
                                    <td>Joseph Parker</td>
                                    <td>14 June, 2021</td>
                                    <td><span class="badge bg-warning-subtle text-warning">Inprogress</span></td>
                                    <td><span class="badge bg-info">Medium</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>05</td>
                                    <td>VLZ-456</td>
                                    <td>VLZ1400087547</td>
                                    <td><a href="#!">Support for theme</a></td>
                                    <td>Donald Palmer</td>
                                    <td>Admin</td>
                                    <td>Donald Palmer</td>
                                    <td>25 June, 2021</td>
                                    <td><span class="badge bg-danger-subtle text-danger">Closed</span></td>
                                    <td><span class="badge bg-success">Low</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>06</td>
                                    <td>VLZ-457</td>
                                    <td>VLZ1400087245</td>
                                    <td><a href="#!">Benner design for FB & Twitter</a></td>
                                    <td>Mary Rucker</td>
                                    <td>Jennifer Carter</td>
                                    <td>Mary Rucker</td>
                                    <td>14 Aug, 2021</td>
                                    <td><span class="badge bg-warning-subtle text-warning">Inprogress</span></td>
                                    <td><span class="badge bg-info">Medium</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>07</td>
                                    <td>VLZ-458</td>
                                    <td>VLZ1400087785</td>
                                    <td><a href="#!">Change email option process</a></td>
                                    <td>James Morris</td>
                                    <td>Admin</td>
                                    <td>James Morris</td>
                                    <td>12 March, 2022</td>
                                    <td><span class="badge bg-primary-subtle text-primary">Open</span></td>
                                    <td><span class="badge bg-danger">High</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>08</td>
                                    <td>VLZ-460</td>
                                    <td>VLZ1400087745</td>
                                    <td><a href="#!">Support for theme</a></td>
                                    <td>Nathan Cole</td>
                                    <td>Nancy Martino</td>
                                    <td>Nathan Cole</td>
                                    <td>28 Feb, 2022</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary">On-Hold</span></td>
                                    <td><span class="badge bg-success">Low</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>09</td>
                                    <td>VLZ-461</td>
                                    <td>VLZ1400087179</td>
                                    <td><a href="#!">Form submit issue</a></td>
                                    <td>Grace Coles</td>
                                    <td>Admin</td>
                                    <td>Grace Coles</td>
                                    <td>07 Jan, 2022</td>
                                    <td><span class="badge bg-success-subtle text-success">New</span></td>
                                    <td><span class="badge bg-danger">High</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>10</td>
                                    <td>VLZ-462</td>
                                    <td>VLZ140008856</td>
                                    <td><a href="#!">Edit customer testimonial</a></td>
                                    <td>Freda</td>
                                    <td>Alexis Clarke</td>
                                    <td>Freda</td>
                                    <td>16 Aug, 2021</td>
                                    <td><span class="badge bg-danger-subtle text-danger">Closed</span></td>
                                    <td><span class="badge bg-info">Medium</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>11</td>
                                    <td>VLZ-463</td>
                                    <td>VLZ1400078031</td>
                                    <td><a href="#!">Ca i have an e-copy invoice</a></td>
                                    <td>Williams</td>
                                    <td>Admin</td>
                                    <td>Williams</td>
                                    <td>24 Feb, 2022</td>
                                    <td><span class="badge bg-primary-subtle text-primary">Open</span></td>
                                    <td><span class="badge bg-success">Low</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>12</td>
                                    <td>VLZ-464</td>
                                    <td>VLZ1400087416</td>
                                    <td><a href="#!">Brand logo design</a></td>
                                    <td>Richard V.</td>
                                    <td>Admin</td>
                                    <td>Richard V.</td>
                                    <td>16 March, 2021</td>
                                    <td><span class="badge bg-warning-subtle text-warning">Inprogress</span></td>
                                    <td><span class="badge bg-danger">High</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>13</td>
                                    <td>VLZ-466</td>
                                    <td>VLZ1400089015</td>
                                    <td><a href="#!">Issue with finding information about order ?</a></td>
                                    <td>Olive Gunther</td>
                                    <td>Alexis Clarke</td>
                                    <td>Schaefer</td>
                                    <td>32 March, 2022</td>
                                    <td><span class="badge bg-success-subtle text-success">New</span></td>
                                    <td><span class="badge bg-danger">High</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td>14</td>
                                    <td>VLZ-467</td>
                                    <td>VLZ1400090324</td>
                                    <td><a href="#!">Make a creating an account profile</a></td>
                                    <td>Edwin</td>
                                    <td>Admin</td>
                                    <td>Edwin</td>
                                    <td>05 April, 2022</td>
                                    <td><span class="badge bg-warning-subtle text-warning">Inprogress</span></td>
                                    <td><span class="badge bg-success">Low</span></td>
                                    <td>
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-more-fill align-middle"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a href="#!" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                                <li><a class="dropdown-item edit-item-btn"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                                <li>
                                                    <a class="dropdown-item remove-item-btn">
                                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="table-responsive table-card mb-3">
                            <table class="table table-bordered dt-responsive nowrap table-striped align-middle" id="driveFilesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="checkAll" value="option">
                                            </div>
                                        </th>
                                        <th class="sort" data-sort="name" scope="col">Name</th>
                                        <th class="sort" data-sort="mime" scope="col">mime</th>
                                        <th class="sort" data-sort="file_id" scope="col">File Id</th>
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
                    </div>
                    <div class="modal fade" id="showModal" tabindex="-1"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0">
                                <div class="modal-header bg-info-subtle p-3">
                                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close" id="close-modal"></button>
                                </div>
                                <form class="tablelist-form" autocomplete="off">
                                    <div class="modal-body">
                                        <input type="hidden" id="id-field" />
                                        <div class="row g-3">
                                            <div class="col-lg-12">
                                                <div class="text-center">
                                                    <div class="position-relative d-inline-block">
                                                        <div class="position-absolute  bottom-0 end-0">
                                                            <label for="customer-image-input" class="mb-0"  data-bs-toggle="tooltip" data-bs-placement="right" title="Select Image">
                                                                <div class="avatar-xs cursor-pointer">
                                                                    <div class="avatar-title bg-light border rounded-circle text-muted">
                                                                        <i class="ri-image-fill"></i>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <input class="form-control d-none" value="" id="customer-image-input" type="file"
                                                                accept="image/png, image/gif, image/jpeg">
                                                        </div>
                                                        <div class="avatar-lg p-1">
                                                            <div class="avatar-title bg-light rounded-circle">
                                                                <img src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                                                        alt="" id="customer-img" class="avatar-md rounded-circle object-fit-cover" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label for="name-field" class="form-label">Name</label>
                                                    <input type="text" id="customername-field" class="form-control" placeholder="Enter name" required />
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div>
                                                    <label for="company_name-field" class="form-label">Company Name</label>
                                                    <input type="text" id="company_name-field" class="form-control" placeholder="Enter company name" required />
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div>
                                                    <label for="designation-field" class="form-label">Designation</label>
                                                    <input type="text" id="designation-field" class="form-control" placeholder="Enter Designation" required />
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div>
                                                    <label for="email_id-field" class="form-label">Email ID</label>
                                                    <input type="text" id="email_id-field" class="form-control" placeholder="Enter email" required />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="phone-field" class="form-label">Phone</label>
                                                    <input type="text" id="phone-field" class="form-control" placeholder="Enter phone no" required />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="lead_score-field" class="form-label">Lead Score</label>
                                                    <input type="text" id="lead_score-field" class="form-control" placeholder="Enter value" required />
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div>
                                                    <label for="taginput-choices" class="form-label font-size-13 text-muted">Tags</label>
                                                    <select class="form-control" name="taginput-choices"
                                                        id="taginput-choices"  multiple>
                                                        <option value="Lead">Lead</option>
                                                        <option value="Partner">Partner</option>
                                                        <option value="Exiting">Exiting</option>
                                                        <option value="Long-term">Long-term</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success" id="add-btn">Add Contact</button>
                                            {{-- <button type="button" class="btn btn-success" id="edit-btn">Update</button> --}}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--end add modal-->

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
                    <!--end delete modal -->

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
            <div class="card" id="contact-view-detail">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block">
                        <img src="{{ URL::asset('build/images/users/avatar-10.jpg') }}" alt=""
                            class="avatar-lg rounded-circle img-thumbnail">
                        <span class="contact-active position-absolute rounded-circle bg-success"><span
                                class="visually-hidden"></span>
                    </div>
                    <h5 class="mt-4 mb-1">Tonya Noble</h5>
                    <p class="text-muted">Nesta Technologies</p>

                    <ul class="list-inline mb-0">
                        <li class="list-inline-item avatar-xs">
                            <a href="javascript:void(0);"
                                class="avatar-title bg-success-subtle text-success fs-15 rounded">
                                <i class="ri-phone-line"></i>
                            </a>
                        </li>
                        <li class="list-inline-item avatar-xs">
                            <a href="javascript:void(0);"
                                class="avatar-title bg-danger-subtle text-danger fs-15 rounded">
                                <i class="ri-mail-line"></i>
                            </a>
                        </li>
                        <li class="list-inline-item avatar-xs">
                            <a href="javascript:void(0);"
                                class="avatar-title bg-warning-subtle text-warning fs-15 rounded">
                                <i class="ri-question-answer-line"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Personal Information</h6>
                    <p class="text-muted mb-4">Hello, I'm Tonya Noble, The most effective objective is
                        one that is tailored to the job you are applying for. It states what kind of
                        career you are seeking, and what skills and experiences.</p>
                    <div class="table-responsive table-card">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-medium" scope="row">Designation</td>
                                    <td>Lead Designer / Developer</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">Email ID</td>
                                    <td>tonyanoble@velzon.com</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">Phone No</td>
                                    <td>414-453-5725</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">Lead Score</td>
                                    <td>154</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">Tags</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">Lead</span>
                                        <span class="badge bg-primary-subtle text-primary">Partner</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium" scope="row">Last Contacted</td>
                                    <td>15 Dec, 2021 <small class="text-muted">08:58AM</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end card-->
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
