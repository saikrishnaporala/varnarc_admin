<script lang="ts" setup>
import { ref, computed, watch, onMounted } from 'vue';
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

import { EmployeeGrade } from '../../../interfaces/EmployeeGrade';

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
const addEmployeeGradesModal = ref(false);
const page = ref(1);
const perPage = ref(8);
const pages = ref<number[]>([]);
const value = ref<string | null>(null);
const tagvalue = ref<string | null>(null);
const employeegradesList = ref(contacts);
const submitted = ref(false);
const dataEdit = ref(false);
const deleteModal = ref(false);
const event = ref<EmployeeGrade>({
  id: null,
  grade_name: "",
  min_salary: 0,
  max_salary: 0,
  benefits: null,
  promotion_criteria: null,
  created_at: null,
  updated_at: null
});

// Computed properties
const displayedPosts = computed(() => paginate(employeegradesList.value));
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
watch(employeegradesList, () => {
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
    event.value.grade_name;
  if (isValid) {
    if (dataEdit.value) {
      if (isFormValid()) {
        addEmployeeGradesModal.value = false;
        employeegradesList.value = employeegradesList.value.map(item => item.id.toString() === event.value.id.toString() ? { ...item, ...event.value } : item);
        try {
          const response = await axios.post('/api/employee_grades/store', event.value);
          console.log('Employee Grade updated successfully:', response.data);
        } catch (error) {
          console.error('Error submitting employee grade:', error);
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
          const response = await axios.post('/api/employee_grades/store', data);
          console.log('Employee grade submitted successfully:', response.data);
        } catch (error) {
          console.error('Error submitting employee grade:', error);
        }
        employeegradesList.value = [data, ...employeegradesList.value];
        addEmployeeGradesModal.value = false;
      }
    }
  }
}

function isFormValid() {
  return event.value.grade_name
}

function onSort(column: keyof typeof event.value) {
  const direction = ref<'asc' | 'desc'>('asc');
  direction.value = direction.value === 'asc' ? 'desc' : 'asc';
  const sortedArray = [...employeegradesList.value];
  sortedArray.sort((a, b) => {
    const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
    return direction.value === 'asc' ? res : -res;
  });
  employeegradesList.value = sortedArray;
}

function editDetails(data: any) {
  addEmployeeGradesModal.value = true;
  dataEdit.value = true;
  event.value = {
    id: data.id,
    image: data.image,
    grade_name: data.grade_name
  };
  submitted.value = false;
}

function toggleModal() {
  addEmployeeGradesModal.value = true;
  dataEdit.value = false;
  event.value = {
    id: "",
    image: "",
    grade_name: ""
  };
  submitted.value = false;
}

function deleteModalToggle(data: any) {
  deleteModal.value = true;
  event.value.id = data.id;
}

async function deleteData() {
  if (event.value.id) {
    employeegradesList.value = employeegradesList.value.filter((item) => item.id !== event.value.id);
    deleteModal.value = false;
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.delete(`/api/employee_grades/${event.value.id}`)
      // const response = await axios.post('/api/leads/delete');  // Adjust the API endpoint as needed
      console.log(response);
      employeegradesList.value = response.data.result;
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
  Inertia.visit(`/apps/employeeType-profile/`+id);
}

function setPages() {
  const numberOfPages = Math.ceil(employeegradesList.value.length / perPage.value);
  pages.value = [];
  for (let index = 1; index <= numberOfPages; index++) {
    pages.value.push(index);
  }
}

function paginate(employeegradesList: any[]) {
  const from = page.value * perPage.value - perPage.value;
  const to = page.value * perPage.value;
  return employeegradesList.slice(from, to);
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
        employeegradesList.value = employeegradesList.value.filter((customer) => !ids_array.includes(customer.id));
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
  fetchEmployeeGrades();
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

const fetchEmployeeGrades = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/api/employee_grades');  // Adjust the API endpoint as needed
    console.log(response);
    employeegradesList.value = response.data.result;
  } catch (err) {
    error.value = 'Failed to fetch employeeGrades data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const getImageSrc = (img: string | null) => {
  console.log(new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/avatar-1.jpg");
  return img ? new URL(`@/images/EmployeeGrades/${img}`, import.meta.url).href : new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/users/avatar-1.jpg";
};
</script>

<template>
  <Layout>
    <Head title="Employee Grade" />
    <PageHeader title="Employee Grade" pageTitle="HR" />
    <BRow>
      <BCol lg="12">
        <BCard no-body>
          <BCardHeader>
            <div class="d-flex align-items-center flex-wrap gap-2">
              <div class="flex-grow-1">
                <BButton variant="info" class="add-btn me-1" @click="toggleModal">
                  <i class="ri-add-fill me-1 align-bottom"></i> Add Employee Grade
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
      <BCol xxl="12">
        <BCard no-body id="employeeGradeList">
          <BCardHeader>
            <BRow class="g-3">
              <BCol md="4">
                <div class="search-box">
                  <input type="text" class="form-control search" placeholder="Search for employee grade..." v-model="searchQuery" />
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
                      <th class="sort" data-sort="name" scope="col" @click="onSort('grade_name')">Name</th>
                      <th class="sort" data-sort="name" scope="col" @click="onSort('min_salary')">Min Salary</th>
                      <th class="sort" data-sort="name" scope="col" @click="onSort('max_salary')">Max Salary</th>
                      <th class="sort" data-sort="name" scope="col" @click="onSort('benefits')">Benefits</th>
                      <th class="sort" data-sort="name" scope="col" @click="onSort('promotion_criteria')">Promotion Criteria</th>
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
                          <div class="flex-grow-1 ms-2 name">{{ data.grade_name }}</div>
                        </div>
                      </td>
                      <td class="">
                        <div class="d-flex align-items-center">
                          <div class="flex-grow-1 ms-2 name">{{ data.min_salary }}</div>
                        </div>
                      </td>
                      <td class="">
                        <div class="d-flex align-items-center">
                          <div class="flex-grow-1 ms-2 name">{{ data.max_salary }}</div>
                        </div>
                      </td>
                      <td class="">
                        <div class="d-flex align-items-center">
                          <div class="flex-grow-1 ms-2 name">{{ data.benefits }}</div>
                        </div>
                      </td>
                      <td class="">
                        <div class="d-flex align-items-center">
                          <div class="flex-grow-1 ms-2 name">{{ data.promotion_criteria }}</div>
                        </div>
                      </td>
                      <td>
                        <ul class="list-inline hstack gap-2 mb-0">
                          <li class="list-inline-item edit" v-b-tooltip.hover title="Edit">
                              <BLink @click="editDetails(data)" class="text-muted d-inline-block">
                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                              </BLink>
                          </li>
                          <li class="list-inline-item edit" v-b-tooltip.hover title="Delete">
                              <BLink @click="deleteModalToggle(data)" class="text-muted d-inline-block">
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
    </BRow>
    <!-- add Employee Grade modal  -->
    <BModal v-model="addEmployeeGradesModal" id="showmodal" hide-footer title-class="exampleModalLabel" header-class="bg-info-subtle p-3" class="v-modal-custom" centered :title="dataEdit ? 'Edit Employee Grade' : 'Add Employee Grade'">
      <BForm action="" id="addform" class="tablelist-form" autocomplete="off">
        <input type="hidden" id="id" />
        <BRow class="g-3">
          <BCol lg="12">
            <div>
              <label for="name-field" class="form-label">Name</label>
              <input type="text" id="grade_name" class="form-control" placeholder="Enter name" v-model="event.grade_name" :class="{ 'is-invalid': submitted && !event.grade_name }" />
              <div class="invalid-feedback">Please enter a name.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="name-field" class="form-label">Min Salary</label>
              <input type="text" id="min_salary" class="form-control" placeholder="Enter Min Salary" v-model="event.min_salary" :class="{ 'is-invalid': submitted && !event.min_salary }" />
              <div class="invalid-feedback">Please enter a name.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="name-field" class="form-label">Max Salary</label>
              <input type="text" id="max_salary" class="form-control" placeholder="Enter Max Salary" v-model="event.max_salary" :class="{ 'is-invalid': submitted && !event.max_salary }" />
              <div class="invalid-feedback">Please enter a name.</div>
            </div>
          </BCol>
          <BCol lg="12">
            <div>
              <label for="name-field" class="form-label">Benefits</label>
              <input type="text" id="benefits" class="form-control" placeholder="Enter benefits" v-model="event.benefits" />
              <div class="invalid-feedback">Please enter benefits.</div>
            </div>
          </BCol>
          <BCol lg="12">
            <div>
              <label for="name-field" class="form-label">Promotion Criteria</label>
              <input type="text" id="promotion_criteria" class="form-control" placeholder="Enter promotion criteria" v-model="event.promotion_criteria" />
              <div class="invalid-feedback">Please enter promotion criteria.</div>
            </div>
          </BCol>
        </BRow>
        <div class="hstack gap-2 justify-content-end mt-3">
          <BButton type="button" variant="light" @click="addEmployeeGradesModal = false" id="edtclosemodal">Close</BButton>
          <BButton type="button" variant="success" id="add-btn" @click="handleSubmit">
            {{ dataEdit ? 'Update' : 'Add Employee Grade' }}
          </BButton>
        </div>
      </BForm>
    </BModal>

    <!-- delete modal -->
    <BModal v-model="deleteModal" modal-class="zoomIn" hide-footer no-close-on-backdrop centered>
      <div class="mt-2 text-center">
        <lottie class="avatar-xl" colors="primary:#f7b84b,secondary:#f06548" :options="defaultOptions1" :height="100" :width="100" />
        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
          <h4>You are about to delete a employee grade ?</h4>
          <p class="text-muted mx-4 mb-0">Deleting your employee grade will remove all of your information from our database.</p>
        </div>
      </div>
      <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
        <button type="button" class="btn w-sm btn-light material-shadow-none" @click="deleteModal = false">Close</button>
        <button type="button" class="btn w-sm btn-danger" id="delete-record" @click="deleteData">Yes, Delete It!</button>
      </div>
    </BModal>
  </Layout>
</template>