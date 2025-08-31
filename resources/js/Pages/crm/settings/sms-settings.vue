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
    <Head title="SMS Settings" />
    <PageHeader title="SMS Settings" pageTitle="CRM" subTitle="Settings"/>
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
          <div class="content page-container editable-form" id="page-SMS Settings" data-page-route="SMS Settings" data-state="clean" style="">
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
                          <h3 class="ellipsis title-text" title="SMS Settings">SMS Settings</h3>
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
                                <span class="liked-by like-action d-flex align-items-center not-liked" data-doctype="SMS Settings" data-name="SMS Settings">
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
                          <li class="modified-by"><a href="/app/user/Administrator">Administrator</a> last edited this · <span class="frappe-timestamp " data-timestamp="2024-11-23 15:41:11.958360" title="23-11-2024 15:41:11">5 days ago</span></li>
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
                                      <div id="heatmap-sms_settings" class="heatmap"></div>
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
                                        <div class="frappe-control" data-fieldtype="Small Text" data-fieldname="sms_gateway_url">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">SMS Gateway URL</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><textarea type="text" autocomplete="off" class="input-with-feedback form-control bold" data-fieldtype="Small Text" data-fieldname="sms_gateway_url" placeholder="" data-doctype="SMS Settings" style="height: 150px;"></textarea></div>
                                              <div class="control-value like-disabled-input for-description bold" style="display: none; white-space-collapse: preserve;"></div>
                                              <p class="help-box small text-muted">Eg. smsgateway.com/api/send_sms.cgi</p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">sms_gateway_url</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="message_parameter">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Message Parameter</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold"></div>
                                              <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                              <p class="help-box small text-muted">Enter url parameter for message</p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">message_parameter</span>
                                        </div>
                                        <div class="frappe-control input-max-width" data-fieldtype="Data" data-fieldname="receiver_parameter">
                                          <div class="form-group">
                                            <div class="clearfix">
                                              <label class="control-label reqd" style="padding-right: 0px;">Receiver Parameter</label>
                                              <span class="help"></span>
                                            </div>
                                            <div class="control-input-wrapper">
                                              <div class="control-input"><input type="text" autocomplete="off" class="input-with-feedback form-control bold"></div>
                                              <div class="control-value like-disabled-input bold" style="display: none;"></div>
                                              <p class="help-box small text-muted">Enter url parameter for receiver nos</p>
                                            </div>
                                          </div>
                                          <span class="tooltip-content">receiver_parameter</span>
                                        </div>
                                      </form>
                                    </div>
                                    <div class="form-column col-sm-6" data-fieldname="static_parameters_section">
                                      <form>
                                        <div class="frappe-control form-group" data-fieldtype="Table" data-fieldname="parameters">
                                          <span class="tooltip-content">parameters</span>
                                          <div class="grid-field">
                                            <label class="control-label">Static Parameters</label>
                                            <span class="help"></span>
                                            <p class="text-muted small grid-description">Enter static url parameters here (Eg. sender=ERPNext, username=ERPNext, password=1234 etc.)</p>
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
                                                      <div class="col grid-static-col col-xs-5 " data-fieldname="parameter" data-fieldtype="Data" title="Parameter">
                                                        <div class="field-area" style="display: none;"></div>
                                                        <div class="static-area ellipsis reqd">Parameter</div>
                                                      </div>
                                                      <div class="col grid-static-col col-xs-4 " data-fieldname="value" data-fieldtype="Data" title="Value">
                                                        <div class="field-area" style="display: none;"></div>
                                                        <div class="static-area ellipsis reqd">Value</div>
                                                      </div>
                                                      <div class="col grid-static-col col-xs-1  text-center" data-fieldname="header" data-fieldtype="Check" title="Header">
                                                        <div class="field-area" style="display: none;"></div>
                                                        <div class="static-area ellipsis">Header</div>
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
                                        <div class="form-group frappe-control input-max-width" data-fieldtype="Check" data-fieldname="use_post">
                                          <div class="checkbox">
                                            <label>
                                            <span class="input-area"><input type="checkbox" autocomplete="off" class="input-with-feedback" data-fieldtype="Check" data-fieldname="use_post" placeholder="" data-doctype="SMS Settings"></span>
                                            <span class="disp-area" style="display: none;"><input type="checkbox" disabled="" class="disabled-deselected"></span>
                                            <span class="label-area">Use POST</span>
                                            <span class="ml-1 help"></span>
                                            </label>
                                            <p class="help-box small text-muted"></p>
                                          </div>
                                          <span class="tooltip-content">use_post</span>
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
                                <div class="timeline-item" data-timestamp="2024-11-23 15:41:11.958360">
                                  <div class="timeline-dot"></div>
                                  <div class="timeline-content "><a href="/app/user/Administrator">Administrator</a> last edited this<span> · <span class="frappe-timestamp " data-timestamp="2024-11-23 15:41:11.958360" title="23-11-2024 15:41:11">5 days ago</span></span></div>
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