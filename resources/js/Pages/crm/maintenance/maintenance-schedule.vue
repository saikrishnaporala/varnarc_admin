<script lang="ts" setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import animationData from "@/Components/widgets/msoeawqm.json";
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import Swal from "sweetalert2";
import Lottie from "@/Components/widgets/lottie.vue";
import multi_user_image from "@assets/images/users/multi-user.jpg";
import { contacts } from "@/common/data";
import axios from 'axios';

// Setup refs and reactive variables
const multi_user_image_ref = ref(multi_user_image);
const timeConfig = ref({
  enableTime: false,
  dateFormat: "d M, Y",
});
const date2 = ref<Date | null>(null);
const defaultOptions = ref({
  animationData: animationData
});
const defaultOptions1 = ref({
  animationData: animationData1
});
const searchQuery = ref<string | null>(null);
const addContactModal = ref(false);
const page = ref(1);
const perPage = ref(8);
const pages = ref<number[]>([]);
const value = ref<string | null>(null);
const tagvalue = ref<string | null>(null);
const tagoption = ref([
  { value: 'Lead', label: 'Lead' },
  { value: 'Partner', label: 'Partner' },
  { value: 'Exiting', label: 'Exiting' },
  { value: 'Long-term', label: 'Long-term' }
]);
const contactsList = ref(contacts);
const companiesList = ref();
const submitted = ref(false);
const dataEdit = ref(false);
const deleteModal = ref(false);
const event = ref({
  id: "",
  image: "",
  name: "",
  company: "",
  email: "",
  score: "",
  phone: "",
  tags: [] as string[],
});

// Computed properties
const displayedPosts = computed(() => paginate(contactsList.value));
const resultQuery = computed(() => {
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase();
    return displayedPosts.value.filter((data) => {
      return (
        data.company.toLowerCase().includes(search) ||
        data.email.toLowerCase().includes(search) ||
        data.image.toLowerCase().includes(search) ||
        data.score.toString().toLowerCase().includes(search) ||
        data.name.toLowerCase().includes(search) ||
        data.phone.toLowerCase().includes(search)
      );
    });
  } else {
    return displayedPosts.value;
  }
});

// Watchers
watch(contactsList, () => {
  setPages();
});

// Lifecycle hooks
onMounted(() => {
  setPages();
});

// Methods
async function handleSubmit() {
  submitted.value = true;
  const isValid =
    event.value.name &&
    event.value.company &&
    event.value.email &&
    event.value.phone &&
    event.value.score &&
    event.value.tags.length;
  if (isValid) {
    if (dataEdit.value) {
      if (isFormValid()) {
        addContactModal.value = false;
        contactsList.value = contactsList.value.map(item => item.id.toString() === event.value.id.toString() ? { ...item, ...event.value } : item);
        try {
          const response = await axios.post('/api/contacts/store', event.value);
          console.log('Contacts updated successfully:', response.data);
        } catch (error) {
          console.error('Error submitting contact:', error);
        }
      }
    } else {
      if (isFormValid()) {
        const data = {
          id: Math.floor(Math.random() * 100 + 20) - 20,
          image: multi_user_image,
          date: dateFormat(),
          ...event.value
        };
        try {
          const response = await axios.post('/api/contacts/store', data);
          console.log('Contact submitted successfully:', response.data);
        } catch (error) {
          console.error('Error submitting contact:', error);
        }
        contactsList.value = [data, ...contactsList.value];
        addContactModal.value = false;
      }
    }
  }
}

function isFormValid() {
  return event.value.name && event.value.company && event.value.email && event.value.phone && event.value.score && event.value.tags.length > 0;
}

function onSort(column: keyof typeof event.value) {
  const direction = ref<'asc' | 'desc'>('asc');
  direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  const sortedArray = [...contactsList.value];
  sortedArray.sort((a, b) => {
    const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
    return direction.value === 'asc' ? res : -res;
  });
  contactsList.value = sortedArray;
}

function editDetails(data: any) {
  addContactModal.value = true;
  dataEdit.value = true;
  event.value = {
    id: data.id,
    image: data.image,
    name: data.name,
    company: data.company,
    email: data.email,
    score: data.score,
    phone: data.phone,
    tags: data.tags
  };
  submitted.value = false;
}

function toggleModal() {
  addContactModal.value = true;
  dataEdit.value = false;
  event.value = {
    id: "",
    image: "",
    name: "",
    company: "",
    email: "",
    score: "",
    phone: "",
    tags: [],
  };
  submitted.value = false;
}

function deleteModalToggle(data: any) {
  deleteModal.value = true;
  event.value.id = data.id;
}

async function deleteData() {
  if (event.value.id) {
    contactsList.value = contactsList.value.filter((item) => item.id !== event.value.id);
    deleteModal.value = false;
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.delete(`/api/contacts/${event.value.id}`)
      // const response = await axios.post('/api/leads/delete');  // Adjust the API endpoint as needed
      console.log(response);
      contactsList.value = response.data.result;
    } catch (err) {
      error.value = 'Failed to fetch leads data.';
      console.error(err);
    } finally {
      loading.value = false;
    }
  }
}

function dateFormat() {
  const d = new Date();
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  return `${d.getDate()} ${months[d.getMonth()]}, ${d.getFullYear()}`;
}

function showdetail(data: any) {
  const imageEl = document.getElementById('image') as HTMLImageElement;
  const nameEl = document.getElementById('name')!;
  const companyEl = document.getElementById('company')!;
  const emailEl = document.getElementById('email')!;
  const phoneEl = document.getElementById('phoneno')!;
  const scoreEl = document.getElementById('leadscoreno')!;
  const tagsEl = document.getElementById('tags')!;
  const lastDateEl = document.getElementById('lastdate')!;
  const lastTimeEl = document.getElementById('lasttime')!;

  imageEl.src = data.image;
  nameEl.innerHTML = data.name;
  companyEl.innerHTML = data.company;
  emailEl.innerHTML = data.email;
  phoneEl.innerHTML = data.phone;
  scoreEl.innerHTML = data.score;
  tagsEl.innerHTML = '';
  data.tags.forEach((tag: string) => {
    tagsEl.innerHTML += `&nbsp;<span class="badge bg-primary-subtle text-primary">${tag}</span>`;
  });
  lastDateEl.innerHTML = data.date;
  lastTimeEl.innerHTML = data.time;
}

function setPages() {
  const numberOfPages = Math.ceil(contactsList.value.length / perPage.value);
  pages.value = [];
  for (let index = 1; index <= numberOfPages; index++) {
    pages.value.push(index);
  }
}

function paginate(contactsList: any[]) {
  const from = page.value * perPage.value - perPage.value;
  const to = page.value * perPage.value;
  return contactsList.slice(from, to);
}

function deleteMultiple() {
  const ids_array: string[] = [];
  const items = document.getElementsByName("chk_child");
  items.forEach(function (ele) {
    if ((ele as HTMLInputElement).checked) {
      const trNode = ele.parentNode?.parentNode?.parentNode as HTMLElement;
      const id = trNode.querySelector(".id a")?.innerHTML;
      if (id) ids_array.push(id);
    }
  });

  if (ids_array.length > 0) {
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      cancelButtonColor: "#f46a6a",
      confirmButtonColor: "#34c38f",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.value) {
        contactsList.value = contactsList.value.filter((customer) => !ids_array.includes(customer.id));
        document.getElementById("checkAll")!.checked = false;
      }
    });
  } else {
    Swal.fire({
      title: "Please select at least one checkbox",
      confirmButtonClass: "btn btn-info",
      buttonsStyling: true,
      showCloseButton: true,
    });
  }
}

onMounted(() => {
  fetchCompanies();
  fetchContacts();
  const checkAll = document.getElementById("checkAll") as HTMLInputElement;
  if (checkAll) {
    checkAll.onclick = () => {
      const checkboxes = document.querySelectorAll('.form-check-all input[type="checkbox"]');
      checkboxes.forEach((checkbox: any) => {
        checkbox.checked = checkAll.checked;
        if (checkAll.checked) {
          checkbox.closest("tr")?.classList.add("table-active");
        } else {
          checkbox.closest("tr")?.classList.remove("table-active");
        }
      });
    };
  }
});

const loading = ref(false);
const error = ref<string | null>(null);

const fetchContacts = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/api/contacts');  // Adjust the API endpoint as needed
    console.log(response);
    response.data.result.forEach(contact => {
      if (contact.tags) {
        contact.tags = contact.tags.split(',').map(tag => tag.trim());
      }
    });
    contactsList.value = response.data.result;
  } catch (err) {
    error.value = 'Failed to fetch contacts data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const getImageSrc = (img: string | null) => {
  console.log(new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/avatar-1.jpg");
  return img ? new URL(`@/images/contacts/${img}`, import.meta.url).href : new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/users/avatar-1.jpg";
};

const fetchCompanies = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/api/companies');  // Adjust the API endpoint as needed
    console.log(response);
    companiesList.value = response.data.result;
  } catch (err) {
    error.value = 'Failed to fetch companies data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};
</script>


<template>
  <Layout>

    <Head title="Maintenance Schedule" />
    <PageHeader title="Maintenance Schedule" pageTitle="CRM" subTitle="Maintenance"/>
    <BRow>
      <BCol lg="12">
        <BCard no-body>
          <BCardHeader>
            <div class="d-flex align-items-center flex-wrap gap-2">
              <div class="flex-grow-1">
                <BButton variant="info" class="add-btn me-1" @click="toggleModal">
                  <i class="ri-add-fill me-1 align-bottom"></i> Add Contacts
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
        <BCard no-body id="contactList">
          <BCardHeader>
            <BRow class="g-3">
              <BCol md="4">
                <div class="search-box">
                  <input type="text" class="form-control search" placeholder="Search for contact..." v-model="searchQuery" />
                  <i class="ri-search-line search-icon"></i>
                </div>
              </BCol>
              <BCol md="auto" class="ms-auto">
                <div class="d-flex align-items-center gap-2">
                  <span class="text-muted flex-shrink-0">Sort by: </span>
                  <Multiselect class="form-control" style="width:150px;" v-model="value" :close-on-select="true" :searchable="true" :create-option="true" :options="[
                    { value: 'Name', label: 'Name' },
                    { value: 'Company', label: 'Company' },
                    { value: 'Lead', label: 'Lead' }
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
                      <th class="sort" data-sort="name" scope="col" @click="onSort('name')">Name</th>
                      <th class="sort" data-sort="company_name" scope="col" @click="onSort('company')">
                        Company
                      </th>
                      <th class="sort" data-sort="email_id" scope="col" @click="onSort('email')">
                        Email ID
                      </th>
                      <th class="sort" data-sort="phone" scope="col" @click="onSort('phone')">
                        Phone No
                      </th>
                      <th class="sort" data-sort="lead_score" scope="col" @click="onSort('lead_score')">
                        Lead Score
                      </th>
                      <th class="sort" data-sort="tags" scope="col" @click="onSort('tags')">
                        Tags
                      </th>
                      <th class="sort" data-sort="date" scope="col" @click="onSort('last_contacted')">
                        Last Contacted
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
                      <td class="">
                        <div class="d-flex align-items-center">
                          <div class="flex-shrink-0"><img :src="data.image" alt="" class="user_image avatar-xs rounded-circle">
                          </div>
                          <div class="flex-grow-1 ms-2 name">{{ data.name }}</div>
                        </div>
                      </td>
                      <td class="company_name">{{ data.company }}</td>
                      <td class="email_id">{{ data.email }}</td>
                      <td class="phone">{{ data.phone }}</td>
                      <td class="lead_score">{{ data.score }}</td>
                      <td class="tags">
                        <span v-for="(tag, index) of data.tags" :key="index">
                          &nbsp;<BBadge variant="primary-subtle" class="bg-primary-subtle text-primary"> {{ tag
                          }}</BBadge>
                        </span>
                      </td>
                      <td> <span class="date">{{ data.date }}</span>&nbsp;<small class="text-muted time">{{
                        data.time
                      }}</small> </td>
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
                          <li class="list-inline-item">
                            <BDropdown variant="link" toggle-class="btn btn-soft-secondary btn-sm dropdown" no-caret menu-class="dropdown-menu-end" :offset="{ alignmentAxis: -130, crossAxis: 0, mainAxis: 10 }">
                              <template #button-content><i class="ri-more-fill align-middle"></i></template>
                              <BDropdownItem @click="showdetail(data)"><i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                View</BDropdownItem>
                              <BDropdownItem @click="editDetails(data)"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                Edit </BDropdownItem>
                              <BDropdownItem @click="deleteModalToggle(data)"> <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                Delete </BDropdownItem>
                            </BDropdown>
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
                  <BLink class="page-item pagination-next" href="#" :disabled="page >= pages.length" @click="page++">
                    Next
                  </BLink>
                </div>
              </div>
            </div>


          </BCardBody>
        </BCard>
      </BCol>
      <BCol xxl="3">
        <BCard no-body id="contact-view-detail">
          <BCardBody class="text-center">
            <div class="position-relative d-inline-block">
              <img src="@assets/images/users/avatar-10.jpg" id="image" alt="" class="avatar-lg rounded-circle img-thumbnail material-shadow">
              <span class="contact-active position-absolute rounded-circle bg-success"><span class="visually-hidden"></span></span>
            </div>
            <h5 class="mt-4 mb-1" id="name">Tonya Noble</h5>
            <p class="text-muted" id="company">Nesta Technologies</p>

            <ul class="list-inline mb-0">
              <li class="list-inline-item avatar-xs">
                <BLink href="javascript:void(0);" class="avatar-title bg-success-subtle text-success fs-15 rounded">
                  <i class="ri-phone-line"></i>
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
            <h6 class="text-muted text-uppercase fw-semibold mb-3">Personal Information</h6>
            <p class="text-muted mb-4">Hello, I'm Tonya Noble, The most effective objective is
              one that is tailored to the job you are applying for. It states what kind of
              career you are seeking, and what skills and experiences.</p>
            <div class="table-responsive table-card">
              <table class="table table-borderless mb-0">
                <tbody>
                  <tr>
                    <td class="fw-medium" scope="row">Designation</td>
                    <td id="designation">Lead Designer / Developer</td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Email ID</td>
                    <td id="email">tonyanoble@velzon.com</td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Phone No</td>
                    <td id="phoneno">414-453-5725</td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Lead Score</td>
                    <td id="leadscoreno">154</td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Tags</td>
                    <td id="tags" class="hstack gap-1">
                      <BBadge variant="primary-subtle" class="bg-primary-subtle text-primary">Lead</BBadge>
                      <BBadge variant="primary-subtle" class="bg-primary-subtle text-primary">Partner</BBadge>
                    </td>
                  </tr>
                  <tr>
                    <td class="fw-medium" scope="row">Last Contacted</td>
                    <td><span id="lastdate">15 Dec, 2021 </span><small class="text-muted" id="lasttime">08:58AM</small>
                    </td>
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
          <div class="content page-container" id="page-List/Maintenance Schedule/List" data-page-route="List/Maintenance Schedule/List" style="">
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
                          <h3 class="ellipsis title-text" title="Maintenance Schedule">Maintenance Schedule</h3>
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
                      <button class="btn btn-primary btn-sm primary-action" data-label="Add Maintenance Schedule">
                        <svg class="icon  icon-xs" style="" aria-hidden="true">
                          <use class="" href="#icon-add"></use>
                        </svg>
                        <span class="hidden-xs" data-label="Add%20Maintenance%20Schedule"> <span><span class="alt-underline">A</span>dd Maintenance Schedule</span> </span>
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
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Link" data-fieldname="customer" title="" data-original-title="Customer">
                              <div class="link-field ui-front" style="position: relative;">
                                <div class="awesomplete">
                                  <input type="text" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Link" data-fieldname="customer" placeholder="Customer" data-target="Customer" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
                                  <ul hidden="" role="listbox" id="awesomplete_list_2"></ul>
                                  <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                </div>
                              </div>
                              <span class="tooltip-content">customer</span>
                            </div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Select" data-fieldname="status" title="" data-original-title="Status">
                              <select type="text" autocomplete="off" class="input-with-feedback form-control input-xs ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="status" placeholder="Status">
                                <option></option>
                                <option value="Draft">Draft</option>
                                <option value="Submitted">Submitted</option>
                                <option value="Cancelled">Cancelled</option>
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
                                    <li>      <a class="dropdown-item option" data-value="name">       ID      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="creation">       Created On      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="idx">       Most Used      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="naming_series">       Series      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="status">       Status      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="transaction_date">       Transaction Date      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="customer_name">       Customer Name      </a>     </li>
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
                                  <span class="level-item" data-sort-by="name" title="Click to sort by ID">
                                  ID
                                  </span>
                                </div>
                                <div class="list-row-col ellipsis hidden-xs tag-col hide "><span>Tag</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span>Status</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="customer_name" title="Click to sort by Customer Name">Customer Name</span></div>
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
                                <p>You haven't created a Maintenance Schedule yet</p>
                                <p><button class="btn btn-default btn-sm btn-new-doc hidden-xs">
                                  Create your first Maintenance Schedule
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
          <div class="content page-container editable-form" id="page-Maintenance Schedule" data-page-route="Maintenance Schedule" style="" data-state="dirty">
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
								<h3 class="ellipsis title-text" title="New Maintenance Schedule">New Maintenance Schedule</h3>
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
							<div role="menu" class="dropdown-menu"><a class="dropdown-item" href="#" onclick="return false;" data-label="Sales%20Order">Sales Order</a></div>
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
									<span class="menu-item-label" data-label="New%20Maintenance%20Schedule"><span><span class="alt-underline">N</span>ew Maintenance Schedule</span></span>
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
										<input class="tags-input form-control" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_3" role="combobox">
										<ul hidden="" role="listbox" id="awesomplete_list_3"></ul>
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
														<div id="heatmap-maintenance_schedule" class="heatmap"></div>
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
													<div class="form-column col-sm-6" data-fieldname="__column_2">
														<form>
															<div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="naming_series">
																<div class="form-group">
																	<div class="clearfix">
																		<label class="control-label reqd" style="padding-right: 0px;">Series</label>
																		<span class="help"></span>
																	</div>
																	<div class="control-input-wrapper">
																		<div class="control-input flex align-center">
																			<select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold" maxlength="140" data-fieldtype="Select" data-fieldname="naming_series" placeholder="" data-doctype="Maintenance Schedule">
																				<option value="MAT-MSH-.YYYY.-">MAT-MSH-.YYYY.-</option>
																			</select>
																			<div class="select-icon ">
																				<svg class="icon  icon-sm" style="" aria-hidden="true">
																					<use class="" href="#icon-select"></use>
																				</svg>
																			</div>
																		</div>
																		<div class="control-value like-disabled-input bold" style="display: none;">MAT-MSH-.YYYY.-</div>
																		<p class="help-box small text-muted"></p>
																	</div>
																</div>
																<span class="tooltip-content">naming_series</span>
															</div>
															<div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="customer">
																<div class="form-group">
																	<div class="clearfix">
																		<label class="control-label" style="padding-right: 0px;">Customer</label>
																		<span class="help"></span>
																	</div>
																	<div class="control-input-wrapper">
																		<div class="control-input">
																			<div class="link-field ui-front" style="position: relative;">
																				<div class="awesomplete">
																					<input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="customer" placeholder="" data-doctype="Maintenance Schedule" data-target="Customer" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_4" role="combobox">
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
																<span class="tooltip-content">customer</span>
															</div>
														</form>
													</div>
													<div class="form-column col-sm-6" data-fieldname="column_break0">
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
															<div class="frappe-control input-max-width" data-fieldtype="Date" data-fieldname="transaction_date">
																<div class="form-group">
																	<div class="clearfix">
																		<label class="control-label reqd" style="padding-right: 0px;">Transaction Date</label>
																		<span class="help"></span>
																	</div>
																	<div class="control-input-wrapper">
																		<div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold" data-fieldtype="Date" data-fieldname="transaction_date" placeholder="" data-doctype="Maintenance Schedule"></div>
																		<div class="control-value like-disabled-input bold" style="display: none;">29-11-2024</div>
																		<p class="help-box small text-muted"></p>
																	</div>
																</div>
																<span class="tooltip-content">transaction_date</span>
															</div>
														</form>
													</div>
												</div>
											</div>
											<div class="row form-section card-section visible-section" data-fieldname="items_section">
												<div class="section-body">
													<div class="form-column col-sm-12" data-fieldname="__column_3">
														<form>
															<div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="items">
																<span class="tooltip-content">items</span>
																<div class="grid-field">
																	<label class="control-label">Items</label>
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
																						<div class="col grid-static-col col-xs-4 " data-fieldname="item_code" data-fieldtype="Link" title="Item Code">
																							<div class="field-area" style="display: none;"></div>
																							<div class="static-area ellipsis reqd">Item Code</div>
																						</div>
																						<div class="col grid-static-col col-xs-1 " data-fieldname="item_name" data-fieldtype="Data" title="Item Name">
																							<div class="field-area" style="display: none;"></div>
																							<div class="static-area ellipsis">Item Name</div>
																						</div>
																						<div class="col grid-static-col col-xs-3 " data-fieldname="start_date" data-fieldtype="Date" title="Start Date">
																							<div class="field-area" style="display: none;"></div>
																							<div class="static-area ellipsis reqd">Start Date</div>
																						</div>
																						<div class="col grid-static-col col-xs-1 " data-fieldname="periodicity" data-fieldtype="Select" title="Periodicity">
																							<div class="field-area" style="display: none;"></div>
																							<div class="static-area ellipsis">Periodicity</div>
																						</div>
																						<div class="col grid-static-col col-xs-1  text-right" data-fieldname="no_of_visits" data-fieldtype="Int" title="No of Visits">
																							<div class="field-area" style="display: none;"></div>
																							<div class="static-area ellipsis reqd">No of Visits</div>
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
																				<div class="rows">
																					<div class="grid-row" data-name="new-maintenance-schedule-item-fmuxwdyjiy" data-idx="1">
																						<div class="data-row row">
																							<div class="row-check sortable-handle col">
																								<input type="checkbox" class="grid-row-check">
																							</div>
																							<div class="row-index sortable-handle col">
																								<span>1</span>
																							</div>
																							<div class="col grid-static-col col-xs-4 error bold" data-fieldname="item_code" data-fieldtype="Link">
																								<div class="field-area" style="display: none;"></div>
																								<div class="static-area ellipsis reqd"></div>
																							</div>
																							<div class="col grid-static-col col-xs-1 " data-fieldname="item_name" data-fieldtype="Data">
																								<div class="field-area" style="display: none;"></div>
																								<div class="static-area ellipsis"></div>
																							</div>
																							<div class="col grid-static-col col-xs-3 error bold" data-fieldname="start_date" data-fieldtype="Date">
																								<div class="field-area" style="display: none;"></div>
																								<div class="static-area ellipsis reqd"></div>
																							</div>
																							<div class="col grid-static-col col-xs-1 " data-fieldname="periodicity" data-fieldtype="Select">
																								<div class="field-area" style="display: none;"></div>
																								<div class="static-area ellipsis"></div>
																							</div>
																							<div class="col grid-static-col col-xs-1 text-right bold" data-fieldname="no_of_visits" data-fieldtype="Int">
																								<div class="field-area" style="display: none;"></div>
																								<div class="static-area ellipsis reqd">
																									<div style="text-align: right"></div>
																								</div>
																							</div>
																							<div class="col">
																								<div class="btn-open-row" data-toggle="tooltip" data-placement="right" title="" data-original-title="Edit">
																									<a>
																										<svg class="icon  icon-xs" style="" aria-hidden="true">
																											<use class="" href="#icon-edit"></use>
																										</svg>
																									</a>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																				<div class="grid-empty text-center hidden">
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
											<div class="row form-section card-section visible-section hide-control" data-fieldname="schedule">
												<div class="section-head">
													Schedule
													<span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
												</div>
												<div class="section-body">
													<div class="form-column col-sm-12" data-fieldname="__column_4">
														<form>
															<div class="frappe-control input-max-width hide-control" data-fieldtype="Button" data-fieldname="generate_schedule">
																<div class="form-group">
																	<div class="clearfix">
																		<label class="control-label hide" style="padding-right: 0px;">&nbsp;</label>
																		<span class="help"></span>
																	</div>
																	<div class="control-input-wrapper">
																		<div class="control-input"><button class="btn btn-xs btn-default" data-fieldtype="Button" data-fieldname="generate_schedule" placeholder="" data-doctype="Maintenance Schedule" value="">Generate Schedule</button></div>
																		<div class="hide" style="display: none;"></div>
																		<p class="help-box small text-muted"></p>
																	</div>
																</div>
																<span class="tooltip-content">generate_schedule</span>
															</div>
															<div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="schedules">
																<span class="tooltip-content">schedules</span>
																<div class="grid-field">
																	<label class="control-label">Schedules</label>
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
																						<div class="col grid-static-col col-xs-2 " data-fieldname="scheduled_date" data-fieldtype="Date" title="Scheduled Date">
																							<div class="field-area" style="display: none;"></div>
																							<div class="static-area ellipsis reqd">Scheduled Date</div>
																						</div>
																						<div class="col grid-static-col col-xs-2 " data-fieldname="actual_date" data-fieldtype="Date" title="Actual Date">
																							<div class="field-area" style="display: none;"></div>
																							<div class="static-area ellipsis">Actual Date</div>
																						</div>
																						<div class="col grid-static-col col-xs-2 " data-fieldname="sales_person" data-fieldtype="Link" title="Sales Person">
																							<div class="field-area" style="display: none;"></div>
																							<div class="static-area ellipsis">Sales Person</div>
																						</div>
																						<div class="col grid-static-col col-xs-2 " data-fieldname="completion_status" data-fieldtype="Select" title="Completion Status">
																							<div class="field-area" style="display: none;"></div>
																							<div class="static-area ellipsis">Completion Status</div>
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
											<div class="row form-section card-section visible-section" data-fieldname="contact_info">
												<div class="section-head">
													Contact Info
													<span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
												</div>
												<div class="section-body">
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
															<div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="contact_person">
																<div class="form-group">
																	<div class="clearfix">
																		<label class="control-label" style="padding-right: 0px;">Contact Person</label>
																		<span class="help"></span>
																	</div>
																	<div class="control-input-wrapper">
																		<div class="control-input">
																			<div class="link-field ui-front" style="position: relative;">
																				<div class="awesomplete">
																					<input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="contact_person" placeholder="" data-doctype="Maintenance Schedule" data-target="Contact" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_5" role="combobox">
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
																<span class="tooltip-content">contact_person</span>
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
														</form>
													</div>
													<div class="form-column col-sm-6" data-fieldname="column_break_17">
														<form>
															<div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="customer_address">
																<div class="form-group">
																	<div class="clearfix">
																		<label class="control-label" style="padding-right: 0px;">Customer Address</label>
																		<span class="help"></span>
																	</div>
																	<div class="control-input-wrapper">
																		<div class="control-input">
																			<div class="link-field ui-front" style="position: relative;">
																				<div class="awesomplete">
																					<input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="customer_address" placeholder="" data-doctype="Maintenance Schedule" data-target="Address" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_6" role="combobox">
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
															<div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="territory">
																<div class="form-group">
																	<div class="clearfix">
																		<label class="control-label" style="padding-right: 0px;">Territory</label>
																		<span class="help"></span>
																	</div>
																	<div class="control-input-wrapper">
																		<div class="control-input">
																			<div class="link-field ui-front" style="position: relative;">
																				<div class="awesomplete">
																					<input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="territory" placeholder="" data-doctype="Maintenance Schedule" data-target="Territory" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_7" role="combobox">
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
																		<div class="control-value like-disabled-input" style="display: none;"></div>
																		<p class="help-box small text-muted"></p>
																	</div>
																</div>
																<span class="tooltip-content">territory</span>
															</div>
															<div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="customer_group">
																<div class="form-group">
																	<div class="clearfix">
																		<label class="control-label" style="padding-right: 0px;">Customer Group</label>
																		<span class="help"></span>
																	</div>
																	<div class="control-input-wrapper">
																		<div class="control-input">
																			<div class="link-field ui-front" style="position: relative;">
																				<div class="awesomplete">
																					<input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="customer_group" placeholder="" data-doctype="Maintenance Schedule" data-target="Customer Group" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_8" role="combobox">
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
																<span class="tooltip-content">customer_group</span>
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
																					<input type="text" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Link" data-fieldname="company" placeholder="" data-doctype="Maintenance Schedule" data-target="Company" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_9" role="combobox">
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
																		<div class="control-value like-disabled-input bold" style="display: none;"><a href="/app/company/Porala%20Online%20Services%20(Demo)" data-doctype="Company" data-name="Porala Online Services (Demo)" data-value="Porala Online Services (Demo)">Porala Online Services (Demo)</a></div>
																		<p class="help-box small text-muted"></p>
																	</div>
																</div>
																<span class="tooltip-content">company</span>
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
        </div>
      </BCol>
    </BRow>
    <!-- add contact modal  -->
    <BModal v-model="addContactModal" id="showmodal" hide-footer title-class="exampleModalLabel" header-class="bg-info-subtle p-3" class="v-modal-custom" centered :title="dataEdit ? 'Edit Contact' : 'Add Contact'">
      <BForm action="" id="addform" class="tablelist-form" autocomplete="off">
        <input type="hidden" id="id" />
        <BRow class="g-3">
          <BCol lg="12">
            <div class="text-center">
              <div class="position-relative d-inline-block">
                <div class="position-absolute  bottom-0 end-0">
                  <label for="customer-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Select Image" data-bs-original-title="Select Image">
                    <div class="avatar-xs cursor-pointer">
                      <div class="avatar-title bg-light border rounded-circle text-muted">
                        <i class="ri-image-fill"></i>
                      </div>
                    </div>
                  </label>
                  <input class="form-control d-none" value="" id="customer-image-input" type="file" accept="image/png, image/gif, image/jpeg">
                </div>
                <div class="avatar-lg p-1">
                  <div class="avatar-title bg-light rounded-circle">
                    <img :src="event.image || multi_user_image" id="customer-img" class="avatar-md rounded-circle object-fit-cover">
                  </div>
                </div>
              </div>
            </div>
            <div>
              <label for="name-field" class="form-label">Name</label>
              <input type="text" id="customername" class="form-control" placeholder="Enter name" v-model="event.name" :class="{ 'is-invalid': submitted && !event.name }" />
              <div class="invalid-feedback">Please enter a name.</div>
            </div>
          </BCol>
          <BCol lg="12">
            <div>
              <label for="company_name-field" class="form-label">Company Name</label>
              <select v-if="!loading" class="form-control" id="company" v-model="event.company" :class="{ 'is-invalid': submitted && !event.company }">
                <option value="" disabled>Select a company</option>
                <option v-for="company in companiesList" :key="company.id" :value="company.id">
                  {{ company.name }}
                </option>
              </select>
              <div class="invalid-feedback">Please enter a company name.</div>
            </div>
          </BCol>
          <BCol lg="12">
            <div>
              <label for="email_id-field" class="form-label">Email ID</label>
              <input type="text" id="emailid" class="form-control" placeholder="Enter email" v-model="event.email" :class="{ 'is-invalid': submitted && !event.email }" />
              <div class="invalid-feedback">Please enter a email.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="phone-field" class="form-label">Phone</label>
              <input type="text" id="phone" class="form-control" placeholder="Enter phone no" v-model="event.phone" :class="{ 'is-invalid': submitted && !event.phone }" />
              <div class="invalid-feedback">Please enter a phone number.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="lead_score-field" class="form-label">Lead Score</label>
              <input type="text" id="leadscore" class="form-control" placeholder="Enter value" v-model="event.score" :class="{ 'is-invalid': submitted && !event.score }" />
              <div class="invalid-feedback">Please enter a lead score.</div>
            </div>
          </BCol>
          <div>
            <label for="" class="form-label">Tags</label>
            <Multiselect mode="tags" :close-on-select="true" :searchable="true" :create-option="false" :options="tagoption" v-model="event.tags" :class="{ 'is-invalid': submitted && !event.tags }" />
            <div class="invalid-feedback">Please select a tags.</div>
          </div>
        </BRow>
        <div class="hstack gap-2 justify-content-end mt-3">
          <BButton type="button" variant="light" @click="addContactModal = false" id="edtclosemodal">Close</BButton>
          <BButton type="button" variant="success" id="add-btn" @click="handleSubmit">
            {{ dataEdit ? 'Update' : 'Add Contact' }}
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
          <p class="text-muted mx-4 mb-0">Deleting your contact will remove all of your information from our database.</p>
        </div>
      </div>
      <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
        <button type="button" class="btn w-sm btn-light material-shadow-none" @click="deleteModal = false">Close</button>
        <button type="button" class="btn w-sm btn-danger" id="delete-record" @click="deleteData">Yes, Delete It!</button>
      </div>
    </BModal>
  </Layout>
</template>