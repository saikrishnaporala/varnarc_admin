<script lang="ts" setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Swal from "sweetalert2";

import Lottie from "@/Components/widgets/lottie.vue";
import animationData from "@/Components/widgets/msoeawqm.json";
import axios from 'axios';
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import multi_user_image from "@assets/images/users/multi-user.jpg";
import { companies as companiesData } from "@/common/data";

// Data & component imports
const companies = ref(companiesData);
const addCompanyModal = ref(false);
const value = ref(null);
const page = ref(1);
const perPage = ref(8);
const pages = ref<number[]>([]);
const searchQuery = ref<string | null>(null);
const submitted = ref(false);
const dataEdit = ref(false);
const deleteModal = ref(false);
const multi_user_image_src = multi_user_image;

// Default animation options
const defaultOptions = { animationData: animationData };
const defaultOptions1 = { animationData: animationData1 };

// Event object with types
interface Event {
  id: string;
  img_src: string;
  name: string;
  owner: string;
  industry_type: string;
  company_type: string;
  star_value: string;
  location: string;
  employee: string;
  website: string;
  contact_email: string;
  contact_phone: string;
  since: string;
}

const event = ref<Event>({
  id: "",
  img_src: "",
  name: "",
  owner: "",
  industry_type: "",
  company_type: "",
  star_value: "",
  location: "",
  employee: "",
  website: "",
  contact_email: "",
  contact_phone: "",
  since: "",
});

// Computed properties
const displayedPosts = computed(() => paginate(companies.value));
const resultQuery = computed(() => {
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase();
    return displayedPosts.value.filter((data) => {
      return (
        data.contact_email.toLowerCase().includes(search) ||
        data.employee.toLowerCase().includes(search) ||
        data.owner.toLowerCase().includes(search) ||
        data.industry_type.toLowerCase().includes(search) ||
        data.website.toLowerCase().includes(search) ||
        data.star_value.toString().toLowerCase().includes(search) ||
        data.name.toLowerCase().includes(search) ||
        data.location.toLowerCase().includes(search)
      );
    });
  } else {
    return displayedPosts.value;
  }
});

// Methods
const handleSubmit = async () => {
  submitted.value = true;
  const isValid =
    event.value.name &&
    event.value.owner &&
    event.value.industry_type &&
    event.value.company_type &&
    event.value.star_value &&
    event.value.location &&
    event.value.employee &&
    event.value.website &&
    event.value.contact_email &&
    event.value.contact_phone &&
    event.value.since;
  if (isValid) {
    if (dataEdit.value) {
      addCompanyModal.value = false;
      companies.value = companies.value.map((item) =>
        item.id.toString() == event.value.id.toString()
          ? { ...item, ...event.value }
          : item
      );
      try {
        const response = await axios.post('/api/companies/store', event.value);
        console.log('Company updated successfully:', response.data);
      } catch (error) {
        console.error('Error submitting company:', error);
      }
    } else {
      const newCompany = {
        id: (Math.floor(Math.random() * 100 + 20) - 20).toString(),
        img_src: multi_user_image_src,
        ...event.value,
      };
      try {
        const response = await axios.post('/api/companies/store', newCompany);
        console.log('Company submitted successfully:', response.data);
      } catch (error) {
        console.error('Error submitting company:', error);
      }
      companies.value = [newCompany, ...companies.value];
      addCompanyModal.value = false;
    }
  }
};

const onSort = (column: string) => {
  const direction = ref('asc');
  direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  const sortedArray = [...companies.value];
  sortedArray.sort((a, b) => {
    const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
    return direction.value === 'asc' ? res : -res;
  });
  companies.value = sortedArray;
};

const editDetails = (data: Event) => {
  addCompanyModal.value = true;
  dataEdit.value = true;
  event.value = {
    id: data.id,
    img_src: data.img_src,
    name: data.name,
    owner: data.owner,
    industry_type: data.industry_type,
    company_type: data.company_type,
    star_value: data.star_value,
    location: data.location,
    employee: data.employee,
    website: data.website,
    contact_email: data.contact_email,
    contact_phone: data.contact_phone,
    since: data.since,
  };
  submitted.value = false;
};

const toggleModal = () => {
  addCompanyModal.value = true;
  dataEdit.value = false;
  event.value = {
    id: "",
    img_src: "",
    name: "",
    owner: "",
    industry_type: "",
    company_type: "",
    star_value: "",
    location: "",
    employee: "",
    website: "",
    contact_email: "",
    contact_phone: "",
    since: "",
  };
  submitted.value = false;
};

const deleteModalToggle = (data: Event) => {
  deleteModal.value = true;
  event.value.id = data.id;
};

const deleteData = async () => {
  if (event.value.id) {
    companies.value = companies.value.filter((item: { id: string; }) => item.id !== event.value.id);
    deleteModal.value = false;
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.delete(`/api/companies/${event.value.id}`)
      console.log(response);
      companies.value = response.data.result;
    } catch (err) {
      error.value = 'Failed to fetch leads data.';
      console.error(err);
    } finally {
      loading.value = false;
    }
  }
};

// Pagination methods
const setPages = () => {
  const numberOfPages = Math.ceil(companies.value.length / perPage.value);
  pages.value = [];
  for (let index = 1; index <= numberOfPages; index++) {
    pages.value.push(index);
  }
};

const paginate = (items: any[]) => {
  const from = page.value * perPage.value - perPage.value;
  const to = page.value * perPage.value;
  return items.slice(from, to);
};

// Watchers
watch(companies, setPages);

// Lifecycle hooks
onMounted(() => {
  fetchCompanies();
  // setPages();
});

const loading = ref(false);
const error = ref<string | null>(null);

const fetchCompanies = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/api/companies');  // Adjust the API endpoint as needed
    console.log(response);
    companies.value = response.data.result;
  } catch (err) {
    error.value = 'Failed to fetch companies data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};
const getImageSrc = (img: string | null) => {
  // If leadImage exists, use it, otherwise use the default image
  console.log(new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/avatar-1.jpg");
  return img ? new URL(`@/images/users/${img}`, import.meta.url).href : new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/users/avatar-1.jpg";
};

</script>

<template>
  <Layout>

    <Head title="SMS Center" />
    <PageHeader title="SMS Center" pageTitle="CRM" subTitle="Campaign"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body>
          <BCardHeader>
            <div class="d-flex align-items-center flex-wrap gap-2">
              <div class="flex-grow-1">
                <BButton variant="info" class="add-btn me-1" @click="toggleModal">
                  <i class="ri-add-fill me-1 align-bottom"></i> Add Company
                </BButton>

              </div>
              <div class="flex-shrink-0">
                <div class="hstack text-nowrap gap-2">
                  <BButton variant="soft-danger" class="material-shadow-none" id="remove-actions" @click="deleteMultiple">
                    <i class="ri-delete-bin-2-line"></i>
                  </BButton>
                  <BButton variant="danger" class="material-shadow-none">
                    <i class="ri-filter-2-line me-1 align-bottom"></i> Filters
                  </BButton>
                  <BButton variant="soft-success" class="material-shadow-none">Import</BButton>
                  <BDropdown variant="link" class="card-header-dropdown" toggle-class="btn btn-soft-info material-shadow-none" no-caret menu-class="dropdown-menu-end" :offset="{ alignmentAxis: -105, crossAxis: 0, mainAxis: 10 }">
                    <template #button-content><i class="ri-more-2-fill"></i></template>
                    <BDropdownItem>All</BDropdownItem>
                    <BDropdownItem>Last Week</BDropdownItem>
                    <BDropdownItem>Last Month</BDropdownItem>
                    <BDropdownItem>Last Year</BDropdownItem>
                  </BDropdown>
                </div>
              </div>
            </div>
          </BCardHeader>
        </BCard>
      </BCol>
      <BCol xxl="9">
        <BCard no-body id="companyList">
          <BCardHeader>
            <BRow class="g-2">
              <BCol md="3">
                <div class="search-box">
                  <input type="text" class="form-control search" placeholder="Search for company..." v-model="searchQuery" />
                  <i class="ri-search-line search-icon"></i>
                </div>
              </BCol>
              <BCol md="auto" class="ms-auto">
                <div class="d-flex align-items-center gap-2">
                  <span class="text-muted flex-shrink-0">Sort by: </span>

                  <Multiselect class="form-control" style="width:150px;" v-model="value" :close-on-select="true" :searchable="true" :create-option="true" :options="[
                    { value: 'Owner', label: 'Owner' },
                    { value: 'Company', label: 'Company' },
                    { value: 'location', label: 'Location' },
                  ]" />
                </div>
              </BCol>
            </BRow>
          </BCardHeader>
          <BCardBody>
            <div>
              <div class="table-responsive table-card mb-3">
                <table class="table align-middle table-nowrap mb-0" id="customerTable">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" style="width: 50px">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="checkAll" value="option" />
                        </div>
                      </th>
                      <th class="sort" data-sort="name" scope="col" @click="onSort('name')">
                        Company Name
                      </th>
                      <th class="sort" data-sort="owner" scope="col" @click="onSort('owner')">Owner</th>
                      <th class="sort" data-sort="industry_type" scope="col" @click="onSort('industry_type')">
                        Industry Type
                      </th>
                      <th class="sort" data-sort="star_value" scope="col" @click="onSort('star_value')">
                        Rating
                      </th>
                      <th class="sort" data-sort="location" scope="col" @click="onSort('location               ')">
                        Location
                      </th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody class="list form-check-all">
                    <tr v-for="(data, index) of resultQuery" :key="index">
                      <th scope="row">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="chk_child" value="option1" />
                        </div>
                      </th>
                      <td class="id" style="display: none">
                        <BLink href="javascript:void(0);" class="fw-medium link-primary">{{ data.id }}</BLink>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="flex-shrink-0">
                            <img :src="data.img_src" alt="" class="avatar-xxs rounded-circle" />
                          </div>
                          <div class="flex-grow-1 ms-2 name">
                            {{ data.name }}
                          </div>
                        </div>
                      </td>
                      <td class="owner">{{ data.owner }}</td>
                      <td class="industry_type">{{ data.industry_type }}</td>
                      <td>
                        <span class="star_value">{{ data.star_value }}</span>
                        <i class="ri-star-fill text-warning align-bottom"></i>
                      </td>
                      <td class="location">{{ data.location }}</td>
                      <td>
                        <ul class="list-inline hstack gap-2 mb-0">
                          <li class="list-inline-item edit" v-b-tooltip.hover title="Call">
                            <BLink href="javascript:void(0);" class="text-muted d-inline-block">
                              <i class="ri-phone-line fs-16"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item edit" v-b-tooltip.hover title="Message">
                            <BLink href="javascript:void(0);" class="text-muted d-inline-block">
                              <i class="ri-question-answer-line fs-16"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="View" @click="showdetail(data)">
                            <BLink href="javascript:void(0);"><i class="ri-eye-fill align-bottom text-muted"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item" data-bs-trigger="hover" title="Edit" @click="editDetails(data)">
                            <BLink class="edit-item-btn"><i class="ri-pencil-fill align-bottom text-muted"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Delete" @click="deleteModalToggle(data)">
                            <BLink class="remove-item-btn">
                              <i class="ri-delete-bin-fill align-bottom text-muted"></i>
                            </BLink>
                          </li>
                        </ul>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div class="noresult" v-if="resultQuery.length < 1">
                  <div class="text-center">
                    <lottie class="avatar-xl" colors="primary:#121331,secondary:#08a88a" :options="defaultOptions" :height="75" :width="75" />
                    <h5 class="mt-2">Sorry! No Result Found</h5>
                    <p class="text-muted mb-0">
                      We've searched more than 150+ Orders We did not find any
                      orders for you search.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3" v-if="resultQuery.length >= 1">
              <div class="pagination-wrap hstack gap-2">
                <BLink class="page-item pagination-prev" href="#" :disabled="page <= 1" @click="page--">
                  Previous
                </BLink>
                <ul class="pagination listjs-pagination mb-0">
                  <li :class="{ active: pageNumber == page, disabled: pageNumber == '...', }" v-for="(pageNumber, index) in pages" :key="index" @click="page = pageNumber">
                    <BLink class="page" href="#">{{ pageNumber }}</BLink>
                  </li>
                </ul>
                <BLink class="page-item pagination-next" href="#" @click="page++" :disabled="page >= pages.length">
                  Next
                </BLink>
              </div>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol xxl="3">
        <BCard no-body>
          <BCardBody class="text-center">
            <div class="position-relative d-inline-block">
              <div class="avatar-md">
                <div class="avatar-title bg-light rounded-circle material-shadow">
                  <img src="@assets/images/brands/mail_chimp.png" alt="" class="avatar-sm rounded-circle object-fit-cover" id="imageid" />
                </div>
              </div>
            </div>
            <h5 class="mt-3 mb-1" id="cname">Syntyce Solution</h5>
            <p class="text-muted" id="oname">Michael Morris</p>

            <ul class="list-inline mb-0">
              <li class="list-inline-item avatar-xs">
                <BLink href="javascript:void(0);" class="avatar-title bg-success-subtle text-success fs-15 rounded">
                  <i class="ri-global-line"></i>
                </BLink>
              </li>
              <li class="list-inline-item avatar-xs">
                <BLink href="javascript:void(0);" class="avatar-title bg-danger-subtle text-danger fs-15 rounded">
                  <i class="ri-mail-line"></i>
                </BLink>
              </li>
              <li class="list-inline-item avatar-xs">
                <BLink href="javascript:void(0);" class="avatar-title bg-warning-subtle text-warning fs-15 rounded">
                  <i class="ri-question-answer-line"></i>
                </BLink>
              </li>
            </ul>
          </BCardBody>
          <BCardBody>
            <h6 class="text-muted text-uppercase fw-semibold mb-3">
              Information
            </h6>
            <p class="text-muted mb-4">
              A company incurs fixed and variable costs such as the purchase of
              raw materials, salaries and overhead, as explained by
              AccountingTools, Inc. Business owners have the discretion to
              determine the actions.
            </p>
            <div class="table-responsive table-card">
              <table class="table table-borderless mb-0">
                <tbody>
                  <tr>
                    <td class="fw-medium" scope="row">Industry Type</td>
                    <td id="iname">Chemical Industries</td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Location</td>
                    <td id="loc">Damascus, Syria</td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Employee</td>
                    <td id="emp">10-50</td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Rating</td>
                    <td>
                      <span id="rtng">4.0</span> <i class="ri-star-fill text-warning align-bottom"></i>
                    </td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Website</td>
                    <td>
                      <BLink href="javascript:void(0);" class="link-primary text-decoration-underline" id="webs">
                        www.syntycesolution.com</BLink>
                    </td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Contact Email</td>
                    <td id="eml">info@syntycesolution.com</td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Since</td>
                    <td id="sic">1995</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
    <BRow>
      <BCol lg="12">
        <div id="body">
          <div class="content page-container editable-form" id="page-SMS Center" data-page-route="SMS Center" data-state="clean" style="">
            <div class="page-head flex drop-shadow" style="top: -15px;">
              <div class="container">
                <div class="row flex align-center page-head-content justify-between">
                  <div class="col-md-4 col-sm-6 col-xs-7 page-title">
                    <!-- <div class="title-image hide hidden-md hidden-lg"></div> -->     <!-- title -->     
                    <button class="btn-reset sidebar-toggle-btn" title="" aria-label="Toggle Sidebar" data-original-title="Toggle Sidebar">
                      <svg class="es-icon icon-md sidebar-toggle-placeholder">
                        <use href="#es-line-align-justify"></use>
                      </svg>
                      <span class="sidebar-toggle-icon">
                        <svg class="es-icon icon-md">
                          <use href="#es-line-sidebar-collapse">        </use>
                        </svg>
                      </span>
                    </button>
                    <div class="flex fill-width title-area">
                      <div>
                        <div class="flex">
                          <h3 class="ellipsis title-text" title="SMS Center">SMS Center</h3>
                          <span class="indicator-pill no-indicator-dot whitespace-nowrap hide"></span>       
                        </div>
                        <div class="ellipsis sub-heading hide text-muted"></div>
                      </div>
                      <button class="btn btn-default more-button hide">
                        <svg class="icon icon-sm">
                          <use href="#icon-dot-horizontal">        </use>
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="flex col page-actions justify-content-end">
                    <div class="form-viewers d-flex" data-original-title="" title=""></div>
                    <!-- buttons -->     
                    <div class="custom-actions hide hidden-xs hidden-md"></div>
                    <div class="standard-actions flex">
                      <span class="page-icon-group hide hidden-xs hidden-sm"></span>      
                      <div class="menu-btn-group">
                        <button type="button" class="btn btn-default icon-btn" data-toggle="dropdown" aria-expanded="false" aria-label="Menu" title="" data-original-title="Menu">
                          <span>
                            <span class="menu-btn-group-label" data-label="">
                              <svg class="icon icon-sm">
                                <use href="#icon-dot-horizontal">           </use>
                              </svg>
                            </span>
                          </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Jump%20to%20field"><span><span class="alt-underline">J</span>ump to field</span></span>
                            <kbd class="pull-right">
                            <span>⌘+J</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Copy%20to%20Clipboard"><span><span class="alt-underline">C</span>opy to Clipboard</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Reload"><span><span class="alt-underline">R</span>eload</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Remind%20Me"><span>R<span class="alt-underline">e</span>mind Me</span></span>
                            <kbd class="pull-right">
                            <span>⇧+R</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Undo"><span><span class="alt-underline">U</span>ndo</span></span>
                            <kbd class="pull-right">
                            <span>⌘+Z</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Redo"><span>Re<span class="alt-underline">d</span>o</span></span>
                            <kbd class="pull-right">
                            <span>⌘+Y</span>
                            </kbd>
                            </a>
                          </li>
                        </ul>
                      </div>
                      <button class="btn btn-secondary btn-default btn-sm hide"></button>      
                      <div class="actions-btn-group hide">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="dropdown" aria-expanded="false">
                          <span>
                            <span class="hidden-xs actions-btn-group-label" data-label="Actions"><span><span class="alt-underline">A</span>ctions</span></span>         
                            <svg class="icon icon-xs">
                              <use href="#icon-select">          </use>
                            </svg>
                          </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">       </ul>
                      </div>
                      <button class="btn btn-primary btn-sm primary-action" data-label="Save"><span>S<span class="alt-underline">a</span>ve</span></button>     
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="container page-body">
              <div class="page-toolbar hide">
                <div class="container">   </div>
              </div>
              <div class="page-wrapper">
                <div class="page-content">
                  <div class="workflow-button-area btn-group pull-right hide"></div>
                  <div class="clearfix"></div>
                  <div class="row layout-main">
                    <div class="col-lg-2 layout-side-section">
                      <div class="form-sidebar overlay-sidebar hidden-xs hidden-sm">
                        <ul class="list-unstyled sidebar-menu user-actions hidden"></ul>
                        <ul class="list-unstyled sidebar-menu sidebar-image-section hide">
                          <li class="sidebar-image-wrapper">
                            <img class="sidebar-image">   
                            <div class="sidebar-standard-image">
                              <div class="standard-image"></div>
                            </div>
                            <div class="sidebar-image-actions">
                              <div class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Change</a>     
                                <div class="dropdown-menu" role="menu">      <a class="dropdown-item sidebar-image-change">Upload</a>      <a class="dropdown-item sidebar-image-remove">Remove</a>     </div>
                              </div>
                            </div>
                          </li>
                        </ul>
                        <ul class="list-unstyled sidebar-menu sidebar-rating hide">
                          <li style="position: relative;">   <a class="strong badge-hover">    <span>Feedback</span>   </a>  </li>
                          <li class="rating-icons"></li>
                        </ul>
                        <ul class="list-unstyled sidebar-menu hidden">   </ul>
                        <ul class="list-unstyled sidebar-menu form-assignments">
                          <li>
                            <span class="form-sidebar-items">
                              <span class="add-assignment-label">
                                <svg class="es-icon ml-0 icon-sm">
                                  <use href="#es-line-add-people"></use>
                                </svg>
                                <span class="ellipsis">Assigned To</span>    
                              </span>
                              <button class="add-assignment-btn btn btn-link icon-btn">
                                <svg class="es-icon icon-sm">
                                  <use href="#es-line-add"></use>
                                </svg>
                              </button>
                            </span>
                            <div class="assignments" style="display: none;"></div>
                          </li>
                        </ul>
                        <ul class="list-unstyled sidebar-menu form-attachments">
                          <li class="attachments-actions">
                            <span class="form-sidebar-items">
                              <span>
                                <svg class="es-icon ml-0 icon-sm">
                                  <use href="#es-line-attachment"></use>
                                </svg>
                                <a class="pill-label ellipsis explore-link">      Attachments     </a>    
                              </span>
                              <button class="add-attachment-btn btn btn-link icon-btn">
                                <svg class="es-icon icon-sm">
                                  <use href="#es-line-add"></use>
                                </svg>
                              </button>
                            </span>
                          </li>
                          <a class="show-all-btn hidden" href="">
                            <svg class="es-icon icon-sm">
                              <use href="#es-line-preview"></use>
                            </svg>
                            <span class="pill-label ellipsis">    Show All   </span>  
                          </a>
                        </ul>
                        <ul class="list-unstyled sidebar-menu form-tags" style="display: none;">
                          <li>
                            <span class="form-sidebar-items">
                              <span>
                                <svg class="es-icon ml-0 icon-sm">
                                  <use href="#es-line-tag"></use>
                                </svg>
                                <span class="tags-label ellipsis">Tags</span>     
                              </span>
                            </span>
                          </li>
                        </ul>
                        <ul class="list-unstyled sidebar-menu form-shared">
                          <li>
                            <span class="form-sidebar-items">
                              <span class="share-label">
                                <svg class="es-icon ml-0 icon-sm">
                                  <use href="#es-line-add-people"></use>
                                </svg>
                                <span class="ellipsis">Share</span>    
                              </span>
                              <button class="share-doc-btn btn btn-link icon-btn">
                                <svg class="es-icon icon-sm">
                                  <use href="#es-line-add"></use>
                                </svg>
                              </button>
                            </span>
                            <div class="shares" style="display: none;"></div>
                          </li>
                        </ul>
                        <ul class="list-unstyled sidebar-menu followed-by-section">
                          <li class="sidebar-label followed-by-label hidden">
                            <svg class="icon icon-sm">
                              <use href="#icon-link-url" class="like-icon"></use>
                            </svg>
                            Followed by  
                          </li>
                          <li class="followed-by"></li>
                          <li class="document-follow">   <a class="badge-hover follow-document-link hidden">    Follow   </a>   <a class="badge-hover unfollow-document-link hidden">    Unfollow   </a>  </li>
                        </ul>
                        <ul class="list-unstyled sidebar-menu">
                          <a>
                            <li class="auto-repeat-status"></li>
                          </a>
                          <li><a></a> </li>
                        </ul>
                        <ul class="list-unstyled sidebar-menu form-sidebar-stats">
                          <li class="flex">
                            <div class="form-stats d-flex">
                              <span class="form-stats-likes">
                                <span class="liked-by like-action d-flex align-items-center not-liked" data-doctype="SMS Center" data-name="SMS Center">
                                  <svg class="es-icon icon-sm">
                                    <use href="#es-solid-heart" class="like-icon"></use>
                                  </svg>
                                  <span class="like-count ml-2">0</span>     
                                </span>
                              </span>
                              <span class="mx-2">·</span>    
                              <a class="comments d-flex align-items-center">
                                <svg class="es-icon icon-sm">
                                  <use href="#es-line-chat-alt" class="comment-icon"></use>
                                </svg>
                                <span class="comments-count ml-2">0</span>    
                              </a>
                            </div>
                            <a class="form-follow text-sm">Follow</a>  
                          </li>
                        </ul>
                        <hr>
                        <ul class="list-unstyled sidebar-menu text-muted">
                          <li class="pageview-count"></li>
                          <li class="modified-by"><a href="/app/user/Administrator">Administrator</a> last edited this · <span class="frappe-timestamp " data-timestamp="2024-11-23 15:42:27.401076" title="23-11-2024 15:42:27">5 days ago</span></li>
                          <li class="created-by"><a href="/app/user/Administrator">Administrator</a> created this · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col layout-main-section-wrapper">
                      <div class="layout-main-section">
                        <div class="page-form row hide"></div>
                        <div>
                          <div class="std-form-layout">
                            <div class="form-layout">
                              <div class="form-message hidden"></div>
                              <div class="form-page">
                                <div class="form-dashboard">
                                  <div class="row form-dashboard-section progress-area hide-control" data-fieldname="undefined">
                                    <div class="section-body"></div>
                                  </div>
                                  <div class="row form-dashboard-section form-heatmap hide-control" data-fieldname="undefined">
                                    <div class="section-head collapsible">
                                      Activity
                                      <span class="ml-2 collapse-indicator mb-1" style="">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="mb-1" href="#es-line-up"></use>
                                        </svg>
                                      </span>
                                    </div>
                                    <div class="section-body">
                                      <div id="heatmap-sms_center" class="heatmap"></div>
                                      <div class="text-muted small heatmap-message hidden"></div>
                                    </div>
                                  </div>
                                  <div class="row form-dashboard-section form-graph hide-control" data-fieldname="undefined">
                                    <div class="section-head collapsible">
                                      Graph
                                      <span class="ml-2 collapse-indicator mb-1" style="">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="mb-1" href="#es-line-up"></use>
                                        </svg>
                                      </span>
                                    </div>
                                    <div class="section-body"></div>
                                  </div>
                                  <div class="row form-dashboard-section form-stats hide-control" data-fieldname="undefined">
                                    <div class="section-head collapsible">
                                      Stats
                                      <span class="ml-2 collapse-indicator mb-1" style="">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="mb-1" href="#es-line-up"></use>
                                        </svg>
                                      </span>
                                    </div>
                                    <div class="section-body">
                                      <div class="row"></div>
                                    </div>
                                  </div>
                                  <div class="row form-dashboard-section form-links hide-control" data-fieldname="undefined">
                                    <div class="section-head collapsible">
                                      Connections
                                      <span class="ml-2 collapse-indicator mb-1" style="">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="mb-1" href="#es-line-up"></use>
                                        </svg>
                                      </span>
                                    </div>
                                    <div class="section-body">
                                      <div class="transactions"></div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="__section_1">
                                  <div class="section-body">
                                    <div class="form-column col-sm-6" data-fieldname="__column_1">
                                      <form>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="__newname">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;"></label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"></div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">__newname</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="send_to">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Send To</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                                  <option></option>
                                                  <option value="All Contact">All Contact</option>
                                                  <option value="All Customer Contact">All Customer Contact</option>
                                                  <option value="All Supplier Contact">All Supplier Contact</option>
                                                  <option value="All Sales Partner Contact">All Sales Partner Contact</option>
                                                  <option value="All Lead (Open)">All Lead (Open)</option>
                                                  <option value="All Employee (Active)">All Employee (Active)</option>
                                                  <option value="All Sales Person">All Sales Person</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">send_to</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="customer">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Customer</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" data-target="Customer" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_2"></ul>
                                                    <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                                  </div>
                                                  <span class="link-btn">
                                                    <a class="btn-clear no-decoration">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-close-alt"></use>
                                                      </svg>
                                                    </a>
                                                    <a class="btn-open no-decoration" title="Open Link">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-arrow-right"></use>
                                                      </svg>
                                                    </a>
                                                  </span>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">customer</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="supplier">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Supplier</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" data-target="Supplier" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_3" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_3"></ul>
                                                    <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                                  </div>
                                                  <span class="link-btn">
                                                    <a class="btn-clear no-decoration">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-close-alt"></use>
                                                      </svg>
                                                    </a>
                                                    <a class="btn-open no-decoration" title="Open Link">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-arrow-right"></use>
                                                      </svg>
                                                    </a>
                                                  </span>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">supplier</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="sales_partner">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Sales Partner</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" data-target="Sales Partner" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_4" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_4"></ul>
                                                    <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                                  </div>
                                                  <span class="link-btn">
                                                    <a class="btn-clear no-decoration">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-close-alt"></use>
                                                      </svg>
                                                    </a>
                                                    <a class="btn-open no-decoration" title="Open Link">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-arrow-right"></use>
                                                      </svg>
                                                    </a>
                                                  </span>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">sales_partner</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="department">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Department</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" data-target="Department" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_5" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_5"></ul>
                                                    <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                                  </div>
                                                  <span class="link-btn">
                                                    <a class="btn-clear no-decoration">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-close-alt"></use>
                                                      </svg>
                                                    </a>
                                                    <a class="btn-open no-decoration" title="Open Link">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-arrow-right"></use>
                                                      </svg>
                                                    </a>
                                                  </span>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">department</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="branch">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Branch</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" data-target="Branch" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_6" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_6"></ul>
                                                    <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                                  </div>
                                                  <span class="link-btn">
                                                    <a class="btn-clear no-decoration">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-close-alt"></use>
                                                      </svg>
                                                    </a>
                                                    <a class="btn-open no-decoration" title="Open Link">
                                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                        <use class="" href="#icon-arrow-right"></use>
                                                      </svg>
                                                    </a>
                                                  </span>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">branch</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Button" data-fieldname="create_receiver_list">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label hide" style="padding-right: 0px;">&nbsp;</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><button class="btn btn-xs btn-default" data-fieldtype="Button" data-fieldname="create_receiver_list" placeholder="" data-doctype="SMS Center" value="">Create Receiver List</button></div>
                                              <div class="hide" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">create_receiver_list</span>
                                        </div>
                                        <div class="frappe-control" data-fieldtype="Code" data-fieldname="receiver_list">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Receiver List</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="ace-editor-target border rounded ace_editor ace_hidpi ace-tomorrow" style="height: 300px;">
                                                  <textarea class="ace_text-input" wrap="off" autocorrect="off" autocapitalize="off" spellcheck="false" aria-haspopup="false" aria-autocomplete="both" role="textbox" style="opacity: 0; font-size: 1px; height: 1px; width: 1px; transform: translate(44px, 16px);"></textarea>
                                                  <div class="ace_gutter" aria-hidden="true" style="left: 0px; width: 41px;">
                                                    <div class="ace_layer ace_gutter-layer ace_folding-enabled" style="height: 1e+06px; transform: translate(0px, 0px); width: 41px;">
                                                      <div class="ace_gutter-cell ace_gutter-active-line " aria-hidden="true" style="height: 16px; top: 0px;">1<span tabindex="0" style="display: none;"></span><span tabindex="0" style="display: none;"><span></span></span></div>
                                                    </div>
                                                  </div>
                                                  <div class="ace_scroller " style="line-height: 16px; left: 40.4805px; right: 0px; bottom: 0px;">
                                                    <div class="ace_content" style="transform: translate(0px, 0px); width: 444.52px; height: 330px;">
                                                      <div class="ace_layer ace_print-margin-layer">
                                                        <div class="ace_print-margin" style="left: 602px; visibility: hidden;"></div>
                                                      </div>
                                                      <div class="ace_layer ace_marker-layer">
                                                        <div class="ace_active-line" style="height: 16px; top: 0px; left: 0px; right: 0px;"></div>
                                                      </div>
                                                      <div class="ace_layer ace_text-layer" style="height: 1e+06px; margin: 0px 4px; transform: translate(0px, 0px);">
                                                        <div class="ace_line" style="height: 16px; top: 0px;"></div>
                                                      </div>
                                                      <div class="ace_layer ace_marker-layer"></div>
                                                      <div class="ace_layer ace_cursor-layer ace_hidden-cursors">
                                                        <div class="ace_cursor" style="display: block; transform: translate(4px, 0px); width: 7px; height: 16px;"></div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                  <div class="ace_scrollbar ace_scrollbar-v" style="display: none; width: 20px; height: 298px; bottom: 0px;">
                                                    <div class="ace_scrollbar-inner" style="width: 20px; height: 16px;">&nbsp;</div>
                                                  </div>
                                                  <div class="ace_scrollbar ace_scrollbar-h" style="display: none; height: 20px; left: 40.4805px; right: 0px; width: 444.52px;">
                                                    <div class="ace_scrollbar-inner" style="height: 20px; width: 444.52px;">&nbsp;</div>
                                                  </div>
                                                  <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: hidden;">
                                                    <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font: inherit; overflow: visible;">הההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההההה</div>
                                                    <div style="height: auto; width: auto; top: 0px; left: 0px; visibility: hidden; position: absolute; white-space: pre; font-style: inherit; font-variant: inherit; font-stretch: inherit; font-size: inherit; line-height: inherit; font-family: inherit; font-optical-sizing: inherit; font-size-adjust: inherit; font-kerning: inherit; font-feature-settings: inherit; font-variation-settings: inherit; overflow: visible;">XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX</div>
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;">
                                                <pre></pre>
                                              </div>
                                              <p class="help-box small text-muted"></p>
                                              <button class="btn btn-xs btn-default">Expand</button>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">receiver_list</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="column_break9">
                                      <form>
                                        <div class="frappe-control" data-fieldtype="Text" data-fieldname="message">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Message</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control bold" data-fieldtype="Text" data-fieldname="message" placeholder="" data-doctype="SMS Center" style="height: 300px;"></textarea></div>
                                              <div class="control-value like-disabled-input for-description bold" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted">Messages greater than 160 characters will be split into multiple messages</p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">message</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="total_characters">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Total Characters</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input" style="display: none;"></div>
                                              <div class="control-value like-disabled-input" style="">0</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">total_characters</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Int" data-fieldname="total_messages">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Total Message(s)</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input" style="display: none;"></div>
                                              <div class="control-value like-disabled-input" style="">0</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">total_messages</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Button" data-fieldname="send_sms">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label hide" style="padding-right: 0px;">&nbsp;</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><button class="btn btn-xs btn-default" data-fieldtype="Button" data-fieldname="send_sms" placeholder="" data-doctype="SMS Center" value="">Send SMS</button></div>
                                              <div class="hide" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">send_sms</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="layout-footer hide"></div>
                      <div>
                        <div class="form-footer">
                          <div class="after-save">
                            <div class="comment-box">
                              <div class="comment-input-wrapper">
                                <div class="comment-input-header">
                                  <span>Comments</span>
                                </div>
                                <div class="comment-input-container">
                                  <span class="avatar avatar-medium" title="Sai">
                                    <div class="avatar-frame standard-image" style="background-color: var(--blue-avatar-bg); color: var(--blue-avatar-color)" title="Sai">
                                      S
                                    </div>
                                  </span>
                                  <div class="frappe-control col" data-fieldtype="Comment" data-fieldname="comment">
                                    <span class="tooltip-content">comment</span>
                                    <div class="ql-container ql-bubble" style="position: relative;">
                                      <div class="ql-editor ql-blank" data-gramm="false" contenteditable="true" data-placeholder="Type a reply / comment">
                                        <p><br></p>
                                      </div>
                                      <div class="ql-tooltip ql-hidden">
                                        <span class="ql-tooltip-arrow"></span>
                                        <div class="ql-tooltip-editor"><input type="text" data-formula="e=mc^2" data-link="https://quilljs.com" data-video="Embed URL"><a class="ql-close"></a></div>
                                        <div class="ql-toolbar">
                                          <span class="ql-formats">
                                            <button type="button" class="ql-bold">
                                              <svg viewBox="0 0 18 18">
                                                <path class="ql-stroke" d="M5,4H9.5A2.5,2.5,0,0,1,12,6.5v0A2.5,2.5,0,0,1,9.5,9H5A0,0,0,0,1,5,9V4A0,0,0,0,1,5,4Z"></path>
                                                <path class="ql-stroke" d="M5,9h5.5A2.5,2.5,0,0,1,13,11.5v0A2.5,2.5,0,0,1,10.5,14H5a0,0,0,0,1,0,0V9A0,0,0,0,1,5,9Z"></path>
                                              </svg>
                                            </button>
                                            <button type="button" class="ql-italic">
                                              <svg viewBox="0 0 18 18">
                                                <line class="ql-stroke" x1="7" x2="13" y1="4" y2="4"></line>
                                                <line class="ql-stroke" x1="5" x2="11" y1="14" y2="14"></line>
                                                <line class="ql-stroke" x1="8" x2="10" y1="14" y2="4"></line>
                                              </svg>
                                            </button>
                                            <button type="button" class="ql-underline">
                                              <svg viewBox="0 0 18 18">
                                                <path class="ql-stroke" d="M5,3V9a4.012,4.012,0,0,0,4,4H9a4.012,4.012,0,0,0,4-4V3"></path>
                                                <rect class="ql-fill" height="1" rx="0.5" ry="0.5" width="12" x="3" y="15"></rect>
                                              </svg>
                                            </button>
                                            <button type="button" class="ql-strike">
                                              <svg viewBox="0 0 18 18">
                                                <line class="ql-stroke ql-thin" x1="15.5" x2="2.5" y1="8.5" y2="9.5"></line>
                                                <path class="ql-fill" d="M9.007,8C6.542,7.791,6,7.519,6,6.5,6,5.792,7.283,5,9,5c1.571,0,2.765.679,2.969,1.309a1,1,0,0,0,1.9-.617C13.356,4.106,11.354,3,9,3,6.2,3,4,4.538,4,6.5a3.2,3.2,0,0,0,.5,1.843Z"></path>
                                                <path class="ql-fill" d="M8.984,10C11.457,10.208,12,10.479,12,11.5c0,0.708-1.283,1.5-3,1.5-1.571,0-2.765-.679-2.969-1.309a1,1,0,1,0-1.9.617C4.644,13.894,6.646,15,9,15c2.8,0,5-1.538,5-3.5a3.2,3.2,0,0,0-.5-1.843Z"></path>
                                              </svg>
                                            </button>
                                          </span>
                                          <span class="ql-formats">
                                            <button type="button" class="ql-blockquote">
                                              <svg viewBox="0 0 18 18">
                                                <rect class="ql-fill ql-stroke" height="3" width="3" x="4" y="5"></rect>
                                                <rect class="ql-fill ql-stroke" height="3" width="3" x="11" y="5"></rect>
                                                <path class="ql-even ql-fill ql-stroke" d="M7,8c0,4.031-3,5-3,5"></path>
                                                <path class="ql-even ql-fill ql-stroke" d="M14,8c0,4.031-3,5-3,5"></path>
                                              </svg>
                                            </button>
                                            <button type="button" class="ql-code-block">
                                              <svg viewBox="0 0 18 18">
                                                <polyline class="ql-even ql-stroke" points="5 7 3 9 5 11"></polyline>
                                                <polyline class="ql-even ql-stroke" points="13 7 15 9 13 11"></polyline>
                                                <line class="ql-stroke" x1="10" x2="8" y1="5" y2="13"></line>
                                              </svg>
                                            </button>
                                          </span>
                                          <span class="ql-formats">
                                            <button type="button" class="ql-direction" value="rtl">
                                              <svg viewBox="0 0 18 18">
                                                <polygon class="ql-stroke ql-fill" points="3 11 5 9 3 7 3 11"></polygon>
                                                <line class="ql-stroke ql-fill" x1="15" x2="11" y1="4" y2="4"></line>
                                                <path class="ql-fill" d="M11,3a3,3,0,0,0,0,6h1V3H11Z"></path>
                                                <rect class="ql-fill" height="11" width="1" x="11" y="4"></rect>
                                                <rect class="ql-fill" height="11" width="1" x="13" y="4"></rect>
                                              </svg>
                                              <svg viewBox="0 0 18 18">
                                                <polygon class="ql-stroke ql-fill" points="15 12 13 10 15 8 15 12"></polygon>
                                                <line class="ql-stroke ql-fill" x1="9" x2="5" y1="4" y2="4"></line>
                                                <path class="ql-fill" d="M5,3A3,3,0,0,0,5,9H6V3H5Z"></path>
                                                <rect class="ql-fill" height="11" width="1" x="5" y="4"></rect>
                                                <rect class="ql-fill" height="11" width="1" x="7" y="4"></rect>
                                              </svg>
                                            </button>
                                          </span>
                                          <span class="ql-formats">
                                            <button type="button" class="ql-link">
                                              <svg viewBox="0 0 18 18">
                                                <line class="ql-stroke" x1="7" x2="11" y1="7" y2="11"></line>
                                                <path class="ql-even ql-stroke" d="M8.9,4.577a3.476,3.476,0,0,1,.36,4.679A3.476,3.476,0,0,1,4.577,8.9C3.185,7.5,2.035,6.4,4.217,4.217S7.5,3.185,8.9,4.577Z"></path>
                                                <path class="ql-even ql-stroke" d="M13.423,9.1a3.476,3.476,0,0,0-4.679-.36,3.476,3.476,0,0,0,.36,4.679c1.392,1.392,2.5,2.542,4.679.36S14.815,10.5,13.423,9.1Z"></path>
                                              </svg>
                                            </button>
                                            <button type="button" class="ql-image">
                                              <svg viewBox="0 0 18 18">
                                                <rect class="ql-stroke" height="10" width="12" x="3" y="4"></rect>
                                                <circle class="ql-fill" cx="6" cy="7" r="1"></circle>
                                                <polyline class="ql-even ql-fill" points="5 12 5 11 7 9 8 10 11 7 13 9 13 12 5 12"></polyline>
                                              </svg>
                                            </button>
                                          </span>
                                          <span class="ql-formats">
                                            <button type="button" class="ql-list" value="ordered">
                                              <svg viewBox="0 0 18 18">
                                                <line class="ql-stroke" x1="7" x2="15" y1="4" y2="4"></line>
                                                <line class="ql-stroke" x1="7" x2="15" y1="9" y2="9"></line>
                                                <line class="ql-stroke" x1="7" x2="15" y1="14" y2="14"></line>
                                                <line class="ql-stroke ql-thin" x1="2.5" x2="4.5" y1="5.5" y2="5.5"></line>
                                                <path class="ql-fill" d="M3.5,6A0.5,0.5,0,0,1,3,5.5V3.085l-0.276.138A0.5,0.5,0,0,1,2.053,3c-0.124-.247-0.023-0.324.224-0.447l1-.5A0.5,0.5,0,0,1,4,2.5v3A0.5,0.5,0,0,1,3.5,6Z"></path>
                                                <path class="ql-stroke ql-thin" d="M4.5,10.5h-2c0-.234,1.85-1.076,1.85-2.234A0.959,0.959,0,0,0,2.5,8.156"></path>
                                                <path class="ql-stroke ql-thin" d="M2.5,14.846a0.959,0.959,0,0,0,1.85-.109A0.7,0.7,0,0,0,3.75,14a0.688,0.688,0,0,0,.6-0.736,0.959,0.959,0,0,0-1.85-.109"></path>
                                              </svg>
                                            </button>
                                            <button type="button" class="ql-list" value="bullet">
                                              <svg viewBox="0 0 18 18">
                                                <line class="ql-stroke" x1="6" x2="15" y1="4" y2="4"></line>
                                                <line class="ql-stroke" x1="6" x2="15" y1="9" y2="9"></line>
                                                <line class="ql-stroke" x1="6" x2="15" y1="14" y2="14"></line>
                                                <line class="ql-stroke" x1="3" x2="3" y1="4" y2="4"></line>
                                                <line class="ql-stroke" x1="3" x2="3" y1="9" y2="9"></line>
                                                <line class="ql-stroke" x1="3" x2="3" y1="14" y2="14"></line>
                                              </svg>
                                            </button>
                                          </span>
                                          <span class="ql-formats">
                                            <span class="ql-align ql-picker ql-icon-picker">
                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-0">
                                                <svg viewBox="0 0 18 18">
                                                  <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                  <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                  <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                </svg>
                                              </span>
                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-0">
                                                <span tabindex="0" role="button" class="ql-picker-item ql-selected">
                                                  <svg viewBox="0 0 18 18">
                                                    <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                    <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                    <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                  </svg>
                                                </span>
                                                <span tabindex="0" role="button" class="ql-picker-item" data-value="center">
                                                  <svg viewBox="0 0 18 18">
                                                    <line class="ql-stroke" x1="15" x2="3" y1="9" y2="9"></line>
                                                    <line class="ql-stroke" x1="14" x2="4" y1="14" y2="14"></line>
                                                    <line class="ql-stroke" x1="12" x2="6" y1="4" y2="4"></line>
                                                  </svg>
                                                </span>
                                                <span tabindex="0" role="button" class="ql-picker-item" data-value="right">
                                                  <svg viewBox="0 0 18 18">
                                                    <line class="ql-stroke" x1="15" x2="3" y1="9" y2="9"></line>
                                                    <line class="ql-stroke" x1="15" x2="5" y1="14" y2="14"></line>
                                                    <line class="ql-stroke" x1="15" x2="9" y1="4" y2="4"></line>
                                                  </svg>
                                                </span>
                                                <span tabindex="0" role="button" class="ql-picker-item" data-value="justify">
                                                  <svg viewBox="0 0 18 18">
                                                    <line class="ql-stroke" x1="15" x2="3" y1="9" y2="9"></line>
                                                    <line class="ql-stroke" x1="15" x2="3" y1="14" y2="14"></line>
                                                    <line class="ql-stroke" x1="15" x2="3" y1="4" y2="4"></line>
                                                  </svg>
                                                </span>
                                              </span>
                                            </span>
                                            <select class="ql-align" style="display: none;">
                                              <option selected="selected"></option>
                                              <option value="center"></option>
                                              <option value="right"></option>
                                              <option value="justify"></option>
                                            </select>
                                          </span>
                                          <span class="ql-formats">
                                            <button type="button" class="ql-clean">
                                              <svg class="" viewBox="0 0 18 18">
                                                <line class="ql-stroke" x1="5" x2="13" y1="3" y2="3"></line>
                                                <line class="ql-stroke" x1="6" x2="9.35" y1="12" y2="3"></line>
                                                <line class="ql-stroke" x1="11" x2="15" y1="11" y2="15"></line>
                                                <line class="ql-stroke" x1="15" x2="11" y1="11" y2="15"></line>
                                                <rect class="ql-fill" height="1" rx="0.5" ry="0.5" width="7" x="2" y="14"></rect>
                                              </svg>
                                            </button>
                                          </span>
                                        </div>
                                      </div>
                                      <div class="ql-mention-list-container" style="display: none; position: absolute;">
                                        <ul class="ql-mention-list"></ul>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <button class="btn hidden btn-comment btn-xs" style="margin-left:48px;">
                                Comment
                                </button>
                              </div>
                            </div>
                            <div class="new-timeline">
                              <div class="timeline-item activity-title">
                                <h4>Activity</h4>
                                <div class="timeline-items timeline-actions" style="display: none;">
                                  <div class="timeline-item">
                                    <div class="timeline-content action-buttons"></div>
                                  </div>
                                </div>
                              </div>
                              <div class="timeline-items">
                                <div class="timeline-item">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a> created this<span> · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></span></div>
                                </div>
                                <div class="timeline-item" data-timestamp="2024-11-23 15:42:27.401076">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a> last edited this<span> · <span class="frappe-timestamp " data-timestamp="2024-11-23 15:42:27.401076" title="23-11-2024 15:42:27">5 days ago</span></span></div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <button class="scroll-to-top btn btn-default icon-btn" onclick="frappe.utils.scroll_to(0)">
                            <svg class="icon icon-xs">
                              <use href="#icon-up-line"></use>
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </BCol>
    </BRow>
    <BModal v-model="addCompanyModal" id="showmodal" hide-footer title-class="exampleModalLabel" header-class="bg-info-subtle p-3" class="v-modal-custom" centered size="lg" :title="dataEdit ? 'Edit Company' : 'Add Company'">
      <BForm id="addform" class="tablelist-form" autocomplete="off">
        <input type="hidden" id="id">
        <BRow class="g-3">
          <BCol lg="12">
            <div class="text-center">
              <div class="position-relative d-inline-block">
                <div class="position-absolute bottom-0 end-0">
                  <label for="company-logo-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Select Image">
                    <div class="avatar-xs cursor-pointer">
                      <div class="avatar-title bg-light border rounded-circle text-muted">
                        <i class="ri-image-fill"></i>
                      </div>
                    </div>
                  </label>
                  <input class="form-control d-none" value="" id="company-logo-input" type="file" accept="image/png, image/gif, image/jpeg">
                </div>
                <div class="avatar-lg p-1">
                  <div class="avatar-title bg-light rounded-circle">
                    <img :src="event.img_src || multi_user_image" id="companylogo-img" class="avatar-md rounded-circle object-fit-cover">
                  </div>
                </div>
              </div>
              <h5 class="fs-13 mt-3">Company Logo</h5>
            </div>
            <div>
              <label for="companyname" class="form-label">Name</label>
              <input type="text" id="companyname" class="form-control" placeholder="Enter company name" v-model="event.name" :class="{ 'is-invalid': submitted && !event.name }">
              <div class="invalid-feedback">Please enter a company name.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="ownername" class="form-label">Owner Name</label>
              <input type="text" id="ownername" class="form-control" placeholder="Enter owner name" v-model="event.owner" :class="{ 'is-invalid': submitted && !event.owner }">
              <div class="invalid-feedback">Please enter a owner name.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="industrytype" class="form-label">Industry Type</label>
              <select class="form-select" id="industrytype" v-model="event.industry_type" :class="{ 'is-invalid': submitted && !event.industry_type }">
                <option value="">Select industry type</option>
                <option value="Computer Industry">Computer Industry</option>
                <option value="Chemical Industries">Chemical Industries</option>
                <option value="Health Services">Health Services</option>
                <option value="Telecommunications Services">Telecommunications Services</option>
                <option value="Textiles: Clothing, Footwear">Textiles: Clothing, Footwear</option>
              </select>
              <div class="invalid-feedback">Please select a industry type.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="companytype" class="form-label">Company Type</label>
              <select class="form-select" id="companytype" v-model="event.company_type" :class="{ 'is-invalid': submitted && !event.company_type }">
                <option value="">Select company type</option>
                <option value="Supplier">Supplier</option>
                <option value="Buyer">Buyer</option>
              </select>
              <div class="invalid-feedback">Please select a company type.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="starvalue" class="form-label">Rating</label>
              <input type="text" id="starvalue" class="form-control" placeholder="Enter rating" v-model="event.star_value" :class="{ 'is-invalid': submitted && !event.star_value }">
              <div class="invalid-feedback">Please enter a rating.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="location" class="form-label">Location</label>
              <input type="text" id="location" class="form-control" placeholder="Enter location" v-model="event.location" :class="{ 'is-invalid': submitted && !event.location }">
              <div class="invalid-feedback">Please enter a location.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="employee" class="form-label">Employee</label>
              <input type="text" id="employee" class="form-control" placeholder="Enter employee" v-model="event.employee" :class="{ 'is-invalid': submitted && !event.employee }">
              <div class="invalid-feedback">Please enter a employee.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="website" class="form-label">Website</label>
              <input type="text" id="website" class="form-control" placeholder="Enter website" v-model="event.website" :class="{ 'is-invalid': submitted && !event.website }">
              <div class="invalid-feedback">Please enter a website.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="contactemail" class="form-label">Contact Email</label>
              <input type="text" id="contactemail" class="form-control" placeholder="Enter contact email" v-model="event.contact_email" :class="{ 'is-invalid': submitted && !event.contact_email }">
              <div class="invalid-feedback">Please enter a email.</div>
            </div>
          </BCol>
          <BCol lg="4">
            <div>
              <label for="contactphone" class="form-label">Contact Phone</label>
              <input type="text" id="contactphone" class="form-control" placeholder="Enter contact phone" v-model="event.contact_phone" :class="{ 'is-invalid': submitted && !event.contact_phone }">
              <div class="invalid-feedback">Please enter a phone.</div>
            </div>
          </BCol>
          <BCol lg="12">
            <div>
              <label for="since" class="form-label">Since</label>
              <input type="text" id="since" class="form-control" placeholder="Enter since" v-model="event.since" :class="{ 'is-invalid': submitted && !event.since }">
              <div class="invalid-feedback">Please enter a year.</div>
            </div>
          </BCol>
        </BRow>
        <div class="hstack gap-2 justify-content-end mt-3">
          <BButton type="button" variant="light" id="closemodal" @click="addCompanyModal = false">Close</BButton>
          <BButton type="button" variant="success" id="add-btn" @click="handleSubmit">
            {{ dataEdit ? 'Update' : 'Add Company' }}
          </BButton>
        </div>
      </BForm>
    </BModal>

    <!-- delete modal -->
    <BModal v-model="deleteModal" modal-class="zoomIn" hide-footer no-close-on-backdrop centered>
      <div class="mt-2 text-center">
        <lottie class="avatar-xl" colors="primary:#f7b84b,secondary:#f06548" :options="defaultOptions1" :height="100" :width="100" />
        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
          <h4>You are about to delete a contact ?</h4>
          <p class="text-muted mx-4 mb-0">ADeleting your contact will remove all of your information from our database.</p>
        </div>
      </div>
      <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
        <button type="button" class="btn w-sm btn-light material-shadow-none" @click="deleteModal = false">Close</button>
        <button type="button" class="btn w-sm btn-danger" id="delete-record" @click="deleteData">Yes, Delete It!</button>
      </div>
    </BModal>
  </Layout>
</template>