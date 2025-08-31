<script lang="ts" setup>
import { ref, computed, watch, onMounted, Ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import { Inertia, Method } from '@inertiajs/inertia';

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import animationData from "@/Components/widgets/msoeawqm.json";
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import Swal from "sweetalert2";
import Lottie from "@/Components/widgets/lottie.vue";
import multi_user_image from "@assets/images/users/multi-user.jpg";
import { contacts } from "@/common/data";
import axios from 'axios';

import flatPickr from "vue-flatpickr-component";
import Pickr from "@simonwep/pickr";

import "flatpickr/dist/flatpickr.css";
import "@simonwep/pickr/dist/themes/classic.min.css"; // 'classic' theme
import "@simonwep/pickr/dist/themes/monolith.min.css"; // 'monolith' theme
import "@simonwep/pickr/dist/themes/nano.min.css";

import { Branch } from '../../interfaces/Branch';
import { capitalizeWords } from '../../utils/textUtils';

const date2 = ref<Date | null>(null);
const defaultOptions = ref({
  animationData: animationData
});
const defaultOptions1 = ref({
  animationData: animationData1
});
const searchQuery = ref<string | null>(null);
const addBranchModal = ref(false);
const page = ref(1);
const perPage = ref(8);
const pages = ref<number[]>([]);
const value = ref<string | null>(null);
const tagvalue = ref<string | null>(null);

const branchesList = ref(contacts);
const companiesList = ref();
const submitted = ref(false);
const dataEdit = ref(false);
const deleteModal = ref(false);
const event = ref<Branch>({
  id: null,
  company_id: null,
  branch_name: '',
  address: '',
  city: '',
  state: '',
  zip_code: '',
  country: '',
  phone_number: '',
  email: '',
  manager_name: '',
  created_at: null,
  updated_at: null,
  deleted_at: null,
});

// const handleInput = (branch: Event) => {
//   const target = branch.target as HTMLInputElement;
//   const field = target.name as keyof Branch; // Get the field name from input
//   event.value[field] = capitalizeWords(target.value);
// };

// Computed properties
const displayedPosts = computed(() => paginate(branchesList.value));
const resultQuery = computed(() => {
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase();
    return displayedPosts.value.filter((data) => {
      return (
        data.company_id.toLowerCase().includes(search) ||
        data.email.toLowerCase().includes(search) ||
        data.image.toLowerCase().includes(search) ||
        data.branch_name.toString().toLowerCase().includes(search) ||
        data.manager_name.toLowerCase().includes(search) ||
        data.phone_number.toLowerCase().includes(search)
      );
    });
  } else {
    return displayedPosts.value;
  }
});

// Watchers
watch(branchesList, () => {
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
    event.value.branch_name &&
    event.value.address &&
    event.value.city &&
    event.value.state &&
    event.value.zip_code &&
    event.value.country &&
    event.value.email &&
    event.value.phone_number &&
    event.value.manager_name;
  if (isValid) {
    if (dataEdit.value) {
      if (isFormValid()) {
        addBranchModal.value = false;
        branchesList.value = branchesList.value.map(item => item.id.toString() === event.value.id.toString() ? { ...item, ...event.value } : item);
        try {
          const response = await axios.post('/api/company/branches/store', event.value);
          console.log('Branches updated successfully:', response.data);
        } catch (error) {
          console.error('Error submitting branch:', error);
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
          const response = await axios.post('/api/company/branches/store', data);
          console.log('Branch submitted successfully:', response.data);
        } catch (error) {
          console.error('Error submitting branch:', error);
        }
        branchesList.value = [data, ...branchesList.value];
        addBranchModal.value = false;
      }
    }
  }
}

function isFormValid() {
  return event.value.branch_name &&
    event.value.address &&
    event.value.city &&
    event.value.state &&
    event.value.zip_code &&
    event.value.country &&
    event.value.email &&
    event.value.phone_number &&
    event.value.manager_name;
}

function onSort(column: keyof typeof event.value) {
  const direction = ref<'asc' | 'desc'>('asc');
  direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  const sortedArray = [...branchesList.value];
  sortedArray.sort((a, b) => {
    const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
    return direction.value === 'asc' ? res : -res;
  });
  branchesList.value = sortedArray;
}

function editDetails(data: any) {
  addBranchModal.value = true;
  dataEdit.value = true;
  event.value = {
    id: data.id,
    branch_name: data.branch_name,
    address: data.address,
    city: data.city,
    state: data.state,
    zip_code: data.zip_code,
    country: data.country,
    email: data.email,
    phone_number: data.phone_number,
    manager_name: data.manager_name,
    company_id: null,
    created_at: null,
    updated_at: null,
    deleted_at: null
  };
  submitted.value = false;
}

function toggleModal() {
  addBranchModal.value = true;
  dataEdit.value = false;
  event.value = {
    id: null,
    branch_name: "",
    address: "",
    city: "",
    state: "",
    zip_code: "",
    country: "",
    email: "",
    phone_number: "",
    manager_name: "",
    company_id: null,
    created_at: null,
    updated_at: null,
    deleted_at: null
  };
  submitted.value = false;
}

function deleteModalToggle(data: any) {
  deleteModal.value = true;
  event.value.id = data.id;
}

async function deleteData() {
  if (event.value.id) {
    branchesList.value = branchesList.value.filter((item: any) => item.id !== event.value.id);
    deleteModal.value = false;
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.delete(`/api/company/branches/${event.value.id}`)
      console.log(response);
      branchesList.value = response.data.result;
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
  const id = data.id;
  Inertia.visit(`/apps/branch-profile/`+id);
}

function setPages() {
  const numberOfPages = Math.ceil(branchesList.value.length / perPage.value);
  pages.value = [];
  for (let index = 1; index <= numberOfPages; index++) {
    pages.value.push(index);
  }
}

function paginate(branchesList: any[]) {
  const from = page.value * perPage.value - perPage.value;
  const to = page.value * perPage.value;
  return branchesList.slice(from, to);
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
        branchesList.value = branchesList.value.filter((customer) => !ids_array.includes(customer.id));
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
  fetchBranches();
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

const fetchBranches = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/api/company/branches');  // Adjust the API endpoint as needed
    console.log(response);
    branchesList.value = response.data.result;
  } catch (err) {
    error.value = 'Failed to fetch branches data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const getImageSrc = (img: string | null) => {
  console.log(new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/avatar-1.jpg");
  return img ? new URL(`@/images/branches/${img}`, import.meta.url).href : new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/users/avatar-1.jpg";
};
</script>

<template>
  <Layout>
    <Head title="Branches" />
    <PageHeader title="Branches" pageTitle="Company" />
    <BRow>
      <BCol lg="12">
        <BCard no-body>
          <BCardHeader>
            <div class="d-flex align-items-center flex-wrap gap-2">
              <div class="flex-grow-1">
                <BButton variant="info" class="add-btn me-1" @click="toggleModal">
                  <i class="ri-add-fill me-1 align-bottom"></i> Add Branch
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
        <BCard no-body id="branchesList">
          <BCardHeader>
            <BRow class="g-3">
              <BCol md="4">
                <div class="search-box">
                  <input type="text" class="form-control search" placeholder="Search for branch..." v-model="searchQuery" />
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
                      <th class="sort" data-sort="empid" scope="col" @click="onSort('empid')">City</th>
                      <th class="sort" data-sort="email_id" scope="col" @click="onSort('email')">Country</th>
                      <th class="sort" data-sort="phone" scope="col" @click="onSort('phone')">Phone</th>
                      <th class="sort" data-sort="department" scope="col" @click="onSort('department')">Email</th>
                      <th class="sort" data-sort="designation" scope="col" @click="onSort('designation')">Manager</th>
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
                          <div class="flex-grow-1 ms-2 name">{{ data.branch_name }}</div>
                        </div>
                      </td>
                      <td class="city">{{ data.city }}</td>
                      <td class="country">{{ data.country }}</td>
                      <td class="phone_number">{{ data.phone_number }}</td>
                      <td class="email">{{ data.email }}</td>
                      <td class="manager">{{ data.manager_name }}</td>
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
                          <li class="list-inline-item edit" v-b-tooltip.hover title="View">
                            <BLink href="javascript:void(0);" @click="showdetail(data)" class="text-muted d-inline-block">
                              <i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item edit" v-b-tooltip.hover title="Edit">
                            <BLink href="javascript:void(0);" @click="editDetails(data)" class="text-muted d-inline-block">
                              <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item edit" v-b-tooltip.hover title="Delete">
                            <BLink href="javascript:void(0);" @click="deleteModalToggle(data)" class="text-muted d-inline-block">
                              <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
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
        <BCard no-body id="branch-view-detail">
          <BCardBody class="text-center">
            <div class="position-relative d-inline-block">
              <img src="@assets/images/users/avatar-10.jpg" id="image" alt="" class="avatar-lg rounded-circle img-thumbnail material-shadow">
              <span class="branch-active position-absolute rounded-circle bg-success"><span class="visually-hidden"></span></span>
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

    <!-- add branch modal  -->
    <BModal v-model="addBranchModal" id="showmodal" hide-footer title-class="exampleModalLabel" header-class="bg-info-subtle p-3" class="v-modal-custom" centered :title="dataEdit ? 'Edit Branch' : 'Add Branch'">
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
              <label for="name-field" class="form-label">Branch Name</label>
              <input type="text" id="branch_name" class="form-control" placeholder="Enter branch name" v-model="event.branch_name" :class="{ 'is-invalid': submitted && !event.branch_name }" />
              <div class="invalid-feedback">Please enter branch name.</div>
            </div>
          </BCol>
          <BCol lg="12">
            <div>
              <label for="email_id-field" class="form-label">Address</label>
              <input type="text" id="address" class="form-control" placeholder="Enter address" v-model="event.address" :class="{ 'is-invalid': submitted && !event.address }"/>
              <div class="invalid-feedback">Please enter address.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="email_id-field" class="form-label">City</label>
              <input type="text" id="city" class="form-control" placeholder="Enter city" v-model="event.city" :class="{ 'is-invalid': submitted && !event.city }"/>
              <div class="invalid-feedback">Please enter city.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="email_id-field" class="form-label">State</label>
              <input type="text" id="state" class="form-control" placeholder="Enter state" v-model="event.state" :class="{ 'is-invalid': submitted && !event.state }"/>
              <div class="invalid-feedback">Please enter state.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="email_id-field" class="form-label">Zip Code</label>
              <input type="text" id="zip_code" class="form-control" placeholder="Enter zip code" v-model="event.zip_code" :class="{ 'is-invalid': submitted && !event.zip_code }"/>
              <div class="invalid-feedback">Please enter zip code.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="email_id-field" class="form-label">Country</label>
              <input type="text" id="country" class="form-control" placeholder="Enter country" v-model="event.country" :class="{ 'is-invalid': submitted && !event.country }"/>
              <div class="invalid-feedback">Please enter country.</div>
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
              <input type="text" id="phone_number" class="form-control" placeholder="Enter phone no" v-model="event.phone_number" :class="{ 'is-invalid': submitted && !event.phone_number }" />
              <div class="invalid-feedback">Please enter a phone number.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="email_id-field" class="form-label">Manager</label>
              <input type="text" id="manager_name" class="form-control" placeholder="Enter manager name" v-model="event.manager_name" :class="{ 'is-invalid': submitted && !event.manager_name }"/>
              <div class="invalid-feedback">Please enter a manager name.</div>
            </div>
          </BCol>
        </BRow>
        <div class="hstack gap-2 justify-content-end mt-3">
          <BButton type="button" variant="light" @click="addBranchModal = false" id="edtclosemodal">Close</BButton>
          <BButton type="button" variant="success" id="add-btn" @click="handleSubmit">
            {{ dataEdit ? 'Update' : 'Add Branch' }}
          </BButton>
        </div>
      </BForm>
    </BModal>

    <!-- delete modal -->
    <BModal v-model="deleteModal" modal-class="zoomIn" hide-footer no-close-on-backdrop centered>
      <div class="mt-2 text-center">
        <lottie class="avatar-xl" colors="primary:#f7b84b,secondary:#f06548" :options="defaultOptions1" :height="100" :width="100" />
        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
          <h4>You are about to delete a branch ?</h4>
          <p class="text-muted mx-4 mb-0">Deleting your branch will remove all of your information from our database.</p>
        </div>
      </div>
      <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
        <button type="button" class="btn w-sm btn-light material-shadow-none" @click="deleteModal = false">Close</button>
        <button type="button" class="btn w-sm btn-danger" id="delete-record" @click="deleteData">Yes, Delete It!</button>
      </div>
    </BModal>
  </Layout>
</template>