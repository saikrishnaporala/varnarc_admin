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

    <Head title="Communication" />
    <PageHeader title="Communication" pageTitle="CRM" subTitle="Sales"/>
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
          <div class="content page-container" id="page-List/Communication/List" data-page-route="List/Communication/List" style="">
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
                          <h3 class="ellipsis title-text" title="" data-original-title="Keeps track of all communications">Communication</h3>
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
                          <li class="user-action">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Mark%20as%20Read"><span><span class="alt-underline">M</span>ark as Read</span></span>
                            </a>
                          </li>
                          <li class="user-action">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Mark%20as%20Unread"><span>M<span class="alt-underline">a</span>rk as Unread</span></span>
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
                      <button class="btn btn-primary btn-sm primary-action" data-label="Add Communication">
                        <svg class="icon  icon-xs" style="" aria-hidden="true">
                          <use class="" href="#icon-add"></use>
                        </svg>
                        <span class="hidden-xs" data-label="Add%20Communication"> <span><span class="alt-underline">A</span>dd Communication</span> </span>
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
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="subject" title="" data-original-title="Subject"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="subject" placeholder="Subject"><span class="tooltip-content">subject</span></div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Select" data-fieldname="comment_type" title="" data-original-title="Comment Type">
                              <select type="text" autocomplete="off" class="input-with-feedback form-control input-xs ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="comment_type" placeholder="Comment Type">
                                <option></option>
                                <option value="Comment">Comment</option>
                                <option value="Like">Like</option>
                                <option value="Info">Info</option>
                                <option value="Label">Label</option>
                                <option value="Workflow">Workflow</option>
                                <option value="Created">Created</option>
                                <option value="Submitted">Submitted</option>
                                <option value="Cancelled">Cancelled</option>
                                <option value="Updated">Updated</option>
                                <option value="Deleted">Deleted</option>
                                <option value="Assigned">Assigned</option>
                                <option value="Assignment Completed">Assignment Completed</option>
                                <option value="Attachment">Attachment</option>
                                <option value="Attachment Removed">Attachment Removed</option>
                                <option value="Shared">Shared</option>
                                <option value="Unshared">Unshared</option>
                                <option value="Relinked">Relinked</option>
                              </select>
                              <span class="tooltip-content">comment_type</span>
                              <div class="select-icon xs">
                                <svg class="icon  icon-xs" style="" aria-hidden="true">
                                  <use class="" href="#icon-select"></use>
                                </svg>
                              </div>
                              <div class="placeholder ellipsis text-extra-muted xs">
                                <span>Comment Type</span>
                              </div>
                            </div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Select" data-fieldname="status" title="" data-original-title="Status">
                              <select type="text" autocomplete="off" class="input-with-feedback form-control input-xs ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="status" placeholder="Status">
                                <option></option>
                                <option value="Open">Open</option>
                                <option value="Replied">Replied</option>
                                <option value="Closed">Closed</option>
                                <option value="Linked">Linked</option>
                              </select>
                              <span class="tooltip-content">status</span>
                              <div class="select-icon xs">
                                <svg class="icon  icon-xs" style="" aria-hidden="true">
                                  <use class="" href="#icon-select"></use>
                                </svg>
                              </div>
                              <div class="placeholder ellipsis text-extra-muted xs">
                                <span>Status</span>
                              </div>
                            </div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Select" data-fieldname="sent_or_received" title="" data-original-title="Sent or Received">
                              <select type="text" autocomplete="off" class="input-with-feedback form-control input-xs ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="sent_or_received" placeholder="Sent or Received">
                                <option></option>
                                <option value="Sent">Sent</option>
                                <option value="Received">Received</option>
                              </select>
                              <span class="tooltip-content">sent_or_received</span>
                              <div class="select-icon xs">
                                <svg class="icon  icon-xs" style="" aria-hidden="true">
                                  <use class="" href="#icon-select"></use>
                                </svg>
                              </div>
                              <div class="placeholder ellipsis text-extra-muted xs">
                                <span>Sent or Received</span>
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
                                    <li>      <a class="dropdown-item option" data-value="subject">       Subject      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="name">       ID      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="creation">       Created On      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="idx">       Most Used      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="communication_type">       Communication Type      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="comment_type">       Comment Type      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="status">       Status      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="sent_or_received">       Sent or Received      </a>     </li>
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
                                  <span class="level-item" data-sort-by="subject" title="Click to sort by Subject">
                                  Subject
                                  </span>
                                </div>
                                <div class="list-row-col ellipsis hidden-xs tag-col hide "><span>Tag</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="comment_type" title="Click to sort by Comment Type">Comment Type</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="status" title="Click to sort by Status">Status</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="sent_or_received" title="Click to sort by Sent or Received">Sent or Received</span></div>
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
                                <p>Keeps track of all communications</p>
                                <p><button class="btn btn-default btn-sm btn-new-doc hidden-xs">
                                  Create your first Communication
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