<script lang="ts" setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import Layout from "@/Layouts/main.vue";
import PageHeader from "@/Components/page-header.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import flatPickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import Swal from "sweetalert2";
import Lottie from "@/Components/widgets/lottie.vue";
import animationData from "@/Components/widgets/msoeawqm.json";
import animationData1 from "@/Components/widgets/gsqxdxog.json";
import multi_user_image from "@assets/images/users/multi-user.jpg";
import { leads as initialLeads } from "@/common/data";
import axios from 'axios';
import { Console } from 'console';

// Reactive variables
const rightOffcanvas = ref(false);
const addLeadsModal = ref(false);
const timeConfig = ref({
  enableTime: false,
  dateFormat: "d M, Y"
});
const date2 = ref(null);
const defaultOptions = ref({
  animationData: animationData
});
const defaultOptions1 = ref({
  animationData: animationData1
});
const multiUserImage = multi_user_image;
const page = ref(1);
const perPage = ref(8);
const pages = ref<number[]>([]);
const value = ref(null);
const searchQuery = ref<string | null>(null);
const date = ref(null);
const tagvalue = ref(null);
const tagoption = ref([
  { value: 'Lead', label: 'Lead' },
  { value: 'Partner', label: 'Partner' },
  { value: 'Exiting', label: 'Exiting' },
  { value: 'Long-tern', label: 'Long-tern' }
]);

const rangeDateconfig = ref({
  mode: "range",
  dateFormat: "d M, Y"
});
// const leads = ref(initialLeads);
const leads = ref([]);

const submitted = ref(false);
const dataEdit = ref(false);
const deleteModal = ref(false);
const event = ref({
  id: "",
  leadsId: "",
  leadImage: "",
  name: "",
  company: "",
  phone: "",
  email: "",
  score: "",
  tags: [] as string[],
  location: ""
});

// Computed properties
const displayedPosts = computed(() => paginate(leads.value));
const resultQuery = computed(() => {
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase();
    return displayedPosts.value.filter((data) => {
      return (
        data.leadsId.toLowerCase().includes(search) ||
        data.name.toLowerCase().includes(search) ||
        data.company.toLowerCase().includes(search) ||
        data.score.toString().toLowerCase().includes(search) ||
        data.phone.toLowerCase().includes(search) ||
        data.email.toLowerCase().includes(search) ||
        data.location.toLowerCase().includes(search)
      );
    });
  } else {
    return displayedPosts.value;
  }
});

// Watchers
watch(leads, () => {
  setPages();
});

// Methods
const handleSubmit = async () => {
  submitted.value = true;
  if (
    event.value.name &&
    event.value.company &&
    event.value.score &&
    event.value.phone &&
    event.value.email &&
    event.value.location &&
    event.value.tags.length
  ) {
    if (dataEdit.value) {
      leads.value = leads.value.map((item) =>
        item.id.toString() === event.value.id.toString()
          ? { ...item, ...event.value }
          : item
      );
      try {
        // POST request to your API endpoint
        const response = await axios.post('/api/leads/store', event.value);

        // Handle the response if needed
        console.log('Lead updated successfully:', response.data);
      } catch (error) {
        // Handle the error
        console.error('Error submitting lead:', error);
      }
    } else {
      const newLead = {
        id: Math.floor(Math.random() * 100 + 20) - 20,
        leadImage: multiUserImage,
        ...event.value
      };
      try {
        // POST request to your API endpoint
        const response = await axios.post('/api/leads/store', newLead);

        // Handle the response if needed
        console.log('Lead submitted successfully:', response.data);
      } catch (error) {
        // Handle the error
        console.error('Error submitting lead:', error);
      }
      leads.value = [newLead, ...leads.value];
    }
    addLeadsModal.value = false;
  }
};

const onSort = (column: keyof typeof event.value) => {
  const direction = ref("asc");
  direction.value = direction.value === "asc" ? "desc" : "asc";
  leads.value.sort((a, b) => {
    const res = a[column] < b[column] ? -1 : a[column] > b[column] ? 1 : 0;
    return direction.value === "asc" ? res : -res;
  });
};

// const editDetails = (data: typeof event.value) => {
//   dataEdit.value = true;
//   addLeadsModal.value = true;
//   event.value = { ...data };
//   submitted.value = false;
// };

const editDetails = (data: any) => {
  addLeadsModal.value = true;
  dataEdit.value = true;
  console.log(data);
  
  event.value = {
    id: data.id,
    leadsId: "",
    leadImage: "",
    name: data.name,
    company: data.company,
    phone: data.phone,
    email: data.email,
    score: data.score,
    tags: data.tags,
    location: data.location
  };
  submitted.value = false;
};

const toggleModal = () => {
  addLeadsModal.value = true;
  dataEdit.value = false;
  event.value = {
    id: "",
    leadsId: "",
    leadImage: "",
    name: "",
    company: "",
    phone: "",
    email:"",
    score: "",
    tags: [],
    location: ""
  };
  submitted.value = false;
};

const deleteModalToggle = (data: typeof event.value) => {
  deleteModal.value = true;
  event.value.id = data.id;
};

const deleteData = async () => {
  if (event.value.id) {
    leads.value = leads.value.filter((item) => item.id !== event.value.id);
    deleteModal.value = false;
    loading.value = true;
    error.value = null;
    try {
      const response = await axios.delete(`/api/leads/${event.value.id}`)
      // const response = await axios.post('/api/leads/delete');  // Adjust the API endpoint as needed
      console.log(response);
      leads.value = response.data.result;
    } catch (err) {
      error.value = 'Failed to fetch leads data.';
      console.error(err);
    } finally {
      loading.value = false;
    }
  }
};

const setPages = () => {
  const numberOfPages = Math.ceil(leads.value.length / perPage.value);
  pages.value = Array.from({ length: numberOfPages }, (_, i) => i + 1);
};

const paginate = (items: typeof leads.value) => {
  const from = (page.value - 1) * perPage.value;
  return items.slice(from, from + perPage.value);
};

onMounted(() => {
  fetchLeads();
  const checkAll = document.getElementById("checkAll");
  if (checkAll) {
    checkAll.onclick = () => {
      const checkboxes = document.querySelectorAll(
        '.form-check-all input[type="checkbox"]'
      ) as NodeListOf<HTMLInputElement>;
      const removeActions = document.getElementById("remove-actions");

      checkboxes.forEach((checkbox) => {
        checkbox.checked = checkAll.checked;
        checkbox.closest("tr")?.classList.toggle("table-active", checkAll.checked);
      });

      if (removeActions) {
        removeActions.style.display = checkAll.checked ? "block" : "none";
      }
    };
  }

  const checkboxes = document.querySelectorAll(
    '#leadsList .form-check-input'
  ) as NodeListOf<HTMLInputElement>;

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      const checkedCount = document.querySelectorAll(
        '#leadsList .form-check-input:checked'
      ).length;
      const removeActions = document.getElementById("remove-actions");

      if (removeActions) {
        removeActions.style.display = checkedCount > 0 ? "block" : "none";
      }
    });
  });
});

const loading = ref(false);   // For loading state
const error = ref<string | null>(null);   // For handling errors

const fetchLeads = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/api/leads');  // Adjust the API endpoint as needed
    console.log(response);
    response.data.result.forEach(lead => {
      if (lead.tags) {
        lead.tags = lead.tags.split(',').map(tag => tag.trim());
      }
    });
    leads.value = response.data.result;
  } catch (err) {
    error.value = 'Failed to fetch leads data.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};
const getImageSrc = (leadImage: string | null) => {
  // If leadImage exists, use it, otherwise use the default image
  console.log(new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/avatar-1.jpg");
  return leadImage ? new URL(`@/images/users/${leadImage}`, import.meta.url).href : new URL('@/images/avatar-1.jpg', import.meta.url).origin+"/resources/images/users/avatar-1.jpg";
};

</script>

<style>
#mobileAuthNavBar {
  min-height: 125px;
  justify-content: space-between;
}
</style>

<template>
  <Layout>
    <Head title="Leads" />
    <PageHeader title="Leads" pageTitle="CRM" />
    <BRow>
      <BCol lg="12">
        <BCard no-body id="leadsList">
          <BCardHeader class="border-0">
            <BRow class="g-4 align-items-center">
              <BCol sm="3">
                <div class="search-box">
                  <input type="text" class="form-control search" v-model="searchQuery" placeholder="Search for..." />
                  <i class="ri-search-line search-icon"></i>
                </div>
              </BCol>
              <BCol sm="auto" class="ms-auto">
                <div class="hstack gap-2">
                  <BButton variant="soft-danger" id="remove-actions" @click="deleteMultiple">
                    <i class="ri-delete-bin-2-line"></i>
                  </BButton>
                  <BButton @click="rightOffcanvas = true" type="button" variant="info" href="#offcanvasExample">
                    <i class="ri-filter-3-line align-bottom me-1"></i> Fliters
                  </BButton>
                  <BButton type="button" variant="success" class="add-btn" id="create-btn" @click="toggleModal">
                    <i class="ri-add-line align-bottom me-1"></i> Add Leads
                  </BButton>
                  <BDropdown variant="link" class="card-header-dropdown" toggle-class="btn btn-soft-info btn-icon fs-14" no-caret menu-class="dropdown-menu-end">
                    <template #button-content><i class="ri-settings-4-line"></i></template>
                    <BDropdownItem>Copy</BDropdownItem>
                    <BDropdownItem>Move to pipline</BDropdownItem>
                    <BDropdownItem>Add to exceptions</BDropdownItem>
                    <BDropdownItem>Switch to common form view</BDropdownItem>
                    <BDropdownItem>Reset form view to default</BDropdownItem>
                  </BDropdown>
                </div>
              </BCol>
            </BRow>
          </BCardHeader>
          <BCardBody>
            <div>
              <div class="table-responsive table-card">
                <table class="table align-middle" id="customerTable">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" style="width: 50px">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="checkAll" value="option" />
                        </div>
                      </th>
                      <th class="sort" data-sort="name" @click="onSort('fname')">Name</th>
                      <th class="sort" data-sort="company_name" @click="onSort('companyName')">Company</th>
                      <th class="sort" data-sort="leads_score" @click="onSort('score')">Leads Source</th>
                      <th class="sort" data-sort="phone" @click="onSort('phone_mobile')">Phone</th>
                      <th class="sort" data-sort="location" @click="onSort('location')">Location</th>
                      <th class="sort" data-sort="tags" @click="onSort('tags')">Tags</th>
                      <th class="sort" data-sort="date" @click="onSort('date')">Create Date</th>
                      <th class="sort" data-sort="action">Action</th>
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
                          <div class="flex-shrink-0">
                            <img :src="getImageSrc(data.leadImage)" alt="" class="user_image avatar-xs rounded-circle">
                          </div>
                          <div class="flex-grow-1 ms-2 name">{{ data.name }}</div>
                        </div>
                      </td>
                      <td class="company_name">{{ data.company }}</td>
                      <td class="leads_score">{{ data.leadSource }}</td>
                      <td class="phone">{{ data.phone }}</td>
                      <td class="location">{{ data.location }}</td>
                      <td class="tags">
                        <span v-for="(tag, index) of data.tags" :key="index">
                          &nbsp;<BBadge variant="primary-subtle" class="bg-primary-subtle text-primary"> {{ tag }}</BBadge>
                        </span>
                      </td>
                      <td class="date">{{ data.created_at }}</td>
                      <td>
                        <ul class="list-inline hstack gap-2 mb-0">
                          <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Call">
                            <BLink href="javascript:void(0);" class="text-muted d-inline-block">
                              <i class="ri-phone-line fs-16"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Message">
                            <BLink href="javascript:void(0);" class="text-muted d-inline-block">
                              <i class="ri-question-answer-line fs-16"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="View">
                            <BLink href="javascript:void(0);"><i class="ri-eye-fill align-bottom text-muted"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item" @click="editDetails(data)">
                            <BLink class="edit-item-btn"><i class="ri-pencil-fill align-bottom text-muted"></i>
                            </BLink>
                          </li>
                          <li class="list-inline-item" @click="deleteModalToggle(data)">
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
                <div class="d-flex justify-content-end p-3" v-if="resultQuery.length >= 1">
                  <div class="pagination-wrap hstack gap-2">
                    <BLink class="page-item pagination-prev" href="#" :disabled="page <= 1" @click="page--"> Previous
                    </BLink>
                    <ul class="pagination listjs-pagination mb-0">
                      <li :class="{ active: pageNumber == page, disabled: pageNumber == '...', }" v-for="(pageNumber, index) in pages" :key="index" @click="page = pageNumber">
                        <BLink class="page" href="#" data-i="1" data-page="8">{{ pageNumber }}</BLink>
                      </li>
                    </ul>
                    <BLink class="page-item pagination-next" href="#" :disabled="page >= pages.length" @click="page++">
                      Next
                    </BLink>
                  </div>
                </div>
              </div>
            </div>
            <BOffcanvas v-model="rightOffcanvas" placement="end" title="Leads Filters" header-class="bg-light" body-class="p-0 overflow-hidden" footer-class="border-top p-3 text-center">
              <BForm action="" class="d-flex flex-column justify-content-end h-100">
                <div class="offcanvas-body">
                  <div class="mb-4">
                    <label for="datepicker-range" class="form-label text-muted text-uppercase fw-semibold mb-3">Date</label>
                    <flat-pickr placeholder="Select date" v-model="date" :config="rangeDateconfig" class="form-control flatpickr-input" id="demo-datepicker"></flat-pickr>
                  </div>
                  <div class="mb-4">
                    <label for="country-select" class="form-label text-muted text-uppercase fw-semibold mb-3">Country</label>

                    <Multiselect class="form-control" v-model="value" :close-on-select="true" :searchable="true" :create-option="true" :options="[
                      { value: '', label: 'Select country' },
                      { value: 'Argentina', label: 'Argentina' },
                      { value: 'Belgium', label: 'Belgium' },
                      { value: 'Brazil', label: 'Brazil' },
                      { value: 'Colombia', label: 'Colombia' },
                      { value: 'Denmark', label: 'Denmark' },
                      { value: 'France', label: 'France' },
                      { value: 'Germany', label: 'Germany' },
                      { value: 'Mexico', label: 'Mexico' },
                      { value: 'Russia', label: 'Russia' },
                      { value: 'Spain', label: 'Spain' },
                      { value: 'Syria', label: 'Syria' },
                      { value: 'United Kingdom', label: 'United Kingdom' },
                      {
                        value: 'United States of America',
                        label: 'United States of America',
                      },
                    ]" />
                  </div>
                  <div class="mb-4">
                    <label for="status-select" class="form-label text-muted text-uppercase fw-semibold mb-3">Status</label>
                    <BRow class="g-2">
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1" />
                          <label class="form-check-label" for="inlineCheckbox1">New Leads</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2" />
                          <label class="form-check-label" for="inlineCheckbox2">Old Leads</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3" />
                          <label class="form-check-label" for="inlineCheckbox3">Loss Leads</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="inlineCheckbox4" value="option4" />
                          <label class="form-check-label" for="inlineCheckbox4">Follow Up</label>
                        </div>
                      </BCol>
                    </BRow>
                  </div>
                  <div class="mb-4">
                    <label for="leadscore" class="form-label text-muted text-uppercase fw-semibold mb-3">Lead
                      Score</label>
                    <BRow class="g-2 align-items-center">
                      <BCol lg>
                        <input type="number" class="form-control" id="leadscore" placeholder="0" />
                      </BCol>
                      <BCol lg="auto">To</BCol>
                      <BCol lg>
                        <input type="number" class="form-control" id="leadscore" placeholder="0" />
                      </BCol>
                    </BRow>
                  </div>
                  <div>
                    <label for="leads-tags" class="form-label text-muted text-uppercase fw-semibold mb-3">Tags</label>
                    <BRow class="g-3">
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="marketing" value="marketing" />
                          <label class="form-check-label" for="marketing">Marketing</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="management" value="management" />
                          <label class="form-check-label" for="management">Management</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="business" value="business" />
                          <label class="form-check-label" for="business">Business</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="investing" value="investing" />
                          <label class="form-check-label" for="investing">Investing</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="partner" value="partner" />
                          <label class="form-check-label" for="partner">Partner</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="lead" value="lead" />
                          <label class="form-check-label" for="lead">Leads</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="sale" value="sale" />
                          <label class="form-check-label" for="sale">Sale</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="owner" value="owner" />
                          <label class="form-check-label" for="owner">Owner</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="banking" value="banking" />
                          <label class="form-check-label" for="banking">Banking</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="banking" value="banking" />
                          <label class="form-check-label" for="banking">Exiting</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="banking" value="banking" />
                          <label class="form-check-label" for="banking">Finance</label>
                        </div>
                      </BCol>
                      <BCol lg="6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="banking" value="banking" />
                          <label class="form-check-label" for="banking">Fashion</label>
                        </div>
                      </BCol>
                    </BRow>
                  </div>
                </div>
                <!--end offcanvas-body-->
                <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                  <BButton variant="light" class="w-100">Clear Filter</BButton>
                  <BButton type="submit" variant="success" class="w-100">
                    Filters
                  </BButton>
                </div>
                <!--end offcanvas-footer-->
              </BForm>
            </BOffcanvas>
            <!--end offcanvas-->
          </BCardBody>
        </BCard>
      </BCol>
    </BRow>
    <BRow>
      <BCol lg="12">
        <div id="body">
          <div class="content page-container" id="page-Workspaces" data-page-route="Workspaces" style="display: none;">
            <div class="page-head flex" style="top: var(--navbar-height);">
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
                    <div class="custom-actions hidden-xs hidden-md hide"></div>
                    <div class="standard-actions flex">
                      <span class="page-icon-group hide hidden-xs hidden-sm"></span>      
                      <div class="menu-btn-group hidden-xl">
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
                            <span class="menu-item-label" data-label="Settings"><span>S<span class="alt-underline">e</span>ttings</span></span>
                            </a>
                          </li>
                          <li class="dropdown-divider user-action"></li>
                        </ul>
                      </div>
                      <button class="btn btn-secondary btn-default btn-sm hide" data-label="Discard"><span><span class="alt-underline">D</span>iscard</span></button>      
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
                      <button class="btn btn-primary btn-sm primary-action hide" data-label="Save"><span>S<span class="alt-underline">a</span>ve</span></button>     
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
                          Your trial ends in 14 days. Please subscribe for uninterrupted services
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
                                            <div class="widget-subtitle">Last synced <span class="frappe-timestamp " data-timestamp="2024-11-26 00:00:03.246066" title="26-11-2024 00:00:03">33 minutes ago</span></div>
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
          <div class="content page-container editable-form" id="page-Selling Settings" data-page-route="Selling Settings" style="display: none;" data-state="clean">
            <div class="page-head flex" style="top: var(--navbar-height);">
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
                          <h3 class="ellipsis title-text" title="Selling Settings">Selling Settings</h3>
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
                                <span class="liked-by like-action d-flex align-items-center not-liked" data-doctype="Selling Settings" data-name="Selling Settings">
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
                          <li class="modified-by"><a href="/app/user/Administrator">Administrator</a> last edited this · <span class="frappe-timestamp " data-timestamp="2024-11-24 14:01:12.247200" title="24-11-2024 14:01:12">yesterday</span></li>
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
                                      <div id="heatmap-selling_settings" class="heatmap"></div>
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
                                <div class="row form-section card-section visible-section" data-fieldname="customer_defaults_section">
                                  <div class="section-head">
                                    Customer Defaults
                                    <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                  </div>
                                  <div class="section-body">
                                    <div class="form-column col-sm-6" data-fieldname="__column_2">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="cust_master_name">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Customer Naming By</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                                  <option value="Customer Name">Customer Name</option>
                                                  <option value="Naming Series">Naming Series</option>
                                                  <option value="Auto Name">Auto Name</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;">Customer Name</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">cust_master_name</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="customer_group">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Default Customer Group</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" data-target="Customer Group" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_2" role="combobox">
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
                                          <span class="tooltip-content">customer_group</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="column_break_4">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="territory">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Default Territory</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" data-target="Territory" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_3" role="combobox">
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
                                          <span class="tooltip-content">territory</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="item_price_settings_section">
                                  <div class="section-head">
                                    Item Price Settings
                                    <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                  </div>
                                  <div class="section-body">
                                    <div class="form-column col-sm-6" data-fieldname="__column_3">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="selling_price_list">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Default Price List</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" data-target="Price List" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_4" role="combobox">
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
                                              <div class="control-value like-disabled-input" style="display: none;"><a href="/app/price-list/Standard%20Selling" data-doctype="Price List" data-name="Standard Selling" data-value="Standard Selling">Standard Selling</a></div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">selling_price_list</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Select" data-fieldname="maintain_same_rate_action">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Action if Same Rate is Not Maintained Throughout Sales Cycle</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                                  <option value="Stop">Stop</option>
                                                  <option value="Warn">Warn</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;">Stop</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">maintain_same_rate_action</span>
                                        </div>
                                        <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="role_to_override_stop_action">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Role Allowed to Override Stop Action</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input">
                                                <div class="link-field ui-front" style="position: relative;">
                                                  <div class="awesomplete">
                                                    <input type="text" class="input-with-feedback form-control" data-target="Role" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_5" role="combobox">
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
                                          <span class="tooltip-content">role_to_override_stop_action</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="column_break_15">
                                      <form>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="maintain_same_sales_rate">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="maintain_same_sales_rate" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Maintain Same Rate Throughout Sales Cycle</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">maintain_same_sales_rate</span>
                                        </div>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="editable_price_list_rate">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="editable_price_list_rate" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Allow User to Edit Price List Rate in Transactions</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">editable_price_list_rate</span>
                                        </div>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="validate_selling_price">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="validate_selling_price" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Validate Selling Price for Item Against Purchase Rate or Valuation Rate</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">validate_selling_price</span>
                                        </div>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="editable_bundle_item_rates">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="editable_bundle_item_rates" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Calculate Product Bundle Price based on Child Items' Rates</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">editable_bundle_item_rates</span>
                                        </div>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_negative_rates_for_items">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_negative_rates_for_items" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Allow Negative rates for Items</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">allow_negative_rates_for_items</span>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                <div class="row form-section card-section visible-section" data-fieldname="sales_transactions_settings_section">
                                  <div class="section-head">
                                    Transaction Settings
                                    <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                  </div>
                                  <div class="section-body">
                                    <div class="form-column col-sm-6" data-fieldname="__column_4">
                                      <form>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="so_required">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Is Sales Order Required for Sales Invoice &amp; Delivery Note Creation?</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                                  <option value="No">No</option>
                                                  <option value="Yes">Yes</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;">No</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">so_required</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="dn_required">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Is Delivery Note Required for Sales Invoice Creation?</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis">
                                                  <option value="No">No</option>
                                                  <option value="Yes">Yes</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input" style="display: none;">No</div>
                                              <p class="help-box small text-muted"></p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">dn_required</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="sales_update_frequency">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Sales Update Frequency in Company and Project</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input flex align-center">
                                                <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold">
                                                  <option value="Monthly">Monthly</option>
                                                  <option value="Each Transaction">Each Transaction</option>
                                                  <option value="Daily">Daily</option>
                                                </select>
                                                <div class="select-icon ">
                                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                    <use class="" href="#icon-select"></use>
                                                  </svg>
                                                </div>
                                              </div>
                                              <div class="control-value like-disabled-input bold" style="display: none;">Each Transaction</div>
                                              <p class="help-box small text-muted">How often should Project and Company be updated based on Sales Transactions?</p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">sales_update_frequency</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Float" data-fieldname="blanket_order_allowance">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label" style="padding-right: 0px;">Blanket Order Allowance (%)</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Float" data-fieldname="blanket_order_allowance" placeholder="" data-doctype="Selling Settings"></div>
                                              <div class="control-value like-disabled-input" style="display: none;">0</div>
                                              <p class="help-box small text-muted">Percentage you are allowed to sell beyond the Blanket Order quantity.</p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">blanket_order_allowance</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="column_break_5">
                                      <form>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_multiple_items">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_multiple_items" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                            <span class="label-area">Allow Item to be Added Multiple Times in a Transaction</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">allow_multiple_items</span>
                                        </div>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_against_multiple_purchase_orders">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_against_multiple_purchase_orders" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Allow Multiple Sales Orders Against a Customer's Purchase Order</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">allow_against_multiple_purchase_orders</span>
                                        </div>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="allow_sales_order_creation_for_expired_quotation">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="allow_sales_order_creation_for_expired_quotation" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Allow Sales Order Creation For Expired Quotation</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">allow_sales_order_creation_for_expired_quotation</span>
                                        </div>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="dont_reserve_sales_order_qty_on_sales_return">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="dont_reserve_sales_order_qty_on_sales_return" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Don't Reserve Sales Order Qty on Sales Return</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">dont_reserve_sales_order_qty_on_sales_return</span>
                                        </div>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="hide_tax_id">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="hide_tax_id" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Hide Customer's Tax ID from Sales Transactions</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">hide_tax_id</span>
                                        </div>
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="enable_discount_accounting">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="enable_discount_accounting" placeholder="" data-doctype="Selling Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Enable Discount Accounting for Selling</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted">If enabled, additional ledger entries will be made for discounts in a separate Discount Account</p>
                                          </div>
                                          <span class="tooltip-content">enable_discount_accounting</span>
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
                            <div class="comment-box" style="display: none;">
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
                                  <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a> created this<span> · <span class="frappe-timestamp " data-timestamp="undefined" title=""></span></span></div>
                                </div>
                                <div class="timeline-item" data-timestamp="2024-11-24 14:01:13.842839">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a><a href="/app/version/f3744tekub"> changed the value of Default Price List from <b>null</b> to <b>Standard Selling</b></a><span> · <span class="frappe-timestamp " data-timestamp="2024-11-24 14:01:13.842839" title="24-11-2024 14:01:13">yesterday</span></span></div>
                                </div>
                                <div class="timeline-item" data-timestamp="2024-11-24 14:01:12.247200">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a> last edited this<span> · <span class="frappe-timestamp " data-timestamp="2024-11-24 14:01:12.247200" title="24-11-2024 14:01:12">yesterday</span></span></div>
                                </div>
                                <div class="timeline-item" data-timestamp="2024-11-24 14:01:04.638692">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a><a href="/app/version/lr7jbs5069"> changed the value of Is Sales Order Required for Sales Invoice &amp; Delivery Note Creation? from <b>null</b> to <b>No</b>, Is Delivery Note Required for Sales Invoice Creation? from <b>null</b> to <b>No</b>, Allow Item to be Added Multiple Times in a Transaction from <b>0</b> to <b>1</b></a><span> · <span class="frappe-timestamp " data-timestamp="2024-11-24 14:01:04.638692" title="24-11-2024 14:01:04">yesterday</span></span></div>
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
          <div class="content page-container editable-form" id="page-Workspace" data-page-route="Workspace" style="display: none;" data-state="clean">
            <div class="page-head flex" style="top: var(--navbar-height);">
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
                    <div class="flex fill-width title-area editable-title">
                      <div>
                        <div class="flex">
                          <h3 class="ellipsis title-text" title="CRM">CRM</h3>
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
                      <span class="page-icon-group hidden-xs hidden-sm">
                        <button class="text-muted btn btn-default prev-doc icon-btn" title="" data-original-title="Previous Document">
                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                            <use class="" href="#es-line-left-chevron"></use>
                          </svg>
                        </button>
                        <button class="text-muted btn btn-default next-doc icon-btn" title="" data-original-title="Next Document">
                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                            <use class="" href="#es-line-right-chevron"></use>
                          </svg>
                        </button>
                        <button class="text-muted btn btn-default icon-btn" title="" data-original-title="Print">
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
                            <span class="menu-item-label" data-label="Copy%20to%20Clipboard"><span><span class="alt-underline">C</span>opy to Clipboard</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Rename"><span><span class="alt-underline">R</span>ename</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Reload"><span>Rel<span class="alt-underline">o</span>ad</span></span>
                            </a>
                          </li>
                          <li>
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Delete">Delete</span>
                            <kbd class="pull-right">
                            <span>⇧+⌘+D</span>
                            </kbd>
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
                            <span class="menu-item-label" data-label="Undo"><span><span class="alt-underline">U</span>ndo</span></span>
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
                        <ul class="list-unstyled sidebar-menu user-actions">
                          <li class="user-action-row"><a href="/app/crm" target="_blank">Go to Workspace</a></li>
                        </ul>
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
                        <div class="sidebar-menu">
                          <p><label class="indicator-pill yellow" title="This feature is brand new and still experimental">Experimental</label></p>
                          <p><a class="small text-muted" href="https://github.com/frappe/frappe/issues/new" target="_blank">   Click here to post bugs and suggestions</a></p>
                        </div>
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
                              <input class="tags-input form-control" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_8" role="combobox">
                              <ul hidden="" role="listbox" id="awesomplete_list_8"></ul>
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
                                <span class="liked-by like-action d-flex align-items-center not-liked" data-doctype="Workspace" data-name="CRM">
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
                          <li class="modified-by">You last edited this · <span class="frappe-timestamp " data-timestamp="2024-11-24 23:41:02.077669" title="24-11-2024 23:41:02">yesterday</span></li>
                          <li class="created-by"><a href="/app/user/Administrator">Administrator</a> created this · <span class="frappe-timestamp " data-timestamp="2020-01-23 14:48:30.183272" title="23-01-2020 14:48:30">4 years ago</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col layout-main-section-wrapper">
                      <div class="layout-main-section">
                        <div class="page-form row hide"></div>
                        <div>
                          <div class="std-form-layout">
                            <div class="form-layout">
                              <div class="form-message blue">
                                <div>This document allows you to edit limited fields. For all kinds of workspace customization, use the Edit button located on the workspace page</div>
                                <div class="close-message">
                                  <svg class="icon  icon-sm" style="" aria-hidden="true">
                                    <use class="" href="#icon-close"></use>
                                  </svg>
                                </div>
                              </div>
                              <div class="form-page">
                                <div class="form-tabs-list form-tabs-sticky-down">
                                  <ul class="nav form-tabs" id="form-tabs" role="tablist">
                                    <li class="nav-item show">
                                      <a class="nav-link active" id="workspace-__details-tab" data-toggle="tab" data-fieldname="__details" href="#workspace-__details" role="tab" aria-controls="workspace-__details" aria-selected="true">
                                      Details
                                      </a>
                                    </li>
                                    <li class="nav-item show">
                                      <a class="nav-link " id="workspace-number_cards_tab-tab" data-toggle="tab" data-fieldname="number_cards_tab" href="#workspace-number_cards_tab" role="tab" aria-controls="workspace-number_cards_tab">
                                      Number Cards
                                      </a>
                                    </li>
                                    <li class="nav-item show">
                                      <a class="nav-link " id="workspace-tab_break_2-tab" data-toggle="tab" data-fieldname="tab_break_2" href="#workspace-tab_break_2" role="tab" aria-controls="workspace-tab_break_2">
                                      Dashboards
                                      </a>
                                    </li>
                                    <li class="nav-item show">
                                      <a class="nav-link " id="workspace-tab_break_15-tab" data-toggle="tab" data-fieldname="tab_break_15" href="#workspace-tab_break_15" role="tab" aria-controls="workspace-tab_break_15">
                                      Shortcuts
                                      </a>
                                    </li>
                                    <li class="nav-item show">
                                      <a class="nav-link " id="workspace-tab_break_18-tab" data-toggle="tab" data-fieldname="tab_break_18" href="#workspace-tab_break_18" role="tab" aria-controls="workspace-tab_break_18">
                                      Link Cards
                                      </a>
                                    </li>
                                    <li class="nav-item show">
                                      <a class="nav-link " id="workspace-quick_lists_tab-tab" data-toggle="tab" data-fieldname="quick_lists_tab" href="#workspace-quick_lists_tab" role="tab" aria-controls="workspace-quick_lists_tab">
                                      Quick Lists
                                      </a>
                                    </li>
                                    <li class="nav-item show">
                                      <a class="nav-link " id="workspace-custom_blocks_tab-tab" data-toggle="tab" data-fieldname="custom_blocks_tab" href="#workspace-custom_blocks_tab" role="tab" aria-controls="workspace-custom_blocks_tab">
                                      Custom Blocks
                                      </a>
                                    </li>
                                    <li class="nav-item show">
                                      <a class="nav-link " id="workspace-roles_tab-tab" data-toggle="tab" data-fieldname="roles_tab" href="#workspace-roles_tab" role="tab" aria-controls="workspace-roles_tab">
                                      Roles
                                      </a>
                                    </li>
                                  </ul>
                                </div>
                                <div class="form-tab-content tab-content">
                                  <div class="tab-pane fade show active" id="workspace-__details" role="tabpanel" aria-labelledby="workspace-__details-tab">
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
                                          <div id="heatmap-workspace" class="heatmap"></div>
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
                                            <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="label">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label reqd" style="padding-right: 0px;">Name</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Data" data-fieldname="label" placeholder="" data-doctype="Workspace"></div>
                                                  <div class="control-value like-disabled-input bold" style="display: none;">CRM</div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">label</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="title">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label reqd" style="padding-right: 0px;">Title</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input" style="display: none;"></div>
                                                  <div class="control-value like-disabled-input bold" style="">CRM</div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">title</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Float" data-fieldname="sequence_id">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Sequence Id</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input" style="display: none;"></div>
                                                  <div class="control-value like-disabled-input" style="">3</div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">sequence_id</span>
                                            </div>
                                            <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="for_user">
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
                                              <span class="tooltip-content">for_user</span>
                                            </div>
                                            <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="parent_page">
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
                                              <span class="tooltip-content">parent_page</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="module">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Module</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input" style="display: none;"></div>
                                                  <div class="control-value like-disabled-input" style=""><a href="/app/module-def/CRM" data-doctype="Module Def" data-name="CRM" data-value="CRM">CRM</a></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">module</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-6" data-fieldname="column_break_3">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Icon" data-fieldname="icon">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Icon</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input" style="display: none;"></div>
                                                  <div class="control-value like-disabled-input" style="">
                                                    <div>
                                                      <div class="selected-icon">
                                                        <svg class="icon  icon-md" style="" aria-hidden="true">
                                                          <use class="" href="#icon-crm"></use>
                                                        </svg>
                                                      </div>
                                                      <span class="icon-value">crm</span>
                                                    </div>
                                                  </div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">icon</span>
                                            </div>
                                            <div class="frappe-control input-max-width hide-control" data-fieldtype="Select" data-fieldname="indicator_color">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Indicator Color</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input flex align-center">
                                                    <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="indicator_color" placeholder="" data-doctype="Workspace">
                                                      <option value="green">green</option>
                                                      <option value="cyan">cyan</option>
                                                      <option value="blue">blue</option>
                                                      <option value="orange">orange</option>
                                                      <option value="yellow">yellow</option>
                                                      <option value="gray">gray</option>
                                                      <option value="grey">grey</option>
                                                      <option value="red">red</option>
                                                      <option value="pink">pink</option>
                                                      <option value="darkgrey">darkgrey</option>
                                                      <option value="purple">purple</option>
                                                      <option value="light-blue">light-blue</option>
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
                                              <span class="tooltip-content">indicator_color</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="restrict_to_domain">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Restrict to Domain</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="restrict_to_domain" placeholder="" data-doctype="Workspace" data-target="Domain" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_7" role="combobox">
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
                                              <span class="tooltip-content">restrict_to_domain</span>
                                            </div>
                                            <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="hide_custom">
                                              <div class="checkbox">
                                                <label>
                                                <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="hide_custom" placeholder="" data-doctype="Workspace"></span>
                                                <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                                <span class="label-area">Hide Custom DocTypes and Reports</span>
                                                <span class="ml-1 help"></span>
                                                </label>
                                                <p class="help-box small text-muted">Checking this will hide custom doctypes and reports cards in Links section</p>
                                              </div>
                                              <span class="tooltip-content">hide_custom</span>
                                            </div>
                                            <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="public">
                                              <div class="checkbox">
                                                <label>
                                                <span class="input-area" style="display: none;"></span>
                                                <span class="disp-area"><input type="checkbox" disabled="" class="disabled-selected"></span>
                                                <span class="label-area">Public</span>
                                                <span class="ml-1 help"></span>
                                                </label>
                                                <p class="help-box small text-muted"></p>
                                              </div>
                                              <span class="tooltip-content">public</span>
                                            </div>
                                            <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="is_hidden">
                                              <div class="checkbox">
                                                <label>
                                                <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="is_hidden" placeholder="" data-doctype="Workspace"></span>
                                                <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                                <span class="label-area">Is Hidden</span>
                                                <span class="ml-1 help"></span>
                                                </label>
                                                <p class="help-box small text-muted"></p>
                                              </div>
                                              <span class="tooltip-content">is_hidden</span>
                                            </div>
                                            <div class="frappe-control hide-control" data-fieldtype="Long Text" data-fieldname="content">
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
                                              <span class="tooltip-content">content</span>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="tab-pane fade show" id="workspace-number_cards_tab" role="tabpanel" aria-labelledby="workspace-number_cards_tab-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_2">
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_2">
                                          <form>
                                            <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="number_cards">
                                              <span class="tooltip-content">number_cards</span>
                                              <div class="grid-field">
                                                <label class="control-label">Number Cards</label>
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
                                                          <div class="col grid-static-col col-xs-5 " data-fieldname="number_card_name" data-fieldtype="Link" title="Number Card Name">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis reqd">Number Card Name</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-5 " data-fieldname="label" data-fieldtype="Data" title="Label">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">Label</div>
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
                                  </div>
                                  <div class="tab-pane fade show" id="workspace-tab_break_2" role="tabpanel" aria-labelledby="workspace-tab_break_2-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_3">
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_3">
                                          <form>
                                            <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="charts">
                                              <span class="tooltip-content">charts</span>
                                              <div class="grid-field">
                                                <label class="control-label">Charts</label>
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
                                                          <div class="col grid-static-col col-xs-5 " data-fieldname="chart_name" data-fieldtype="Link" title="Chart Name">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis reqd">Chart Name</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-5 " data-fieldname="label" data-fieldtype="Data" title="Label">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">Label</div>
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
                                                        <div class="grid-row" data-name="j5kecv3eon" data-idx="1">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>1</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-5 bold" data-fieldname="chart_name" data-fieldtype="Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/dashboard-chart/Territory%20Wise%20Sales" data-doctype="Dashboard Chart" data-name="Territory Wise Sales" data-value="Territory Wise Sales">Territory Wise Sales</a></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-5 " data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis">Territory Wise Sales</div>
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
                                  </div>
                                  <div class="tab-pane fade show" id="workspace-tab_break_15" role="tabpanel" aria-labelledby="workspace-tab_break_15-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_4">
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_4">
                                          <form>
                                            <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="shortcuts">
                                              <span class="tooltip-content">shortcuts</span>
                                              <div class="grid-field">
                                                <label class="control-label">Shortcuts</label>
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
                                                          <div class="col grid-static-col col-xs-2 " data-fieldname="type" data-fieldtype="Select" title="Type">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis reqd">Type</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link" title="Link To">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">Link To</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-2 " data-fieldname="url" data-fieldtype="Data" title="URL">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">URL</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-2 " data-fieldname="doc_view" data-fieldtype="Select" title="DocType View">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">DocType View</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-2 " data-fieldname="label" data-fieldtype="Data" title="Label">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis reqd">Label</div>
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
                                                        <div class="grid-row" data-name="2nljlt7mld" data-idx="1">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>1</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"><a href="/app/doctype/Lead" data-doctype="DocType" data-name="Lead" data-value="Lead">Lead</a></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="url" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="doc_view" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Lead</div>
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
                                                        <div class="grid-row" data-name="7ghrkdm94i" data-idx="2">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>2</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"><a href="/app/doctype/Opportunity" data-doctype="DocType" data-name="Opportunity" data-value="Opportunity">Opportunity</a></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="url" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="doc_view" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Opportunity</div>
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
                                                        <div class="grid-row" data-name="nn69vvp0df" data-idx="3">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>3</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"><a href="/app/doctype/Customer" data-doctype="DocType" data-name="Customer" data-value="Customer">Customer</a></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="url" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="doc_view" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Customer</div>
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
                                                        <div class="grid-row" data-name="80paj1dda2" data-idx="4">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>4</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Report</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"><a href="/app/report/Sales%20Analytics" data-doctype="Report" data-name="Sales Analytics" data-value="Sales Analytics">Sales Analytics</a></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="url" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="doc_view" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Sales Analytics</div>
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
                                                        <div class="grid-row" data-name="m72ir1lam5" data-idx="5">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>5</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Dashboard</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"><a href="/app/dashboard/CRM" data-doctype="Dashboard" data-name="CRM" data-value="CRM">CRM</a></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="url" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="doc_view" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Dashboard</div>
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
                                  </div>
                                  <div class="tab-pane fade show" id="workspace-tab_break_18" role="tabpanel" aria-labelledby="workspace-tab_break_18-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_5">
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_5">
                                          <form>
                                            <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="links">
                                              <span class="tooltip-content">links</span>
                                              <div class="grid-field">
                                                <label class="control-label">Links</label>
                                                <span class="help"></span>
                                                <p class="text-muted small grid-description" style="display: none;"></p>
                                                <div class="grid-custom-buttons"></div>
                                                <div class="form-grid-container">
                                                  <div class="form-grid">
                                                    <div class="grid-heading-row with-filter">
                                                      <div class="grid-row">
                                                        <div class="data-row row">
                                                          <div class="row-check sortable-handle col">
                                                            <input type="checkbox" class="grid-row-check">
                                                          </div>
                                                          <div class="row-index sortable-handle col">
                                                            <span>No.</span>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-3 " data-fieldname="type" data-fieldtype="Select" title="Type">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis reqd">Type</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-3 " data-fieldname="label" data-fieldtype="Data" title="Label">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis reqd">Label</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-2 " data-fieldname="link_type" data-fieldtype="Select" title="Link Type">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">Link Type</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link" title="Link To">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">Link To</div>
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
                                                      <div class="grid-row">
                                                        <div class="data-row row filter-row">
                                                          <div class="row-check col search"></div>
                                                          <div class="row-index col search">
                                                            <input type="text" class="form-control input-xs text-center">
                                                          </div>
                                                          <div class="col grid-static-col col-xs-3 search"><input type="text" class="form-control input-xs " title="" data-fieldtype="Select"></div>
                                                          <div class="col grid-static-col col-xs-3 search"><input type="text" class="form-control input-xs " title="" data-fieldtype="Data"></div>
                                                          <div class="col grid-static-col col-xs-2 search"><input type="text" class="form-control input-xs " title="" data-fieldtype="Select"></div>
                                                          <div class="col grid-static-col col-xs-2 search"><input type="text" class="form-control input-xs " title="" data-fieldtype="Dynamic Link"></div>
                                                          <div class="col grid-static-col search"></div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                    <div class="grid-body">
                                                      <div class="rows">
                                                        <div class="grid-row" data-name="r4s6aootrh" data-idx="1">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>1</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Card Break</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Reports</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
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
                                                        <div class="grid-row" data-name="vh45tb8bmo" data-idx="2">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>2</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Lead Details</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Report</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/report/Lead%20Details" data-doctype="Report" data-name="Lead Details" data-value="Lead Details">Lead Details</a></div>
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
                                                        <div class="grid-row" data-name="vhoj9cv9fc" data-idx="3">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>3</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Sales Pipeline Analytics</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Report</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/report/Sales%20Pipeline%20Analytics" data-doctype="Report" data-name="Sales Pipeline Analytics" data-value="Sales Pipeline Analytics">Sales Pipeline Analytics</a></div>
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
                                                        <div class="grid-row" data-name="tnc72an98v" data-idx="4">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>4</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Opportunity Summary by Sales Stage</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Report</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/report/Opportunity%20Summary%20by%20Sales%20Stage" data-doctype="Report" data-name="Opportunity Summary by Sales Stage" data-value="Opportunity Summary by Sales Stage">Opportunity Summary by Sales Stage</a></div>
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
                                                        <div class="grid-row" data-name="t3fdtlm0bs" data-idx="5">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>5</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Sales Funnel</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Page</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/page/sales-funnel" data-doctype="Page" data-name="sales-funnel" data-value="sales-funnel">sales-funnel</a></div>
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
                                                        <div class="grid-row" data-name="2sfjk7c61h" data-idx="6">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>6</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Prospects Engaged But Not Converted</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Report</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/report/Prospects%20Engaged%20But%20Not%20Converted" data-doctype="Report" data-name="Prospects Engaged But Not Converted" data-value="Prospects Engaged But Not Converted">Prospects Engaged But Not Converted</a></div>
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
                                                        <div class="grid-row" data-name="7uh2r6b2ee" data-idx="7">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>7</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">First Response Time for Opportunity</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Report</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/report/First%20Response%20Time%20for%20Opportunity" data-doctype="Report" data-name="First Response Time for Opportunity" data-value="First Response Time for Opportunity">First Response Time for Opportunity</a></div>
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
                                                        <div class="grid-row" data-name="j3npv1ij8j" data-idx="8">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>8</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Inactive Customers</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Report</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/report/Inactive%20Customers" data-doctype="Report" data-name="Inactive Customers" data-value="Inactive Customers">Inactive Customers</a></div>
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
                                                        <div class="grid-row" data-name="3cn0dj4kr5" data-idx="9">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>9</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Campaign Efficiency</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Report</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/report/Campaign%20Efficiency" data-doctype="Report" data-name="Campaign Efficiency" data-value="Campaign Efficiency">Campaign Efficiency</a></div>
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
                                                        <div class="grid-row" data-name="0sjgi9m5po" data-idx="10">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>10</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Lead Owner Efficiency</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Report</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/report/Lead%20Owner%20Efficiency" data-doctype="Report" data-name="Lead Owner Efficiency" data-value="Lead Owner Efficiency">Lead Owner Efficiency</a></div>
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
                                                        <div class="grid-row" data-name="ss68s0h23i" data-idx="11">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>11</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Card Break</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Maintenance</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
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
                                                        <div class="grid-row" data-name="d97gq7p150" data-idx="12">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>12</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Maintenance Schedule</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Maintenance%20Schedule" data-doctype="DocType" data-name="Maintenance Schedule" data-value="Maintenance Schedule">Maintenance Schedule</a></div>
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
                                                        <div class="grid-row" data-name="703dm5ip8s" data-idx="13">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>13</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Maintenance Visit</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Maintenance%20Visit" data-doctype="DocType" data-name="Maintenance Visit" data-value="Maintenance Visit">Maintenance Visit</a></div>
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
                                                        <div class="grid-row" data-name="v30t3tamdj" data-idx="14">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>14</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Warranty Claim</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Warranty%20Claim" data-doctype="DocType" data-name="Warranty Claim" data-value="Warranty Claim">Warranty Claim</a></div>
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
                                                        <div class="grid-row" data-name="imb9pgves6" data-idx="15">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>15</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Card Break</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Masters</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
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
                                                        <div class="grid-row" data-name="g4kgr7sdu7" data-idx="16">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>16</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Territory</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Territory" data-doctype="DocType" data-name="Territory" data-value="Territory">Territory</a></div>
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
                                                        <div class="grid-row" data-name="35d27dnolf" data-idx="17">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>17</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Customer Group</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Customer%20Group" data-doctype="DocType" data-name="Customer Group" data-value="Customer Group">Customer Group</a></div>
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
                                                        <div class="grid-row" data-name="ggakoju0mn" data-idx="18">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>18</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Contact</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Contact" data-doctype="DocType" data-name="Contact" data-value="Contact">Contact</a></div>
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
                                                        <div class="grid-row" data-name="3gmu79ldd0" data-idx="19">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>19</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Prospect</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Prospect" data-doctype="DocType" data-name="Prospect" data-value="Prospect">Prospect</a></div>
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
                                                        <div class="grid-row" data-name="e8k4b01s0d" data-idx="20">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>20</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Sales Person</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Sales%20Person" data-doctype="DocType" data-name="Sales Person" data-value="Sales Person">Sales Person</a></div>
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
                                                        <div class="grid-row" data-name="uun34it029" data-idx="21">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>21</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Sales Stage</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Sales%20Stage" data-doctype="DocType" data-name="Sales Stage" data-value="Sales Stage">Sales Stage</a></div>
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
                                                        <div class="grid-row" data-name="2liios5muh" data-idx="22">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>22</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Lead Source</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Lead%20Source" data-doctype="DocType" data-name="Lead Source" data-value="Lead Source">Lead Source</a></div>
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
                                                        <div class="grid-row" data-name="qgbf3pqlai" data-idx="23">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>23</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Card Break</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Sales Pipeline</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
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
                                                        <div class="grid-row" data-name="ccr6haj29c" data-idx="24">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>24</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Lead</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Lead" data-doctype="DocType" data-name="Lead" data-value="Lead">Lead</a></div>
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
                                                        <div class="grid-row" data-name="pq7er8bbb6" data-idx="25">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>25</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Opportunity</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Opportunity" data-doctype="DocType" data-name="Opportunity" data-value="Opportunity">Opportunity</a></div>
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
                                                        <div class="grid-row" data-name="ah6i6puk5d" data-idx="26">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>26</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Customer</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Customer" data-doctype="DocType" data-name="Customer" data-value="Customer">Customer</a></div>
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
                                                        <div class="grid-row" data-name="j3hmo7f93u" data-idx="27">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>27</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Contract</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Contract" data-doctype="DocType" data-name="Contract" data-value="Contract">Contract</a></div>
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
                                                        <div class="grid-row" data-name="rnuk374q6k" data-idx="28">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>28</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Appointment</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Appointment" data-doctype="DocType" data-name="Appointment" data-value="Appointment">Appointment</a></div>
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
                                                        <div class="grid-row" data-name="pif8tgukf8" data-idx="29">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>29</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Newsletter</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Newsletter" data-doctype="DocType" data-name="Newsletter" data-value="Newsletter">Newsletter</a></div>
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
                                                        <div class="grid-row" data-name="jn4v3mmna5" data-idx="30">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>30</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Communication</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Communication" data-doctype="DocType" data-name="Communication" data-value="Communication">Communication</a></div>
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
                                                        <div class="grid-row" data-name="hcvhd050hr" data-idx="31">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>31</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Card Break</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Settings</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
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
                                                        <div class="grid-row" data-name="e1l5qkv58t" data-idx="32">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>32</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">CRM Settings</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/CRM%20Settings" data-doctype="DocType" data-name="CRM Settings" data-value="CRM Settings">CRM Settings</a></div>
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
                                                        <div class="grid-row" data-name="gu03409lqe" data-idx="33">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>33</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">SMS Settings</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/SMS%20Settings" data-doctype="DocType" data-name="SMS Settings" data-value="SMS Settings">SMS Settings</a></div>
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
                                                        <div class="grid-row" data-name="gulb5saf79" data-idx="34">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>34</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Card Break</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Campaign</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 " data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis"></div>
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
                                                        <div class="grid-row" data-name="oc3k7jol93" data-idx="35">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>35</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Campaign</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Campaign" data-doctype="DocType" data-name="Campaign" data-value="Campaign">Campaign</a></div>
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
                                                        <div class="grid-row" data-name="qgrc9te0e2" data-idx="36">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>36</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Email Campaign</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Email%20Campaign" data-doctype="DocType" data-name="Email Campaign" data-value="Email Campaign">Email Campaign</a></div>
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
                                                        <div class="grid-row" data-name="ija6fb3868" data-idx="37">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>37</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">SMS Center</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/SMS%20Center" data-doctype="DocType" data-name="SMS Center" data-value="SMS Center">SMS Center</a></div>
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
                                                        <div class="grid-row" data-name="diuqujle6d" data-idx="38">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>38</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">SMS Log</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/SMS%20Log" data-doctype="DocType" data-name="SMS Log" data-value="SMS Log">SMS Log</a></div>
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
                                                        <div class="grid-row" data-name="0hurp2sgrk" data-idx="39">
                                                          <div class="data-row row">
                                                            <div class="row-check sortable-handle col">
                                                              <input type="checkbox" class="grid-row-check">
                                                            </div>
                                                            <div class="row-index sortable-handle col">
                                                              <span>39</span>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Link</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-3 bold" data-fieldname="label" data-fieldtype="Data">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">Email Group</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_type" data-fieldtype="Select">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd">DocType</div>
                                                            </div>
                                                            <div class="col grid-static-col col-xs-2 bold" data-fieldname="link_to" data-fieldtype="Dynamic Link">
                                                              <div class="field-area" style="display: none;"></div>
                                                              <div class="static-area ellipsis reqd"><a href="/app/doctype/Email%20Group" data-doctype="DocType" data-name="Email Group" data-value="Email Group">Email Group</a></div>
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
                                  </div>
                                  <div class="tab-pane fade show" id="workspace-quick_lists_tab" role="tabpanel" aria-labelledby="workspace-quick_lists_tab-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_6">
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_6">
                                          <form>
                                            <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="quick_lists">
                                              <span class="tooltip-content">quick_lists</span>
                                              <div class="grid-field">
                                                <label class="control-label">Quick Lists</label>
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
                                                          <div class="col grid-static-col col-xs-4 " data-fieldname="document_type" data-fieldtype="Link" title="DocType">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis reqd">DocType</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-3 " data-fieldname="label" data-fieldtype="Data" title="Label">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis reqd">Label</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-3 " data-fieldname="quick_list_filter" data-fieldtype="Code" title="Quick List Filter">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">Quick List Filter</div>
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
                                  </div>
                                  <div class="tab-pane fade show" id="workspace-custom_blocks_tab" role="tabpanel" aria-labelledby="workspace-custom_blocks_tab-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_7">
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_7">
                                          <form>
                                            <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="custom_blocks">
                                              <span class="tooltip-content">custom_blocks</span>
                                              <div class="grid-field">
                                                <label class="control-label">Custom Blocks</label>
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
                                                          <div class="col grid-static-col col-xs-5 " data-fieldname="custom_block_name" data-fieldtype="Link" title="Custom Block Name">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">Custom Block Name</div>
                                                          </div>
                                                          <div class="col grid-static-col col-xs-5 " data-fieldname="label" data-fieldtype="Data" title="Label">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">Label</div>
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
                                  </div>
                                  <div class="tab-pane fade show" id="workspace-roles_tab" role="tabpanel" aria-labelledby="workspace-roles_tab-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_8">
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_8">
                                          <form>
                                            <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="roles">
                                              <span class="tooltip-content">roles</span>
                                              <div class="grid-field">
                                                <label class="control-label">Roles</label>
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
                                                          <div class="col grid-static-col col-xs-10 " data-fieldname="role" data-fieldtype="Link" title="Role">
                                                            <div class="field-area" style="display: none;"></div>
                                                            <div class="static-area ellipsis">Role</div>
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
                            <div class="comment-box" style="display: none;">
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
                                <div class="timeline-item" data-timestamp="2024-11-24 23:41:02.116817">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/version/5ptnouh79u">You changed the value of Sequence Id from <b>18</b> to <b>3</b>, Parent Page from <b>Website</b> to <b>""</b></a><span> · <span class="frappe-timestamp " data-timestamp="2024-11-24 23:41:02.116817" title="24-11-2024 23:41:02">yesterday</span></span></div>
                                </div>
                                <div class="timeline-item" data-timestamp="2024-11-24 23:41:02.077669">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content ">You last edited this<span> · <span class="frappe-timestamp " data-timestamp="2024-11-24 23:41:02.077669" title="24-11-2024 23:41:02">yesterday</span></span></div>
                                </div>
                                <div class="timeline-item" data-timestamp="2024-11-24 23:37:26.049301">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/version/9hucs2sd14">You changed the value of Sequence Id from <b>17</b> to <b>18</b></a><span> · <span class="frappe-timestamp " data-timestamp="2024-11-24 23:37:26.049301" title="24-11-2024 23:37:26">yesterday</span></span></div>
                                </div>
                                <div class="timeline-item" data-timestamp="2024-11-24 23:37:21.175790">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/version/egt23vteu5">You changed the value of Parent Page from <b>""</b> to <b>Website</b></a><span> · <span class="frappe-timestamp " data-timestamp="2024-11-24 23:37:21.175790" title="24-11-2024 23:37:21">yesterday</span></span></div>
                                </div>
                                <div class="timeline-item" data-timestamp="2020-01-23 14:48:30.183272">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a> created this<span> · <span class="frappe-timestamp " data-timestamp="2020-01-23 14:48:30.183272" title="23-01-2020 14:48:30">4 years ago</span></span></div>
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
          <div class="content page-container" id="page-List/Lead/List" data-page-route="List/Lead/List" style="">
            <div class="page-head flex" style="top: var(--navbar-height);">
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
                          <h3 class="ellipsis title-text" title="Lead">Lead</h3>
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
                        <ul class="dropdown-menu" role="menu" style="">
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
                          <li data-view="Image">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                              <span class="menu-item-icon">
                                <svg class="icon  icon-sm" style="" aria-hidden="true">
                                  <use class="" href="#icon-image-view"></use>
                                </svg>
                              </span>
                              <span class="menu-item-label" data-label="Image%20View"><span><span class="alt-underline">I</span>mage View</span></span>
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
                          <li class="user-action">
                            <a class="grey-link dropdown-item" href="#" onclick="return false;">
                            <span class="menu-item-label" data-label="Create%20Prospect"><span>Crea<span class="alt-underline">t</span>e Prospect</span></span>
                            </a>
                          </li>
                          <li class="dropdown-divider user-action"></li>
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
                      <button class="btn btn-primary btn-sm primary-action" data-label="Add Lead">
                        <svg class="icon  icon-xs" style="" aria-hidden="true">
                          <use class="" href="#icon-add"></use>
                        </svg>
                        <span class="hidden-xs" data-label="Add%20Lead"> <span><span class="alt-underline">A</span>dd Lead</span> </span>
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
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="job_title" title="" data-original-title="Job Title"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="job_title" placeholder="Job Title"><span class="tooltip-content">job_title</span></div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Select" data-fieldname="status" title="" data-original-title="Status">
                              <select type="text" autocomplete="off" class="input-with-feedback form-control input-xs ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="status" placeholder="Status">
                                <option></option>
                                <option value="Lead">Lead</option>
                                <option value="Open">Open</option>
                                <option value="Replied">Replied</option>
                                <option value="Opportunity">Opportunity</option>
                                <option value="Quotation">Quotation</option>
                                <option value="Lost Quotation">Lost Quotation</option>
                                <option value="Interested">Interested</option>
                                <option value="Converted">Converted</option>
                                <option value="Do Not Contact">Do Not Contact</option>
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
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="company_name" title="" data-original-title="Organization Name"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="company_name" placeholder="Organization Name"><span class="tooltip-content">company_name</span></div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Link" data-fieldname="territory" title="" data-original-title="Territory">
                              <div class="link-field ui-front" style="position: relative;">
                                <div class="awesomplete">
                                  <input type="text" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Link" data-fieldname="territory" placeholder="Territory" data-target="Territory" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_15" role="combobox">
                                  <ul hidden="" role="listbox" id="awesomplete_list_15"></ul>
                                  <span class="visually-hidden" role="status" aria-live="assertive" aria-atomic="true">Begin typing for results.</span>
                                </div>
                              </div>
                              <span class="tooltip-content">territory</span>
                            </div>
                            <div class="form-group frappe-control input-max-width col-md-2" data-fieldtype="Data" data-fieldname="title" title="" data-original-title="Title"><input type="text" autocomplete="off" class="input-with-feedback form-control input-xs" maxlength="140" data-fieldtype="Data" data-fieldname="title" placeholder="Title"><span class="tooltip-content">title</span></div>
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
                                    <li>      <a class="dropdown-item option" data-value="title">       Title      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="name">       ID      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="creation">       Created On      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="idx">       Most Used      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="job_title">       Job Title      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="status">       Status      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="company_name">       Organization Name      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="territory">       Territory      </a>     </li>
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
                                  <span class="level-item" data-sort-by="title" title="Click to sort by Title">
                                  Title
                                  </span>
                                </div>
                                <div class="list-row-col ellipsis hidden-xs tag-col hide "><span>Tag</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span>Status</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="job_title" title="Click to sort by Job Title">Job Title</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="company_name" title="Click to sort by Organization Name">Organization Name</span></div>
                                <div class="list-row-col ellipsis hidden-xs  "><span data-sort-by="territory" title="Click to sort by Territory">Territory</span></div>
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
                                <p>You haven't created a Lead yet</p>
                                <p><button class="btn btn-default btn-sm btn-new-doc hidden-xs">
                                  Create your first Lead
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
          <div class="content page-container editable-form" id="page-Lead" data-page-route="Lead" style="display: none;" data-state="dirty">
            <div class="page-head flex" style="top: var(--navbar-height);">
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
                          <h3 class="ellipsis title-text" title="New Lead">New Lead</h3>
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
                            <span class="menu-item-label" data-label="New%20Lead"><span><span class="alt-underline">N</span>ew Lead</span></span>
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
                              <input class="tags-input form-control" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_16" role="combobox">
                              <ul hidden="" role="listbox" id="awesomplete_list_16"></ul>
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
                                <div class="form-tabs-list form-tabs-sticky-down">
                                  <ul class="nav form-tabs" id="form-tabs" role="tablist">
                                    <li class="nav-item hide">
                                      <a class="nav-link active" id="lead-__details-tab" data-toggle="tab" data-fieldname="__details" href="#lead-__details" role="tab" aria-controls="lead-__details" aria-selected="true">
                                      Details
                                      </a>
                                    </li>
                                    <li class="nav-item hide">
                                      <a class="nav-link " id="lead-activities_tab-tab" data-toggle="tab" data-fieldname="activities_tab" href="#lead-activities_tab" role="tab" aria-controls="lead-activities_tab">
                                      Activities
                                      </a>
                                    </li>
                                    <li class="nav-item hide">
                                      <a class="nav-link " id="lead-notes_tab-tab" data-toggle="tab" data-fieldname="notes_tab" href="#lead-notes_tab" role="tab" aria-controls="lead-notes_tab">
                                      Comments
                                      </a>
                                    </li>
                                    <li class="nav-item hide">
                                      <a class="nav-link " id="lead-dashboard_tab-tab" data-toggle="tab" data-fieldname="dashboard_tab" href="#lead-dashboard_tab" role="tab" aria-controls="lead-dashboard_tab">
                                      Connections
                                      </a>
                                    </li>
                                  </ul>
                                </div>
                                <div class="form-tab-content tab-content">
                                  <div class="tab-pane fade show active" id="lead-__details" role="tabpanel" aria-labelledby="lead-__details-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_1">
                                      <div class="section-body">
                                        <div class="form-column col-sm-4" data-fieldname="__column_1">
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
                                                  <label class="control-label" style="padding-right: 0px;">Series</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input flex align-center">
                                                    <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="naming_series" placeholder="" data-doctype="Lead">
                                                      <option value="CRM-LEAD-.YYYY.-">CRM-LEAD-.YYYY.-</option>
                                                    </select>
                                                    <div class="select-icon ">
                                                      <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                        <use class="" href="#icon-select"></use>
                                                      </svg>
                                                    </div>
                                                  </div>
                                                  <div class="control-value like-disabled-input" style="display: none;">CRM-LEAD-.YYYY.-</div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">naming_series</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="salutation">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Salutation</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="salutation" placeholder="" data-doctype="Lead" data-target="Salutation" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_17" role="combobox">
                                                        <ul hidden="" role="listbox" id="awesomplete_list_17"></ul>
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
                                              <span class="tooltip-content">salutation</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="first_name">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label reqd" style="padding-right: 0px;">First Name</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Data" data-fieldname="first_name" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">first_name</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="middle_name">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Middle Name</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="middle_name" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">middle_name</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="last_name">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Last Name</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="last_name" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">last_name</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="column_break_1">
                                          <form>
                                            <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="lead_name">
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
                                              <span class="tooltip-content">lead_name</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="job_title">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Job Title</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="job_title" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">job_title</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="gender">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Gender</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="gender" placeholder="" data-doctype="Lead" data-target="Gender" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_18" role="combobox">
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
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">gender</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="source">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Source</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="source" placeholder="" data-doctype="Lead" data-target="Lead Source" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_19" role="combobox">
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
                                              <span class="tooltip-content">source</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="col_break123">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="lead_owner">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Lead Owner</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="lead_owner" placeholder="" data-doctype="Lead" data-target="User" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_20" role="combobox">
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
                                                  <div class="control-value like-disabled-input" style="display: none;"><a href="/app/user/saikrishnaporala%40gmail.com" data-doctype="User" data-name="saikrishnaporala@gmail.com" data-value="saikrishnaporala@gmail.com">saikrishnaporala@gmail.com</a></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">lead_owner</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="status">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label reqd" style="padding-right: 0px;">Status</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input flex align-center">
                                                    <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis bold" maxlength="140" data-fieldtype="Select" data-fieldname="status" placeholder="" data-doctype="Lead">
                                                      <option value="Lead">Lead</option>
                                                      <option value="Open">Open</option>
                                                      <option value="Replied">Replied</option>
                                                      <option value="Opportunity">Opportunity</option>
                                                      <option value="Quotation">Quotation</option>
                                                      <option value="Lost Quotation">Lost Quotation</option>
                                                      <option value="Interested">Interested</option>
                                                      <option value="Converted">Converted</option>
                                                      <option value="Do Not Contact">Do Not Contact</option>
                                                    </select>
                                                    <div class="select-icon ">
                                                      <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                        <use class="" href="#icon-select"></use>
                                                      </svg>
                                                    </div>
                                                  </div>
                                                  <div class="control-value like-disabled-input bold" style="display: none;">Lead</div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">status</span>
                                            </div>
                                            <div class="frappe-control input-max-width hide-control" data-fieldtype="Link" data-fieldname="customer">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">From Customer</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="customer" placeholder="" data-doctype="Lead" data-target="Customer" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_21" role="combobox">
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
                                              <span class="tooltip-content">customer</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="type">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Lead Type</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input flex align-center">
                                                    <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="type" placeholder="" data-doctype="Lead">
                                                      <option></option>
                                                      <option value="Client">Client</option>
                                                      <option value="Channel Partner">Channel Partner</option>
                                                      <option value="Consultant">Consultant</option>
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
                                              <span class="tooltip-content">type</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="request_type">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Request Type</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input flex align-center">
                                                    <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="request_type" placeholder="" data-doctype="Lead">
                                                      <option></option>
                                                      <option value="Product Enquiry">Product Enquiry</option>
                                                      <option value="Request for Information">Request for Information</option>
                                                      <option value="Suggestions">Suggestions</option>
                                                      <option value="Other">Other</option>
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
                                              <span class="tooltip-content">request_type</span>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row form-section card-section visible-section" data-fieldname="contact_info_tab">
                                      <div class="section-head">
                                        Contact Info
                                        <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                      </div>
                                      <div class="section-body">
                                        <div class="form-column col-sm-4" data-fieldname="__column_2">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="email_id">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Email</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="email_id" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">email_id</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="website">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Website</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="website" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">website</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="column_break_20">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="mobile_no">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Mobile No</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="mobile_no" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">mobile_no</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="whatsapp_no">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">WhatsApp</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="whatsapp_no" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">whatsapp_no</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="column_break_16">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="phone">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Phone</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="phone" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">phone</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="phone_ext">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Phone Ext.</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="phone_ext" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">phone_ext</span>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row form-section card-section visible-section" data-fieldname="organization_section">
                                      <div class="section-head">
                                        Organization
                                        <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                      </div>
                                      <div class="section-body">
                                        <div class="form-column col-sm-4" data-fieldname="__column_3">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="company_name">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label reqd" style="padding-right: 0px;">Organization Name</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold" maxlength="140" data-fieldtype="Data" data-fieldname="company_name" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">company_name</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="no_of_employees">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">No of Employees</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input flex align-center">
                                                    <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="no_of_employees" placeholder="" data-doctype="Lead">
                                                      <option value="1-10">1-10</option>
                                                      <option value="11-50">11-50</option>
                                                      <option value="51-200">51-200</option>
                                                      <option value="201-500">201-500</option>
                                                      <option value="501-1000">501-1000</option>
                                                      <option value="1000+">1000+</option>
                                                    </select>
                                                    <div class="select-icon ">
                                                      <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                        <use class="" href="#icon-select"></use>
                                                      </svg>
                                                    </div>
                                                  </div>
                                                  <div class="control-value like-disabled-input" style="display: none;">1-10</div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">no_of_employees</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="column_break_28">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Currency" data-fieldname="annual_revenue">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Annual Revenue</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Currency" data-fieldname="annual_revenue" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;">₹ 0.00</div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">annual_revenue</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="industry">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Industry</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="industry" placeholder="" data-doctype="Lead" data-target="Industry Type" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_22" role="combobox">
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
                                              <span class="tooltip-content">industry</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="market_segment">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Market Segment</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="market_segment" placeholder="" data-doctype="Lead" data-target="Market Segment" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_23" role="combobox">
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
                                              <span class="tooltip-content">market_segment</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="column_break_31">
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
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="territory" placeholder="" data-doctype="Lead" data-target="Territory" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_24" role="combobox">
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
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">territory</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="fax">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Fax</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="fax" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">fax</span>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row form-section card-section visible-section" data-fieldname="address_section">
                                      <div class="section-head collapsible collapsed">
                                        Address &amp; Contacts
                                        <span class="ml-2 collapse-indicator mb-1" style="">
                                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                            <use class="mb-1" href="#es-line-down"></use>
                                          </svg>
                                        </span>
                                      </div>
                                      <div class="section-body hide">
                                        <div class="form-column col-sm-4" data-fieldname="__column_4">
                                          <form>
                                            <div class="frappe-control" data-fieldtype="HTML" data-fieldname="address_html"></div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="column_break_38">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="city">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">City</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="city" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">city</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="state">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">State</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Data" data-fieldname="state" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">state</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="country">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Country</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="country" placeholder="" data-doctype="Lead" data-target="Country" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_25" role="combobox">
                                                        <ul hidden="" role="listbox" id="awesomplete_list_25"></ul>
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
                                                  <div class="control-value like-disabled-input" style="display: none;"><a href="/app/country/India" data-doctype="Country" data-name="India" data-value="India">India</a></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">country</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="column_break2">
                                          <form>
                                            <div class="frappe-control" data-fieldtype="HTML" data-fieldname="contact_html"></div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row form-section card-section visible-section" data-fieldname="qualification_tab">
                                      <div class="section-head collapsible collapsed">
                                        Qualification
                                        <span class="ml-2 collapse-indicator mb-1" style="">
                                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                            <use class="mb-1" href="#es-line-down"></use>
                                          </svg>
                                        </span>
                                      </div>
                                      <div class="section-body hide">
                                        <div class="form-column col-sm-6" data-fieldname="__column_5">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Select" data-fieldname="qualification_status">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Qualification Status</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input flex align-center">
                                                    <select type="text" autocomplete="off" class="input-with-feedback form-control ellipsis" maxlength="140" data-fieldtype="Select" data-fieldname="qualification_status" placeholder="" data-doctype="Lead">
                                                      <option value="Unqualified">Unqualified</option>
                                                      <option value="In Process">In Process</option>
                                                      <option value="Qualified">Qualified</option>
                                                    </select>
                                                    <div class="select-icon ">
                                                      <svg class="icon  icon-sm" style="" aria-hidden="true">
                                                        <use class="" href="#icon-select"></use>
                                                      </svg>
                                                    </div>
                                                  </div>
                                                  <div class="control-value like-disabled-input" style="display: none;">Unqualified</div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">qualification_status</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-6" data-fieldname="column_break_64">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="qualified_by">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Qualified By</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="qualified_by" placeholder="" data-doctype="Lead" data-target="User" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_26" role="combobox">
                                                        <ul hidden="" role="listbox" id="awesomplete_list_26"></ul>
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
                                              <span class="tooltip-content">qualified_by</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Date" data-fieldname="qualified_on">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Qualified on</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control" data-fieldtype="Date" data-fieldname="qualified_on" placeholder="" data-doctype="Lead"></div>
                                                  <div class="control-value like-disabled-input" style="display: none;"></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">qualified_on</span>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row form-section card-section visible-section" data-fieldname="other_info_tab">
                                      <div class="section-head collapsible collapsed">
                                        Additional Information
                                        <span class="ml-2 collapse-indicator mb-1" style="">
                                          <svg class="es-icon es-line  icon-sm" style="" aria-hidden="true">
                                            <use class="mb-1" href="#es-line-down"></use>
                                          </svg>
                                        </span>
                                      </div>
                                      <div class="section-body hide">
                                        <div class="form-column col-sm-4" data-fieldname="__column_6">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="campaign_name">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Campaign Name</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="campaign_name" placeholder="" data-doctype="Lead" data-target="Campaign" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_27" role="combobox">
                                                        <ul hidden="" role="listbox" id="awesomplete_list_27"></ul>
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
                                              <span class="tooltip-content">campaign_name</span>
                                            </div>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="company">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Company</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="company" placeholder="" data-doctype="Lead" data-target="Company" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_28" role="combobox">
                                                        <ul hidden="" role="listbox" id="awesomplete_list_28"></ul>
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
                                                  <div class="control-value like-disabled-input" style="display: none;"><a href="/app/company/Porala%20Online%20Services%20(Demo)" data-doctype="Company" data-name="Porala Online Services (Demo)" data-value="Porala Online Services (Demo)">Porala Online Services (Demo)</a></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">company</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="column_break_22">
                                          <form>
                                            <div class="frappe-control input-max-width" data-fieldtype="Link" data-fieldname="language">
                                              <div class="form-group">
                                                <div class="clearfix">
                                                  <label class="control-label" style="padding-right: 0px;">Print Language</label>
                                                  <span class="help"></span>
                                                </div>
                                                <div class="control-input-wrapper">
                                                  <div class="control-input">
                                                    <div class="link-field ui-front" style="position: relative;">
                                                      <div class="awesomplete">
                                                        <input type="text" class="input-with-feedback form-control" maxlength="140" data-fieldtype="Link" data-fieldname="language" placeholder="" data-doctype="Lead" data-target="Language" autocomplete="off" aria-expanded="false" aria-owns="awesomplete_list_29" role="combobox">
                                                        <ul hidden="" role="listbox" id="awesomplete_list_29"></ul>
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
                                                  <div class="control-value like-disabled-input" style="display: none;"><a href="/app/language/en" data-doctype="Language" data-name="en" data-value="en">en</a></div>
                                                  <p class="help-box small text-muted"></p>
                                                </div>
                                              </div>
                                              <span class="tooltip-content">language</span>
                                            </div>
                                            <div class="frappe-control input-max-width hide-control" data-fieldtype="Attach Image" data-fieldname="image">
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
                                              <span class="tooltip-content">image</span>
                                            </div>
                                            <div class="frappe-control input-max-width hide-control" data-fieldtype="Data" data-fieldname="title">
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
                                              <span class="tooltip-content">title</span>
                                            </div>
                                          </form>
                                        </div>
                                        <div class="form-column col-sm-4" data-fieldname="column_break_50">
                                          <form>
                                            <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="disabled">
                                              <div class="checkbox">
                                                <label>
                                                <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="disabled" placeholder="" data-doctype="Lead"></span>
                                                <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                                <span class="label-area">Disabled</span>
                                                <span class="ml-1 help"></span>
                                                </label>
                                                <p class="help-box small text-muted"></p>
                                              </div>
                                              <span class="tooltip-content">disabled</span>
                                            </div>
                                            <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="unsubscribed">
                                              <div class="checkbox">
                                                <label>
                                                <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="unsubscribed" placeholder="" data-doctype="Lead"></span>
                                                <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                                <span class="label-area">Unsubscribed</span>
                                                <span class="ml-1 help"></span>
                                                </label>
                                                <p class="help-box small text-muted"></p>
                                              </div>
                                              <span class="tooltip-content">unsubscribed</span>
                                            </div>
                                            <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="blog_subscriber">
                                              <div class="checkbox">
                                                <label>
                                                <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="blog_subscriber" placeholder="" data-doctype="Lead"></span>
                                                <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                                <span class="label-area">Blog Subscriber</span>
                                                <span class="ml-1 help"></span>
                                                </label>
                                                <p class="help-box small text-muted"></p>
                                              </div>
                                              <span class="tooltip-content">blog_subscriber</span>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="tab-pane fade hide" id="lead-activities_tab" role="tabpanel" aria-labelledby="lead-activities_tab-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_7">
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_7">
                                          <form>
                                            <div class="frappe-control" data-fieldtype="HTML" data-fieldname="open_activities_html">
                                              <div class="open-activities">
                                                <div class="new-btn pb-3">
                                                  <span>
                                                    <button class="btn btn-sm small new-task-btn mr-1">
                                                      <svg class="icon icon-sm">
                                                        <use href="#icon-small-message"></use>
                                                      </svg>
                                                      New Task    
                                                    </button>
                                                    <button class="btn btn-sm small new-event-btn">
                                                      <svg class="icon icon-sm">
                                                        <use href="#icon-calendar"></use>
                                                      </svg>
                                                      New Event    
                                                    </button>
                                                  </span>
                                                </div>
                                                <div class="section-body">
                                                  <div class="open-tasks pr-1">
                                                    <div class="open-section-head">     <span class="ml-2">Open Tasks</span>    </div>
                                                    <div class="single-activity no-activity text-muted">                     No open task                 </div>
                                                  </div>
                                                  <div class="open-events pl-1">
                                                    <div class="open-section-head">     <span class="ml-2">Open Events</span>    </div>
                                                    <div class="single-activity no-activity text-muted">                 No open event             </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row form-section card-section visible-section" data-fieldname="all_activities_section">
                                      <div class="section-head">
                                        All Activities
                                        <span class="ml-2 collapse-indicator mb-1" style="display: none;"></span>
                                      </div>
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_8">
                                          <form>
                                            <div class="" data-fieldtype="HTML" data-fieldname="all_activities_html">
                                              <div class="form-footer">
                                                <div class="after-save">
                                                  <div class="comment-box" style="display: none;">
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
                                                                    <span class="ql-picker-label" tabindex="0" role="button" aria-expanded="false" aria-controls="ql-picker-options-2">
                                                                      <svg viewBox="0 0 18 18">
                                                                        <line class="ql-stroke" x1="3" x2="15" y1="9" y2="9"></line>
                                                                        <line class="ql-stroke" x1="3" x2="13" y1="14" y2="14"></line>
                                                                        <line class="ql-stroke" x1="3" x2="9" y1="4" y2="4"></line>
                                                                      </svg>
                                                                    </span>
                                                                    <span class="ql-picker-options" aria-hidden="true" tabindex="-1" id="ql-picker-options-2">
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
                                                            <button class="btn btn-xs btn-default action-btn" style="display: none;">
                                                              <svg class="icon  icon-xs" style="" aria-hidden="true">
                                                                <use class="" href="#icon-calendar"></use>
                                                              </svg>
                                                              New Event
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
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="tab-pane fade hide" id="lead-notes_tab" role="tabpanel" aria-labelledby="lead-notes_tab-tab">
                                    <div class="row form-section card-section visible-section" data-fieldname="__section_9">
                                      <div class="section-body">
                                        <div class="form-column col-sm-12" data-fieldname="__column_9">
                                          <form>
                                            <div class="frappe-control" data-fieldtype="HTML" data-fieldname="notes_html">
                                              <span class="tooltip-content">notes_html</span>
                                              <div class="notes-section col-xs-12">
                                                <div class="new-btn pb-3">
                                                  <button class="btn btn-sm small new-note-btn mr-1">
                                                    <svg class="icon icon-sm">
                                                      <use href="#icon-add"></use>
                                                    </svg>
                                                    New Note   
                                                  </button>
                                                </div>
                                                <div class="all-notes">
                                                  <div class="no-activity text-muted pt-6">                 No Notes             </div>
                                                </div>
                                              </div>                                              
                                            </div>
                                            <div class="frappe-control hide-control form-group" data-fieldtype="Table" data-fieldname="notes">
                                              <span class="tooltip-content">notes</span>
                                              <div class="grid-field">
                                                <label class="control-label">Notes</label>
                                                <span class="help"></span>
                                                <p class="text-muted small grid-description" style="display: none;"></p>
                                                <div class="grid-custom-buttons"></div>
                                                <div class="form-grid-container">
                                                  <div class="form-grid">
                                                    <div class="grid-heading-row"></div>
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
                                                      <button type="button" class="grid-add-multiple-rows btn btn-xs btn-secondary hidden">
                                                      Add Multiple
                                                      </button>
                                                    </div>
                                                    <div class="grid-pagination"></div>
                                                    <div class="grid-bulk-actions text-right">
                                                      <button type="button" class="grid-download btn btn-xs btn-secondary hidden">
                                                      Download
                                                      </button>
                                                      <button type="button" class="grid-upload btn btn-xs btn-secondary hidden">
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
                                  </div>
                                  <div class="tab-pane fade hide" id="lead-dashboard_tab" role="tabpanel" aria-labelledby="lead-dashboard_tab-tab">
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
                                          <div id="heatmap-lead" class="heatmap"></div>
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
                                    <div class="row form-section card-section empty-section" data-fieldname="__section_10">
                                      <div class="section-body"></div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="layout-footer hide"></div>
                      <div class="hide"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </BCol>
    </BRow>
    <BModal v-model="addLeadsModal" id="showmodal" hide-footer title-class="exampleModalLabel" :title="dataEdit ? 'Edit Lead' : 'Add Lead'" header-class="bg-light p-3" class="v-modal-custom" centered>
      <BForm action="" id="addform" class="tablelist-form" autocomplete="off">
        <input type="hidden" id="id" />
        <BRow class="g-3">
          <BCol lg="12">
            <div class="text-center">
              <div class="position-relative d-inline-block">
                <div class="position-absolute bottom-0 end-0">
                  <label for="lead-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Select Image" data-bs-original-title="Select Image">
                    <div class="avatar-xs cursor-pointer">
                      <div class="avatar-title bg-light border rounded-circle text-muted">
                        <i class="ri-image-fill"></i>
                      </div>
                    </div>
                  </label>
                  <input class="form-control d-none" value="" id="lead-image-input" type="file" accept="image/png, image/gif, image/jpeg">
                </div>
                <div class="avatar-lg p-1">
                  <div class="avatar-title bg-light rounded-circle">
                    <img :src="event.leadImage || multi_user_image" id="leadImage" class="avatar-md rounded-circle object-fit-cover">
                  </div>
                </div>
              </div>
              <h5 class="fs-13 mt-3">Lead Image</h5>
            </div>
            <div>
              <label for="name-field" class="form-label">Name</label>
              <input type="text" id="customername" class="form-control" placeholder="Enter Name" v-model="event.name" :class="{ 'is-invalid': submitted && !event.name }" />
              <div class="invalid-feedback">Please enter a name.</div>
            </div>
          </BCol>
          <BCol lg="12">
            <div>
              <label for="company_name-field" class="form-label">Company Name</label>
              <input type="text" id="companyname" class="form-control" placeholder="Enter company name" v-model="event.company" :class="{ 'is-invalid': submitted && !event.company }" />
              <div class="invalid-feedback">Please enter a company name.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="leads_score-field" class="form-label">Leads Score</label>
              <input type="text" id="leadsscore" class="form-control" placeholder="Enter lead score" v-model="event.score" :class="{ 'is-invalid': submitted && !event.score }" />
              <div class="invalid-feedback">Please enter a lead score.</div>
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
              <label for="phone-field" class="form-label">Email</label>
              <input type="email" id="email" class="form-control" placeholder="Enter email" v-model="event.email" :class="{ 'is-invalid': submitted && !event.email }" />
              <div class="invalid-feedback">Please enter a email.</div>
            </div>
          </BCol>
          <BCol lg="6">
            <div>
              <label for="location-field" class="form-label">Location</label>
              <input type="text" id="location" class="form-control" placeholder="Enter location" v-model="event.location" :class="{ 'is-invalid': submitted && !event.location }" />
              <div class="invalid-feedback">Please enter a location.</div>
            </div>
          </BCol>
          <div>
            <label for="" class="form-label">Tags</label>
            <Multiselect mode="tags" :close-on-select="true" :searchable="true" :create-option="false" :options="tagoption" v-model="event.tags" :class="{ 'is-invalid': submitted && !event.tags }" />
            <div class="invalid-feedback">Please select a tags.</div>
          </div>
        </BRow>
        <div class="modal-footer v-modal-footer">
          <div class="hstack gap-2 justify-content-end">
            <BButton type="button" variant="light" id="closemodal" @click="addLeadsModal = false">Close</BButton>
            <BButton type="submit" variant="success" id="add-btn" @click="handleSubmit">
              {{ dataEdit ? 'Update' : 'Add leads' }}
            </BButton>
          </div>
        </div>
      </BForm>
    </BModal>

    <!-- delete modal -->
    <BModal v-model="deleteModal" modal-class="zoomIn" hide-footer no-close-on-backdrop centered>
      <div class="mt-2 text-center">
        <lottie class="avatar-xl" colors="primary:#f7b84b,secondary:#f06548" :options="defaultOptions1" :height="100" :width="100" />
        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
          <h4>Are you sure ?You are about to delete a lead ?</h4>
          <p class="text-muted mx-4 mb-0">Deleting your lead will remove all of your information from our database.</p>
        </div>
      </div>
      <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
        <button type="button" class="btn w-sm btn-light material-shadow-none" @click="deleteModal = false">Close</button>
        <button type="button" class="btn w-sm btn-danger" id="delete-record" @click="deleteData">Yes, Delete It!</button>
      </div>
    </BModal>
  </Layout>
</template>
<style> .open-activities {  min-height: 50px;  padding-left: 0px;  padding-bottom: 15px !important; }  .open-activities .new-btn {  text-align: right; }  .single-activity {  min-height: 90px;  border: 1px solid var(--border-color);  padding: 10px;  border-bottom: 0;  padding-right: 0; }  .single-activity:last-child {  border-bottom: 1px solid var(--border-color); }  .single-activity:hover .completion-checkbox{  display: block; }  .completion-checkbox {  vertical-align: middle;  display: none; }  .checkbox {  min-width: 22px; }  .open-tasks {  width: 50%; }  .open-tasks:first-child {  border-right: 0; }  .open-events {  width: 50%; }  .open-section-head {  background-color: var(--bg-color);  min-height: 30px;  border-bottom: 1px solid var(--border-color);  padding: 10px;  font-weight: bold; }  .no-activity {     text-align: center;     padding-top: 30px; }  .form-footer {  background-color: var(--bg-color); } 
 .comment-content {     border: 1px solid var(--border-color);  border-bottom: none; }  .comment-content:last-child {     border-bottom: 1px solid var(--border-color); }  .new-btn {  text-align: right; }  .notes-section .no-activity {  min-height: 100px;  text-align: center; }  .notes-section .btn {  padding: 0.2rem 0.2rem; }  </style>