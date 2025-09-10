<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.create-service'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/dropzone/dropzone.css')); ?>" rel="stylesheet">
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Offerings
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Create Service
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <form id="createservice-form" autocomplete="off" class="needs-validation" novalidate method="post">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="mb-3">
                                <label class="form-label" for="service-title-input">Service Title</label>
                                <input type="hidden" class="form-control" id="formAction" name="formAction" value="add">
                                <input type="text" class="form-control d-none" id="id">
                                <input type="text" class="form-control" id="service-title-input" value=""
                                    placeholder="Enter service title" required name="title">
                                <div class="invalid-feedback">Please Enter a service title.</div>
                            </div>
                        </div>
                        <div>
                            <label>Service Description</label>

                            <textarea id="ckeditor-classic" name="description">
                                <p>Service Description</p>
                                <ul>
                                    <li>Full Sleeve</li>
                                    <li>Cotton</li>
                                    <li>All Sizes available</li>
                                    <li>4 Different Color</li>
                                </ul>
                            </textarea>
                        </div>
                    </div>
                </div>
                <!-- end card -->

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Service Gallery</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5 class="fs-14 mb-1">Service Image</h5>
                            <p class="text-muted">Add Service main Image.</p>
                            <div class="text-center">
                                <div class="position-relative d-inline-block">
                                    <div class="position-absolute top-100 start-100 translate-middle">
                                        <label for="service-image-input" class="mb-0" data-bs-toggle="tooltip"
                                            data-bs-placement="right" title="Select Image">
                                            <div class="avatar-xs">
                                                <div
                                                    class="avatar-title bg-light border rounded-circle text-muted cursor-pointer">
                                                    <i class="ri-image-fill"></i>
                                                </div>
                                            </div>
                                        </label>
                                        <input class="form-control d-none" value="" id="service-image-input"
                                            type="file" accept="image/png, image/gif, image/jpeg" name="image">
                                    </div>
                                    <div class="avatar-lg">
                                        <div class="avatar-title bg-light rounded">
                                            <img src="" id="service-img" class="avatar-md h-auto" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h5 class="fs-14 mb-1">Service Gallery</h5>
                            <p class="text-muted">Add Service Gallery Images.</p>

                            <div class="dropzone">
                                <div class="fallback">
                                    <input name="file" type="file" multiple="multiple" name="gallery" id="gallery">
                                </div>
                                <div class="dz-message needsclick">
                                    <div class="mb-3">
                                        <i class="display-4 text-muted ri-upload-cloud-2-fill"></i>
                                    </div>

                                    <h5>Drop files here or click to upload.</h5>
                                </div>
                            </div>

                            <ul class="list-unstyled mb-0" id="dropzone-preview">
                                <li class="mt-2" id="dropzone-preview-list">
                                    <!-- This is used as the file preview template -->
                                    <div class="border rounded">
                                        <div class="d-flex p-2">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-sm bg-light rounded">
                                                    <img data-dz-thumbnail class="img-fluid rounded d-block" src="#"
                                                        alt="Service-Image" />
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="pt-1">
                                                    <h5 class="fs-14 mb-1" data-dz-name>&nbsp;</h5>
                                                    <p class="fs-13 text-muted mb-0" data-dz-size></p>
                                                    <strong class="error text-danger" data-dz-errormessage></strong>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 ms-3">
                                                <button data-dz-remove class="btn btn-sm btn-danger">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <!-- end dropzon-preview -->
                        </div>
                    </div>
                </div>
                <!-- end card -->

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <h5 class="card-title mb-3">General Info</h5>
                            <div class="mb-3">
                                <label class="form-label" for="manufacturer-name-input">Manufacturer Name</label>
                                <input type="text" class="form-control" id="manufacturer-name-input"
                                    placeholder="Enter manufacturer name" name="manufacturer">
                            </div>
                            <div class="row">
                                <div class="col-lg-3 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="stocks-input">Stocks</label>
                                        <input type="text" class="form-control" id="stocks-input"
                                            placeholder="Stocks" required name="stocks">
                                        <div class="invalid-feedback">Please Enter a service stocks.</div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="service-price-input">Price</label>
                                        <div class="input-group has-validation mb-3">
                                            <span class="input-group-text" id="service-price-addon">$</span>
                                            <input type="text" class="form-control" id="service-price-input"
                                                placeholder="Enter price" aria-label="Price"
                                                aria-describedby="service-price-addon" required name="price">
                                            <div class="invalid-feedback">Please Enter a service price.</div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="service-discount-input">Discount</label>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="service-discount-addon">%</span>
                                            <input type="text" class="form-control" id="service-discount-input"
                                                placeholder="Enter discount" aria-label="discount"
                                                aria-describedby="service-discount-addon" name="discount">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="orders-input">Orders</label>
                                        <input type="text" class="form-control" id="orders-input"
                                            placeholder="Orders" required name="orders">
                                        <div class="invalid-feedback">Please Enter a service orders.</div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <h5 class="card-title mb-3">Meta Data</h5>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="meta-title-input">Meta title</label>
                                    <input type="text" class="form-control" placeholder="Enter meta title"
                                        id="meta-title-input" name="meta_title">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="meta-keywords-input">Meta Keywords</label>
                                    <input type="text" class="form-control" placeholder="Enter meta keywords"
                                        id="meta-keywords-input" name="meta_keywords">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label class="form-label" for="meta-description-input">Meta Description</label>
                                <textarea class="form-control" id="meta-description-input" placeholder="Enter meta description" rows="3" name="meta_description"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success w-sm">Submit</button>
                </div>
            </div>
            <!-- end col -->

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Publish</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="choices-publish-status-input" class="form-label">Status</label>

                            <select class="form-select" id="choices-publish-status-input" data-choices
                                data-choices-search-false name="status">
                                <option value="Published" selected>Published</option>
                                <option value="Scheduled">Scheduled</option>
                                <option value="Draft">Draft</option>
                            </select>
                        </div>

                        <div>
                            <label for="choices-publish-visibility-input" class="form-label">Visibility</label>
                            <select class="form-select" id="choices-publish-visibility-input" data-choices
                                data-choices-search-false name="visibility">
                                <option value="Public" selected>Public</option>
                                <option value="Hidden">Hidden</option>
                            </select>
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Publish Schedule</h5>
                    </div>
                    <!-- end card body -->
                    <div class="card-body">
                        <div>
                            <label for="datepicker-publish-input" class="form-label">Publish Date & Time</label>
                            <input type="text" id="datepicker-publish-input" class="form-control"
                                placeholder="Enter publish date" data-provider="flatpickr" data-date-format="d.m.y"
                                data-enable-time name="publish_at">
                        </div>
                    </div>
                </div>
                <!-- end card -->

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Service Categories</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2"> <a href="#" class="float-end text-decoration-underline">Add
                                New</a>Select service category</p>
                        <select class="form-select" id="choices-category-input" name="category"
                            data-choices data-choices-search-false>
                            <option value="webdesign">Responsive & Clean Design</option>
                            <option value="digitaladvertising">Digital Advertising</option>
                            <option value="seoaudit">SEO Audit</option>
                            <option value="financesolutions">Finance Solutions</option>
                        </select>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Service Tags</h5>
                    </div>
                    <div class="card-body">
                        <div class="hstack gap-3 align-items-start">
                            <div class="flex-grow-1">
                                <input class="form-control" data-choices data-choices-multiple-remove="true"
                                    placeholder="Enter tags" type="text" value="Cotton" name="tags"/>
                            </div>
                        </div>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Service Short Description</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2">Add short description for service</p>
                        <textarea class="form-control" placeholder="Must enter minimum of a 100 characters" rows="3" name="short_description"></textarea>
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->

            </div>
        </div>
        <!-- end row -->
    </form>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/dropzone/dropzone-min.js')); ?>"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

    <script src="<?php echo e(URL::asset('build/js/pages/datatables.init.js')); ?>"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script src="<?php echo e(URL::asset('build/js/pages/offerings-services-create.init.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/saiporala/Documents/Varnarc/varnarc_admin/resources/views/apps-offerings-add-service.blade.php ENDPATH**/ ?>