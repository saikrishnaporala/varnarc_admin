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
    <Head title="Email Groups" />
    <PageHeader title="Email Groups" pageTitle="CRM" subTitle="Campaign"/>
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
          <div class="content page-container" id="page-List/Email Group/List" data-page-route="List/Email Group/List" style="">
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
                          <h3 class="ellipsis title-text" title="Email Group">Email Group</h3>
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
                      <button class="btn btn-primary btn-sm primary-action" data-label="Add Email Group">
                        <svg class="icon  icon-xs" style="" aria-hidden="true">
                          <use class="" href="#icon-add"></use>
                        </svg>
                        <span class="hidden-xs" data-label="Add%20Email%20Group"> <span><span class="alt-underline">A</span>dd Email Group</span> </span>
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
                                    <li>      <a class="dropdown-item option" data-value="title">       Title      </a>     </li>
                                    <li>      <a class="dropdown-item option" data-value="total_subscribers">       Total Subscribers      </a>     </li>
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
                                <div class="list-row-col ellipsis hidden-xs  text-right"><span data-sort-by="total_subscribers" title="Click to sort by Total Subscribers">Total Subscribers</span></div>
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
                                <p>You haven't created a Email Group yet</p>
                                <p><button class="btn btn-default btn-sm btn-new-doc hidden-xs">
                                  Create your first Email Group
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