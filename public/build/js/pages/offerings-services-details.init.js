/*
Template Name: Velzon - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: Ecommerce product Details Js File
*/

var productNavSlider = new Swiper(".product-nav-slider", {
	loop: false,
	spaceBetween: 10,
	slidesPerView: 4,
	freeMode: true,
	watchSlidesProgress: true,
});
var productThubnailSlider = new Swiper(".product-thumbnail-slider", {
	loop: false,
	spaceBetween: 24,
	navigation: {
		nextEl: ".swiper-button-next",
		prevEl: ".swiper-button-prev",
	},
	thumbs: {
		swiper: productNavSlider,
	},
});

$(document).ready(function () {

    let table;

    // Function to load DataTable
    function loadServices() {
        if ($.fn.dataTable && $.fn.dataTable.isDataTable('#servicesTable')) {
            table.ajax.reload(); // reload if already exists
        } else {
            table = $('#servicesTable').DataTable({
                ajax: '/api/services',
                columns: [
                    { data: 'id' },
                    { data: 'title' },
                    { data: 'status' },
                    { data: 'price' },
                    { data: 'category' },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <button class="btn btn-sm btn-info edit" data-id="${data.id}">Edit</button>
                                <button class="btn btn-sm btn-danger delete" data-id="${data.id}">Delete</button>
                            `;
                        }
                    }
                ]
            });
        }
    }

    // Load services when page is ready
    loadServices();

    // Add new service
    $('#addService').on('click', function() {
        $('#service_id').val('');
        $('#serviceForm')[0].reset();
        $('#serviceModal').modal('show');
    });

    // Save service (create or update)
    $('#serviceForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#service_id').val();
        let url = id ? `/api/services/${id}` : '/api/services';
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: {
                title: $('#title').val(),
                status: $('#status').val(),
                price: $('#price').val(),
                meta_title: $('#meta_title').val(),
                meta_keywords: $('#meta_keywords').val(),
                meta_description: $('#meta_description').val(),
            },
            success: function() {
                $('#serviceModal').modal('hide');
                loadServices(); // refresh table
            }
        });
    });

    // Edit service
    $('#servicesTable').on('click', '.edit', function() {
        let id = $(this).data('id');
        $.get(`/api/services/${id}`, function(data) {
            $('#service_id').val(data.id);
            $('#title').val(data.title);
            $('#status').val(data.status);
            $('#price').val(data.price);
            $('#meta_title').val(data.meta_title);
            $('#meta_keywords').val(data.meta_keywords);
            $('#meta_description').val(data.meta_description);
            $('#serviceModal').modal('show');
        });
    });

    // Delete service
    $('#servicesTable').on('click', '.delete', function() {
        if (confirm('Delete this service?')) {
            let id = $(this).data('id');
            $.ajax({
                url: `/api/services/${id}`,
                method: 'DELETE',
                success: function() {
                    loadServices(); // refresh table
                }
            });
        }
    });
});