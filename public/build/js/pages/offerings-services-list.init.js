/*
Template Name: Velzon - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: Ecommerce product list Js File
*/
itemid = 13;
ClassicEditor
    .create(document.querySelector('#ckeditor-classic'))
    .then(function (editor) {
        editor.ui.view.editable.element.style.height = '200px';
    })
    .catch(function (error) {
        console.error(error);
    });

// Dropzone
var dropzonePreviewNode = document.querySelector("#dropzone-preview-list");
dropzonePreviewNode.itemid = "";
var previewTemplate = dropzonePreviewNode.parentNode.innerHTML;
dropzonePreviewNode.parentNode.removeChild(dropzonePreviewNode);
var dropzone = new Dropzone(".dropzone", {
    url: 'https://httpbin.org/post',
    method: "post",
    previewTemplate: previewTemplate,
    previewsContainer: "#dropzone-preview",
});

(function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // date & time
    var date = new Date().toUTCString().slice(5, 16);
    function currentTime() {
        var ampm = new Date().getHours() >= 12 ? "PM" : "AM";
        var hour =
            new Date().getHours() > 12
                ? new Date().getHours() % 12
                : new Date().getHours();
        var minute =
            new Date().getMinutes() < 10
                ? "0" + new Date().getMinutes()
                : new Date().getMinutes();
        if (hour < 10) {
            return "0" + hour + ":" + minute + " " + ampm;
        } else {
            return hour + ":" + minute + " " + ampm;
        }
    }
    setInterval(currentTime, 1000);

    // service image
    document.querySelector("#service-image-input").addEventListener("change", function () {
        var preview = document.querySelector("#service-img");
        var file = document.querySelector("#service-image-input").files[0];
        var reader = new FileReader();
        reader.addEventListener("load",function () {
            preview.src = reader.result;
        },false);
        if (file) {
            reader.readAsDataURL(file);
        }
    });

    

    // choices category input
    var prdoctCategoryInput = new Choices('#choices-category-input', {
        searchEnabled: false,
    });
    
    var editinputValueJson = sessionStorage.getItem('editInputValue');
    if (editinputValueJson) {
        var editinputValueJson = JSON.parse(editinputValueJson);
        document.getElementById("formAction").value = "edit";
        document.getElementById("service-id-input").value = editinputValueJson.id;
        document.getElementById("service-img").src = editinputValueJson.service.img;
        document.getElementById("service-title-input").value = editinputValueJson.service.title;
        document.getElementById("stocks-input").value = editinputValueJson.stock;
        document.getElementById("service-price-input").value = editinputValueJson.price;
        document.getElementById("orders-input").value = editinputValueJson.orders;
        prdoctCategoryInput.setChoiceByValue(editinputValueJson.service.category);
    }

})()


// table-product-list-all 
// var productListAllData = [
// 	{
// 		"id": 1,
// 		"product": {
// 			"img": "build/images/products/img-1.png",
// 			"title": "Half Sleeve Round Neck T-Shirts",
// 			"category": "Fashion"
// 		},
// 		"stock": "12",
// 		"price": "215.00",
// 		"orders": "48",
// 		"rating": "4.2",
// 		"published": {
// 			"publishDate": "12 Oct, 2021",
// 			"publishTime": "10:05 AM",
// 		}
// 	},
// 	{
// 		"id": 2,
// 		"product": {
// 			"img": "build/images/products/img-2.png",
// 			"title": "Urban Ladder Pashe Chair",
// 			"category": "Furniture"
// 		},
// 		"stock": "06",
// 		"price": "160.00",
// 		"orders": "30",
// 		"rating": "4.3",
// 		"published": {
// 			"publishDate": "06 Jan, 2021",
// 			"publishTime": "01:31 PM",
// 		}
// 	},
// 	{
// 		"id": 3,
// 		"product": {
// 			"img": "build/images/products/img-3.png",
// 			"title": "350 ml Glass Grocery Container",
// 			"category": "Grocery"
// 		},
// 		"stock": "10",
// 		"price": "125.00",
// 		"orders": "48",
// 		"rating": "4.5",
// 		"published": {
// 			"publishDate": "26 Mar, 2021",
// 			"publishTime": "11:40 AM",
// 		}
// 	},
// 	{
// 		"id": 4,
// 		"product": {
// 			"img": "build/images/products/img-4.png",
// 			"title": "Fabric Dual Tone Living Room Chair",
// 			"category": "Furniture"
// 		},
// 		"stock": "15",
// 		"price": "340.00",
// 		"orders": "40",
// 		"rating": "4.2",
// 		"published": {
// 			"publishDate": "19 Apr, 2021",
// 			"publishTime": "02:51 PM",
// 		}
// 	},
// 	{
// 		"id": 5,
// 		"product": {
// 			"img": "build/images/products/img-5.png",
// 			"title": "Crux Motorsports Helmet",
// 			"category": "Automotive Accessories"
// 		},
// 		"stock": "08",
// 		"price": "175.00",
// 		"orders": "55",
// 		"rating": "4.4",
// 		"published": {
// 			"publishDate": "30 Mar, 2021",
// 			"publishTime": "09:42 AM",
// 		}
// 	},
// 	{
// 		"id": 6,
// 		"product": {
// 			"img": "build/images/products/img-6.png",
// 			"title": "Half Sleeve T-Shirts (Blue)",
// 			"category": "Fashion"
// 		},
// 		"stock": "15",
// 		"price": "225.00",
// 		"orders": "48",
// 		"rating": "4.2",
// 		"published": {
// 			"publishDate": "12 Oct, 2021",
// 			"publishTime": "04:55 PM",
// 		}
// 	},
// 	{
// 		"id": 7,
// 		"product": {
// 			"img": "build/images/products/img-7.png",
// 			"title": "Noise Evolve Smartwatch",
// 			"category": "Watches"
// 		},
// 		"stock": "12",
// 		"price": "105.00",
// 		"orders": "45",
// 		"rating": "4.3",
// 		"published": {
// 			"publishDate": "15 May, 2021",
// 			"publishTime": "03:40 PM",
// 		}
// 	},
// 	{
// 		"id": 8,
// 		"product": {
// 			"img": "build/images/products/img-8.png",
// 			"title": "Sweatshirt for Men (Pink)",
// 			"category": "Fashion"
// 		},
// 		"stock": "20",
// 		"price": "120.00",
// 		"orders": "48",
// 		"rating": "4.2",
// 		"published": {
// 			"publishDate": "21 Jun, 2021",
// 			"publishTime": "12:18 PM",
// 		}
// 	},
// 	{
// 		"id": 9,
// 		"product": {
// 			"img": "build/images/products/img-9.png",
// 			"title": "Reusable Ecological Coffee Cup",
// 			"category": "Grocery"
// 		},
// 		"stock": "14",
// 		"price": "325.00",
// 		"orders": "55",
// 		"rating": "4.3",
// 		"published": {
// 			"publishDate": "15 Jan, 2021",
// 			"publishTime": "10:29 PM",
// 		}
// 	},
// 	{
// 		"id": 10,
// 		"product": {
// 			"img": "build/images/products/img-10.png",
// 			"title": "Travel Carrying Pouch Bag",
// 			"category": "Kids"
// 		},
// 		"stock": "20",
// 		"price": "180.00",
// 		"orders": "60",
// 		"rating": "4.3",
// 		"published": {
// 			"publishDate": "15 Jun, 2021",
// 			"publishTime": "03:51 PM",
// 		}
// 	},
// 	{
// 		"id": 11,
// 		"product": {
// 			"img": "build/images/products/img-1.png",
// 			"title": "Half Sleeve Round Neck T-Shirts",
// 			"category": "Fashion"
// 		},
// 		"stock": "12",
// 		"price": "215.00",
// 		"orders": "48",
// 		"rating": "4.2",
// 		"published": {
// 			"publishDate": "12 Oct, 2021",
// 			"publishTime": "10:05 AM",
// 		}
// 	},
// 	{
// 		"id": 12,
// 		"product": {
// 			"img": "build/images/products/img-2.png",
// 			"title": "Urban Ladder Pashe Chair",
// 			"category": "Furniture"
// 		},
// 		"stock": "06",
// 		"price": "160.00",
// 		"orders": "30",
// 		"rating": "4.3",
// 		"published": {
// 			"publishDate": "06 Jan, 2021",
// 			"publishTime": "01:31 PM",
// 		}
// 	}
// ];

// var inputValueJson = sessionStorage.getItem('inputValue');
// if (inputValueJson) {
// 	inputValueJson = JSON.parse(inputValueJson);
// 	Array.from(inputValueJson).forEach(element => {
// 		productListAllData.unshift(element);
// 	});
// }

// var editinputValueJson = sessionStorage.getItem('editInputValue');
// if(editinputValueJson){
// 	editinputValueJson = JSON.parse(editinputValueJson);
// 	productListAllData = productListAllData.map(function (item) {
// 		if (item.id == editinputValueJson.id) {
// 			return editinputValueJson;
// 		}
// 		return item;
// 	});
// }
// document.getElementById("addproduct-btn").addEventListener("click", function(){
// 	sessionStorage.setItem('editInputValue',"")
// })

// var productListAll = new gridjs.Grid({
// 	columns:
// 		[
// 			{
// 				name: '#',
// 				width: '40px',
// 				sort: {
// 					enabled: false
// 				},
// 				data: (function (row) {
// 					return gridjs.html('<div class="form-check checkbox-product-list">\
// 					<input class="form-check-input" type="checkbox" value="'+ row.id + '" id="checkbox-' + row.id + '">\
// 					<label class="form-check-label" for="checkbox-'+ row.id + '"></label>\
// 				  </div>');
// 				})
// 			},
// 			{
// 				name: 'Product',
// 				width: '360px',
// 				data: (function (row) {
// 					return gridjs.html('<div class="d-flex align-items-center">' +
// 						'<div class="flex-shrink-0 me-3">' +
// 						'<div class="avatar-sm bg-light rounded p-1"><img src="' + row.product.img + '" alt="" class="img-fluid d-block"></div>' +
// 						'</div>' +
// 						'<div class="flex-grow-1">' +
// 						'<h5 class="fs-14 mb-1"><a href="apps-ecommerce-product-details" class="text-body">' + row.product.title + '</a></h5>' +
// 						'<p class="text-muted mb-0">Category : <span class="fw-medium">' + row.product.category + '</span></p>' +
// 						'</div>' +
// 						'</div>');
// 				})
// 			},
// 			{
// 				name: 'Stock',
// 				width: '94px',
// 			},
// 			{
// 				name: 'Price',
// 				width: '101px',
// 				formatter: (function (cell) {
// 					return gridjs.html('$' + cell);
// 				})
// 			},
// 			{
// 				name: 'Orders',
// 				width: '84px',
// 			},
// 			{
// 				name: 'Rating',
// 				width: '105px',
// 				formatter: (function (cell) {
// 					return gridjs.html('<span class="badge bg-light text-body fs-12 fw-medium"><i class="mdi mdi-star text-warning me-1"></i>' + cell + '</span></td>');
// 				})
// 			},
// 			{
// 				name: 'Published',
// 				width: '220px',
// 				data: (function (row) {
// 					return gridjs.html(row.published.publishDate + '<small class="text-muted ms-1">' + row.published.publishTime + '</small>');
// 				})
// 			},
// 			{
// 				name: "Action",
// 				width: '80px',
// 				sort: {
// 					enabled: false
// 				},
// 				formatter: (function (cell, row) {
// 					var x = new DOMParser().parseFromString(row._cells[0].data.props.content, "text/html").body.querySelector(".checkbox-product-list .form-check-input").value
// 					return gridjs.html('<div class="dropdown">' +
// 						'<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
// 						'<i class="ri-more-fill"></i>' +
// 						'</button>' +
// 						'<ul class="dropdown-menu dropdown-menu-end">' +
// 						'<li><a class="dropdown-item" href="apps-ecommerce-product-details"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>' +
// 						'<li><a class="dropdown-item edit-list" data-edit-id=' + x + ' href="apps-ecommerce-add-product"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>' +
// 						'<li class="dropdown-divider"></li>' +
// 						'<li><a class="dropdown-item remove-list" href="#" data-id=' + x + ' data-bs-toggle="modal" data-bs-target="#removeItemModal"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>' +
// 						'</ul>' +
// 						'</div>');
// 				})
// 			}
// 		],
// 	className: {
// 		th: 'text-muted',
// 	},
// 	pagination: {
// 		limit: 10
// 	},
// 	sort: true,
// 	data: productListAllData
// }).render(document.getElementById("table-product-list-all"));

// // table-product-list-published
// var productListPublishedData = [
// 	{
// 		"id": 1,
// 		"product": {
// 			"img": "build/images/products/img-2.png",
// 			"title": "Urban Ladder Pashe Chair",
// 			"category": "Furniture"
// 		},
// 		"stock": "06",
// 		"price": "160.00",
// 		"orders": "30",
// 		"rating": "4.3",
// 		"published": {
// 			"publishDate": "06 Jan, 2021",
// 			"publishTime": "01:31 PM",
// 		}
// 	},{
// 		"id": 2,
// 		"product": {
// 			"img": "build/images/products/img-6.png",
// 			"title": "Half Sleeve T-Shirts (Blue)",
// 			"category": "Fashion"
// 		},
// 		"stock": "15",
// 		"price": "125.00",
// 		"orders": "48",
// 		"rating": "4.2",
// 		"published": {
// 			"publishDate": "12 Oct, 2021",
// 			"publishTime": "04:55 PM",
// 		}
// 	},{
// 		"id": 3,
// 		"product": {
// 			"img": "build/images/products/img-4.png",
// 			"title": "Fabric Dual Tone Living Room Chair",
// 			"category": "Furniture"
// 		},
// 		"stock": "15",
// 		"price": "140.00",
// 		"orders": "40",
// 		"rating": "4.2",
// 		"published": {
// 			"publishDate": "19 Apr, 2021",
// 			"publishTime": "02:51 PM",
// 		}
// 	},{
// 		"id": 4,
// 		"product": {
// 			"img": "build/images/products/img-4.png",
// 			"title": "350 ml Glass Grocery Container",
// 			"category": "Grocery"
// 		},
// 		"stock": "10",
// 		"price": "125.00",
// 		"orders": "48",
// 		"rating": "4.5",
// 		"published": {
// 			"publishDate": "26 Mar, 2021",
// 			"publishTime": "11:40 AM",
// 		}
// 	},{
// 		"id": 5,
// 		"product": {
// 			"img": "build/images/products/img-5.png",
// 			"title": "Crux Motorsports Helmet",
// 			"category": "Automotive Accessories"
// 		},
// 		"stock": "08",
// 		"price": "135.00",
// 		"orders": "55",
// 		"rating": "4.4",
// 		"published": {
// 			"publishDate": "30 Mar, 2021",
// 			"publishTime": "09:42 AM",
// 		}
// 	}
// ]

// var productListPublished = new gridjs.Grid({
// 	columns:
// 		[
// 			{
// 				name: '#',
// 				width: '40px',
// 				sort: {
// 					enabled: false
// 				},
// 				formatter: (function (cell) {
// 					return gridjs.html('<div class="form-check checkbox-product-list">\
// 					<input class="form-check-input" type="checkbox" value="'+ cell + '" id="checkboxpublished-' + cell + '">\
// 					<label class="form-check-label" for="checkbox-'+ cell + '"></label>\
// 				  </div>');
// 				})
// 			},
// 			{
// 				name: 'Product',
// 				width: '360px',
// 				data: (function (row) {
// 					return gridjs.html('<div class="d-flex align-items-center">' +
// 						'<div class="flex-shrink-0 me-3">' +
// 						'<div class="avatar-sm bg-light rounded p-1"><img src="' + row.product.img + '" alt="" class="img-fluid d-block"></div>' +
// 						'</div>' +
// 						'<div class="flex-grow-1">' +
// 						'<h5 class="fs-14 mb-1"><a href="apps-ecommerce-product-details" class="text-body">' + row.product.title + '</a></h5>' +
// 						'<p class="text-muted mb-0">Category : <span class="fw-medium">' + row.product.category + '</span></p>' +
// 						'</div>' +
// 						'</div>');
// 				})
// 			},
// 			{
// 				name: 'Stock',
// 				width: '94px',
// 			},
// 			{
// 				name: 'Price',
// 				width: '101px',
// 				formatter: (function (cell) {
// 					return gridjs.html('$' + cell);
// 				})
// 			},
// 			{
// 				name: 'Orders',
// 				width: '84px',
// 			},
// 			{
// 				name: 'Rating',
// 				width: '105px',
// 				formatter: (function (cell) {
// 					return gridjs.html('<span class="badge bg-light text-body fs-12 fw-medium"><i class="mdi mdi-star text-warning me-1"></i>' + cell + '</span></td>');
// 				})
// 			},
// 			{
// 				name: 'Published',
// 				width: '220px',
// 				data: (function (row) {
// 					return gridjs.html(row.published.publishDate + '<small class="text-muted ms-1">' + row.published.publishTime + '</small>');
// 				})
// 			},
// 			{
// 				name: "Action",
// 				width: '80px',
// 				sort: {
// 					enabled: false
// 				},
// 				formatter: (function (cell, row) {
// 					return gridjs.html('<div class="dropdown">' +
// 						'<button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">' +
// 						'<i class="ri-more-fill"></i>' +
// 						'</button>' +
// 						'<ul class="dropdown-menu dropdown-menu-end">' +
// 						'<li><a class="dropdown-item" href="apps-ecommerce-product-details"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>' +
// 						'<li><a class="dropdown-item" href="apps-ecommerce-add-product"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>' +
// 						'<li class="dropdown-divider"></li>' +
// 						'<li><a class="dropdown-item remove-list" href="#" data-id=' + row._cells[0].data + ' data-bs-toggle="modal" data-bs-target="#removeItemModal"><i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>' +
// 						'</ul>' +
// 						'</div>');
// 				})
// 			}
// 		],
// 	className: {
// 		th: 'text-muted',
// 	},
// 	pagination: {
// 		limit: 10
// 	},
// 	sort: true,
// 	data: productListPublishedData
// }).render(document.getElementById("table-product-list-published"));


// // Search product list
// var searchProductList = document.getElementById("searchProductList");
// searchProductList.addEventListener("keyup", function () {
// 	var inputVal = searchProductList.value.toLowerCase();
// 	function filterItems(arr, query) {
// 		return arr.filter(function (el) {
// 			return el.product.title.toLowerCase().indexOf(query.toLowerCase()) !== -1
// 		})
// 	}

// 	var filterData = filterItems(productListAllData, inputVal);
// 	var filterPublishData = filterItems(productListPublishedData, inputVal);
// 	productListAll.updateConfig({
// 		data: filterData
// 	}).forceRender();

// 	productListPublished.updateConfig({
// 		data: filterPublishData
// 	}).forceRender();
// 	checkRemoveItem();
// });

// // mail list click event
// Array.from(document.querySelectorAll('.filter-list a')).forEach(function (filteritem) {
// 	filteritem.addEventListener("click", function () {
// 		var filterListItem = document.querySelector(".filter-list a.active");
// 		if (filterListItem) filterListItem.classList.remove("active");
// 		filteritem.classList.add('active');

// 		var filterItemValue = filteritem.querySelector(".listname").innerHTML

// 		var filterData = productListAllData.filter(filterlist => filterlist.product.category === filterItemValue);
// 		var filterPublishedData = productListPublishedData.filter(filterlist => filterlist.product.category === filterItemValue);

// 		productListAll.updateConfig({
// 			data: filterData
// 		}).forceRender();

// 		productListPublished.updateConfig({
// 			data: filterPublishedData
// 		}).forceRender();

// 		checkRemoveItem();
// 	});
// })

// // price range slider
// var slider = document.getElementById('product-price-range');

// noUiSlider.create(slider, {
// 	start: [0, 2000], // Handle start position
// 	step: 10, // Slider moves in increments of '10'
// 	margin: 20, // Handles must be more than '20' apart
// 	connect: true, // Display a colored bar between the handles
// 	behaviour: 'tap-drag', // Move handle on tap, bar is draggable
// 	range: { // Slider can select '0' to '100'
// 		'min': 0,
// 		'max': 2000
// 	},
// 	format: wNumb({ decimals: 0, prefix: '$ ' })
// });

// var minCostInput = document.getElementById('minCost'),
// 	maxCostInput = document.getElementById('maxCost');

// var filterDataAll = '';
// var filterDataPublished = '';

// // When the slider value changes, update the input and span
// slider.noUiSlider.on('update', function (values, handle) {
// 	var productListupdatedAll = productListAllData;
// 	var productListupdatedPublished = productListPublishedData;
// 	if (handle) {
// 		maxCostInput.value = values[handle];

// 	} else {
// 		minCostInput.value = values[handle];
// 	}

// 	var maxvalue = maxCostInput.value.substr(2);
// 	var minvalue = minCostInput.value.substr(2);
// 	filterDataAll = productListupdatedAll.filter(
// 		product => parseFloat(product.price) >= minvalue && parseFloat(product.price) <= maxvalue
// 	);
// 	filterDataPublished = productListupdatedPublished.filter(
// 		product => parseFloat(product.price) >= minvalue && parseFloat(product.price) <= maxvalue
// 	);
// 	productListAll.updateConfig({
// 		data: filterDataAll
// 	}).forceRender();
// 	productListPublished.updateConfig({
// 		data: filterDataPublished
// 	}).forceRender();
// 	checkRemoveItem();
// });


// minCostInput.addEventListener('change', function () {
// 	slider.noUiSlider.set([null, this.value]);
// });

// maxCostInput.addEventListener('change', function () {
// 	slider.noUiSlider.set([null, this.value]);
// });

// // text inputs example
// var filterChoicesInput = new Choices(
// 	document.getElementById('filter-choices-input'),
// 	{
// 		addItems: true,
// 		delimiter: ',',
// 		editItems: true,
// 		maxItemCount: 10,
// 		removeItems: true,
// 		removeItemButton: true,
// 	}
// )

// // sidebar filter check
// Array.from(document.querySelectorAll(".filter-accordion .accordion-item")).forEach(function (item) {
// 	var isFilterSelected = item.querySelectorAll(".filter-check .form-check .form-check-input:checked").length;
// 	item.querySelector(".filter-badge").innerHTML = isFilterSelected;
// 	Array.from(item.querySelectorAll(".form-check .form-check-input")).forEach(function (subitem) {
// 		var checkElm = subitem.value;
// 		if (subitem.checked) {
// 			filterChoicesInput.setValue([checkElm]);
// 		}
// 		subitem.addEventListener("click", function (event) {
// 			if (subitem.checked) {
// 				isFilterSelected++;
// 				item.querySelector(".filter-badge").innerHTML = isFilterSelected;
// 				(isFilterSelected > 0) ? item.querySelector(".filter-badge").style.display = 'block' : item.querySelector(".filter-badge").style.display = 'none';
// 				filterChoicesInput.setValue([checkElm]);

// 			} else {
// 				filterChoicesInput.removeActiveItemsByValue(checkElm);
// 			}
// 		});
// 		filterChoicesInput.passedElement.element.addEventListener('removeItem', function (event) {
// 			if (event.detail.value == checkElm) {
// 				subitem.checked = false;
// 				isFilterSelected--;
// 				item.querySelector(".filter-badge").innerHTML = isFilterSelected;
// 				(isFilterSelected > 0) ? item.querySelector(".filter-badge").style.display = 'block' : item.querySelector(".filter-badge").style.display = 'none';
// 			}
// 		}, false);
// 		// clearall
// 		document.getElementById("clearall").addEventListener("click", function () {
// 			subitem.checked = false;
// 			filterChoicesInput.removeActiveItemsByValue(checkElm);
// 			isFilterSelected = 0;
// 			item.querySelector(".filter-badge").innerHTML = isFilterSelected;
// 			(isFilterSelected > 0) ? item.querySelector(".filter-badge").style.display = 'block' : item.querySelector(".filter-badge").style.display = 'none';
// 			productListAll.updateConfig({
// 				data: productListAllData
// 			}).forceRender();

// 			productListPublished.updateConfig({
// 				data: productListPublishedData
// 			}).forceRender();
// 		});
// 	});
// });

// // Search Brands Options
// var searchBrandsOptions = document.getElementById("searchBrandsList");
// searchBrandsOptions.addEventListener("keyup", function () {
// 	var inputVal = searchBrandsOptions.value.toLowerCase();
// 	var searchItem = document.querySelectorAll("#flush-collapseBrands .form-check");
// 	Array.from(searchItem).forEach(function (elem) {
// 		var searchBrandsTxt = elem.getElementsByClassName("form-check-label")[0].innerText.toLowerCase();
// 		elem.style.display = searchBrandsTxt.includes(inputVal) ? "block" : "none";
// 	})
// });

// // table select to remove
// // checkbox-wrapper
// var isSelected = 0;
// function checkRemoveItem() {
// 	var tabEl = document.querySelectorAll('a[data-bs-toggle="tab"]');
// 	Array.from(tabEl).forEach(function (el) {
// 		el.addEventListener('show.bs.tab', function (event) {
// 			isSelected = 0;
// 			document.getElementById("selection-element").style.display = 'none';
// 		});
// 	});
// 	setTimeout(function () {
// 		Array.from(document.querySelectorAll(".checkbox-product-list input")).forEach(function (item) {
// 			item.addEventListener('click', function (event) {
// 				if (event.target.checked == true) {
// 					event.target.closest('tr').classList.add("gridjs-tr-selected");
// 				} else {
// 					event.target.closest('tr').classList.remove("gridjs-tr-selected");
// 				}

// 				var checkboxes = document.querySelectorAll('.checkbox-product-list input:checked');
// 				isSelected = checkboxes.length;

// 				if (event.target.closest('tr').classList.contains("gridjs-tr-selected")) {
// 					document.getElementById("select-content").innerHTML = isSelected;
// 					(isSelected > 0) ? document.getElementById("selection-element").style.display = 'block' : document.getElementById("selection-element").style.display = 'none';
// 				} else {

// 					document.getElementById("select-content").innerHTML = isSelected;
// 					(isSelected > 0) ? document.getElementById("selection-element").style.display = 'block' : document.getElementById("selection-element").style.display = 'none';
// 				}
// 			});
// 		});
// 		removeItems();
// 		removeSingleItem();
// 	}, 100);
// }


// // check to remove item
// var checkboxes = document.querySelectorAll('.checkbox-wrapper-mail input');
// function removeItems() {
// 	var removeItem = document.getElementById('removeItemModal');
// 	removeItem.addEventListener('show.bs.modal', function (event) {
// 		isSelected = 0;
// 		document.getElementById("delete-product").addEventListener("click", function () {
// 			Array.from(document.querySelectorAll(".gridjs-table tr")).forEach(function (element) {
// 				var filtered = '';
// 				if (element.classList.contains("gridjs-tr-selected")) {
// 					var getid = element.querySelector('.form-check-input').value;
// 					function arrayRemove(arr, value) {
// 						return arr.filter(function (ele) {
// 							return ele.id != value;
// 						});
// 					}
// 					var filtered = arrayRemove(productListAllData, getid);
// 					var filteredPublished = arrayRemove(productListPublishedData, getid);
// 					productListAllData = filtered;
// 					productListPublishedData = filteredPublished;
// 					element.remove();
// 				}
// 			});
// 			document.getElementById("btn-close").click();
// 			if (document.getElementById("selection-element"))
// 				document.getElementById("selection-element").style.display = 'none';

// 			checkboxes.checked = false;
// 		});
// 	})
// }

// function removeSingleItem() {
// 	var getid = 0;
// 	Array.from(document.querySelectorAll(".remove-list")).forEach(function (item) {
// 		item.addEventListener('click', function (event) {
// 			getid = item.getAttribute('data-id');
// 			document.getElementById("delete-product").addEventListener("click", function () {
// 				function arrayRemove(arr, value) {
// 					return arr.filter(function (ele) {
// 						return ele.id != value;
// 					});
// 				}
// 				var filtered = arrayRemove(productListAllData, getid);
// 				var filteredPublished = arrayRemove(productListPublishedData, getid);
// 				productListAllData = filtered;
// 				productListPublishedData = filteredPublished;
// 				var element = item.closest(".gridjs-tr");
// 				element.remove();
// 			});
// 		});
// 	});


// 	var getEditid = 0;
// 	Array.from(document.querySelectorAll(".edit-list")).forEach(function (elem) {
// 		elem.addEventListener('click', function (event) {
// 			getEditid = elem.getAttribute('data-edit-id');

// 			productListAllData = productListAllData.map(function (item) {
// 				if (item.id == getEditid) {

// 					sessionStorage.setItem('editInputValue', JSON.stringify(item));
// 				}
// 				return item;
// 			});
// 		});
// 	});
// }

$(function () {

	let table;

	$('#addservice-btn').on('click', function (e) {
		e.preventDefault();
		$('#service-list').hide();
		$('#service-form').show();
	});

	// Cancel button → Show list, hide form
	$('#cancel-form').on('click', function () {
		$('#service-form').hide();
		$('#service-list').show();
	});

	// Function to load DataTable
	function loadServices() {
		$.get("/api/services", function (data) {
			let allTbody = $("#allServicesTable tbody");
			let publishedTbody = $("#publishedServicesTable tbody");
			let draftTbody = $("#draftServicesTable tbody");

			allTbody.empty();
			publishedTbody.empty();
			draftTbody.empty();

			if (data.length === 0) {
				$(".noresult").show();
				$("#allCount").text(0);
				$("#publishedCount").text(0);
				$("#draftCount").text(0);
				return;
			}
			$(".noresult").hide();

			let allCount = 0, publishedCount = 0, draftCount = 0;
			data.forEach(service => {
				let row = `
					<tr>
						<td>
							<div class="form-check">
								<input class="form-check-input" type="checkbox" name="chk_service" value="${service.id}">
							</div>
						</td>
						<td class="title">${service.title}</td>
						<td class="status">${service.status}</td>
						<td class="price">${service.price ?? ''}</td>
						<td class="category">${service.category ?? ''}</td>
						<td>
							<div class="dropdown">
								<button class="btn btn-soft-secondary btn-sm dropdown" data-bs-toggle="dropdown">
									<i class="ri-more-fill align-middle"></i>
								</button>
								<ul class="dropdown-menu dropdown-menu-end">
									<li><a class="dropdown-item view-item-btn" href="javascript:void(0);" data-id="${service.id}">
										<i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
									<li><a class="dropdown-item edit-item-btn" href="javascript:void(0);" data-id="${service.id}">
										<i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
									<li><a class="dropdown-item remove-item-btn" href="javascript:void(0);" data-id="${service.id}">
										<i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>
								</ul>
							</div>
						</td>
					</tr>
				`;

				// Add row to All
				allTbody.append(row);
				allCount++;

				// Split into Published/Draft
				if (service.status?.toLowerCase() === "published") {
					publishedTbody.append(row);
					publishedCount++;
				}
				if (service.status?.toLowerCase() === "draft") {
					draftTbody.append(row);
					draftCount++;
				}
			});
		});
	}

	// Load services when page is ready
	loadServices();

	// Add new service
	$('#addService').on('click', function () {
		$('#service_id').val('');
		$('#serviceForm')[0].reset();
		$('#serviceModal').modal('show');
	});

	// Save service (create or update)
	$(document).on('submit', '#createservice-form', function (e) {
		e.preventDefault();

		let id = $('#id').val(); // hidden field for update
		let url = id ? `/api/services/${id}` : '/api/services';
		let method = id ? 'POST' : 'POST'; // if Laravel expects PUT, append `_method`

		let form = this;
		let formData = new FormData(form);

		// If editing, spoof PUT method (Laravel understands `_method`)
		if (id) {
			formData.append('_method', 'PUT');
		}

		// CKEditor content (if you’re using it)
		if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['ckeditor-classic']) {
			formData.append('description', CKEDITOR.instances['ckeditor-classic'].getData());
		}

		let existingGallery = [];
		if (dropzone && dropzone.getAcceptedFiles().length > 0) {
            dropzone.getAcceptedFiles().forEach((file, index) => {
                if (file.isExisting) {
					// keep only DB path, not the mock object
					let cleanPath = file.name; // since you stored actual filename
					existingGallery.push(`services/gallery/${cleanPath}`);
				} else if (file instanceof File) {
					// new file → append binary
					formData.append(`gallery[${index}]`, file);
				}
            });
        }

		formData.append("existing_gallery", JSON.stringify(existingGallery));

		$.ajax({
			url: url,
			method: method,
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				console.log("Saved successfully:", response);
				Toastify({
					text: "Service saved successfully!",
					duration: 3000,
					gravity: "top", // top or bottom
					position: "right", // left, center, right
					backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)"
				}).showToast();
				// Optionally reset form
				form.reset();
				// Refresh your services list
				loadServices();
				$('#service-list').show();
				$('#service-form').hide();
			},
			error: function (xhr) {
				console.error("Error saving service:", xhr.responseText);
				Toastify({
					text: "Something went wrong while saving service.!",
					duration: 3000,
					gravity: "top", // top or bottom
					position: "right", // left, center, right
					backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)"
				}).showToast();
			}
		});
	});

	$(document).on('click', '.view-item-btn', function () {
		let id = $(this).data('id');
		$.ajax({
			url: `/api/services/${id}`,
			type: 'GET',
			success: function (res) {
				// Example: Show in a Bootstrap modal
				$('#viewModal .modal-body').html(`
                    <h5>${res.title}</h5>
                    <p>${res.description}</p>
                    <p><strong>Status:</strong> ${res.status}</p>
                `);
				$('#viewModal').modal('show');
			},
			error: function () {
				Toastify({ text: "Failed to load service", backgroundColor: "red", duration: 3000 }).showToast();
			}
		});
	});

	// ✅ Edit Service
	$(document).on('click', '.edit-item-btn', function () {
		let id = $(this).data('id');
		$.ajax({
			url: `/api/services/${id}`,
			type: 'GET',
			success: function (res) {
				// Fill form fields
				$('#createservice-form [name="id"]').val(res.id);
				$('#createservice-form [name="title"]').val(res.title);
				$('#createservice-form [name="description"]').val(res.description);
				$('#createservice-form [name="status"]').val(res.status);
				$('#createservice-form [name="visibility"]').val(res.visibility);
				$('#createservice-form [name="publish_at"]').val(res.publish_at);
				$('#createservice-form [name="category"]').val(res.category);
				$('#createservice-form [name="tags"]').val(res.tags);
				$('#createservice-form [name="manufacturer"]').val(res.manufacturer);
				$('#createservice-form [name="stocks"]').val(res.stocks);
				$('#createservice-form [name="price"]').val(res.price);
				$('#createservice-form [name="discount"]').val(res.discount);
				$('#createservice-form [name="orders"]').val(res.orders);
				$('#createservice-form [name="meta_title"]').val(res.meta_title);
				$('#createservice-form [name="meta_keywords"]').val(res.meta_keywords);
				$('#createservice-form [name="meta_description"]').val(res.meta_description);
				$('#createservice-form [name="short_description"]').val(res.short_description);
				$('#service-img').attr("src", `/storage/${res.image}`);

				// ✅ Reset Dropzone before loading
				dropzone.removeAllFiles(true);

				let galleryArray = [];
				try {
					galleryArray = JSON.parse(res.gallery); // ensure it's valid JSON
				} catch (e) {
					console.error("Gallery parse error:", e);
				}

				// If Laravel returned relative paths like "services/gallery/filename.jpg"
				// prepend /storage/
				if (galleryArray && galleryArray.length > 0) {
					galleryArray.forEach(url => {
						// Fix path if needed
						let imageUrl = url.startsWith("http") ? url : `/storage/${url}`;

						// Create mock file
						let mockFile = { name: imageUrl.split('/').pop(), size: 12345, isExisting: true };

						// Display in Dropzone
						dropzone.displayExistingFile(mockFile, imageUrl);

						// Mark as already uploaded
						mockFile.status = Dropzone.SUCCESS;
						mockFile.accepted = true;

						// Push into Dropzone’s files array
						dropzone.files.push(mockFile);
					});
				}
				// 'gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',

				// Store service id for update
				$('#createservice-form').data('id', id);

				// Show form, hide list
				$('#service-list').hide();
				$('#service-form').show();
			},
			error: function () {
				Toastify({ text: "Failed to load service for editing", backgroundColor: "red", duration: 3000 }).showToast();
			}
		});
	});

	// ✅ Delete Service
	$(document).on('click', '.remove-item-btn', function () {
		let id = $(this).data('id');
		if (!confirm("Are you sure you want to delete this service?")) return;

		$.ajax({
			url: `/api/services/${id}`,
			type: 'DELETE',
			success: function () {
				Toastify({ text: "Service deleted", backgroundColor: "green", duration: 3000 }).showToast();
				// Reload DataTable row
			},
			error: function () {
				Toastify({ text: "Failed to delete service", backgroundColor: "red", duration: 3000 }).showToast();
			}
		});
	});
});