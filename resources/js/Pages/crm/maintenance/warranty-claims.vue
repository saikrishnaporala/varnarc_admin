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

    <Head title="Warranty Claims" />
    <PageHeader title="Warranty Claims" pageTitle="CRM" subTitle="Maintenance"/>
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
          <div class="content page-container" id="page-Workspaces" data-page-route="Workspaces" style="display: none;">
            <div class="page-head flex">
              <div class="container">
                <div class="row flex align-center page-head-content justify-between">
                  <div class="col-md-4 col-sm-6 col-xs-7 page-title">
                    <!-- <div class="title-image hide hidden-md hidden-lg"></div> -->     <!-- title -->     
                    <button class="btn-reset sidebar-toggle-btn" title="Toggle Sidebar" aria-label="Toggle Sidebar" data-original-title="Toggle Sidebar">
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
                          <h3 class="ellipsis title-text" title="CRM">CRM</h3>
                          <span class="indicator-pill whitespace-nowrap"></span>       
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
                    <!-- buttons -->     
                    <div class="custom-actions hide hidden-xs hidden-md"></div>
                    <div class="standard-actions flex">
                      <span class="page-icon-group hide hidden-xs hidden-sm"></span>      
                      <div class="menu-btn-group hide">
                        <button type="button" class="btn btn-default icon-btn" data-toggle="dropdown" aria-expanded="false" aria-label="Menu" title="" data-original-title="Menu">
                          <span>
                            <span class="menu-btn-group-label" data-label="">
                              <svg class="icon icon-sm">
                                <use href="#icon-dot-horizontal">           </use>
                              </svg>
                            </span>
                          </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu"></ul>
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
                      <button class="btn btn-primary btn-sm hide primary-action"></button>     
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
                      <button class="sr-only sr-only-focusable btn btn-primary-light my-2" tabindex="0" role="link">Navigate to main content</button>
                      <div class="list-sidebar overlay-sidebar hidden-xs hidden-sm">
                        <div class="desk-sidebar list-unstyled sidebar-menu">
                          <div class="standard-sidebar-section nested-container hidden" data-title="Personal">
                            <button class="btn-reset standard-sidebar-label" aria-label="Toggle Section: Personal" aria-expanded="true">
                              <span>
                                <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                  <use class="" href="#es-line-down"></use>
                                </svg>
                              </span>
                              <span class="section-title">Personal<span>
                              </span></span>
                            </button>
                          </div>
                          <div class="standard-sidebar-section nested-container" data-title="Public">
                            <button class="btn-reset standard-sidebar-label" aria-label="Toggle Section: Public" aria-expanded="true">
                              <span>
                                <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                  <use class="" href="#es-line-down"></use>
                                </svg>
                              </span>
                              <span class="section-title">Public<span>
                              </span></span>
                            </button>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Home" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/home" class="item-anchor" title="Home">
                                  <span class="sidebar-item-icon" item-icon="getting-started">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-getting-started"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Home<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="ERPNext Settings" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/erpnext-settings" class="item-anchor" title="ERPNext Settings">
                                  <span class="sidebar-item-icon" item-icon="setting">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-setting"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">ERPNext Settings<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="CRM" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item selected">
                                <a href="/app/crm" class="item-anchor" title="CRM">
                                  <span class="sidebar-item-icon" item-icon="crm">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-crm"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">CRM<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Accounting" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/accounting" class="item-anchor" title="Accounting">
                                  <span class="sidebar-item-icon" item-icon="accounting">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-accounting"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Accounting<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon show-in-edit-mode">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container hidden">
                                <div class="sidebar-item-container is-draggable" item-parent="Accounting" item-name="Payables" item-public="1" item-is-hidden="0">
                                  <div class="desk-sidebar-item standard-sidebar-item ">
                                    <a href="/app/payables" class="item-anchor" title="Payables">
                                      <span class="sidebar-item-icon" item-icon="arrow-left">
                                        <svg class="icon  icon-md" style="" aria-hidden="true">
                                          <use class="" href="#icon-arrow-left"></use>
                                        </svg>
                                      </span>
                                      <span class="sidebar-item-label">Payables<span>
                                      </span></span>
                                    </a>
                                    <div class="sidebar-item-control">
                                      <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-drag"></use>
                                        </svg>
                                      </button>
                                      <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-dot-horizontal"></use>
                                        </svg>
                                      </div>
                                      <div class="dropdown-list hidden">
                                        <div class="dropdown-item" title="Edit Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-edit"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Edit</span>
                                        </div>
                                        <div class="dropdown-item" title="Duplicate Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-duplicate"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Duplicate</span>
                                        </div>
                                        <div class="dropdown-item" title="Hide Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-hide"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Hide</span>
                                        </div>
                                      </div>
                                      <button class="btn-reset drop-icon hidden">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-down"></use>
                                        </svg>
                                      </button>
                                    </div>
                                  </div>
                                  <div class="sidebar-child-item nested-container"></div>
                                </div>
                                <div class="sidebar-item-container is-draggable" item-parent="Accounting" item-name="GST India" item-public="1" item-is-hidden="0">
                                  <div class="desk-sidebar-item standard-sidebar-item ">
                                    <a href="/app/gst-india" class="item-anchor" title="GST India">
                                      <span class="sidebar-item-icon" item-icon="expenses">
                                        <svg class="icon  icon-md" style="" aria-hidden="true">
                                          <use class="" href="#icon-expenses"></use>
                                        </svg>
                                      </span>
                                      <span class="sidebar-item-label">GST India<span>
                                      </span></span>
                                    </a>
                                    <div class="sidebar-item-control">
                                      <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-drag"></use>
                                        </svg>
                                      </button>
                                      <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-dot-horizontal"></use>
                                        </svg>
                                      </div>
                                      <div class="dropdown-list hidden">
                                        <div class="dropdown-item" title="Edit Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-edit"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Edit</span>
                                        </div>
                                        <div class="dropdown-item" title="Duplicate Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-duplicate"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Duplicate</span>
                                        </div>
                                        <div class="dropdown-item" title="Hide Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-hide"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Hide</span>
                                        </div>
                                      </div>
                                      <button class="btn-reset drop-icon hidden">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-down"></use>
                                        </svg>
                                      </button>
                                    </div>
                                  </div>
                                  <div class="sidebar-child-item nested-container"></div>
                                </div>
                                <div class="sidebar-item-container is-draggable" item-parent="Accounting" item-name="Receivables" item-public="1" item-is-hidden="0">
                                  <div class="desk-sidebar-item standard-sidebar-item ">
                                    <a href="/app/receivables" class="item-anchor" title="Receivables">
                                      <span class="sidebar-item-icon" item-icon="arrow-right">
                                        <svg class="icon  icon-md" style="" aria-hidden="true">
                                          <use class="" href="#icon-arrow-right"></use>
                                        </svg>
                                      </span>
                                      <span class="sidebar-item-label">Receivables<span>
                                      </span></span>
                                    </a>
                                    <div class="sidebar-item-control">
                                      <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-drag"></use>
                                        </svg>
                                      </button>
                                      <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-dot-horizontal"></use>
                                        </svg>
                                      </div>
                                      <div class="dropdown-list hidden">
                                        <div class="dropdown-item" title="Edit Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-edit"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Edit</span>
                                        </div>
                                        <div class="dropdown-item" title="Duplicate Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-duplicate"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Duplicate</span>
                                        </div>
                                        <div class="dropdown-item" title="Hide Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-hide"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Hide</span>
                                        </div>
                                      </div>
                                      <button class="btn-reset drop-icon hidden">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-down"></use>
                                        </svg>
                                      </button>
                                    </div>
                                  </div>
                                  <div class="sidebar-child-item nested-container"></div>
                                </div>
                                <div class="sidebar-item-container is-draggable" item-parent="Accounting" item-name="Financial Reports" item-public="1" item-is-hidden="0">
                                  <div class="desk-sidebar-item standard-sidebar-item ">
                                    <a href="/app/financial-reports" class="item-anchor" title="Financial Reports">
                                      <span class="sidebar-item-icon" item-icon="file">
                                        <svg class="icon  icon-md" style="" aria-hidden="true">
                                          <use class="" href="#icon-file"></use>
                                        </svg>
                                      </span>
                                      <span class="sidebar-item-label">Financial Reports<span>
                                      </span></span>
                                    </a>
                                    <div class="sidebar-item-control">
                                      <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-drag"></use>
                                        </svg>
                                      </button>
                                      <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-dot-horizontal"></use>
                                        </svg>
                                      </div>
                                      <div class="dropdown-list hidden">
                                        <div class="dropdown-item" title="Edit Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-edit"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Edit</span>
                                        </div>
                                        <div class="dropdown-item" title="Duplicate Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-duplicate"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Duplicate</span>
                                        </div>
                                        <div class="dropdown-item" title="Hide Workspace">
                                          <span class="dropdown-item-icon">
                                            <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                              <use class="" href="#es-line-hide"></use>
                                            </svg>
                                          </span>
                                          <span class="dropdown-item-label">Hide</span>
                                        </div>
                                      </div>
                                      <button class="btn-reset drop-icon hidden">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-down"></use>
                                        </svg>
                                      </button>
                                    </div>
                                  </div>
                                  <div class="sidebar-child-item nested-container"></div>
                                </div>
                              </div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Buying" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/buying" class="item-anchor" title="Buying">
                                  <span class="sidebar-item-icon" item-icon="buying">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-buying"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Buying<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Selling" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/selling" class="item-anchor" title="Selling">
                                  <span class="sidebar-item-icon" item-icon="sell">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-sell"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Selling<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Stock" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/stock" class="item-anchor" title="Stock">
                                  <span class="sidebar-item-icon" item-icon="stock">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-stock"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Stock<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Assets" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/assets" class="item-anchor" title="Assets">
                                  <span class="sidebar-item-icon" item-icon="assets">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-assets"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Assets<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Manufacturing" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/manufacturing" class="item-anchor" title="Manufacturing">
                                  <span class="sidebar-item-icon" item-icon="organization">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-organization"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Manufacturing<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Quality" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/quality" class="item-anchor" title="Quality">
                                  <span class="sidebar-item-icon" item-icon="quality">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-quality"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Quality<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Projects" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/projects" class="item-anchor" title="Projects">
                                  <span class="sidebar-item-icon" item-icon="project">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-project"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Projects<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Support" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/support" class="item-anchor" title="Support">
                                  <span class="sidebar-item-icon" item-icon="support">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-support"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Support<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Users" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/users" class="item-anchor" title="Users">
                                  <span class="sidebar-item-icon" item-icon="users">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-users"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Users<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Website" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/website" class="item-anchor" title="Website">
                                  <span class="sidebar-item-icon" item-icon="website">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-website"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Website<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Tools" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/tools" class="item-anchor" title="Tools">
                                  <span class="sidebar-item-icon" item-icon="tool">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-tool"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Tools<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Integrations" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/integrations" class="item-anchor" title="Integrations">
                                  <span class="sidebar-item-icon" item-icon="integration">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-integration"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Integrations<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="ERPNext Integrations" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/erpnext-integrations" class="item-anchor" title="ERPNext Integrations">
                                  <span class="sidebar-item-icon" item-icon="integration">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-integration"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">ERPNext Integrations<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                            <div class="sidebar-item-container is-draggable" item-parent="" item-name="Build" item-public="1" item-is-hidden="0">
                              <div class="desk-sidebar-item standard-sidebar-item ">
                                <a href="/app/build" class="item-anchor" title="Build">
                                  <span class="sidebar-item-icon" item-icon="tool">
                                    <svg class="icon  icon-md" style="" aria-hidden="true">
                                      <use class="" href="#icon-tool"></use>
                                    </svg>
                                  </span>
                                  <span class="sidebar-item-label">Build<span>
                                  </span></span>
                                </a>
                                <div class="sidebar-item-control">
                                  <button class="btn btn-secondary btn-xs drag-handle" title="Drag">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-drag"></use>
                                    </svg>
                                  </button>
                                  <div class="btn btn-xs setting-btn dropdown-btn" title="Setting">
                                    <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                      <use class="" href="#es-line-dot-horizontal"></use>
                                    </svg>
                                  </div>
                                  <div class="dropdown-list hidden">
                                    <div class="dropdown-item" title="Edit Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-edit"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Edit</span>
                                    </div>
                                    <div class="dropdown-item" title="Duplicate Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-duplicate"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Duplicate</span>
                                    </div>
                                    <div class="dropdown-item" title="Hide Workspace">
                                      <span class="dropdown-item-icon">
                                        <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                          <use class="" href="#es-line-hide"></use>
                                        </svg>
                                      </span>
                                      <span class="dropdown-item-label">Hide</span>
                                    </div>
                                  </div>
                                  <button class="btn-reset drop-icon hidden">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-down"></use>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                              <div class="sidebar-child-item nested-container"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col layout-main-section-wrapper">
                      <div class="flex justify-content-center flex-col px-2" style="
                        background-color: rgb(254 243 199);
                        border-radius: 10px;
                        margin-bottom: 20px;
                        z-index: 1;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle my-auto">
                          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                          <line x1="12" y1="9" x2="12" y2="13"></line>
                          <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <p style="margin: auto 0; margin-right: 20px; padding-left: 10px;">
                          Your trial ends in 10 days. Please subscribe for uninterrupted services
                        </p>
                        <button id="show-dialog" type="button" class="
                          button-renew
                          px-2
                          py-1
                          " onclick="showBanner()" style="
                          margin: auto;
                          height: fit-content;
                          background-color: transparent;
                          border: 1px solid #171717;
                          color: #171717;
                          border-radius: 8px;
                          margin-right: 8px;
                          font-size: 13px;
                          ">
                        Subscribe
                        </button>
                        <a type="button" class="dismiss-upgrade text-muted" data-dismiss="modal" aria-hidden="true" style="font-size:24px;
                          margin-bottom: 5px;
                          margin-right: 5px">×</a>
                      </div>
                      <div class="layout-main-section">
                        <div class="page-form row hide"></div>
                        <div class="editor-js-container">
                          <div id="editorjs" class="desk-page page-main-content">
                            <div class="codex-editor">
                              <div class="codex-editor__redactor" style="padding-bottom: 300px;">
                                <div class="ce-block col-xs-12">
                                  <div class="ce-block__content">
                                    <div chart_name="Territory Wise Sales">
                                      <div class="widget dashboard-widget-box" data-widget-name="j5kecv3eon">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title" title="Territory Wise Sales"><span class="ellipsis" title="Territory Wise Sales">Territory Wise Sales</span></div>
                                            <div class="widget-subtitle">Last synced <span class="frappe-timestamp " data-timestamp="2024-11-29 01:42:19.212545" title="29-11-2024 01:42:19">just now</span></div>
                                          </div>
                                          <div class="widget-control">
                                            <div class="chart-actions dropdown pull-right">
                                              <button data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-xs btn-secondary chart-menu">
                                                <svg class="icon icon-sm">
                                                  <use href="#icon-dot-horizontal">
                                                  </use>
                                                </svg>
                                              </button>
                                              <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a class="dropdown-item" data-action="action-refresh">Refresh</a></li>
                                                <li><a class="dropdown-item" data-action="action-edit">Edit</a></li>
                                                <li><a class="dropdown-item" data-action="action-reset">Reset Chart</a></li>
                                                <li><a class="dropdown-item" data-action="action-list">Opportunity List</a></li>
                                              </ul>
                                            </div>
                                            <div class="filter-chart btn btn-xs pull-right" data-original-title="" title="">
                                              <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                <use class="" href="#icon-filter"></use>
                                              </svg>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="widget-body">
                                          <div class="chart-loading-state text-muted" style="height: 240px; display: none;">Loading...</div>
                                          <div class="chart-loading-state text-muted" style="height: 240px;">No Data</div>
                                          <div style="display: none;"></div>
                                        </div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12">
                                  <div class="ce-block__content">
                                    <div class="widget spacer"></div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12">
                                  <div class="ce-block__content">
                                    <div class="ce-header" data-placeholder=""><span class="h4"><b>Your Shortcuts</b></span></div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                  <div class="ce-block__content">
                                    <div shortcut_name="Lead">
                                      <div class="widget shortcut-widget-box" data-widget-name="2nljlt7mld" role="link" tabindex="0" aria-label="Lead">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Lead">Lead</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control">
                                            <div class="indicator-pill no-indicator-dot ellipsis gray">0 Open</div>
                                            <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#es-line-arrow-up-right"></use>
                                            </svg>
                                          </div>
                                        </div>
                                        <div class="widget-body"></div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                  <div class="ce-block__content">
                                    <div shortcut_name="Opportunity">
                                      <div class="widget shortcut-widget-box" data-widget-name="7ghrkdm94i" role="link" tabindex="0" aria-label="Opportunity">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Opportunity">Opportunity</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control">
                                            <div class="indicator-pill no-indicator-dot ellipsis gray">0 Assigned</div>
                                            <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#es-line-arrow-up-right"></use>
                                            </svg>
                                          </div>
                                        </div>
                                        <div class="widget-body"></div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                  <div class="ce-block__content">
                                    <div shortcut_name="Customer">
                                      <div class="widget shortcut-widget-box" data-widget-name="nn69vvp0df" role="link" tabindex="0" aria-label="Customer">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Customer">Customer</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control">
                                            <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#es-line-arrow-up-right"></use>
                                            </svg>
                                          </div>
                                        </div>
                                        <div class="widget-body"></div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                  <div class="ce-block__content">
                                    <div shortcut_name="Sales Analytics">
                                      <div class="widget shortcut-widget-box" data-widget-name="80paj1dda2" role="link" tabindex="0" aria-label="Sales Analytics">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Sales Analytics">Sales Analytics</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control">
                                            <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#es-line-arrow-up-right"></use>
                                            </svg>
                                          </div>
                                        </div>
                                        <div class="widget-body"></div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4 col-lg-3">
                                  <div class="ce-block__content">
                                    <div shortcut_name="Dashboard">
                                      <div class="widget shortcut-widget-box" data-widget-name="m72ir1lam5" role="link" tabindex="0" aria-label="Dashboard">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Dashboard">Dashboard</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control">
                                            <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                              <use class="" href="#es-line-arrow-up-right"></use>
                                            </svg>
                                          </div>
                                        </div>
                                        <div class="widget-body"></div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12">
                                  <div class="ce-block__content">
                                    <div class="widget spacer"></div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12">
                                  <div class="ce-block__content">
                                    <div class="ce-header" data-placeholder=""><span class="h4"><b>Reports &amp; Masters</b></span></div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                                  <div class="ce-block__content">
                                    <div card_name="Sales Pipeline">
                                      <div class="widget links-widget-box" data-widget-name="qgbf3pqlai">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Sales Pipeline">Sales Pipeline</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control"></div>
                                        </div>
                                        <div class="widget-body">
                                          <a href="/app/lead" class="link-item ellipsis
                                            onboard-spotlight " type="Link" title="Lead">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Lead</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/opportunity" class="link-item ellipsis
                                            onboard-spotlight " type="Link" title="Opportunity">
                                            <span class="link-content ellipsis">
                                              <span class="link-text" data-original-title="" title="">Opportunity</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/customer" class="link-item ellipsis
                                            onboard-spotlight " type="Link" title="Customer">
                                            <span class="link-content ellipsis">
                                              <span class="link-text" data-original-title="" title="">Customer</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/contract" class="link-item ellipsis
                                            " type="Link" title="Contract">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Contract</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/appointment" class="link-item ellipsis
                                            " type="Link" title="Appointment">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Appointment</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/newsletter" class="link-item ellipsis
                                            " type="Link" title="Newsletter">
                                            <span class="link-content ellipsis">
                                              <span class="link-text" data-original-title="" title="">Newsletter</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/communication" class="link-item ellipsis
                                            " type="Link" title="Communication">
                                            <span class="link-content ellipsis">
                                              <span class="link-text" data-original-title="" title="">Communication</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                        </div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                                  <div class="ce-block__content">
                                    <div card_name="Masters">
                                      <div class="widget links-widget-box" data-widget-name="imb9pgves6">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Masters">Masters</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control"></div>
                                        </div>
                                        <div class="widget-body">
                                          <a href="/app/territory" class="link-item ellipsis
                                            " type="Link" title="Territory">
                                            <span class="link-content ellipsis">
                                              <span class="link-text" data-original-title="" title="">Territory</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/customer-group" class="link-item ellipsis
                                            " type="Link" title="Customer Group">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Customer Group</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/contact" class="link-item ellipsis
                                            " type="Link" title="Contact">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Contact</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/prospect" class="link-item ellipsis
                                            " type="Link" title="Prospect">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Prospect</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/sales-person" class="link-item ellipsis
                                            " type="Link" title="Sales Person">
                                            <span class="link-content ellipsis">
                                              <span class="link-text" data-original-title="" title="">Sales Person</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/sales-stage" class="link-item ellipsis
                                            " type="Link" title="Sales Stage">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Sales Stage</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/lead-source" class="link-item ellipsis
                                            " type="Link" title="Lead Source">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Lead Source</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                        </div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                                  <div class="ce-block__content">
                                    <div card_name="Reports">
                                      <div class="widget links-widget-box" data-widget-name="r4s6aootrh">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Reports">Reports</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control"></div>
                                        </div>
                                        <div class="widget-body">
                                          <a href="/app/query-report/Lead Details" class="link-item ellipsis
                                            onboard-spotlight disabled-link" type="Link" title="Lead Details">
                                            <span class="link-content ellipsis disabled-link">Lead Details</span>
                                            <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                              <div class="arrow"></div>
                                              <h3 class="popover-title" style="display: none;"></h3>
                                              <div class="popover-content" style="padding: 12px;">
                                                <div class="small text-muted">You need to create these first: </div>
                                                <div class="small">Lead</div>
                                              </div>
                                            </div>
                                          </a>
                                          <a href="/app/query-report/Sales Pipeline Analytics" class="link-item ellipsis
                                            " type="Link" title="Sales Pipeline Analytics">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Sales Pipeline Analytics</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/query-report/Opportunity Summary by Sales Stage" class="link-item ellipsis
                                            " type="Link" title="Opportunity Summary by Sales Stage">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Opportunity Summary by Sales Stage</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/sales-funnel" class="link-item ellipsis
                                            onboard-spotlight " type="Link" title="Sales Funnel">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Sales Funnel</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/query-report/Prospects Engaged But Not Converted" class="link-item ellipsis
                                            onboard-spotlight disabled-link" type="Link" title="Prospects Engaged But Not Converted">
                                            <span class="link-content ellipsis disabled-link">Prospects Engaged But Not Converted</span>
                                            <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                              <div class="arrow"></div>
                                              <h3 class="popover-title" style="display: none;"></h3>
                                              <div class="popover-content" style="padding: 12px;">
                                                <div class="small text-muted">You need to create these first: </div>
                                                <div class="small">Lead</div>
                                              </div>
                                            </div>
                                          </a>
                                          <a href="/app/query-report/First Response Time for Opportunity" class="link-item ellipsis
                                            disabled-link" type="Link" title="First Response Time for Opportunity">
                                            <span class="link-content ellipsis disabled-link">First Response Time for Opportunity</span>
                                            <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                              <div class="arrow"></div>
                                              <h3 class="popover-title" style="display: none;"></h3>
                                              <div class="popover-content" style="padding: 12px;">
                                                <div class="small text-muted">You need to create these first: </div>
                                                <div class="small">Opportunity</div>
                                              </div>
                                            </div>
                                          </a>
                                          <a href="/app/query-report/Inactive Customers" class="link-item ellipsis
                                            " type="Link" title="Inactive Customers">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Inactive Customers</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/query-report/Campaign Efficiency" class="link-item ellipsis
                                            disabled-link" type="Link" title="Campaign Efficiency">
                                            <span class="link-content ellipsis disabled-link">Campaign Efficiency</span>
                                            <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                              <div class="arrow"></div>
                                              <h3 class="popover-title" style="display: none;"></h3>
                                              <div class="popover-content" style="padding: 12px;">
                                                <div class="small text-muted">You need to create these first: </div>
                                                <div class="small">Lead</div>
                                              </div>
                                            </div>
                                          </a>
                                          <a href="/app/query-report/Lead Owner Efficiency" class="link-item ellipsis
                                            disabled-link" type="Link" title="Lead Owner Efficiency">
                                            <span class="link-content ellipsis disabled-link">Lead Owner Efficiency</span>
                                            <div class="module-link-popover popover fade top in" role="tooltip" style="display: none;">
                                              <div class="arrow"></div>
                                              <h3 class="popover-title" style="display: none;"></h3>
                                              <div class="popover-content" style="padding: 12px;">
                                                <div class="small text-muted">You need to create these first: </div>
                                                <div class="small">Lead</div>
                                              </div>
                                            </div>
                                          </a>
                                        </div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                                  <div class="ce-block__content">
                                    <div card_name="Campaign">
                                      <div class="widget links-widget-box" data-widget-name="gulb5saf79">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Campaign">Campaign</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control"></div>
                                        </div>
                                        <div class="widget-body">
                                          <a href="/app/campaign" class="link-item ellipsis
                                            " type="Link" title="Campaign">
                                            <span class="link-content ellipsis">
                                              <span class="link-text" data-original-title="" title="">Campaign</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/email-campaign" class="link-item ellipsis
                                            " type="Link" title="Email Campaign">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Email Campaign</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/sms-center" class="link-item ellipsis
                                            " type="Link" title="SMS Center">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">SMS Center</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/sms-log" class="link-item ellipsis
                                            " type="Link" title="SMS Log">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">SMS Log</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/email-group" class="link-item ellipsis
                                            " type="Link" title="Email Group">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Email Group</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                        </div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                                  <div class="ce-block__content">
                                    <div card_name="Settings">
                                      <div class="widget links-widget-box" data-widget-name="hcvhd050hr">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Settings">Settings</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control"></div>
                                        </div>
                                        <div class="widget-body">
                                          <a href="/app/crm-settings" class="link-item ellipsis
                                            " type="Link" title="CRM Settings">
                                            <span class="link-content ellipsis">
                                              <span class="link-text" data-original-title="" title="">CRM Settings</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/sms-settings" class="link-item ellipsis
                                            " type="Link" title="SMS Settings">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">SMS Settings</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                        </div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="ce-block col-xs-12 col-sm-6 col-md-4">
                                  <div class="ce-block__content">
                                    <div card_name="Maintenance">
                                      <div class="widget links-widget-box" data-widget-name="ss68s0h23i">
                                        <div class="widget-head">
                                          <div class="widget-label">
                                            <div class="widget-title"><span class="ellipsis" title="Maintenance">Maintenance</span></div>
                                            <div class="widget-subtitle"></div>
                                          </div>
                                          <div class="widget-control"></div>
                                        </div>
                                        <div class="widget-body">
                                          <a href="/app/maintenance-schedule" class="link-item ellipsis
                                            onboard-spotlight " type="Link" title="Maintenance Schedule">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Maintenance Schedule</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/maintenance-visit" class="link-item ellipsis
                                            " type="Link" title="Maintenance Visit">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Maintenance Visit</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                          <a href="/app/warranty-claim" class="link-item ellipsis
                                            " type="Link" title="Warranty Claim">
                                            <span class="link-content ellipsis">
                                              <span class="link-text">Warranty Claim</span>
                                              <svg class="es-icon es-line ml-2 icon-xs" style="" aria-hidden="true">
                                                <use class="" href="#es-line-arrow-up-right"></use>
                                              </svg>
                                            </span>
                                          </a>
                                        </div>
                                        <div class="widget-footer"></div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="codex-editor-overlay">
                                <div class="codex-editor-overlay__container">
                                  <div class="codex-editor-overlay__rectangle" style="display: none;"></div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="workspace-footer">
                          <button data-label="New" class="btn btn-default ellipsis btn-new-workspace">
                            <svg class="es-icon es-line icon-xs" style="" aria-hidden="true">
                              <use class="" href="#es-line-add"></use>
                            </svg>
                            <span class="hidden-xs" data-label="New">New</span>
                          </button>
                          <button class="btn btn-default btn-sm mr-2 btn-edit-workspace" data-label="Edit">
                            <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                              <use class="" href="#es-line-edit"></use>
                            </svg>
                            <span class="hidden-xs" data-label="Edit">Edit</span>
                          </button>
                        </div>
                      </div>
                      <div class="layout-footer hide"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="content page-container" id="page-List/Maintenance Visit/List" data-page-route="List/Maintenance Visit/List" style="display: none;">
            <div class="page-head flex">
              <div class="container">
                <div class="row flex align-center page-head-content justify-between">
                  <div class="col-md-4 col-sm-6 col-xs-7 page-title">
                    <!-- <div class="title-image hide hidden-md hidden-lg"></div> -->     <!-- title -->     
                    <button class="btn-reset sidebar-toggle-btn" title="Toggle Sidebar" aria-label="Toggle Sidebar" data-original-title="Toggle Sidebar">
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
                          <h3 class="ellipsis title-text" title="Maintenance Visit">Maintenance Visit</h3>
                          <span class="indicator-pill whitespace-nowrap"></span>       
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
                    <!-- buttons -->     
                    <div class="custom-actions hidden-xs hidden-md">
                      <div class="custom-btn-group">
                        <button type="button" class="btn btn-default btn-sm ellipsis" data-toggle="dropdown" aria-expanded="false">
                          <span class="hidden-xs">
                            <svg class="icon  icon-sm" style="" aria-hidden="true">
                              <use class="" href="#icon-list"></use>
                            </svg>
                            <span class="custom-btn-group-label">List View</span>
                            <svg class="icon  icon-xs" style="" aria-hidden="true">
                              <use class="" href="#icon-select"></use>
                            </svg>
                          </span>
                          <span class="visible-xs">
                            <svg class="icon  icon-sm" style="" aria-hidden="true">
                              <use class="" href="#icon-list"></use>
                            </svg>
                          </span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <li data-view="Report">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                              <span class="menu-item-icon">
                                <svg class="icon  icon-sm" style="" aria-hidden="true">
                                  <use class="" href="#icon-small-file"></use>
                                </svg>
                              </span>
                              <span class="menu-item-label" data-label="Report%20View"><span><span class="alt-underline">R</span>eport View</span></span>
                            </a>
                          </li>
                          <li data-view="Dashboard">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                              <span class="menu-item-icon">
                                <svg class="icon  icon-sm" style="" aria-hidden="true">
                                  <use class="" href="#icon-dashboard"></use>
                                </svg>
                              </span>
                              <span class="menu-item-label" data-label="Dashboard%20View"><span><span class="alt-underline">D</span>ashboard View</span></span>
                            </a>
                          </li>
                          <li data-view="Kanban">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                              <span class="menu-item-icon">
                                <svg class="icon  icon-sm" style="" aria-hidden="true">
                                  <use class="" href="#icon-kanban"></use>
                                </svg>
                              </span>
                              <span class="menu-item-label" data-label="Kanban%20View"><span><span class="alt-underline">K</span>anban View</span></span>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="standard-actions flex">
                      <span class="page-icon-group hidden-xs hidden-sm">
                        <button class="text-muted btn btn-default  icon-btn" title="" data-original-title="Reload List">
                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                            <use class="" href="#es-line-reload"></use>
                          </svg>
                        </button>
                      </span>
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
                          <li class="user-action">
                            <a class="grey-link dropdown-item visible-xs" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Refresh"><span><span class="alt-underline">R</span>efresh</span></span>
                            </a>
                          </li>
                          <li class="dropdown-divider user-action"></li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="User%20Permissions"><span><span class="alt-underline">U</span>ser Permissions</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Role%20Permissions%20Manager"><span>R<span class="alt-underline">o</span>le Permissions Manager</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Customize"><span><span class="alt-underline">C</span>ustomize</span></span>
                            <kbd class="pull-right">
                            <span>⌘+J</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Toggle%20Sidebar"><span><span class="alt-underline">T</span>oggle Sidebar</span></span>
                            <kbd class="pull-right">
                            <span>⌘+K</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="List%20Settings"><span><span class="alt-underline">L</span>ist Settings</span></span>
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
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Edit"><span><span class="alt-underline">E</span>dit</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Export"><span>E<span class="alt-underline">x</span>port</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Assign%20To"><span><span class="alt-underline">A</span>ssign To</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Clear%20Assignment"><span><span class="alt-underline">C</span>lear Assignment</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Apply%20Assignment%20Rule"><span>A<span class="alt-underline">p</span>ply Assignment Rule</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Add%20Tags"><span>A<span class="alt-underline">d</span>d Tags</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Print"><span>P<span class="alt-underline">r</span>int</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Submit"><span>S<span class="alt-underline">u</span>bmit</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Cancel"><span>Ca<span class="alt-underline">n</span>cel</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Delete"><span>De<span class="alt-underline">l</span>ete</span></span>
                            </a>
                          </li>
                        </ul>
                      </div>
                      <button class="btn btn-primary btn-sm primary-action" data-label="Add Maintenance Visit">
                        <svg class="icon  icon-xs" style="" aria-hidden="true">
                          <use class="" href="#icon-add"></use>
                        </svg>
                        <span class="hidden-xs" data-label="Add%20Maintenance%20Visit"> <span><span class="alt-underline">A</span>dd Maintenance Visit</span> </span>
                      </button>
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
                  <div class="row layout-main" style="">
                    <div class="col-lg-2 layout-side-section">
                      <div class="list-sidebar overlay-sidebar hidden-xs hidden-sm">
                        <ul class="list-unstyled sidebar-menu user-actions hide">
                          <li class="divider"></li>
                        </ul>
                        <ul class="list-unstyled sidebar-menu">
                          <div class="sidebar-section views-section hide">
                            <li class="sidebar-label">   </li>
                            <div class="current-view">
                              <li class="list-link">
                                <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                                  <span class="selected-view ellipsis">      </span>      
                                  <span>
                                    <svg class="icon icon-xs">
                                      <use href="#icon-select"></use>
                                    </svg>
                                  </span>
                                </a>
                                <ul class="dropdown-menu views-dropdown" role="menu">     </ul>
                              </li>
                              <li class="sidebar-action">     <a class="view-action"></a>    </li>
                            </div>
                          </div>
                          <div class="sidebar-section filter-section">
                            <li class="sidebar-label">    Filter By   </li>
                            <div class="list-group-by">
                              <div class="list-group-by-fields">
                                <li class="group-by-field list-link">
                                  <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-label="Assigned To" data-fieldname="assigned_to" data-fieldtype="undefined" href="#" onclick="return false;">
                                    <span class="ellipsis">Assigned To</span>
                                    <span>
                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                        <use class="" href="#icon-select"></use>
                                      </svg>
                                    </span>
                                  </a>
                                  <ul class="dropdown-menu group-by-dropdown" role="menu">
                                  </ul>
                                </li>
                                <li class="group-by-field list-link">
                                  <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-label="Created By" data-fieldname="owner" data-fieldtype="undefined" href="#" onclick="return false;">
                                    <span class="ellipsis">Created By</span>
                                    <span>
                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                        <use class="" href="#icon-select"></use>
                                      </svg>
                                    </span>
                                  </a>
                                  <ul class="dropdown-menu group-by-dropdown" role="menu">
                                  </ul>
                                </li>
                              </div>
                              <li class="add-list-group-by sidebar-action">
                                <a class="add-group-by">
                                Edit Filters
                                </a>
                              </li>
                            </div>
                            <div class="list-tags">
                              <li class="list-stats list-link">
                                <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                                  <span>Tags</span>      
                                  <span>
                                    <svg class="icon icon-xs">
                                      <use href="#icon-select"></use>
                                    </svg>
                                  </span>
                                </a>
                                <ul class="dropdown-menu list-stats-dropdown" role="menu">
                                  <div class="dropdown-search">       <input type="text" placeholder="Search" data-element="search" class="form-control input-xs">      </div>
                                  <div class="stat-result">      </div>
                                </ul>
                              </li>
                              <li class="sidebar-action show-tags">     <a class="list-tag-preview">Show Tags</a>    </li>
                            </div>
                          </div>
                          <div class="sidebar-section save-filter-section">
                            <li class="sidebar-label">    Save Filter   </li>
                            <li class="list-filters list-link">
                            <li class="input-area">
                              <div class="frappe-control input-max-width" data-fieldtype="Data">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;"></label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" placeholder="Filter Name"></div>
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">undefined</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" placeholder=""></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Is Global</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">undefined</span>
                              </div>
                            </li>
                            <li class="sidebar-action">
                              <a class="saved-filters-preview" style="display: none;">Hide Saved</a>
                            </li>
                            <div class="saved-filters" style=""></div>
                            </li> 
                          </div>
                        </ul>
                      </div>
                    </div>
                    <div class="col layout-main-section-wrapper">
                      <div class="layout-main-section frappe-card">
                        <div class="page-form flex">
                          <div class="standard-filter-section flex">
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="name" title="" data-original-title="ID"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="name" placeholder="ID"><span class="tooltip-content">name</span></div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="customer_name" title="" data-original-title="Customer Name"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="customer_name" placeholder="Customer Name"><span class="tooltip-content">customer_name</span></div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Select" data-fieldname="completion_status" title="" data-original-title="Completion Status">
                              <select type="text" autocomplete="off" class="input-with-feedback form-control input-xs ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="completion_status" placeholder="Completion Status">
                                <option></option>
                                <option value="Partially Completed">Partially Completed</option>
                                <option value="Fully Completed">Fully Completed</option>
                              </select>
                              <span class="tooltip-content">completion_status</span>
                              <div class="select-icon xs">
                                <svg class="icon  icon-xs" style="" aria-hidden="true">
                                  <use class="" href="#icon-select"></use>
                                </svg>
                              </div>
                              <div class="placeholder ellipsis text-extra-muted xs">
                                <span>Completion Status</span>
                              </div>
                            </div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Select" data-fieldname="maintenance_type" title="" data-original-title="Maintenance Type">
                              <select type="text" autocomplete="off" class="input-with-feedback form-control input-xs ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="maintenance_type" placeholder="Maintenance Type">
                                <option></option>
                                <option value="Scheduled">Scheduled</option>
                                <option value="Unscheduled">Unscheduled</option>
                                <option value="Breakdown">Breakdown</option>
                              </select>
                              <span class="tooltip-content">maintenance_type</span>
                              <div class="select-icon xs">
                                <svg class="icon  icon-xs" style="" aria-hidden="true">
                                  <use class="" href="#icon-select"></use>
                                </svg>
                              </div>
                              <div class="placeholder ellipsis text-extra-muted xs">
                                <span>Maintenance Type</span>
                              </div>
                            </div>
                          </div>
                          <div class="filter-section flex">
                            <div class="filter-selector">
                              <div class="btn-group">
                                <button class="btn btn-default btn-sm filter-button" data-original-title="" title="">
                                  <span class="filter-icon">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-filter"></use>
                                    </svg>
                                  </span>
                                  <span class="button-label hidden-xs">
                                  Filter
                                  <span>
                                  </span></span>
                                </button>
                                <button class="btn btn-default btn-sm filter-x-button" title="Clear all filters">
                                  <span class="filter-icon">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-small-close"></use>
                                    </svg>
                                  </span>
                                </button>
                              </div>
                            </div>
                            <div class="sort-selector">
                              <div class="btn-group">
                                <button class="btn btn-default btn-sm btn-order" data-value="desc" title="descending">
                                  <span class="sort-order">
                                    <svg class="icon icon-sm">
                                      <use href="#icon-sort-descending"></use>
                                    </svg>
                                  </span>
                                </button>
                                <button type="button" class="btn btn-default btn-sm sort-selector-button" data-toggle="dropdown">
                                  <span class="dropdown-text">Last Updated On</span>    
                                  <ul class="dropdown-menu dropdown-menu-right">
                                    <li>      <a class="dropdown-item option" data-value="modified">       Last Updated On      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="customer_name">       Customer Name      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="name">       ID      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="creation">       Created On      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="idx">       Most Used      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="naming_series">       Series      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="customer">       Customer      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="mntc_date">       Maintenance Date      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="completion_status">       Completion Status      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="maintenance_type">       Maintenance Type      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="status">       Status      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="company">       Company      </a>     </li>
                                  </ul>
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="frappe-list">
                          <div class="result" style="display: none;">
                            <header class="level list-row-head text-muted">
                              <div class="level-left list-header-subject">
                                <div class="list-row-col ellipsis list-subject level  ">
                                  <input class="level-item list-check-all" type="checkbox" title="Select All">
                                  <span class="level-item" data-sort-by="customer_name" title="Click to sort by Customer Name">
                                  Customer Name
                                  </span>
                                </div>
                                <div class="list-row-col ellipsis hidden-xs tag-col hide "><span>Tag</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span>Status</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="completion_status" title="Click to sort by Completion Status">Completion Status</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="maintenance_type" title="Click to sort by Maintenance Type">Maintenance Type</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="name" title="Click to sort by ID">ID</span></div>
                              </div>
                              <div class="level-left checkbox-actions">
                                <div class="level list-subject">
                                  <input class="level-item list-check-all" type="checkbox" title="Select All">
                                  <span class="level-item list-header-meta"></span>
                                </div>
                              </div>
                              <div class="level-right">
                                <span class="list-count"><span>0 of 0</span></span>
                                <span class="level-item list-liked-by-me hidden-xs">
                                  <span title="Liked by me">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="like-icon" href="#es-solid-heart"></use>
                                    </svg>
                                  </span>
                                </span>
                              </div>
                            </header>
                            <div class="freeze flex justify-center align-center text-muted" style="display: none;">
                              Loading...
                            </div>
                          </div>
                          <div class="no-result text-muted flex justify-center align-center" style="">
                            <div class="no-result text-muted flex justify-center align-center">
                              <div class="msg-box no-border">
                                <div>
                                  <img src="/assets/frappe/images/ui-states/list-empty-state.svg" alt="Generic Empty State" class="null-state">
                                </div>
                                <p>You haven't created a Maintenance Visit yet</p>
                                <p><button class="btn btn-default btn-sm btn-new-doc hidden-xs">
                                  Create your first Maintenance Visit
                                  </button> <button class="btn btn-primary btn-new-doc visible-xs">
                                  Create New
                                  </button>
                                </p>
                              </div>
                            </div>
                          </div>
                          <div class="list-paging-area level" style="">
                            <div class="level-left">
                              <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm btn-paging btn-info" data-value="20">
                                20
                                </button>
                                <button type="button" class="btn btn-default btn-sm btn-paging" data-value="100">
                                100
                                </button>
                                <button type="button" class="btn btn-default btn-sm btn-paging" data-value="500">
                                500
                                </button>
                                <button type="button" class="btn btn-default btn-sm btn-paging" data-value="2500">
                                2500
                                </button>
                              </div>
                            </div>
                            <div class="level-right">
                              <button class="btn btn-default btn-more btn-sm" style="display: none;">
                              Load More
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="layout-footer hide"></div>
                    </div>
                  </div>
                  <div class="row list-skeleton" style="display: none;">
                    <div class="col-lg-2">
                      <div class="list-skeleton-box"></div>
                    </div>
                    <div class="col">
                      <div class="list-skeleton-box"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="content page-container editable-form" id="page-Maintenance Visit" data-page-route="Maintenance Visit" style="display: none;" data-state="dirty">
            <div class="page-head flex">
              <div class="container">
                <div class="row flex align-center page-head-content justify-between">
                  <div class="col-md-4 col-sm-6 col-xs-7 page-title">
                    <!-- <div class="title-image hide hidden-md hidden-lg"></div> -->     <!-- title -->     
                    <button class="btn-reset sidebar-toggle-btn" title="Toggle Sidebar" aria-label="Toggle Sidebar" data-original-title="Toggle Sidebar">
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
                          <h3 class="ellipsis title-text" title="New Maintenance Visit">New Maintenance Visit</h3>
                          <span class="indicator-pill no-indicator-dot whitespace-nowrap orange"><span>Not Saved</span></span>       
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
                    <div class="custom-actions hidden-xs hidden-md">
                      <div class="inner-group-button" data-label="Get%20Items%20From">
                        <button type="button" class="btn btn-default ellipsis" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Get Items From
                          <svg class="icon  icon-xs" style="" aria-hidden="true">
                            <use class="" href="#icon-select"></use>
                          </svg>
                        </button>
                        <div role="menu" class="dropdown-menu"><a class="dropdown-item" href="#" onclick="return false;" data-label="Maintenance%20Schedule">Maintenance Schedule</a><a class="dropdown-item" href="#" onclick="return false;" data-label="Warranty%20Claim">Warranty Claim</a><a class="dropdown-item" href="#" onclick="return false;" data-label="Sales%20Order">Sales Order</a></div>
                      </div>
                    </div>
                    <div class="standard-actions flex">
                      <span class="page-icon-group hidden-xs hidden-sm">
                        <button class="text-muted btn btn-default icon-btn hide" title="" data-original-title="Print">
                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                            <use class="" href="#icon-printer"></use>
                          </svg>
                        </button>
                      </span>
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
                          <li class="user-action hidden-xl">
                            <a class="grey-link dropdown-item hidden-xl" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Get%20Items%20From%20%3E%20Maintenance%20Schedule">Get Items From &gt; Maintenance Schedule</span>
                            </a>
                          </li>
                          <li class="user-action hidden-xl">
                            <a class="grey-link dropdown-item hidden-xl" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Get%20Items%20From%20%3E%20Warranty%20Claim">Get Items From &gt; Warranty Claim</span>
                            </a>
                          </li>
                          <li class="user-action hidden-xl">
                            <a class="grey-link dropdown-item hidden-xl" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Get%20Items%20From%20%3E%20Sales%20Order">Get Items From &gt; Sales Order</span>
                            </a>
                          </li>
                          <li class="dropdown-divider user-action"></li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Print"><span><span class="alt-underline">P</span>rint</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Email"><span><span class="alt-underline">E</span>mail</span></span>
                            <kbd class="pull-right">
                            <span>⌘+E</span>
                            </kbd>
                            </a>
                          </li>
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
                            <span class="menu-item-label" data-label="Links"><span><span class="alt-underline">L</span>inks</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Duplicate"><span><span class="alt-underline">D</span>uplicate</span></span>
                            <kbd class="pull-right">
                            <span>⇧+D</span>
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
                            <span class="menu-item-label" data-label="Remind%20Me"><span>Re<span class="alt-underline">m</span>ind Me</span></span>
                            <kbd class="pull-right">
                            <span>⇧+R</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Undo">Undo</span>
                            <kbd class="pull-right">
                            <span>⌘+Z</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Redo">Redo</span>
                            <kbd class="pull-right">
                            <span>⌘+Y</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Customize"><span>Cus<span class="alt-underline">t</span>omize</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="New%20Maintenance%20Visit"><span><span class="alt-underline">N</span>ew Maintenance Visit</span></span>
                            <kbd class="pull-right">
                            <span>⌘+B</span>
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
                        <ul class="dropdown-menu dropdown-menu-right" role="menu"></ul>
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
                    <div class="col-lg-2 layout-side-section hide-sidebar">
                      <div class="form-sidebar overlay-sidebar hidden-xs hidden-sm" style="display: none;">
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
                        <ul class="list-unstyled sidebar-menu form-assignments" style="display: none;">
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
                            <div class="assignments"></div>
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
                        <ul class="list-unstyled sidebar-menu form-tags">
                          <li>
                            <span class="form-sidebar-items">
                              <span>
                                <svg class="es-icon ml-0 icon-sm">
                                  <use href="#es-line-tag"></use>
                                </svg>
                                <span class="tags-label ellipsis">Tags</span>     
                              </span>
                              <button class="add-tags-btn text-muted btn btn-link icon-btn" id="add_tags">
                                <svg class="es-icon icon-sm">
                                  <use href="#es-line-add"></use>
                                </svg>
                              </button>
                            </span>
                          </li>
                          <li class="" style="display: none;">
                            <div class="awesomplete">
                              <input class="tags-input form-control" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_5" role="combobox">
                              <ul hidden="" role="listbox" id="awesomplete_list_5"></ul>
                              <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                            </div>
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
                            <div class="shares"></div>
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
                                <span class="liked-by like-action d-flex align-items-center">
                                  <svg class="es-icon icon-sm">
                                    <use href="#es-solid-heart" class="like-icon"></use>
                                  </svg>
                                  <span class="like-count ml-2"></span>     
                                </span>
                              </span>
                              <span class="mx-2">·</span>    
                              <a class="comments d-flex align-items-center">
                                <svg class="es-icon icon-sm">
                                  <use href="#es-line-chat-alt" class="comment-icon"></use>
                                </svg>
                                <span class="comments-count ml-2"></span>    
                              </a>
                            </div>
                            <a class="form-follow text-sm">    Follow   </a>  
                          </li>
                        </ul>
                        <hr>
                        <ul class="list-unstyled sidebar-menu text-muted">
                          <li class="pageview-count"></li>
                          <li class="modified-by"></li>
                          <li class="created-by"></li>
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
                                      <div id="heatmap-maintenance_visit" class="heatmap"></div>
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
                                <div class="row form-section card-section empty-section" data-fieldname="__section_1">
                                  <div class="section-body">
                                    <div class="form-column col-sm-12" data-fieldname="__column_1">
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
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="customer_details">
                                  <div class="section-body">
                                    <div class="form-column col-sm-6" data-fieldname="column_break0">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="naming_series">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Series</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold" maxlength="140" data-fieldtype="Select" data-fieldname="naming_series" placeholder="" data-doctype="Maintenance Visit">
                                                  <option value="MAT-MVS-.YYYY.-">MAT-MVS-.YYYY.-</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input bold" style="display: none;">MAT-MVS-.YYYY.-</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">naming_series</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="customer">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Customer</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Link" data-fieldname="customer" placeholder="" data-doctype="Maintenance Visit" data-target="Customer" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_6" role="combobox">
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
                                              <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">customer</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="customer_name">
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
                                          <span class="tooltip-content">customer_name</span>
                                        </div>
                                        <div class="frappe-control hide-control" data-fieldtype="Small Text" data-fieldname="address_display">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;"></label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"></div>
                                              <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">address_display</span>
                                        </div>
                                        <div class="frappe-control hide-control" data-fieldtype="Small Text" data-fieldname="contact_display">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;"></label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"></div>
                                              <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">contact_display</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="contact_mobile">
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
                                          <span class="tooltip-content">contact_mobile</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="contact_email">
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
                                          <span class="tooltip-content">contact_email</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="maintenance_schedule">
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
                                          <span class="tooltip-content">maintenance_schedule</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="maintenance_schedule_detail">
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
                                          <span class="tooltip-content">maintenance_schedule_detail</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="column_break1">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Date" data-fieldname="mntc_date">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Maintenance Date</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold" data-fieldtype="Date" data-fieldname="mntc_date" placeholder="" data-doctype="Maintenance Visit"></div>
                                              <div class="control-value like-disabled-input bold" style="display: none;">29-11-2024</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">mntc_date</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Time" data-fieldname="mntc_time">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Maintenance Time</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Time" data-fieldname="mntc_time" placeholder="" data-doctype="Maintenance Visit"></div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">mntc_time</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="maintenance_details">
                                  <div class="section-body">
                                    <div class="form-column col-sm-6" data-fieldname="__column_3">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="completion_status">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Completion Status</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold" maxlength="140" data-fieldtype="Select" data-fieldname="completion_status" placeholder="" data-doctype="Maintenance Visit">
                                                  <option></option>
                                                  <option value="Partially Completed">Partially Completed</option>
                                                  <option value="Fully Completed">Fully Completed</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">completion_status</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="column_break_14">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="maintenance_type">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Maintenance Type</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold" maxlength="140" data-fieldtype="Select" data-fieldname="maintenance_type" placeholder="" data-doctype="Maintenance Visit">
                                                  <option></option>
                                                  <option value="Scheduled">Scheduled</option>
                                                  <option value="Unscheduled">Unscheduled</option>
                                                  <option value="Breakdown">Breakdown</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input bold" style="display: none;">Unscheduled</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">maintenance_type</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="section_break0">
                                  <div class="section-body">
                                    <div class="form-column col-sm-12" data-fieldname="__column_4">
                                      <form>
                                        <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="purposes">
                                          <span class="tooltip-content">purposes</span>
                                          <div class="grid-field">
                                            <label class="control-label">Purposes</label>
                                            <span class="help"></span>
                                            <p class="text-muted small grid-description" style="display: none;"></p>
                                            <div class="grid-custom-buttons"></div>
                                            <div class="form-grid-container">
                                              <div class="form-grid">
                                                <div class="grid-heading-row">
                                                  <div class="grid-row">
                                                    <div class="data-row row">
                                                      <div class="row-check sortable-handle col">
                                                        <input type="checkbox" class="grid-row-check">
                                                      </div>
                                                      <div class="row-index sortable-handle col">
                                                        <span>No.</span>
                                                      </div>
                                                      <div class="col grid-static-col col-xs-2 " data-fieldname="item_code" data-fieldtype="Link" title="Item Code">
                                                        <div class="field-area" style="display: none;"></div>
                                                        <div class="static-area ellipsis">Item Code</div>
                                                      </div>
                                                      <div class="col grid-static-col col-xs-2 " data-fieldname="item_name" data-fieldtype="Data" title="Item Name">
                                                        <div class="field-area" style="display: none;"></div>
                                                        <div class="static-area ellipsis">Item Name</div>
                                                      </div>
                                                      <div class="col grid-static-col col-xs-2 " data-fieldname="service_person" data-fieldtype="Link" title="Sales Person">
                                                        <div class="field-area" style="display: none;"></div>
                                                        <div class="static-area ellipsis reqd">Sales Person</div>
                                                      </div>
                                                      <div class="col grid-static-col col-xs-2 " data-fieldname="description" data-fieldtype="Text Editor" title="Description">
                                                        <div class="field-area" style="display: none;"></div>
                                                        <div class="static-area ellipsis">Description</div>
                                                      </div>
                                                      <div class="col grid-static-col d-flex justify-content-center" style="cursor: pointer;">
                                                        <a>
                                                          <svg class="icon  icon-sm" style="filter: opacity(0.5)" aria-hidden="true">
                                                            <use class="" href="#icon-setting-gear"></use>
                                                          </svg>
                                                        </a>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                                <div class="grid-body">
                                                  <div class="rows"></div>
                                                  <div class="grid-empty text-center">
                                                    <img src="/assets/frappe/images/ui-states/grid-empty-state.svg" alt="Grid Empty State" class="grid-empty-illustration">
                                                    No Data
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="small form-clickable-section grid-footer">
                                              <div class="flex justify-between">
                                                <div class="grid-buttons">
                                                  <button type="button" class="btn btn-xs btn-danger grid-remove-rows hidden" data-action="delete_rows">
                                                  Delete
                                                  </button>
                                                  <button type="button" class="btn btn-xs btn-danger grid-remove-all-rows hidden" data-action="delete_all_rows">
                                                  Delete All
                                                  </button>
                                                  <!-- hack to allow firefox include this in tabs -->
                                                  <button type="button" class="btn btn-xs btn-secondary grid-add-row">
                                                  Add Row
                                                  </button>
                                                  <button type="button" class="grid-add-multiple-rows btn btn-xs btn-secondary hidden" style="display: inline-block;">
                                                  Add Multiple
                                                  </button>
                                                </div>
                                                <div class="grid-pagination"></div>
                                                <div class="grid-bulk-actions text-right">
                                                  <button type="button" class="grid-download btn btn-xs btn-secondary hidden">
                                                  Download
                                                  </button>
                                                  <button type="button" class="grid-upload btn btn-xs btn-secondary hidden" style="display: inline-block;">
                                                  Upload
                                                  </button>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="more_info">
                                  <div class="section-head">
                                    More Information
                                    <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                  </div>
                                  <div class="section-body">
                                    <div class="form-column col-sm-6" data-fieldname="__column_5">
                                      <form>
                                        <div class="frappe-control" data-fieldtype="Small Text" data-fieldname="customer_feedback">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Customer Feedback</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Small Text" data-fieldname="customer_feedback" placeholder="" data-doctype="Maintenance Visit" style="height: 150px;"></textarea></div>
                                              <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">customer_feedback</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="col_break3">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="status">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Status</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input" style="display: none;"></div>
                                              <div class="control-value like-disabled-input bold" style="">Draft</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">status</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="amended_from">
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
                                          <span class="tooltip-content">amended_from</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="company">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Company</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Link" data-fieldname="company" placeholder="" data-doctype="Maintenance Visit" data-target="Company" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_7" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_7"></ul>
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
                                              <div class="control-value like-disabled-input bold" style="display: none;"><a href="/app/company/Porala%20Online%20Services%20(Demo)" data-doctype="Company" data-name="Porala Online Services (Demo)" data-value="Porala Online Services (Demo)">Porala Online Services (Demo)</a></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">company</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section hide-control" data-fieldname="contact_info_section">
                                  <div class="section-head">
                                    Contact Info
                                    <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                  </div>
                                  <div class="section-body">
                                    <div class="form-column col-sm-6" data-fieldname="__column_6">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="customer_address">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Customer Address</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="customer_address" placeholder="" data-doctype="Maintenance Visit" data-target="Address" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_8" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_8"></ul>
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
                                          <span class="tooltip-content">customer_address</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="contact_person">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Contact Person</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="contact_person" placeholder="" data-doctype="Maintenance Visit" data-target="Contact" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_9" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_9"></ul>
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
                                          <span class="tooltip-content">contact_person</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="col_break4">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="territory">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Territory</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="territory" placeholder="" data-doctype="Maintenance Visit" data-target="Territory" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_10" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_10"></ul>
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
                                          <span class="tooltip-content">territory</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="customer_group">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Customer Group</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="customer_group" placeholder="" data-doctype="Maintenance Visit" data-target="Customer Group" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_11" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_11"></ul>
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
                                          <span class="tooltip-content">customer_group</span>
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
                      <div class="hide">
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
                                <div class="timeline-items timeline-actions" style="">
                                  <div class="timeline-item">
                                    <div class="timeline-content action-buttons">
                                      <button class="btn btn-xs btn-secondary action-btn">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-add"></use>
                                        </svg>
                                        New Email
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="timeline-items">
                                <div class="timeline-item">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content ">You created this<span> · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></span></div>
                                </div>
                                <div class="timeline-item">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/user/undefined">Sai</a> last edited this<span> · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></span></div>
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
          <div class="content page-container" id="page-List/Warranty Claim/List" data-page-route="List/Warranty Claim/List" style="">
            <div class="page-head flex">
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
                          <h3 class="ellipsis title-text" title="Warranty Claim">Warranty Claim</h3>
                          <span class="indicator-pill whitespace-nowrap"></span>       
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
                    <!-- buttons -->     
                    <div class="custom-actions hidden-xs hidden-md">
                      <div class="custom-btn-group">
                        <button type="button" class="btn btn-default btn-sm ellipsis" data-toggle="dropdown" aria-expanded="false">
                          <span class="hidden-xs">
                            <svg class="icon  icon-sm" style="" aria-hidden="true">
                              <use class="" href="#icon-list"></use>
                            </svg>
                            <span class="custom-btn-group-label">List View</span>
                            <svg class="icon  icon-xs" style="" aria-hidden="true">
                              <use class="" href="#icon-select"></use>
                            </svg>
                          </span>
                          <span class="visible-xs">
                            <svg class="icon  icon-sm" style="" aria-hidden="true">
                              <use class="" href="#icon-list"></use>
                            </svg>
                          </span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                          <li data-view="Report">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                              <span class="menu-item-icon">
                                <svg class="icon  icon-sm" style="" aria-hidden="true">
                                  <use class="" href="#icon-small-file"></use>
                                </svg>
                              </span>
                              <span class="menu-item-label" data-label="Report%20View"><span><span class="alt-underline">R</span>eport View</span></span>
                            </a>
                          </li>
                          <li data-view="Dashboard">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                              <span class="menu-item-icon">
                                <svg class="icon  icon-sm" style="" aria-hidden="true">
                                  <use class="" href="#icon-dashboard"></use>
                                </svg>
                              </span>
                              <span class="menu-item-label" data-label="Dashboard%20View"><span><span class="alt-underline">D</span>ashboard View</span></span>
                            </a>
                          </li>
                          <li data-view="Kanban">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                              <span class="menu-item-icon">
                                <svg class="icon  icon-sm" style="" aria-hidden="true">
                                  <use class="" href="#icon-kanban"></use>
                                </svg>
                              </span>
                              <span class="menu-item-label" data-label="Kanban%20View"><span><span class="alt-underline">K</span>anban View</span></span>
                            </a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="standard-actions flex">
                      <span class="page-icon-group hidden-xs hidden-sm">
                        <button class="text-muted btn btn-default  icon-btn" title="" data-original-title="Reload List">
                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                            <use class="" href="#es-line-reload"></use>
                          </svg>
                        </button>
                      </span>
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
                          <li class="user-action">
                            <a class="grey-link dropdown-item visible-xs" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Refresh"><span><span class="alt-underline">R</span>efresh</span></span>
                            </a>
                          </li>
                          <li class="dropdown-divider user-action"></li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Import"><span><span class="alt-underline">I</span>mport</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="User%20Permissions"><span><span class="alt-underline">U</span>ser Permissions</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Role%20Permissions%20Manager"><span>R<span class="alt-underline">o</span>le Permissions Manager</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Customize"><span><span class="alt-underline">C</span>ustomize</span></span>
                            <kbd class="pull-right">
                            <span>⌘+J</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Toggle%20Sidebar"><span><span class="alt-underline">T</span>oggle Sidebar</span></span>
                            <kbd class="pull-right">
                            <span>⌘+K</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="List%20Settings"><span><span class="alt-underline">L</span>ist Settings</span></span>
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
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Edit"><span><span class="alt-underline">E</span>dit</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Export"><span>E<span class="alt-underline">x</span>port</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Assign%20To"><span><span class="alt-underline">A</span>ssign To</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Clear%20Assignment"><span><span class="alt-underline">C</span>lear Assignment</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Apply%20Assignment%20Rule"><span>A<span class="alt-underline">p</span>ply Assignment Rule</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Add%20Tags"><span>A<span class="alt-underline">d</span>d Tags</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Print"><span>P<span class="alt-underline">r</span>int</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Delete"><span>De<span class="alt-underline">l</span>ete</span></span>
                            </a>
                          </li>
                        </ul>
                      </div>
                      <button class="btn btn-primary btn-sm primary-action" data-label="Add Warranty Claim">
                        <svg class="icon  icon-xs" style="" aria-hidden="true">
                          <use class="" href="#icon-add"></use>
                        </svg>
                        <span class="hidden-xs" data-label="Add%20Warranty%20Claim"> <span><span class="alt-underline">A</span>dd Warranty Claim</span> </span>
                      </button>
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
                  <div class="row layout-main" style="">
                    <div class="col-lg-2 layout-side-section">
                      <div class="list-sidebar overlay-sidebar hidden-xs hidden-sm">
                        <ul class="list-unstyled sidebar-menu user-actions hide">
                          <li class="divider"></li>
                        </ul>
                        <ul class="list-unstyled sidebar-menu">
                          <div class="sidebar-section views-section hide">
                            <li class="sidebar-label">   </li>
                            <div class="current-view">
                              <li class="list-link">
                                <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                                  <span class="selected-view ellipsis">      </span>      
                                  <span>
                                    <svg class="icon icon-xs">
                                      <use href="#icon-select"></use>
                                    </svg>
                                  </span>
                                </a>
                                <ul class="dropdown-menu views-dropdown" role="menu">     </ul>
                              </li>
                              <li class="sidebar-action">     <a class="view-action"></a>    </li>
                            </div>
                          </div>
                          <div class="sidebar-section filter-section">
                            <li class="sidebar-label">    Filter By   </li>
                            <div class="list-group-by">
                              <div class="list-group-by-fields">
                                <li class="group-by-field list-link">
                                  <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-label="Assigned To" data-fieldname="assigned_to" data-fieldtype="undefined" href="#" onclick="return false;">
                                    <span class="ellipsis">Assigned To</span>
                                    <span>
                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                        <use class="" href="#icon-select"></use>
                                      </svg>
                                    </span>
                                  </a>
                                  <ul class="dropdown-menu group-by-dropdown" role="menu">
                                  </ul>
                                </li>
                                <li class="group-by-field list-link">
                                  <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-label="Created By" data-fieldname="owner" data-fieldtype="undefined" href="#" onclick="return false;">
                                    <span class="ellipsis">Created By</span>
                                    <span>
                                      <svg class="icon  icon-xs" style="" aria-hidden="true">
                                        <use class="" href="#icon-select"></use>
                                      </svg>
                                    </span>
                                  </a>
                                  <ul class="dropdown-menu group-by-dropdown" role="menu">
                                  </ul>
                                </li>
                              </div>
                              <li class="add-list-group-by sidebar-action">
                                <a class="add-group-by">
                                Edit Filters
                                </a>
                              </li>
                            </div>
                            <div class="list-tags">
                              <li class="list-stats list-link">
                                <a class="btn btn-default btn-sm list-sidebar-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                                  <span>Tags</span>      
                                  <span>
                                    <svg class="icon icon-xs">
                                      <use href="#icon-select"></use>
                                    </svg>
                                  </span>
                                </a>
                                <ul class="dropdown-menu list-stats-dropdown" role="menu">
                                  <div class="dropdown-search">       <input type="text" placeholder="Search" data-element="search" class="form-control input-xs">      </div>
                                  <div class="stat-result">      </div>
                                </ul>
                              </li>
                              <li class="sidebar-action show-tags">     <a class="list-tag-preview">Show Tags</a>    </li>
                            </div>
                          </div>
                          <div class="sidebar-section save-filter-section">
                            <li class="sidebar-label">    Save Filter   </li>
                            <li class="list-filters list-link">
                            <li class="input-area">
                              <div class="frappe-control input-max-width" data-fieldtype="Data">
                                <div class="form-group">
                                  <div class="clearfix">
                                    <label class="control-label" style="padding-right: 0px;"></label>
                                    <span class="help"></span>
                                  </div>
                                  <div class="control-input-wrapper">
                                    <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" placeholder="Filter Name"></div>
                                    <div class="control-value like-disabled-input" style="display: none;"></div>
                                    <p class="help-box small text-muted"></p>
                                  </div>
                                </div>
                                <span class="tooltip-content">undefined</span>
                              </div>
                              <div class="form-group frappe-control input-max-width hide-control" data-fieldtype="Check">
                                <div class="checkbox">
                                  <label>
                                  <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" placeholder=""></span>
                                  <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                  <span class="label-area">Is Global</span>
                                  <span class="ml-1 help"></span>
                                  </label>
                                  <p class="help-box small text-muted"></p>
                                </div>
                                <span class="tooltip-content">undefined</span>
                              </div>
                            </li>
                            <li class="sidebar-action">
                              <a class="saved-filters-preview" style="display: none;">Hide Saved</a>
                            </li>
                            <div class="saved-filters" style=""></div>
                            </li> 
                          </div>
                        </ul>
                      </div>
                    </div>
                    <div class="col layout-main-section-wrapper">
                      <div class="layout-main-section frappe-card">
                        <div class="page-form flex">
                          <div class="standard-filter-section flex">
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="name" title="" data-original-title="ID"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="name" placeholder="ID"><span class="tooltip-content">name</span></div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Select" data-fieldname="status" title="" data-original-title="Status">
                              <select type="text" autocomplete="off" class="input-with-feedback form-control input-xs ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="status" placeholder="Status">
                                <option></option>
                                <option value="Open">Open</option>
                                <option value="Closed">Closed</option>
                                <option value="Work In Progress">Work In Progress</option>
                                <option value="Cancelled">Cancelled</option>
                              </select>
                              <span class="tooltip-content">status</span>
                              <div class="select-icon xs">
                                <svg class="icon  icon-xs" style="" aria-hidden="true">
                                  <use class="" href="#icon-select"></use>
                                </svg>
                              </div>
                              <div class="placeholder ellipsis text-extra-muted xs" style="display: none;">
                                <span>Status</span>
                              </div>
                            </div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Link" data-fieldname="customer" title="" data-original-title="Customer">
                              <div class="link-field ui-front" style="position: relative;">
                                <div class="awesomplete">
                                  <input type="text" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Link" data-fieldname="customer" placeholder="Customer" data-target="Customer" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_15" role="combobox">
                                  <ul hidden="" role="listbox" id="awesomplete_list_15"></ul>
                                  <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                </div>
                              </div>
                              <span class="tooltip-content">customer</span>
                            </div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Link" data-fieldname="item_code" title="" data-original-title="Item Code">
                              <div class="link-field ui-front" style="position: relative;">
                                <div class="awesomplete">
                                  <input type="text" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Link" data-fieldname="item_code" placeholder="Item Code" data-target="Item" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_16" role="combobox">
                                  <ul hidden="" role="listbox" id="awesomplete_list_16"></ul>
                                  <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                </div>
                              </div>
                              <span class="tooltip-content">item_code</span>
                            </div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="customer_name" title="" data-original-title="Customer Name"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="customer_name" placeholder="Customer Name"><span class="tooltip-content">customer_name</span></div>
                          </div>
                          <div class="filter-section flex">
                            <div class="filter-selector">
                              <div class="btn-group">
                                <button class="btn btn-default btn-sm filter-button" data-original-title="" title="0 Filter Applied">
                                  <span class="filter-icon">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-line-filter"></use>
                                    </svg>
                                  </span>
                                  <span class="button-label hidden-xs">Filter</span>
                                </button>
                                <button class="btn btn-default btn-sm filter-x-button" title="Clear all filters">
                                  <span class="filter-icon">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="" href="#es-small-close"></use>
                                    </svg>
                                  </span>
                                </button>
                              </div>
                            </div>
                            <div class="sort-selector">
                              <div class="btn-group">
                                <button class="btn btn-default btn-sm btn-order" data-value="desc" title="descending">
                                  <span class="sort-order">
                                    <svg class="icon icon-sm">
                                      <use href="#icon-sort-descending"></use>
                                    </svg>
                                  </span>
                                </button>
                                <button type="button" class="btn btn-default btn-sm sort-selector-button" data-toggle="dropdown">
                                  <span class="dropdown-text">Last Updated On</span>    
                                  <ul class="dropdown-menu dropdown-menu-right">
                                    <li>      <a class="dropdown-item option" data-value="modified">       Last Updated On      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="customer_name">       Customer Name      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="name">       ID      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="creation">       Created On      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="idx">       Most Used      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="naming_series">       Series      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="status">       Status      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="complaint_date">       Issue Date      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="customer">       Customer      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="complaint">       Issue      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="item_code">       Item Code      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="company">       Company      </a>     </li>
                                  </ul>
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="frappe-list">
                          <div class="result" style="display: none;">
                            <header class="level list-row-head text-muted">
                              <div class="level-left list-header-subject">
                                <div class="list-row-col ellipsis list-subject level  ">
                                  <input class="level-item list-check-all" type="checkbox" title="Select All">
                                  <span class="level-item" data-sort-by="customer_name" title="Click to sort by Customer Name">
                                  Customer Name
                                  </span>
                                </div>
                                <div class="list-row-col ellipsis hidden-xs tag-col hide "><span>Tag</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="status" title="Click to sort by Status">Status</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="item_code" title="Click to sort by Item Code">Item Code</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="name" title="Click to sort by ID">ID</span></div>
                              </div>
                              <div class="level-left checkbox-actions">
                                <div class="level list-subject">
                                  <input class="level-item list-check-all" type="checkbox" title="Select All">
                                  <span class="level-item list-header-meta"></span>
                                </div>
                              </div>
                              <div class="level-right">
                                <span class="list-count"><span>0 of 0</span></span>
                                <span class="level-item list-liked-by-me hidden-xs">
                                  <span title="Liked by me">
                                    <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                      <use class="like-icon" href="#es-solid-heart"></use>
                                    </svg>
                                  </span>
                                </span>
                              </div>
                            </header>
                            <div class="freeze flex justify-center align-center text-muted" style="display: none;">
                              Loading...
                            </div>
                          </div>
                          <div class="no-result text-muted flex justify-center align-center" style="">
                            <div class="no-result text-muted flex justify-center align-center">
                              <div class="msg-box no-border">
                                <div>
                                  <img src="/assets/frappe/images/ui-states/list-empty-state.svg" alt="Generic Empty State" class="null-state">
                                </div>
                                <p>No Warranty Claim found with matching filters. Clear filters to see all Warranty Claim.</p>
                                <p><button class="btn btn-default btn-sm btn-new-doc hidden-xs">
                                  Create a new Warranty Claim
                                  </button> <button class="btn btn-primary btn-new-doc visible-xs">
                                  Create New
                                  </button>
                                </p>
                              </div>
                            </div>
                          </div>
                          <div class="list-paging-area level" style="">
                            <div class="level-left">
                              <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm btn-paging btn-info" data-value="20">
                                20
                                </button>
                                <button type="button" class="btn btn-default btn-sm btn-paging" data-value="100">
                                100
                                </button>
                                <button type="button" class="btn btn-default btn-sm btn-paging" data-value="500">
                                500
                                </button>
                                <button type="button" class="btn btn-default btn-sm btn-paging" data-value="2500">
                                2500
                                </button>
                              </div>
                            </div>
                            <div class="level-right">
                              <button class="btn btn-default btn-more btn-sm" style="display: none;">
                              Load More
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="layout-footer hide"></div>
                    </div>
                  </div>
                  <div class="row list-skeleton" style="display: none;">
                    <div class="col-lg-2">
                      <div class="list-skeleton-box"></div>
                    </div>
                    <div class="col">
                      <div class="list-skeleton-box"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="content page-container editable-form" id="page-Warranty Claim" data-page-route="Warranty Claim" style="" data-state="dirty">
            <div class="page-head flex">
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
                          <h3 class="ellipsis title-text" title="New Warranty Claim">New Warranty Claim</h3>
                          <span class="indicator-pill no-indicator-dot whitespace-nowrap orange"><span>Not Saved</span></span>       
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
                      <span class="page-icon-group hidden-xs hidden-sm">
                        <button class="text-muted btn btn-default icon-btn hide" title="" data-original-title="Print">
                          <svg class="icon  icon-sm" style="" aria-hidden="true">
                            <use class="" href="#icon-printer"></use>
                          </svg>
                        </button>
                      </span>
                      <div class="menu-btn-group hide">
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
                            <span class="menu-item-label" data-label="Print"><span><span class="alt-underline">P</span>rint</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Email"><span><span class="alt-underline">E</span>mail</span></span>
                            <kbd class="pull-right">
                            <span>⌘+E</span>
                            </kbd>
                            </a>
                          </li>
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
                            <span class="menu-item-label" data-label="Links"><span><span class="alt-underline">L</span>inks</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Duplicate"><span><span class="alt-underline">D</span>uplicate</span></span>
                            <kbd class="pull-right">
                            <span>⇧+D</span>
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
                            <span class="menu-item-label" data-label="Remind%20Me"><span>Re<span class="alt-underline">m</span>ind Me</span></span>
                            <kbd class="pull-right">
                            <span>⇧+R</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Undo">Undo</span>
                            <kbd class="pull-right">
                            <span>⌘+Z</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Redo">Redo</span>
                            <kbd class="pull-right">
                            <span>⌘+Y</span>
                            </kbd>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Customize"><span>Cus<span class="alt-underline">t</span>omize</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="New%20Warranty%20Claim"><span><span class="alt-underline">N</span>ew Warranty Claim</span></span>
                            <kbd class="pull-right">
                            <span>⌘+B</span>
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
                        <ul class="dropdown-menu dropdown-menu-right" role="menu"></ul>
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
                    <div class="col-lg-2 layout-side-section hide-sidebar">
                      <div class="form-sidebar overlay-sidebar hidden-xs hidden-sm" style="display: none;">
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
                        <ul class="list-unstyled sidebar-menu form-assignments" style="display: none;">
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
                            <div class="assignments"></div>
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
                        <ul class="list-unstyled sidebar-menu form-tags">
                          <li>
                            <span class="form-sidebar-items">
                              <span>
                                <svg class="es-icon ml-0 icon-sm">
                                  <use href="#es-line-tag"></use>
                                </svg>
                                <span class="tags-label ellipsis">Tags</span>     
                              </span>
                              <button class="add-tags-btn text-muted btn btn-link icon-btn" id="add_tags">
                                <svg class="es-icon icon-sm">
                                  <use href="#es-line-add"></use>
                                </svg>
                              </button>
                            </span>
                          </li>
                          <li class="" style="display: none;">
                            <div class="awesomplete">
                              <input class="tags-input form-control" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_17" role="combobox">
                              <ul hidden="" role="listbox" id="awesomplete_list_17"></ul>
                              <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                            </div>
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
                            <div class="shares"></div>
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
                                <span class="liked-by like-action d-flex align-items-center">
                                  <svg class="es-icon icon-sm">
                                    <use href="#es-solid-heart" class="like-icon"></use>
                                  </svg>
                                  <span class="like-count ml-2"></span>     
                                </span>
                              </span>
                              <span class="mx-2">·</span>    
                              <a class="comments d-flex align-items-center">
                                <svg class="es-icon icon-sm">
                                  <use href="#es-line-chat-alt" class="comment-icon"></use>
                                </svg>
                                <span class="comments-count ml-2"></span>    
                              </a>
                            </div>
                            <a class="form-follow text-sm">    Follow   </a>  
                          </li>
                        </ul>
                        <hr>
                        <ul class="list-unstyled sidebar-menu text-muted">
                          <li class="pageview-count"></li>
                          <li class="modified-by"></li>
                          <li class="created-by"></li>
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
                                      <div id="heatmap-warranty_claim" class="heatmap"></div>
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
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="naming_series">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Series</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold" maxlength="140" data-fieldtype="Select" data-fieldname="naming_series" placeholder="" data-doctype="Warranty Claim">
                                                  <option value="SER-WRN-.YYYY.-">SER-WRN-.YYYY.-</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input bold" style="display: none;">SER-WRN-.YYYY.-</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">naming_series</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="status">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Status</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold" maxlength="140" data-fieldtype="Select" data-fieldname="status" placeholder="" data-doctype="Warranty Claim">
                                                  <option></option>
                                                  <option value="Open">Open</option>
                                                  <option value="Closed">Closed</option>
                                                  <option value="Work In Progress">Work In Progress</option>
                                                  <option value="Cancelled">Cancelled</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input bold" style="display: none;">Open</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">status</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Date" data-fieldname="complaint_date">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Issue Date</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold" data-fieldtype="Date" data-fieldname="complaint_date" placeholder="" data-doctype="Warranty Claim"></div>
                                              <div class="control-value like-disabled-input bold" style="display: none;">29-11-2024</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">complaint_date</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="column_break0">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="customer">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Customer</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Link" data-fieldname="customer" placeholder="" data-doctype="Warranty Claim" data-target="Customer" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_18" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_18"></ul>
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
                                              <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">customer</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="serial_no">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Serial No</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="serial_no" placeholder="" data-doctype="Warranty Claim" data-target="Serial No" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_19" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_19"></ul>
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
                                          <span class="tooltip-content">serial_no</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="section_break_7">
                                  <div class="section-body">
                                    <div class="form-column col-sm-12" data-fieldname="__column_2">
                                      <form>
                                        <div class="frappe-control" data-fieldtype="Text Editor" data-fieldname="complaint">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Issue</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="ql-toolbar ql-snow">
                                                  <span class="ql-formats">
                                                    <span class="ql-header ql-picker">
                                                      <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-2">
                                                        <svg viewBox="0 0 18 18">
                                                          <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                          <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                        </svg>
                                                      </span>
                                                      <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-2"><span tabindex="0" role="button" class="ql-picker-item" data-value="1"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="2"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="3"></span><span tabindex="0" role="button" class="ql-picker-item ql-selected"></span></span>
                                                    </span>
                                                    <select class="ql-header" style="display: none;">
                                                      <option value="1"></option>
                                                      <option value="2"></option>
                                                      <option value="3"></option>
                                                      <option selected="selected"></option>
                                                    </select>
                                                  </span>
                                                  <span class="ql-formats">
                                                    <span class="ql-size ql-picker">
                                                      <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-3" data-value="---">
                                                        <svg viewBox="0 0 18 18">
                                                          <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                          <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                        </svg>
                                                      </span>
                                                      <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-3"><span tabindex="0" role="button" class="ql-picker-item ql-selected" data-value="---"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="8px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="9px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="10px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="11px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="12px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="13px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="14px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="15px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="16px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="18px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="20px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="22px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="24px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="32px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="36px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="40px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="48px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="54px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="64px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="96px"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="128px"></span></span>
                                                    </span>
                                                    <select class="ql-size" style="display: none;">
                                                      <option selected="selected"></option>
                                                      <option value="8px"></option>
                                                      <option value="9px"></option>
                                                      <option value="10px"></option>
                                                      <option value="11px"></option>
                                                      <option value="12px"></option>
                                                      <option value="13px"></option>
                                                      <option value="14px"></option>
                                                      <option value="15px"></option>
                                                      <option value="16px"></option>
                                                      <option value="18px"></option>
                                                      <option value="20px"></option>
                                                      <option value="22px"></option>
                                                      <option value="24px"></option>
                                                      <option value="32px"></option>
                                                      <option value="36px"></option>
                                                      <option value="40px"></option>
                                                      <option value="48px"></option>
                                                      <option value="54px"></option>
                                                      <option value="64px"></option>
                                                      <option value="96px"></option>
                                                      <option value="128px"></option>
                                                    </select>
                                                  </span>
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
                                                  <span class="ql-formats">
                                                    <span class="ql-color ql-picker ql-color-picker">
                                                      <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-4">
                                                        <svg viewBox="0 0 18 18">
                                                          <line class="ql-color-label ql-stroke ql-transparent" x1="3" x2="15" y1="15" y2="15"></line>
                                                          <polyline class="ql-stroke" points="5.5 11 9 3 12.5 11"></polyline>
                                                          <line class="ql-stroke" x1="11.63" x2="6.38" y1="9" y2="9"></line>
                                                        </svg>
                                                      </span>
                                                      <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-4"><span tabindex="0" role="button" class="ql-picker-item ql-selected ql-primary"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#e60000" style="background-color: rgb(230, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ff9900" style="background-color: rgb(255, 153, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ffff00" style="background-color: rgb(255, 255, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#008a00" style="background-color: rgb(0, 138, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#0066cc" style="background-color: rgb(0, 102, 204);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#9933ff" style="background-color: rgb(153, 51, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffffff" style="background-color: rgb(255, 255, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#facccc" style="background-color: rgb(250, 204, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffebcc" style="background-color: rgb(255, 235, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffffcc" style="background-color: rgb(255, 255, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce8cc" style="background-color: rgb(204, 232, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce0f5" style="background-color: rgb(204, 224, 245);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ebd6ff" style="background-color: rgb(235, 214, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#bbbbbb" style="background-color: rgb(187, 187, 187);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#f06666" style="background-color: rgb(240, 102, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffc266" style="background-color: rgb(255, 194, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffff66" style="background-color: rgb(255, 255, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66b966" style="background-color: rgb(102, 185, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66a3e0" style="background-color: rgb(102, 163, 224);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#c285ff" style="background-color: rgb(194, 133, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#888888" style="background-color: rgb(136, 136, 136);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#a10000" style="background-color: rgb(161, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b26b00" style="background-color: rgb(178, 107, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b2b200" style="background-color: rgb(178, 178, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#006100" style="background-color: rgb(0, 97, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#0047b2" style="background-color: rgb(0, 71, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#6b24b2" style="background-color: rgb(107, 36, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#444444" style="background-color: rgb(68, 68, 68);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#5c0000" style="background-color: rgb(92, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#663d00" style="background-color: rgb(102, 61, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#666600" style="background-color: rgb(102, 102, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#003700" style="background-color: rgb(0, 55, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#002966" style="background-color: rgb(0, 41, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#3d1466" style="background-color: rgb(61, 20, 102);"></span></span>
                                                    </span>
                                                    <select class="ql-color" style="display: none;">
                                                      <option selected="selected"></option>
                                                      <option value="#e60000"></option>
                                                      <option value="#ff9900"></option>
                                                      <option value="#ffff00"></option>
                                                      <option value="#008a00"></option>
                                                      <option value="#0066cc"></option>
                                                      <option value="#9933ff"></option>
                                                      <option value="#ffffff"></option>
                                                      <option value="#facccc"></option>
                                                      <option value="#ffebcc"></option>
                                                      <option value="#ffffcc"></option>
                                                      <option value="#cce8cc"></option>
                                                      <option value="#cce0f5"></option>
                                                      <option value="#ebd6ff"></option>
                                                      <option value="#bbbbbb"></option>
                                                      <option value="#f06666"></option>
                                                      <option value="#ffc266"></option>
                                                      <option value="#ffff66"></option>
                                                      <option value="#66b966"></option>
                                                      <option value="#66a3e0"></option>
                                                      <option value="#c285ff"></option>
                                                      <option value="#888888"></option>
                                                      <option value="#a10000"></option>
                                                      <option value="#b26b00"></option>
                                                      <option value="#b2b200"></option>
                                                      <option value="#006100"></option>
                                                      <option value="#0047b2"></option>
                                                      <option value="#6b24b2"></option>
                                                      <option value="#444444"></option>
                                                      <option value="#5c0000"></option>
                                                      <option value="#663d00"></option>
                                                      <option value="#666600"></option>
                                                      <option value="#003700"></option>
                                                      <option value="#002966"></option>
                                                      <option value="#3d1466"></option>
                                                    </select>
                                                    <span class="ql-background ql-picker ql-color-picker">
                                                      <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-5">
                                                        <svg viewBox="0 0 18 18">
                                                          <g class="ql-fill ql-color-label">
                                                            <polygon points="6 6.868 6 6 5 6 5 7 5.942 7 6 6.868"></polygon>
                                                            <rect height="1" width="1" x="4" y="4"></rect>
                                                            <polygon points="6.817 5 6 5 6 6 6.38 6 6.817 5"></polygon>
                                                            <rect height="1" width="1" x="2" y="6"></rect>
                                                            <rect height="1" width="1" x="3" y="5"></rect>
                                                            <rect height="1" width="1" x="4" y="7"></rect>
                                                            <polygon points="4 11.439 4 11 3 11 3 12 3.755 12 4 11.439"></polygon>
                                                            <rect height="1" width="1" x="2" y="12"></rect>
                                                            <rect height="1" width="1" x="2" y="9"></rect>
                                                            <rect height="1" width="1" x="2" y="15"></rect>
                                                            <polygon points="4.63 10 4 10 4 11 4.192 11 4.63 10"></polygon>
                                                            <rect height="1" width="1" x="3" y="8"></rect>
                                                            <path d="M10.832,4.2L11,4.582V4H10.708A1.948,1.948,0,0,1,10.832,4.2Z"></path>
                                                            <path d="M7,4.582L7.168,4.2A1.929,1.929,0,0,1,7.292,4H7V4.582Z"></path>
                                                            <path d="M8,13H7.683l-0.351.8a1.933,1.933,0,0,1-.124.2H8V13Z"></path>
                                                            <rect height="1" width="1" x="12" y="2"></rect>
                                                            <rect height="1" width="1" x="11" y="3"></rect>
                                                            <path d="M9,3H8V3.282A1.985,1.985,0,0,1,9,3Z"></path>
                                                            <rect height="1" width="1" x="2" y="3"></rect>
                                                            <rect height="1" width="1" x="6" y="2"></rect>
                                                            <rect height="1" width="1" x="3" y="2"></rect>
                                                            <rect height="1" width="1" x="5" y="3"></rect>
                                                            <rect height="1" width="1" x="9" y="2"></rect>
                                                            <rect height="1" width="1" x="15" y="14"></rect>
                                                            <polygon points="13.447 10.174 13.469 10.225 13.472 10.232 13.808 11 14 11 14 10 13.37 10 13.447 10.174"></polygon>
                                                            <rect height="1" width="1" x="13" y="7"></rect>
                                                            <rect height="1" width="1" x="15" y="5"></rect>
                                                            <rect height="1" width="1" x="14" y="6"></rect>
                                                            <rect height="1" width="1" x="15" y="8"></rect>
                                                            <rect height="1" width="1" x="14" y="9"></rect>
                                                            <path d="M3.775,14H3v1H4V14.314A1.97,1.97,0,0,1,3.775,14Z"></path>
                                                            <rect height="1" width="1" x="14" y="3"></rect>
                                                            <polygon points="12 6.868 12 6 11.62 6 12 6.868"></polygon>
                                                            <rect height="1" width="1" x="15" y="2"></rect>
                                                            <rect height="1" width="1" x="12" y="5"></rect>
                                                            <rect height="1" width="1" x="13" y="4"></rect>
                                                            <polygon points="12.933 9 13 9 13 8 12.495 8 12.933 9"></polygon>
                                                            <rect height="1" width="1" x="9" y="14"></rect>
                                                            <rect height="1" width="1" x="8" y="15"></rect>
                                                            <path d="M6,14.926V15H7V14.316A1.993,1.993,0,0,1,6,14.926Z"></path>
                                                            <rect height="1" width="1" x="5" y="15"></rect>
                                                            <path d="M10.668,13.8L10.317,13H10v1h0.792A1.947,1.947,0,0,1,10.668,13.8Z"></path>
                                                            <rect height="1" width="1" x="11" y="15"></rect>
                                                            <path d="M14.332,12.2a1.99,1.99,0,0,1,.166.8H15V12H14.245Z"></path>
                                                            <rect height="1" width="1" x="14" y="15"></rect>
                                                            <rect height="1" width="1" x="15" y="11"></rect>
                                                          </g>
                                                          <polyline class="ql-stroke" points="5.5 13 9 5 12.5 13"></polyline>
                                                          <line class="ql-stroke" x1="11.63" x2="6.38" y1="11" y2="11"></line>
                                                        </svg>
                                                      </span>
                                                      <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-5"><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#000000" style="background-color: rgb(0, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#e60000" style="background-color: rgb(230, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ff9900" style="background-color: rgb(255, 153, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#ffff00" style="background-color: rgb(255, 255, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#008a00" style="background-color: rgb(0, 138, 0);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#0066cc" style="background-color: rgb(0, 102, 204);"></span><span tabindex="0" role="button" class="ql-picker-item ql-primary" data-value="#9933ff" style="background-color: rgb(153, 51, 255);"></span><span tabindex="0" role="button" class="ql-picker-item ql-selected"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#facccc" style="background-color: rgb(250, 204, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffebcc" style="background-color: rgb(255, 235, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffffcc" style="background-color: rgb(255, 255, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce8cc" style="background-color: rgb(204, 232, 204);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#cce0f5" style="background-color: rgb(204, 224, 245);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ebd6ff" style="background-color: rgb(235, 214, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#bbbbbb" style="background-color: rgb(187, 187, 187);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#f06666" style="background-color: rgb(240, 102, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffc266" style="background-color: rgb(255, 194, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#ffff66" style="background-color: rgb(255, 255, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66b966" style="background-color: rgb(102, 185, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#66a3e0" style="background-color: rgb(102, 163, 224);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#c285ff" style="background-color: rgb(194, 133, 255);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#888888" style="background-color: rgb(136, 136, 136);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#a10000" style="background-color: rgb(161, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b26b00" style="background-color: rgb(178, 107, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#b2b200" style="background-color: rgb(178, 178, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#006100" style="background-color: rgb(0, 97, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#0047b2" style="background-color: rgb(0, 71, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#6b24b2" style="background-color: rgb(107, 36, 178);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#444444" style="background-color: rgb(68, 68, 68);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#5c0000" style="background-color: rgb(92, 0, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#663d00" style="background-color: rgb(102, 61, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#666600" style="background-color: rgb(102, 102, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#003700" style="background-color: rgb(0, 55, 0);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#002966" style="background-color: rgb(0, 41, 102);"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="#3d1466" style="background-color: rgb(61, 20, 102);"></span></span>
                                                    </span>
                                                    <select class="ql-background" style="display: none;">
                                                      <option value="#000000"></option>
                                                      <option value="#e60000"></option>
                                                      <option value="#ff9900"></option>
                                                      <option value="#ffff00"></option>
                                                      <option value="#008a00"></option>
                                                      <option value="#0066cc"></option>
                                                      <option value="#9933ff"></option>
                                                      <option selected="selected"></option>
                                                      <option value="#facccc"></option>
                                                      <option value="#ffebcc"></option>
                                                      <option value="#ffffcc"></option>
                                                      <option value="#cce8cc"></option>
                                                      <option value="#cce0f5"></option>
                                                      <option value="#ebd6ff"></option>
                                                      <option value="#bbbbbb"></option>
                                                      <option value="#f06666"></option>
                                                      <option value="#ffc266"></option>
                                                      <option value="#ffff66"></option>
                                                      <option value="#66b966"></option>
                                                      <option value="#66a3e0"></option>
                                                      <option value="#c285ff"></option>
                                                      <option value="#888888"></option>
                                                      <option value="#a10000"></option>
                                                      <option value="#b26b00"></option>
                                                      <option value="#b2b200"></option>
                                                      <option value="#006100"></option>
                                                      <option value="#0047b2"></option>
                                                      <option value="#6b24b2"></option>
                                                      <option value="#444444"></option>
                                                      <option value="#5c0000"></option>
                                                      <option value="#663d00"></option>
                                                      <option value="#666600"></option>
                                                      <option value="#003700"></option>
                                                      <option value="#002966"></option>
                                                      <option value="#3d1466"></option>
                                                    </select>
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
                                                    <button type="button" class="ql-list" value="check">
                                                      <svg class="" viewBox="0 0 18 18">
                                                        <line class="ql-stroke" x1="9" x2="15" y1="4" y2="4"></line>
                                                        <polyline class="ql-stroke" points="3 4 4 5 6 3"></polyline>
                                                        <line class="ql-stroke" x1="9" x2="15" y1="14" y2="14"></line>
                                                        <polyline class="ql-stroke" points="3 14 4 15 6 13"></polyline>
                                                        <line class="ql-stroke" x1="9" x2="15" y1="9" y2="9"></line>
                                                        <polyline class="ql-stroke" points="3 9 4 10 6 8"></polyline>
                                                      </svg>
                                                    </button>
                                                  </span>
                                                  <span class="ql-formats">
                                                    <span class="ql-align ql-picker ql-icon-picker">
                                                      <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-6">
                                                        <svg viewBox="0 0 18 18">
                                                          <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                          <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                          <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                        </svg>
                                                      </span>
                                                      <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-6">
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
                                                    <button type="button" class="ql-indent" value="-1">
                                                      <svg viewBox="0 0 18 18">
                                                        <line class="ql-stroke" x1="3" x2="15" y1="14" y2="14"></line>
                                                        <line class="ql-stroke" x1="3" x2="15" y1="4" y2="4"></line>
                                                        <line class="ql-stroke" x1="9" x2="15" y1="9" y2="9"></line>
                                                        <polyline class="ql-stroke" points="5 7 5 11 3 9 5 7"></polyline>
                                                      </svg>
                                                    </button>
                                                    <button type="button" class="ql-indent" value="+1">
                                                      <svg viewBox="0 0 18 18">
                                                        <line class="ql-stroke" x1="3" x2="15" y1="14" y2="14"></line>
                                                        <line class="ql-stroke" x1="3" x2="15" y1="4" y2="4"></line>
                                                        <line class="ql-stroke" x1="9" x2="15" y1="9" y2="9"></line>
                                                        <polyline class="ql-fill ql-stroke" points="3 7 3 11 5 9 3 7"></polyline>
                                                      </svg>
                                                    </button>
                                                  </span>
                                                  <span class="ql-formats">
                                                    <span class="ql-table ql-picker">
                                                      <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-7" data-value="insert-table">
                                                        <svg viewBox="0 0 18 18">
                                                          <polygon class="ql-stroke" points="7 11 9 13 11 11 7 11"></polygon>
                                                          <polygon class="ql-stroke" points="7 7 9 5 11 7 7 7"></polygon>
                                                        </svg>
                                                      </span>
                                                      <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-7"><span tabindex="0" role="button" class="ql-picker-item ql-selected" data-value="insert-table"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-row-above"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-row-below"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-column-right"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="insert-column-left"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="delete-row"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="delete-column"></span><span tabindex="0" role="button" class="ql-picker-item" data-value="delete-table"></span></span>
                                                    </span>
                                                    <select class="ql-table" style="display: none;">
                                                      <option value="insert-table"></option>
                                                      <option value="insert-row-above"></option>
                                                      <option value="insert-row-below"></option>
                                                      <option value="insert-column-right"></option>
                                                      <option value="insert-column-left"></option>
                                                      <option value="delete-row"></option>
                                                      <option value="delete-column"></option>
                                                      <option value="delete-table"></option>
                                                    </select>
                                                  </span>
                                                </div>
                                                <div class="ql-container ql-snow" style="position: relative;">
                                                  <div class="ql-editor ql-blank" data-gramm="false" contenteditable="true">
                                                    <p><br></p>
                                                  </div>
                                                  <div class="ql-tooltip ql-hidden"><a class="ql-preview" rel="noopener noreferrer" target="_blank" href="about:blank"></a><input type="text" data-formula="e=mc^2" data-link="https://quilljs.com" data-video="Embed URL"><a class="ql-action"></a><a class="ql-remove"></a></div>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input for-description bold" style="display: none; white-space-collapse: preserve;">
                                                <div class="ql-editor read-mode"></div>
                                              </div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">complaint</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="issue_details">
                                  <div class="section-head">
                                    Item and Warranty Details
                                    <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                  </div>
                                  <div class="section-body">
                                    <div class="form-column col-sm-6" data-fieldname="__column_3">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="item_code">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Item Code</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="item_code" placeholder="" data-doctype="Warranty Claim" data-target="Item" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_20" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_20"></ul>
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
                                          <span class="tooltip-content">item_code</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="item_name">
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
                                          <span class="tooltip-content">item_name</span>
                                        </div>
                                        <div class="frappe-control hide-control" data-fieldtype="Small Text" data-fieldname="description">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;"></label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"></div>
                                              <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">description</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="column_break1">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="warranty_amc_status">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Warranty / AMC Status</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="warranty_amc_status" placeholder="" data-doctype="Warranty Claim">
                                                  <option></option>
                                                  <option value="Under Warranty">Under Warranty</option>
                                                  <option value="Out of Warranty">Out of Warranty</option>
                                                  <option value="Under AMC">Under AMC</option>
                                                  <option value="Out of AMC">Out of AMC</option>
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
                                          <span class="tooltip-content">warranty_amc_status</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Date" data-fieldname="warranty_expiry_date">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Warranty Expiry Date</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Date" data-fieldname="warranty_expiry_date" placeholder="" data-doctype="Warranty Claim"></div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">warranty_expiry_date</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Date" data-fieldname="amc_expiry_date">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">AMC Expiry Date</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Date" data-fieldname="amc_expiry_date" placeholder="" data-doctype="Warranty Claim"></div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">amc_expiry_date</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="resolution_section">
                                  <div class="section-head">
                                    Resolution
                                    <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                  </div>
                                  <div class="section-body">
                                    <div class="form-column col-sm-12" data-fieldname="__column_4">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Datetime" data-fieldname="resolution_date">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Resolution Date</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Datetime" data-fieldname="resolution_date" placeholder="" data-doctype="Warranty Claim"></div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted">Asia/Kolkata</p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">resolution_date</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="resolved_by">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Resolved By</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="resolved_by" placeholder="" data-doctype="Warranty Claim" data-target="User" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_21" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_21"></ul>
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
                                          <span class="tooltip-content">resolved_by</span>
                                        </div>
                                        <div class="frappe-control" data-fieldtype="Text" data-fieldname="resolution_details">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Resolution Details</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Text" data-fieldname="resolution_details" placeholder="" data-doctype="Warranty Claim" style="height: 300px;"></textarea></div>
                                              <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">resolution_details</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section hide-control" data-fieldname="contact_info">
                                  <div class="section-head collapsible collapsed">
                                    Customer Details
                                    <span class="ml-2 collapse-indicator mb-1" style="">
                                      <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                        <use class="mb-1" href="#es-line-down"></use>
                                      </svg>
                                    </span>
                                  </div>
                                  <div class="section-body hide">
                                    <div class="form-column col-sm-6" data-fieldname="__column_5">
                                      <form>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="customer_name">
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
                                          <span class="tooltip-content">customer_name</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="contact_person">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Contact Person</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="contact_person" placeholder="" data-doctype="Warranty Claim" data-target="Contact" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_22" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_22"></ul>
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
                                          <span class="tooltip-content">contact_person</span>
                                        </div>
                                        <div class="frappe-control hide-control" data-fieldtype="Small Text" data-fieldname="contact_display">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;"></label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"></div>
                                              <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">contact_display</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="contact_mobile">
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
                                          <span class="tooltip-content">contact_mobile</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="contact_email">
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
                                          <span class="tooltip-content">contact_email</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="territory">
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
                                          <span class="tooltip-content">territory</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="customer_group">
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
                                          <span class="tooltip-content">customer_group</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="col_break4">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="customer_address">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Customer Address</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="customer_address" placeholder="" data-doctype="Warranty Claim" data-target="Address" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_23" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_23"></ul>
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
                                          <span class="tooltip-content">customer_address</span>
                                        </div>
                                        <div class="frappe-control hide-control" data-fieldtype="Small Text" data-fieldname="address_display">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;"></label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"></div>
                                              <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">address_display</span>
                                        </div>
                                        <div class="frappe-control hide-control" data-fieldtype="Small Text" data-fieldname="service_address">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Service Address</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Small Text" data-fieldname="service_address" placeholder="" data-doctype="Warranty Claim" style="height: 150px;"></textarea></div>
                                              <div class="control-value like-disabled-input for-description" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted">If different than customer address</p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">service_address</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="more_info">
                                  <div class="section-head collapsible collapsed">
                                    More Information
                                    <span class="ml-2 collapse-indicator mb-1" style="">
                                      <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                        <use class="mb-1" href="#es-line-down"></use>
                                      </svg>
                                    </span>
                                  </div>
                                  <div class="section-body hide">
                                    <div class="form-column col-sm-6" data-fieldname="__column_6">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="company">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Company</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Link" data-fieldname="company" placeholder="" data-doctype="Warranty Claim" data-target="Company" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_24" role="combobox">
                                                    <ul hidden="" role="listbox" id="awesomplete_list_24"></ul>
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
                                              <div class="control-value like-disabled-input bold" style="display: none;"><a href="/app/company/Porala%20Online%20Services%20(Demo)" data-doctype="Company" data-name="Porala Online Services (Demo)" data-value="Porala Online Services (Demo)">Porala Online Services (Demo)</a></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">company</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="col_break6">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="complaint_raised_by">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Raised By</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="complaint_raised_by" placeholder="" data-doctype="Warranty Claim"></div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">complaint_raised_by</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="from_company">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">From Company</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="from_company" placeholder="" data-doctype="Warranty Claim"></div>
                                              <div class="control-value like-disabled-input" style="display: none;"></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">from_company</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="amended_from">
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
                                          <span class="tooltip-content">amended_from</span>
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
                      <div class="hide">
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
                                              <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-1">
                                                <svg viewBox="0 0 18 18">
                                                  <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                  <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                  <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                </svg>
                                              </span>
                                              <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-1">
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
                                <div class="timeline-items timeline-actions" style="">
                                  <div class="timeline-item">
                                    <div class="timeline-content action-buttons">
                                      <button class="btn btn-xs btn-secondary action-btn">
                                        <svg class="es-icon es-line  icon-xs" style="" aria-hidden="true">
                                          <use class="" href="#es-line-add"></use>
                                        </svg>
                                        New Email
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="timeline-items">
                                <div class="timeline-item">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content ">You created this<span> · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></span></div>
                                </div>
                                <div class="timeline-item">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/user/undefined">Sai</a> last edited this<span> · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></span></div>
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